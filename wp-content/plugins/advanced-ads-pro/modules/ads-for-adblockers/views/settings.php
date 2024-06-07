<?php
$options = Advanced_Ads_Pro::get_instance()->get_options();
$module_enabled = isset( $options['ads-for-adblockers']['enabled'] ) && $options['ads-for-adblockers']['enabled'];
?>
<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[ads-for-adblockers][enabled]" id="advanced-ads-pro-ads-for-adblockers-enabled" type="checkbox" value="1" <?php checked( $module_enabled ); ?> />
<label for="advanced-ads-pro-ads-for-adblockers-enabled" class="description">
	<?php esc_html_e( 'Activate module.', 'advanced-ads-pro' ); ?>
</label>
<a href="<?php echo esc_url( ADVADS_URL ) . 'manual/ad-blockers/?utm_source=advanced-ads&utm_medium=link&utm_campaign=pro-ab-manual'; ?>" target="_blank" class="advads-manual-link"><?php esc_html_e( 'Manual', 'advanced-ads-pro' ); ?></a>
