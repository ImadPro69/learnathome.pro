<div class="advads-condition-line-wrap">
    <input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
    <?php if( 0 <= version_compare( ADVADS_VERSION, '1.9.1' ) ) {
	    include( ADVADS_BASE_PATH . 'admin/views/ad-conditions-string-operators.php' );
    } ?>
    <input type="text" name="<?php echo $name; ?>[value]" value="<?php echo $value; ?>"/>
</div>
<p class="description">
	<?php echo esc_html( $type_options[ $options['type'] ]['description'] ); ?>
	<?php if ( isset( $type_options[ $options['type'] ]['helplink'] ) ) : ?>
		<a href="<?php echo esc_url( $type_options[ $options['type'] ]['helplink'] ) ?>" class="advads-manual-link" target="_blank">
			<?php esc_html_e( 'Manual', 'advanced-ads-pro' ); ?>
		</a>
	<?php endif; ?>
</p>
