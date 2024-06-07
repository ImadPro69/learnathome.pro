<?php
/**
 * @var string $license_key
 */
?>
<input id="advanced-ads-geo-maxmind-licence" type="text" name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ) . '[' . esc_attr( Advanced_Ads_Geo_Plugin::OPTIONS_SLUG ) . '][maxmind-license-key]'; ?>" value="<?php echo esc_attr( $license_key ); ?>">

<p class="description">
	<a target="_blank" class="advads-external-link" rel="noopener" href="https://support.maxmind.com/hc/en-us/articles/4407111582235-Generate-a-License-Key">
		<?php esc_attr_e( 'Manual', 'advanced-ads-pro' ); ?>
	</a>
</p>
