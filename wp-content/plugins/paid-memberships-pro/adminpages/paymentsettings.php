<?php
	//only admins can get this
	if(!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_paymentsettings")))
	{
		die(esc_html__("You do not have permissions to perform this action.", 'paid-memberships-pro' ));
	}

	global $wpdb, $pmpro_currency_symbol, $msg, $msgt;

	/*
		Since 2.0, we let each gateway define what options they have in the class files
	*/
	//define options
	$payment_options = array_unique(apply_filters("pmpro_payment_options", array('gateway')));

	//check nonce for saving settings
	if (!empty($_REQUEST['savesettings']) && (empty($_REQUEST['pmpro_paymentsettings_nonce']) || !check_admin_referer('savesettings', 'pmpro_paymentsettings_nonce'))) {
		$msg = -1;
		$msgt = __("Are you sure you want to do that? Try again.", 'paid-memberships-pro' );
		unset($_REQUEST['savesettings']);
	}

	//get/set settings
	if(!empty($_REQUEST['savesettings']))
	{
		/*
			Save any value that might have been passed in
		*/
		foreach($payment_options as $option) {
			//for now we make a special case for sslseal, but we need a way to specify sanitize functions for other fields
			if( in_array( $option, array( 'sslseal', 'instructions' ) ) ) {
				global $allowedposttags;
				$html = wp_kses(wp_unslash($_POST[$option]), $allowedposttags);
				update_option("pmpro_{$option}", $html);
            } else {
				pmpro_setOption($option);
			}
		}

		do_action( 'pmpro_after_saved_payment_options', $payment_options );

		/*
			Some special case options still worked out here
		*/
		//credit cards
		$pmpro_accepted_credit_cards = array();
		if(!empty($_REQUEST['creditcards_visa']))
			$pmpro_accepted_credit_cards[] = "Visa";
		if(!empty($_REQUEST['creditcards_mastercard']))
			$pmpro_accepted_credit_cards[] = "Mastercard";
		if(!empty($_REQUEST['creditcards_amex']))
			$pmpro_accepted_credit_cards[] = "American Express";
		if(!empty($_REQUEST['creditcards_discover']))
			$pmpro_accepted_credit_cards[] = "Discover";
		if(!empty($_REQUEST['creditcards_dinersclub']))
			$pmpro_accepted_credit_cards[] = "Diners Club";
		if(!empty($_REQUEST['creditcards_enroute']))
			$pmpro_accepted_credit_cards[] = "EnRoute";
		if(!empty($_REQUEST['creditcards_jcb']))
			$pmpro_accepted_credit_cards[] = "JCB";

		pmpro_setOption("accepted_credit_cards", implode(",", $pmpro_accepted_credit_cards));

		//assume success
		$msg = true;
		$msgt = __("Your payment settings have been updated.", 'paid-memberships-pro' );
	}

	/*
		Extract values for use later
	*/
	$payment_option_values = array();
	foreach($payment_options as $option)
		$payment_option_values[$option] = get_option( 'pmpro_' . $option);
	extract($payment_option_values);

	/*
		Some special cases that get worked out here.
	*/
	//make sure the tax rate is not > 1
	$tax_state = get_option( "pmpro_tax_state");
	$tax_rate = get_option( "pmpro_tax_rate");
	if((double)$tax_rate > 1)
	{
		//assume the entered X%
		$tax_rate = $tax_rate / 100;
		pmpro_setOption("tax_rate", $tax_rate);
	}

	//accepted credit cards
	$pmpro_accepted_credit_cards = $payment_option_values['accepted_credit_cards'];	//this var has the pmpro_ prefix

	//default settings
	if(empty($gateway_environment))
	{
		$gateway_environment = "sandbox";
		pmpro_setOption("gateway_environment", $gateway_environment);
	}
	if(empty($pmpro_accepted_credit_cards))
	{
		$pmpro_accepted_credit_cards = "Visa,Mastercard,American Express,Discover";
		pmpro_setOption("accepted_credit_cards", $pmpro_accepted_credit_cards);
	}
	$pmpro_accepted_credit_cards = explode(",", $pmpro_accepted_credit_cards);

	require_once(dirname(__FILE__) . "/admin_header.php");
?>

	<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('savesettings', 'pmpro_paymentsettings_nonce');?>
		<hr class="wp-header-end">
        <h1><?php esc_html_e( 'Payment Gateway', 'paid-memberships-pro' );?> &amp; <?php esc_html_e( 'SSL Settings', 'paid-memberships-pro' ); ?></h1>
		<div id="choose-gateway" class="pmpro_section" data-visibility="shown" data-activated="true">
			<div class="pmpro_section_toggle">
				<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
					<?php esc_html_e( 'Payment Gateway', 'paid-memberships-pro' ); ?>
				</button>
			</div>
			<div class="pmpro_section_inside">
				<p><?php			
					$gateway_settings_link = '<a title="' . esc_attr__( 'Paid Memberships Pro - Payment Gateway Settings', 'paid-memberships-pro' ) . '" target="_blank" rel="nofollow noopener" href="https://www.paidmembershipspro.com/documentation/admin/payment-ssl-settings/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=documentation&utm_content=payment-gateway-settings">' . esc_html__( 'Payment Gateway Settings', 'paid-memberships-pro' ) . '</a>';
					$ssl_settings_link = '<a title="' . esc_attr__( 'Paid Memberships Pro - SSL Settings', 'paid-memberships-pro' ) . '" target="_blank" rel="nofollow noopener" href="https://www.paidmembershipspro.com/documentation/initial-plugin-setup/ssl/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=documentation&utm_content=ssl&utm_term=link1">' . esc_html__( 'SSL', 'paid-memberships-pro' ) . '</a>';
					// translators: %s and %s: Links to Payment Gateway Settings and SSL Settings docs.
					printf( esc_html__('Learn more about %s and %s.', 'paid-memberships-pro' ), $gateway_settings_link, $ssl_settings_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?></p>
				<table class="form-table">
				<tbody>
					<tr>
						<th scope="row" valign="top">
							<label for="gateway"><?php esc_html_e('Payment Gateway', 'paid-memberships-pro' );?></label>
						</th>
						<td>
							<select id="gateway" name="gateway">
								<?php
									$pmpro_gateways = pmpro_gateways();
									foreach($pmpro_gateways as $pmpro_gateway_name => $pmpro_gateway_label)
									{
									?>
									<option value="<?php echo esc_attr($pmpro_gateway_name);?>" <?php selected($gateway, $pmpro_gateway_name);?>><?php echo esc_html( $pmpro_gateway_label );?></option>
									<?php
									}
								?>
							</select>
							<?php if( pmpro_onlyFreeLevels() ) { ?>
								<div id="pmpro-default-gateway-message" style="display:none;"><p class="description"><?php echo esc_html__( 'This gateway is for membership sites with Free levels or for sites that accept payment offline.', 'paid-memberships-pro' )
								. '<br/>'
								. esc_html__( 'It is not connected to a live gateway environment and cannot accept payments.', 'paid-memberships-pro' ); ?></p></div>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="gateway_environment"><?php esc_html_e('Gateway Environment', 'paid-memberships-pro' );?></label>
						</th>
						<td>
							<select id="gateway_environment" name="gateway_environment">
								<option value="sandbox" <?php selected( $gateway_environment, "sandbox" ); ?>><?php esc_html_e('Sandbox/Testing', 'paid-memberships-pro' );?></option>
								<option value="live" <?php selected( $gateway_environment, "live" ); ?>><?php esc_html_e('Live/Production', 'paid-memberships-pro' );?></option>
							</select>
							<script>
								function pmpro_changeGateway()
								{
									const gateway = jQuery('#gateway').val();
									const gateway_environment = jQuery('#gateway_environment').val();

									//hide all gateway options
									jQuery('tr.gateway').hide();
									jQuery('tr.gateway_'+gateway).show();
									jQuery('tr.gateway_'+gateway+'_'+gateway_environment).show();
									
									//hide sub settings and toggle them on based on triggers
									jQuery('tr.pmpro_toggle_target').hide();
									jQuery( 'input[pmpro_toggle_trigger_for]' ).each( function() {										
										if ( jQuery( this ).is( ':visible' ) ) {
											pmpro_toggle_elements_by_selector( jQuery( this ).attr( 'pmpro_toggle_trigger_for' ), jQuery( this ).prop( 'checked' ) );
										}
									});							

									if ( jQuery('#gateway').val() === '' ) {
										jQuery('#pmpro-default-gateway-message').show();
									} else {
										jQuery('#pmpro-default-gateway-message').hide();
									}
								}
								pmpro_changeGateway();

								// Handle change events.
								jQuery('#gateway, #gateway_environment').on('change', pmpro_changeGateway);
							</script>
						</td>
					</tr>

					<?php /* Gateway Specific Settings */ ?>
					<?php do_action('pmpro_payment_option_fields', $payment_option_values, $gateway); ?>
				</tbody>
				</table>
			</div> <!-- end pmpro_section_inside -->
		</div> <!-- end pmpro_section -->
		<div id="currency-tax-settings" class="pmpro_section" data-visibility="shown" data-activated="true">
			<div class="pmpro_section_toggle">
				<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
					<?php esc_html_e( 'Currency and Tax Settings', 'paid-memberships-pro' ); ?>
				</button>
			</div>
			<div class="pmpro_section_inside">
				<table class="form-table">
				<tbody>
					<tr class="gateway gateway_ <?php echo esc_attr(pmpro_getClassesForPaymentSettingsField("currency"));?>" <?php if(!empty($gateway) && $gateway != "paypal" && $gateway != "paypalexpress" && $gateway != "check" && $gateway != "paypalstandard" && $gateway != "braintree" && $gateway != "twocheckout" && $gateway != "cybersource" && $gateway != "payflowpro" && $gateway != "stripe" && $gateway != "authorizenet" && $gateway != "gourl") { ?>style="display: none;"<?php } ?>>
						<th scope="row" valign="top">
							<label for="currency"><?php esc_html_e('Currency', 'paid-memberships-pro' );?></label>
						</th>
						<td>
							<select name="currency">
							<?php
								global $pmpro_currencies;
								foreach($pmpro_currencies as $ccode => $cdescription)
								{
									if(is_array($cdescription))
										$cdescription = $cdescription['name'];
								?>
								<option value="<?php echo esc_attr( $ccode ) ?>" <?php if($currency == $ccode) { ?>selected="selected"<?php } ?>><?php echo esc_html( $cdescription ); ?></option>
								<?php
								}
							?>
							</select>
							<p class="description"><?php esc_html_e( 'Not all currencies will be supported by every gateway. Please check with your gateway.', 'paid-memberships-pro' ); ?></p>
						</td>
					</tr>
					<tr class="gateway gateway_ <?php echo esc_attr(pmpro_getClassesForPaymentSettingsField("accepted_credit_cards"));?>" <?php if(!empty($gateway) && $gateway != "authorizenet" && $gateway != "paypal" && $gateway != "payflowpro" && $gateway != "braintree" && $gateway != "twocheckout" && $gateway != "cybersource") { ?>style="display: none;"<?php } ?>>
						<th scope="row" valign="top">
							<label for="creditcards"><?php esc_html_e('Accepted Credit Card Types', 'paid-memberships-pro' );?></label>
						</th>
						<td>
							<input type="checkbox" id="creditcards_visa" name="creditcards_visa" value="1" <?php if(in_array("Visa", $pmpro_accepted_credit_cards)) { ?>checked="checked"<?php } ?> /> <label for="creditcards_visa">Visa</label><br />
							<input type="checkbox" id="creditcards_mastercard" name="creditcards_mastercard" value="1" <?php if(in_array("Mastercard", $pmpro_accepted_credit_cards)) { ?>checked="checked"<?php } ?> /> <label for="creditcards_mastercard">Mastercard</label><br />
							<input type="checkbox" id="creditcards_amex" name="creditcards_amex" value="1" <?php if(in_array("American Express", $pmpro_accepted_credit_cards)) { ?>checked="checked"<?php } ?> /> <label for="creditcards_amex">American Express</label><br />
							<input type="checkbox" id="creditcards_discover" name="creditcards_discover" value="1" <?php if(in_array("Discover", $pmpro_accepted_credit_cards)) { ?>checked="checked"<?php } ?> /> <label for="creditcards_discover">Discover</label><br />
							<input type="checkbox" id="creditcards_dinersclub" name="creditcards_dinersclub" value="1" <?php if(in_array("Diners Club", $pmpro_accepted_credit_cards)) {?>checked="checked"<?php } ?> /> <label for="creditcards_dinersclub">Diner's Club</label><br />
							<input type="checkbox" id="creditcards_enroute" name="creditcards_enroute" value="1" <?php if(in_array("EnRoute", $pmpro_accepted_credit_cards)) {?>checked="checked"<?php } ?> /> <label for="creditcards_enroute">EnRoute</label><br />
							<input type="checkbox" id="creditcards_jcb" name="creditcards_jcb" value="1" <?php if(in_array("JCB", $pmpro_accepted_credit_cards)) {?>checked="checked"<?php } ?> /> <label for="creditcards_jcb">JCB</label><br />
						</td>
					</tr>
					<tr class="gateway gateway_ <?php echo esc_attr(pmpro_getClassesForPaymentSettingsField("tax_rate"));?>" <?php if(!empty($gateway) && $gateway != "stripe" && $gateway != "authorizenet" && $gateway != "paypal" && $gateway != "paypalexpress" && $gateway != "check" && $gateway != "paypalstandard" && $gateway != "payflowpro" && $gateway != "braintree" && $gateway != "twocheckout" && $gateway != "cybersource") { ?>style="display: none;"<?php } ?>>
						<th scope="row" valign="top">
							<label for="tax"><?php esc_html_e('Sales Tax', 'paid-memberships-pro' );?> (<?php esc_html_e('optional', 'paid-memberships-pro' );?>)</label>
						</th>
						<td>
							<?php esc_html_e('Tax State', 'paid-memberships-pro' );?>:
							<input type="text" id="tax_state" name="tax_state" value="<?php echo esc_attr($tax_state)?>" class="small-text" /> (<?php esc_html_e('abbreviation, e.g. "PA"', 'paid-memberships-pro' );?>)
							&nbsp; <?php esc_html_e('Tax Rate', 'paid-memberships-pro' ); ?>:
							<input type="text" id="tax_rate" name="tax_rate" size="10" value="<?php echo esc_attr($tax_rate)?>" class="small-text" /> (<?php esc_html_e('decimal, e.g. "0.06"', 'paid-memberships-pro' );?>)					
							<p class="description">
								<?php
									$filter_link = '<a target="_blank" rel="nofollow noopener" href="https://www.paidmembershipspro.com/non-us-taxes-paid-memberships-pro/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=blog&utm_content=non-us-taxes-paid-memberships-pro">pmpro_tax filter</a>';
									// translators: %s: A link to the docs for the pmpro_tax filter.
									printf( esc_html__('US only. If values are given, tax will be applied for any members ordering from the selected state. For non-US or more complex tax rules, use the %s.', 'paid-memberships-pro' ), $filter_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</p>
						</td>
					</tr>
				</tbody>
				</table>
			</div> <!-- end pmpro_section_inside -->
		</div> <!-- end pmpro_section -->
		<div id="ssl-settings" class="pmpro_section" data-visibility="shown" data-activated="true">
			<div class="pmpro_section_toggle">
				<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
					<?php esc_html_e( 'SSL Settings', 'paid-memberships-pro' ); ?>
				</button>
			</div>
			<div class="pmpro_section_inside">
				<table class="form-table">
				<tbody>
					<tr class="gateway gateway_ <?php echo esc_attr(pmpro_getClassesForPaymentSettingsField("use_ssl"));?>">
						<th scope="row" valign="top">
							<label for="use_ssl"><?php esc_html_e('Force SSL', 'paid-memberships-pro' );?></label>
						</th>
						<td>
							<?php
								if( pmpro_check_site_url_for_https() ) {
									//entire site is over HTTPS
									?>
									<p class="description"><?php esc_html_e( 'Your Site URL starts with https:// and so PMPro will allow your entire site to be served over HTTPS.', 'paid-memberships-pro' ); ?></p>
									<?php
								} else {
									//site is not over HTTPS, show setting
									?>
									<select id="use_ssl" name="use_ssl">
										<option value="0" <?php if(empty($use_ssl)) { ?>selected="selected"<?php } ?>><?php esc_html_e('No', 'paid-memberships-pro' );?></option>
										<option value="1" <?php if(!empty($use_ssl) && $use_ssl == 1) { ?>selected="selected"<?php } ?>><?php esc_html_e('Yes', 'paid-memberships-pro' );?></option>
										<option value="2" <?php if(!empty($use_ssl) && $use_ssl == 2) { ?>selected="selected"<?php } ?>><?php esc_html_e('Yes (with JavaScript redirects)', 'paid-memberships-pro' );?></option>
									</select>
									<p class="description"><?php esc_html_e('Recommended: Yes. Try the JavaScript redirects setting if you are having issues with infinite redirect loops.', 'paid-memberships-pro' ); ?></p>
									<?php
								}
							?>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="sslseal"><?php esc_html_e('SSL Seal Code', 'paid-memberships-pro' );?></label>
						</th>
						<td>
							<textarea id="sslseal" name="sslseal" rows="3" cols="50" class="large-text">
								<?php
								// This value is set by admins and could contain JS. Will be replaced with a hook in future versions.
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo stripslashes(esc_textarea($sslseal))
								?>
							</textarea>
							<p class="description">
								<?php
									$ssl_certificate_link = '<a target="_blank" rel="nofollow noopener" href="https://www.paidmembershipspro.com/documentation/initial-plugin-setup/ssl/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=documentation&utm_content=ssl&utm_term=link2">' . esc_html__( 'SSL Certificate', 'paid-memberships-pro' ) . '</a>';
									// translators: %s: Link to SSL Certificate docs.
									printf( esc_html__('Your %s must be installed by your web host. Use this field to display your seal or other trusted merchant images. This field does not accept JavaScript.', 'paid-memberships-pro' ), $ssl_certificate_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="nuclear_HTTPS"><?php esc_html_e('Extra HTTPS URL Filter', 'paid-memberships-pro' );?></label>
						</th>
						<td>
							<input type="checkbox" id="nuclear_HTTPS" name="nuclear_HTTPS" value="1" <?php if(!empty($nuclear_HTTPS)) { ?>checked="checked"<?php } ?> /> <label for="nuclear_HTTPS"><?php esc_html_e('Pass all generated HTML through a URL filter to add HTTPS to URLs used on secure pages. Check this if you are using SSL and have warnings on your checkout pages.', 'paid-memberships-pro' );?></label>
						</td>
					</tr>
				</tbody>
				</table>
			</div> <!-- end pmpro_section_inside -->
		</div> <!-- end pmpro_section -->
		<p class="submit">
			<input name="savesettings" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Settings', 'paid-memberships-pro' );?>" />
		</p>
	</form>

<?php
	require_once(dirname(__FILE__) . "/admin_footer.php");
?>
