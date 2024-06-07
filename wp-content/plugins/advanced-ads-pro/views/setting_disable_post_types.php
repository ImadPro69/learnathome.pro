<div id="advads-settings-hide-by-post-type">
	<?php
	foreach ( $post_types as $_type_id => $_type ) {
		$checked = in_array( $_type_id, $selected, true );

		if ( $type_label_counts[ $_type->label ] < 2 ) {
			$_label = $_type->label;
		} else {
			$_label = sprintf( '%s (%s)', $_type->label, $_type_id );
		}
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( ADVADS_SLUG ); ?>[pro][general][disable-by-post-types][]" <?php checked( $checked, true ); ?> value="<?php echo esc_attr( $_type_id ); ?>"><?php echo esc_html( $_label ); ?>
		</label>
		<?php
	}
	?>
</div>
