<?php
/**
 * Refresh interval setting
 *
 * @var bool               $cb_module_enabled cache-busting module enabled.
 * @var Advanced_Ads_Group $group             the current group.
 * @var bool               $enabled           refresh interval enabled.
 * @var int                $interval          refresh interval in milliseconds.
 * @var bool               $show_warning      show warning about placement.
 */
?>
<fieldset <?php if ( ! $cb_module_enabled ) { echo 'style="display:none;"'; } ?>>
	<label><input type="checkbox" name="advads-groups[<?php echo esc_attr( $group->id ); ?>][options][refresh][enabled]" value="1" <?php checked( $enabled, 1 ); ?>><?php esc_html_e( 'Enabled', 'advanced-ads-pro' ); ?></label>
    <br>
	<label><input type="number" min="1" required="required" name="advads-groups[<?php echo esc_attr( $group->id ); ?>][options][refresh][interval]" value="<?php echo esc_attr( $interval ); ?>"> <?php esc_html_e( 'milliseconds', 'advanced-ads-pro' ); ?></label>
</fieldset>
<p class="description">
	<?php esc_html_e( 'Refresh ads on the same spot. Works when cache-busting is used.', 'advanced-ads-pro' ); ?>
	<?php if ( ! $cb_module_enabled ) : ?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=advanced-ads-settings#top#pro' ) ); ?>">(<?php esc_html_e( 'Activate now', 'advanced-ads-pro' ); ?>)</a>
	<?php endif; ?>
	<a href="<?php echo esc_url( ADVADS_URL ) . 'refresh-ads-on-the-same-spot/?utm_source=advanced-ads&utm_medium=link&utm_campaign=groups-refresh-manual'; ?>" target="_blank" class="advads-manual-link"><?php esc_html_e( 'Manual', 'advanced-ads-pro' ); ?></a>
</p>

<?php if ( $show_warning ): ?>
<p class="advads-notice-inline advads-error"><?php esc_html_e( 'Please use a placement to deliver this group using cache-busting.', 'advanced-ads-pro' ); ?></p>
<?php endif; ?>
