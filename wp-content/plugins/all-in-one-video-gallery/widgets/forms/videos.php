<?php

/**
 * Admin form: Videos widget.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div class="aiovg aiovg-widget-form aiovg-widget-form-videos aiovg-template-<?php echo esc_attr( $instance['template'] ); ?>">
	<?php foreach ( $this->fields['videos']['sections'] as $key => $section ) :	?>
		<div class="aiovg-widget-section aiovg-widget-section-<?php echo esc_attr( $key ); ?>">
			<?php if ( 'general' !== $key ) : ?>
				<div class="aiovg-widget-section-header aiovg-margin-top aiovg-text-small">
					<?php echo wp_kses_post( $section['title'] ); ?>
				</div>
			<?php endif; ?>
			
			<?php foreach ( $section['fields'] as $field ) :
				$field_name = sanitize_text_field( $field['name'] );
				if ( in_array( $field_name, $this->excluded_fields ) ) continue;
				?>
				<div class="aiovg-widget-field aiovg-widget-field-<?php echo esc_attr( $field_name ); ?>">
					<?php if ( 'header' == $field['type'] ) : ?>
						<label class="aiovg-widget-label aiovg-font-bold" for="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
					<?php elseif ( 'categories' == $field['type'] ) : ?>
						<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>"><?php echo esc_html( $field['label'] ); ?></label> 
						<ul id="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>" class="widefat aiovg-widget-input-<?php echo esc_attr( $field_name ); ?> aiovg-checklist">
							<?php
							$args = array(
								'taxonomy'      => 'aiovg_categories',
								'selected_cats' => array_map( 'intval', $instance[ $field_name ] ),
								'walker'        => new AIOVG_Walker_Terms_Checklist( $this->get_field_name( $field_name ) ),
								'checked_ontop' => false
							); 

							wp_terms_checklist( 0, $args );
							?>
						</ul>
					<?php elseif ( 'tags' == $field['type'] ) : ?>
						<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>"><?php echo esc_html( $field['label'] ); ?></label> 
						<ul id="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>" class="widefat aiovg-widget-input-<?php echo esc_attr( $field_name ); ?> aiovg-checklist">
							<?php
							$args = array(
								'taxonomy'      => 'aiovg_tags',
								'selected_cats' => array_map( 'intval', $instance[ $field_name ] ),
								'walker'        => new AIOVG_Walker_Terms_Checklist( $this->get_field_name( $field_name ) ),
								'checked_ontop' => false
							); 

							wp_terms_checklist( 0, $args );
							?>
						</ul>
					<?php elseif ( 'text' == $field['type'] || 'url' == $field['type'] || 'number' == $field['type'] ) : ?>
						<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>"><?php echo esc_html( $field['label'] ); ?></label> 
						<input type="text" name="<?php echo esc_attr( $this->get_field_name( $field_name ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>" class="widefat aiovg-widget-input-<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $instance[ $field_name ] ); ?>" />
					<?php elseif ( 'select' == $field['type'] ) : ?>
						<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
						<select name="<?php echo esc_attr( $this->get_field_name( $field_name ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>" class="widefat aiovg-widget-input-<?php echo esc_attr( $field_name ); ?>"> 
							<?php				
							foreach ( $field['options'] as $key => $value ) {
								printf( 
									'<option value="%s"%s>%s</option>', 
									esc_attr( $key ), 
									selected( $key, $instance[ $field_name ], false ), 
									esc_html( $value )
								);
							}
							?>
						</select>
					<?php elseif ( 'checkbox' == $field['type'] ) : ?>						
						<label for="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>">
							<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( $field_name ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>" class="aiovg-widget-input-<?php echo esc_attr( $field_name ); ?>" value="1" <?php checked( 1, $instance[ $field_name ] ); ?> />
							<?php echo esc_html( $field['label'] ); ?>
						</label>
					<?php elseif ( 'color' == $field['type'] ) : ?>
						<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>"><?php echo esc_html( $field['label'] ); ?></label> 
						<input type="text" name="<?php echo esc_attr( $this->get_field_name( $field_name ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>" class="widefat aiovg-widget-input-<?php echo esc_attr( $field_name ); ?> aiovg-color-picker" value="<?php echo esc_attr( $instance[ $field_name ] ); ?>" />
					<?php endif; ?>

					<?php if ( ! empty( $field['description'] ) ) : // Description ?>
						<p class="description"><?php echo wp_kses_post( $field['description'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>				
	<?php endforeach; ?>
</div>