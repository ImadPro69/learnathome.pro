<?php
/**
 * Show the visitor information saved to the `advanced_ads_pro_server_info` cookie.
 *
 * @var Advanced_Ads_Geo_Visitor_Profile $visitor_profile
 */
?>
<p class="advads-condition-visitor-profile">
	<strong><?php esc_html_e( 'Location based on your visitor profile cookie:', 'advanced-ads-pro' ); ?></strong>

	<br>

	<?php printf( '%s, %s, %s', esc_html( $visitor_profile->get_country() ), esc_html( $visitor_profile->region ), esc_html( $visitor_profile->city ) ); ?>
	<br>
	<?php esc_html_e( 'Coordinates', 'advanced-ads-pro' ); ?>: (<?php echo (float) $visitor_profile->lat; ?> / <?php echo (float) $visitor_profile->lon; ?>)

	<br>

	<button class="advads-condition-visitor-profile-reset" class="hide-if-no-js" type="button">
		<?php esc_html_e( 'Reset Visitor Profile', 'advanced-ads-pro' ); ?>
	</button>
</p>
