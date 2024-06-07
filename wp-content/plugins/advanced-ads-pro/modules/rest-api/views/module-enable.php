<?php
/**
 * Render the REST API module.
 *
 * @var bool   $module_enabled
 * @var string $option_key
 */
?>
<input name="<?php echo esc_attr( $option_key ); ?>[enabled]" id="advanced-ads-pro-rest-api-enabled" type="checkbox" value="1" <?php checked( $module_enabled ); ?> />

<label for="advanced-ads-pro-rest-api-enabled" class="description">
	<?php esc_html_e( 'Activate module.', 'advanced-ads-pro' ); ?>
</label>

<a href="<?php echo esc_url( ADVADS_URL ) . 'manual/rest-api/?utm_source=advanced-ads&utm_medium=link&utm_campaign=pro-rest-api-manual'; ?>" target="_blank" class="advads-manual-link"><?php esc_html_e( 'Manual', 'advanced-ads-pro' ); ?></a>
