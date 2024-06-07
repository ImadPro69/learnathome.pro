<?php

/**
 * Admin form: Video player widget.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

global $pagenow; 
?>

<div class="aiovg aiovg-widget-form aiovg-widget-form-video">
	<div class="aiovg-widget-section">
		<div class="aiovg-widget-field aiovg-widget-field-title">
			<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'all-in-one-video-gallery' ); ?></label> 
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat aiovg-widget-input-title" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</div>

		<?php if ( 'widgets.php' === $pagenow || ! is_admin() ) : ?>
			<div class="aiovg-widget-field aiovg-widget-field-id">
				<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php esc_html_e( 'Select Video', 'all-in-one-video-gallery' ); ?></label> 
				<input type="text" class="widefat aiovg-autocomplete-input" placeholder="<?php esc_attr_e( 'Start typing for suggestions', 'all-in-one-video-gallery' ); ?>" />
				<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" class="widefat aiovg-widget-input-id" value="<?php echo esc_attr( $instance['id'] ); ?>" />
				<p class="description aiovg-autocomplete-result">
					<?php if ( ! empty( $instance['id'] ) ) : ?>
						<span class="dashicons dashicons-yes-alt"></span>
						<span><?php echo esc_html( get_the_title( (int) $instance['id'] ) ); ?></span>
						<a href="javascript:void(0);" class="aiovg-remove-autocomplete-result"><?php esc_html_e( 'Remove', 'all-in-one-video-gallery' ); ?></a>
					<?php else : ?>
						<span class="dashicons dashicons-info"></span>
						<span><?php esc_html_e( 'No video selected. The last added video will be displayed.', 'all-in-one-video-gallery' ); ?></span>
					<?php endif; ?>
				</p>
			</div>
		<?php else: ?>
			<div class="aiovg-widget-field aiovg-widget-field-id">
				<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php esc_html_e( 'Select Video', 'all-in-one-video-gallery' ); ?></label> 
				<select name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" class="widefat aiovg-widget-input-id">
					<option value="0">— <?php esc_html_e( 'Latest Video', 'all-in-one-video-gallery' ); ?> —</option>
					<?php
					$args = array(				
						'post_type' => 'aiovg_videos',			
						'post_status' => 'publish',
						'posts_per_page' => 500,
						'orderby' => 'title', 
						'order' => 'ASC', 
						'no_found_rows' => true,
						'update_post_term_cache' => false,
						'update_post_meta_cache' => false
					);
			
					$aiovg_query = new WP_Query( $args );
					
					if ( $aiovg_query->have_posts() ) {
						$posts = $aiovg_query->posts;

						foreach ( $posts as $post ) {					
							printf(
								'<option value="%d"%s>%s</option>', 
								$post->ID, 
								selected( $post->ID, (int) $instance['id'], false ), 
								esc_html( $post->post_title )
							);
						}
					}
					?>
				</select>
			</div>
		<?php endif; ?>

		<div class="aiovg-widget-field aiovg-widget-field-width">
			<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php esc_html_e( 'Width', 'all-in-one-video-gallery' ); ?></label> 
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" class="widefat aiovg-widget-input-width" value="<?php echo esc_attr( $instance['width'] ); ?>" />
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-ratio">
			<label class="aiovg-widget-label" for="<?php echo esc_attr( $this->get_field_id( 'ratio' ) ); ?>"><?php esc_html_e( 'Height (Ratio)', 'all-in-one-video-gallery' ); ?></label> 
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'ratio' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'ratio' ) ); ?>" class="widefat aiovg-widget-input-ratio" value="<?php echo esc_attr( $instance['ratio'] ); ?>" />
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-autoplay">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>" class="aiovg-widget-input-autoplay" value="1" <?php checked( 1, $instance['autoplay'] ); ?> />
				<?php esc_html_e( 'Autoplay', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-loop">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'loop' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'loop' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'loop' ) ); ?>" class="aiovg-widget-input-loop" value="1" <?php checked( 1, $instance['loop'] ); ?> />
				<?php esc_html_e( 'Loop', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-muted">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'muted' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'muted' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'muted' ) ); ?>" class="aiovg-widget-input-muted" value="1" <?php checked( 1, $instance['muted'] ); ?> />
				<?php esc_html_e( 'Muted', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-header">
			<label class="aiovg-widget-label aiovg-font-bold">
				<?php esc_html_e( 'Player Controls', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-playpause">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'playpause' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'playpause' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'playpause' ) ); ?>" class="aiovg-widget-input-playpause" value="1" <?php checked( 1, $instance['playpause'] ); ?> />
				<?php esc_html_e( 'Play / Pause', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-current">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'current' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'current' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'current' ) ); ?>" class="aiovg-widget-input-current" value="1" <?php checked( 1, $instance['current'] ); ?> />
				<?php esc_html_e( 'Current Time', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-progress">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'progress' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'progress' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'progress' ) ); ?>" class="aiovg-widget-input-progress" value="1" <?php checked( 1, $instance['progress'] ); ?> />
				<?php esc_html_e( 'Progressbar', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-duration">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'duration' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'duration' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'duration' ) ); ?>" class="aiovg-widget-input-duration" value="1" <?php checked( 1, $instance['duration'] ); ?> />
				<?php esc_html_e( 'Duration', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-tracks">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'tracks' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'tracks' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'tracks' ) ); ?>" class="aiovg-widget-input-tracks" value="1" <?php checked( 1, $instance['tracks'] ); ?> />
				<?php esc_html_e( 'Subtitles', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-chapters">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'chapters' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'chapters' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'chapters' ) ); ?>" class="aiovg-widget-input-chapters" value="1" <?php checked( 1, $instance['chapters'] ); ?> />
				<?php esc_html_e( 'Chapters', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-speed">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'speed' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>" class="aiovg-widget-input-speed" value="1" <?php checked( 1, $instance['speed'] ); ?> />
				<?php esc_html_e( 'Speed Control', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-quality">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'quality' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'quality' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'quality' ) ); ?>" class="aiovg-widget-input-quality" value="1" <?php checked( 1, $instance['quality'] ); ?> />
				<?php esc_html_e( 'Quality Selector', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>	
		
		<div class="aiovg-widget-field aiovg-widget-field-volume">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'volume' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'volume' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'volume' ) ); ?>" class="aiovg-widget-input-volume" value="1" <?php checked( 1, $instance['volume'] ); ?> />
				<?php esc_html_e( 'Volume Button', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-fullscreen">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'fullscreen' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'fullscreen' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'fullscreen' ) ); ?>" class="aiovg-widget-input-fullscreen" value="1" <?php checked( 1, $instance['fullscreen'] ); ?> />
				<?php esc_html_e( 'Fullscreen Button', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-share">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'share' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'share' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'share' ) ); ?>" class="aiovg-widget-input-share" value="1" <?php checked( 1, $instance['share'] ); ?> />
				<?php esc_html_e( 'Share Buttons', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-embed">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'embed' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'embed' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'embed' ) ); ?>" class="aiovg-widget-input-embed" value="1" <?php checked( 1, $instance['embed'] ); ?> />
				<?php esc_html_e( 'Embed Button', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>

		<div class="aiovg-widget-field aiovg-widget-field-download">		
			<label for="<?php echo esc_attr( $this->get_field_id( 'download' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'download' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'download' ) ); ?>" class="aiovg-widget-input-download" value="1" <?php checked( 1, $instance['download'] ); ?> />
				<?php esc_html_e( 'Download Button', 'all-in-one-video-gallery' ); ?>
			</label>
		</div>
	</div>
</div>