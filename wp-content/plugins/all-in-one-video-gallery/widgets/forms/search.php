<?php

/**
 * Admin form: Search widget.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div class="aiovg aiovg-widget-form aiovg-widget-form-search">
	<div class="aiovg-widget-section">
		<div class="aiovg-widget-field aiovg-widget-field-title">
			<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'all-in-one-video-gallery' ); ?></label> 
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat aiovg-widget-input-title" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-template">
			<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>"><?php esc_html_e( 'Select Template', 'all-in-one-video-gallery' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>" class="widefat aiovg-widget-input-template"> 
				<?php
				$options = array(
					'vertical'   => __( 'Vertical', 'all-in-one-video-gallery' ),
					'horizontal' => __( 'Horizontal', 'all-in-one-video-gallery' )	
				);
			
				foreach( $options as $key => $value ) {
					printf( 
						'<option value="%s"%s>%s</option>', 
						$key, 
						selected( $key, $instance['template'], false ), 
						esc_html( $value )
					);
				}
				?>
			</select>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-has_keyword">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'has_keyword' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'has_keyword' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'has_keyword' ) ); ?>" class="aiovg-widget-input-has_keyword" value="1" <?php checked( 1, $instance['has_keyword'] ); ?> /> 
				<?php esc_html_e( 'Search By Video Title, Description', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-has_category">		 
			<label for="<?php echo esc_attr( $this->get_field_id( 'has_category' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'has_category' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'has_category' ) ); ?>" class="aiovg-widget-input-has_category" value="1" <?php checked( 1, $instance['has_category'] ); ?> />
				<?php esc_html_e( 'Search By Categories', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-has_tag">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'has_tag' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'has_tag' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'has_tag' ) ); ?>" class="aiovg-widget-input-has_tag" value="1" <?php checked( 1, $instance['has_tag'] ); ?> /> 
				<?php esc_html_e( 'Search By Tags', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>
	</div>
</div>