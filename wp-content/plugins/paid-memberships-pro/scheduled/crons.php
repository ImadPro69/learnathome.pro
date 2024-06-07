<?php
/*
	Expiring Memberships
*/	
add_action("pmpro_cron_expire_memberships", "pmpro_cron_expire_memberships");
function pmpro_cron_expire_memberships()
{
	global $wpdb;

	//Don't let anything run if PMPro is paused
	if( pmpro_is_paused() ) {
		return;
	}

	//clean up errors in the memberships_users table that could cause problems
	pmpro_cleanup_memberships_users_table();

	$today = date("Y-m-d H:i:00", current_time("timestamp"));

	//look for memberships that expired before today
	$sqlQuery = "SELECT mu.user_id, mu.membership_id, mu.startdate, mu.enddate FROM $wpdb->pmpro_memberships_users mu WHERE mu.status = 'active' AND mu.enddate IS NOT NULL AND mu.enddate <> '0000-00-00 00:00:00' AND mu.enddate <= '" . esc_sql( $today ) . "' ORDER BY mu.enddate";

	if(defined('PMPRO_CRON_LIMIT'))
		$sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;
	$expired = $wpdb->get_results($sqlQuery);

	foreach($expired as $e)
	{
		do_action("pmpro_membership_pre_membership_expiry", $e->user_id, $e->membership_id );		

		//remove their membership
		pmpro_cancelMembershipLevel( $e->membership_id, $e->user_id, 'expired' );

		do_action("pmpro_membership_post_membership_expiry", $e->user_id, $e->membership_id );

		if( get_user_meta( $e->user_id, 'pmpro_disable_notifications', true ) ){
			$send_email = false;
		}
		
		$send_email = apply_filters("pmpro_send_expiration_email", true, $e->user_id);

		if($send_email)
		{
			//send an email
			$pmproemail = new PMProEmail();
			$euser = get_userdata($e->user_id);
			if ( ! empty( $euser ) ) {
				$pmproemail->sendMembershipExpiredEmail( $euser, $e->membership_id );

				if ( WP_DEBUG ) {
					error_log( sprintf(__("Membership expired email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email) );
				}
			}
		}
	}
}

/*
	Expiration Warning Emails
*/
add_action("pmpro_cron_expiration_warnings", "pmpro_cron_expiration_warnings");
function pmpro_cron_expiration_warnings()
{
	global $wpdb;

	//Don't let anything run if PMPro is paused
	if( pmpro_is_paused() ) {
		return;
	}

	//clean up errors in the memberships_users table that could cause problems
	pmpro_cleanup_memberships_users_table();

	$today = date("Y-m-d H:i:s", current_time("timestamp"));

	$pmpro_email_days_before_expiration = apply_filters("pmpro_email_days_before_expiration", 7);

	// Configure the interval to select records from
	$interval_start = $today;
	$interval_end = date( 'Y-m-d H:i:s', strtotime( "{$today} +{$pmpro_email_days_before_expiration} days", current_time( 'timestamp' ) ) );

	//look for memberships that are going to expire within one week (but we haven't emailed them within a week)
	$sqlQuery = $wpdb->prepare(
		"SELECT DISTINCT
  				mu.user_id,
  				mu.membership_id,
  				mu.startdate,
 				mu.enddate,
 				um.meta_value AS notice
 			FROM {$wpdb->pmpro_memberships_users} AS mu
 			  LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = mu.user_id
            	AND um.meta_key = CONCAT( 'pmpro_expiration_notice_', mu.membership_id )
			WHERE ( um.meta_value IS NULL OR DATE_ADD(um.meta_value, INTERVAL %d DAY) < %s )
				AND ( mu.status = 'active' )
 			    AND ( mu.enddate IS NOT NULL )
 			    AND ( mu.enddate <> '0000-00-00 00:00:00' )
 			    AND ( mu.enddate BETWEEN %s AND %s )
 			    AND ( mu.membership_id <> 0 OR mu.membership_id <> NULL )
			ORDER BY mu.enddate
			",
		$pmpro_email_days_before_expiration,
		$today,
		$interval_start,
		$interval_end
	);

	if(defined('PMPRO_CRON_LIMIT'))
		$sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;

	$expiring_soon = $wpdb->get_results($sqlQuery);

	foreach($expiring_soon as $e)
	{
		$send_email = apply_filters("pmpro_send_expiration_warning_email", true, $e->user_id);
		if($send_email)
		{
			//send an email
			$pmproemail = new PMProEmail();
			$euser = get_userdata($e->user_id);
			if ( ! empty( $euser ) ) {
				$pmproemail->sendMembershipExpiringEmail( $euser, $e->membership_id);

				if ( WP_DEBUG ) {
					error_log( sprintf( __("Membership expiring email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email) );
				}
			}
		}

		//delete all user meta for this key to prevent duplicate user meta rows
		delete_user_meta( $e->user_id, 'pmpro_expiration_notice' );

		//update user meta so we don't email them again
		update_user_meta( $e->user_id, 'pmpro_expiration_notice_' . $e->membership_id, $today );
	}
}

/*
	Credit Card Expiring Warnings
	@deprecated 3.0
*/
function pmpro_cron_credit_card_expiring_warnings() {
	global $wpdb;

	_deprecated_function( __FUNCTION__, '3.0' );

	//Don't let anything run if PMPro is paused
	if( pmpro_is_paused() ) {
		return;
	}

	//clean up errors in the memberships_users table that could cause problems
	pmpro_cleanup_memberships_users_table();

	$next_month_date = date("Y-m-01", strtotime("+2 months", current_time("timestamp")));
			
	$sqlQuery = "SELECT mu.user_id
					FROM  $wpdb->pmpro_memberships_users mu
						LEFT JOIN $wpdb->usermeta um1 ON mu.user_id = um1.user_id
							AND meta_key =  'pmpro_ExpirationMonth'
						LEFT JOIN $wpdb->usermeta um2 ON mu.user_id = um2.user_id
							AND um2.meta_key =  'pmpro_ExpirationYear'
						LEFT JOIN $wpdb->usermeta um3 ON mu.user_id = um3.user_id
							AND um3.meta_key = 'pmpro_credit_card_expiring_warning'
					WHERE mu.status =  'active'
						AND mu.cycle_number > 0
						AND um1.meta_value IS NOT NULL AND um2.meta_value IS NOT NULL
						AND um1.meta_value <> '' AND um2.meta_value <> ''
						AND CONCAT(um2.meta_value, '-', um1.meta_value, '-01') < '" . esc_sql( $next_month_date ) . "'
						AND (um3.meta_value IS NULL OR CONCAT(um2.meta_value, '-', um1.meta_value, '-01') <> um3.meta_value)
				";

	if(defined('PMPRO_CRON_LIMIT'))
		$sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;

	$cc_expiring_user_ids = $wpdb->get_col($sqlQuery);

	if(!empty($cc_expiring_user_ids))
	{
		require_once(ABSPATH . 'wp-includes/pluggable.php');

		foreach($cc_expiring_user_ids as $user_id)
		{
			//get user
			$euser = get_userdata($user_id);
			if ( empty( $euser ) ) {
				continue;
			}

			//make sure their level doesn't have a billing limit that's been reached
			$euser->membership_level = pmpro_getMembershipLevelForUser($euser->ID);
			if(!empty($euser->membership_level->billing_limit))
			{
				/*
					There is a billing limit on this level, skip for now.
					We should figure out how to tell if the limit has been reached
					and if not, email the user about the expiring credit card.
				*/
				continue;
			}

			//make sure they are using a credit card type billing method for their current membership level (check the last order)
			$last_order = new MemberOrder();
			$last_order->getLastMemberOrder($euser->ID);
			if(empty($last_order->accountnumber) || (!empty($last_order->accountnumber) && 'XXXXXXXXXXXXXXXX' == $last_order->accountnumber))
				continue;

			//okay send them an email
			$send_email = apply_filters("pmpro_send_credit_card_expiring_email", true, $euser->ID);

			if($send_email)
			{
				//send an email
				$pmproemail = new PMProEmail();
				$pmproemail->sendCreditCardExpiringEmail($euser,$last_order);

				if ( WP_DEBUG ) {
					error_log( sprintf( __("Credit card expiring email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email) );
				}
			}

		}
	}
}

add_action( 'pmpro_cron_admin_activity_email', 'pmpro_cron_admin_activity_email' );
function pmpro_cron_admin_activity_email() {
	//Don't let anything run if PMPro is paused
	if( pmpro_is_paused() ) {
		return;
	}
	
	$frequency = get_option( 'pmpro_activity_email_frequency' );
	if ( empty( $frequency ) ) {
		$frequency = 'week';
	}
	// Send every day, Monday each week, or first Monday of every month.
	if (
		'day' === $frequency ||
		( 'week' === $frequency && 'Mon' === date( 'D' ) ) ||
		( 'month' === $frequency && 'Mon' === date( 'D' ) && 7 >= date( 'j' ) )
	) {
		$pmproemail = new PMPro_Admin_Activity_Email();
		$pmproemail->sendAdminActivity();
	}
}
