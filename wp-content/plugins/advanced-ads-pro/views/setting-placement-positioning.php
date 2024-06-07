<?php
/**
 * Render the setting to enable output buffering.
 *
 * @var string $placement_positioning Placement positioning method.
 * @var array  $allowed_types_names A list of placement types that use output buffering.
 */
?>

<p class="description">
<?php
/* translators: %s: Names of placement types */
echo esc_html( sprintf( __( 'Choose when Advanced Ads will add the following placement types: %s', 'advanced-ads-pro' ), implode( ', ', $allowed_types_names ) ) );
?>
</p>

<label>
	<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[placement-positioning]" type="radio" value="js" <?php checked( $placement_positioning, 'js' ); ?> />
	<?php esc_html_e( 'After page load using JavaScript', 'advanced-ads-pro' ); ?>
</label>

<label>
	<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[placement-positioning]" type="radio" value="php" <?php checked( $placement_positioning, 'php' ); ?> />
	<?php esc_html_e( 'Before page load using PHP.', 'advanced-ads-pro' ); ?>
</label>

<span>
	<?php
	esc_html_e( 'This method also works on AMP pages and causes fewer conflicts with website optimization features. However, it can cause critical issues with a few other plugins that use a similar technique (i.e., output buffering). We recommend less technical users test it carefully.', 'advanced-ads-pro' );
	?>
</span>
