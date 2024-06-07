<?php

/**
 * Videos: "Video Info" meta box.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div class="aiovg">
	<table class="aiovg-table form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="aiovg-video-type"><?php esc_html_e( 'Source Type', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>        
					<select name="type" id="aiovg-video-type" class="widefat">
						<?php 
						$options = aiovg_get_video_source_types( true );

						foreach ( $options as $key => $label ) {
							printf( 
								'<option value="%s"%s>%s</option>', 
								esc_attr( $key ), 
								selected( $key, $type, false ), 
								esc_html( $label )
							);
						}
						?>
					</select>
				</td>
			</tr>
			<tr id="aiovg-field-mp4" class="aiovg-toggle-fields aiovg-type-default">
				<th scope="row">
					<label for="aiovg-mp4"><?php esc_html_e( 'Video File', 'all-in-one-video-gallery' ); ?></label>
					<p class="description">(mp4, webm, ogv, m4v, mov)</p>
				</th>
				<td>
					<div class="aiovg-sources aiovg-flex aiovg-flex-col aiovg-gap-4">
						<div class="aiovg-source aiovg-flex aiovg-flex-col aiovg-gap-2">
							<?php
							if ( ! empty( $quality_levels ) ) {
								echo sprintf( 
									'<div class="aiovg-quality-selector aiovg-flex aiovg-flex-col aiovg-gap-2"%s>', 
									( empty( $sources ) ? ' style="display: none;"' : '' ) 
								);

								echo '<p class="aiovg-no-margin">';
								echo '<span class="aiovg-text-muted dashicons dashicons-video-alt3"></span> ';
								echo sprintf( 
									'%s (%s)',
									esc_html__( 'Select a Quality Level', 'all-in-one-video-gallery' ),
									esc_html__( 'This will be the default quality level for this video', 'all-in-one-video-gallery' )
								);
								echo '</p>';

								echo '<div class="aiovg-flex aiovg-flex-wrap aiovg-gap-3">';

								echo sprintf( 
									'<label><input type="radio" name="quality_level" value=""%s/>%s</label>',
									checked( $quality_level, '', false ),
									esc_html__( 'None', 'all-in-one-video-gallery' )
								);

								foreach ( $quality_levels as $quality ) {
									echo sprintf( 
										'<label><input type="radio" name="quality_level" value="%s"%s/>%s</label>',
										esc_attr( $quality ),
										checked( $quality_level, $quality, false ),
										esc_html( $quality )
									);
								}

								echo '</div>';
								echo '</div>';
							}
							?>       
							<div class="aiovg-media-uploader">                                         
								<input type="text" name="mp4" id="aiovg-mp4" class="widefat" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $mp4 ); ?>" />
								<button type="button" class="aiovg-upload-media button" data-format="mp4">
									<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
								</button>
							</div>
						</div>

						<?php if ( ! empty( $sources ) ) : 
							foreach ( $sources as $index => $source ) :	?>
								<div class="aiovg-source aiovg-flex aiovg-flex-col aiovg-gap-2">
									<?php
									echo '<div class="aiovg-quality-selector aiovg-flex aiovg-flex-col aiovg-gap-2">';

									echo '<p class="aiovg-no-margin">';
									echo '<span class="aiovg-text-muted dashicons dashicons-video-alt3"></span> ';
									echo esc_html__( 'Select a Quality Level', 'all-in-one-video-gallery' );
									echo '</p>';

									echo '<div class="aiovg-flex aiovg-flex-wrap aiovg-gap-3">';

									echo sprintf( 
										'<label><input type="radio" name="quality_levels[%d]" value=""%s/>%s</label>',
										$index,
										checked( $source['quality'], '', false ),
										esc_html__( 'None', 'all-in-one-video-gallery' )
									);

									foreach ( $quality_levels as $quality ) {
										echo sprintf( 
											'<label><input type="radio" name="quality_levels[%d]" value="%s"%s/>%s</label>',
											$index,
											esc_attr( $quality ),
											checked( $source['quality'], $quality, false ),
											esc_html( $quality )
										);
									}
									
									echo '</div>';
									echo '</div>';
									?>
									<div class="aiovg-media-uploader">
										<input type="text" name="sources[<?php echo $index; ?>]" class="widefat" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $source['src'] ); ?>" />
										<button type="button" class="aiovg-upload-media button" data-format="mp4">
											<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
										</button>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>

						<?php if ( ! empty( $quality_levels ) && count( $sources ) < ( count( $quality_levels ) - 1 ) ) : ?>
							<a href="javascript:;" id="aiovg-add-new-source" class="aiovg-text-small" data-limit="<?php echo count( $quality_levels ); ?>">
								<?php esc_html_e( '[+] Add More Quality Levels', 'all-in-one-video-gallery' ); ?>
							</a>
						<?php endif; ?> 
					</div>
				</td>
			</tr>
			<?php if ( ! empty( $webm ) ) : ?>
				<tr id="aiovg-field-webm" class="aiovg-toggle-fields aiovg-type-default">
					<th scope="row">
						<label for="aiovg-webm"><?php esc_html_e( 'WebM', 'all-in-one-video-gallery' ); ?></label>
						<p class="description">(<?php esc_html_e( 'deprecated', 'all-in-one-video-gallery' ); ?>)</p>
					</th>
					<td>
						<div class="aiovg-media-uploader">                                                
							<input type="text" name="webm" id="aiovg-webm" class="widefat" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $webm ); ?>" />
							<button type="button" class="aiovg-upload-media button" data-format="webm">
								<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
							</button>
						</div>
					</td>
				</tr>
			<?php endif; ?>

			<?php if ( ! empty( $ogv ) ) : ?>
				<tr id="aiovg-field-ogv" class="aiovg-toggle-fields aiovg-type-default">
					<th scope="row">
						<label for="aiovg-ogv"><?php esc_html_e( 'OGV', 'all-in-one-video-gallery' ); ?></label>
						<p class="description">(<?php esc_html_e( 'deprecated', 'all-in-one-video-gallery' ); ?>)</p>
					</th>
					<td>
						<div class="aiovg-media-uploader">                                                
							<input type="text" name="ogv" id="aiovg-ogv" class="widefat" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $ogv ); ?>" />
							<button type="button" class="aiovg-upload-media button" data-format="ogv">
								<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
							</button>
						</div> 
					</td>
				</tr> 
			<?php endif; ?> 
			<tr class="aiovg-toggle-fields aiovg-type-adaptive">
				<th scope="row">
					<label for="aiovg-hls"><?php esc_html_e( 'HLS URL', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="hls" id="aiovg-hls" class="widefat" placeholder="<?php printf( '%s: https://www.mysite.com/stream.m3u8', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $hls ); ?>" />
				</td>
			</tr>
			<tr class="aiovg-toggle-fields aiovg-type-adaptive">
				<th scope="row">
					<label for="aiovg-dash"><?php esc_html_e( 'MPEG-DASH URL', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="dash" id="aiovg-dash" class="widefat" placeholder="<?php printf( '%s: https://www.mysite.com/stream.mpd', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $dash ); ?>" />
				</td>
			</tr>
			<tr class="aiovg-toggle-fields aiovg-type-youtube">
				<th scope="row">
					<label for="aiovg-youtube"><?php esc_html_e( 'YouTube URL', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="youtube" id="aiovg-youtube" class="widefat" placeholder="<?php printf( '%s: https://www.youtube.com/watch?v=twYp6W6vt2U', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $youtube ); ?>" />
				</td>
			</tr>
			<tr class="aiovg-toggle-fields aiovg-type-vimeo">
				<th scope="row">
					<label for="aiovg-vimeo"><?php esc_html_e( 'Vimeo URL', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="vimeo" id="aiovg-vimeo" class="widefat" placeholder="<?php printf( '%s: https://vimeo.com/108018156', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $vimeo ); ?>" />
				</td>
			</tr>
			<tr class="aiovg-toggle-fields aiovg-type-dailymotion">
				<th scope="row">
					<label for="aiovg-dailymotion"><?php esc_html_e( 'Dailymotion URL', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="dailymotion" id="aiovg-dailymotion" class="widefat" placeholder="<?php printf( '%s: https://www.dailymotion.com/video/x11prnt', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $dailymotion ); ?>" />
				</td>
			</tr>
			<tr class="aiovg-toggle-fields aiovg-type-rumble">
				<th scope="row">
					<label for="aiovg-rumble"><?php esc_html_e( 'Rumble URL', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="rumble" id="aiovg-rumble" class="widefat" placeholder="<?php printf( '%s: https://rumble.com/val8vm-how-to-use-rumble.html', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $rumble ); ?>" />
				</td>
			</tr>
			<tr class="aiovg-toggle-fields aiovg-type-facebook">
				<th scope="row">
					<label for="aiovg-facebook"><?php esc_html_e( 'Facebook URL', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="facebook" id="aiovg-facebook" class="widefat" placeholder="<?php printf( '%s: https://www.facebook.com/facebook/videos/10155278547321729', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $facebook ); ?>" />
				</td>
			</tr>
			<tr class="aiovg-toggle-fields aiovg-type-embedcode">
				<th scope="row">
					<label for="aiovg-embedcode"><?php esc_html_e( 'Embed Code', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<textarea name="embedcode" id="aiovg-embedcode" class="widefat" rows="6" placeholder="<?php esc_attr_e( 'Enter your Iframe Embed Code', 'all-in-one-video-gallery' ); ?>"><?php echo esc_textarea( $embedcode ); ?></textarea>

					<p class="description">
						<?php
						printf(
							'<span class="aiovg-text-error"><strong>%s</strong></span>: %s',
							esc_html__( 'Warning', 'all-in-one-video-gallery' ),
							esc_html__( 'This field allows "iframe" and "script" tags. So, make sure the code you\'re adding with this field is harmless to your website.', 'all-in-one-video-gallery' )
						);
						?>
					</p>
				</td>
			</tr>

			<?php do_action( 'aiovg_admin_add_video_source_fields', $post->ID ); ?>

			<tr>
				<th scope="row">
					<label for="aiovg-duration"><?php esc_html_e( 'Video Duration', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="duration" id="aiovg-duration" class="widefat" placeholder="6:30" value="<?php echo esc_attr( $duration ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="aiovg-views"><?php esc_html_e( 'Views Count', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="views" id="aiovg-views" class="widefat" value="<?php echo esc_attr( $views ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="aiovg-likes"><?php esc_html_e( 'Likes Count', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="likes" id="aiovg-likes" class="widefat" value="<?php echo esc_attr( $likes ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="aiovg-dislikes"><?php esc_html_e( 'Dislikes Count', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<input type="text" name="dislikes" id="aiovg-dislikes" class="widefat" value="<?php echo esc_attr( $dislikes ); ?>" />
				</td>
			</tr>
			<tr id="aiovg-field-download" class="aiovg-toggle-fields aiovg-type-default">
				<th scope="row">
					<label for="aiovg-download"><?php esc_html_e( 'Download', 'all-in-one-video-gallery' ); ?></label>
				</th>
				<td>
					<label>
						<input type="checkbox" name="download" id="aiovg-download" value="1" <?php checked( $download, 1 ); ?> />
						<?php esc_html_e( 'Check this option to allow users to download this video.', 'all-in-one-video-gallery' ); ?>
					</label>
				</td>
			</tr>     
		</tbody>
	</table>

	<?php if ( ! empty( $quality_levels ) ) : ?>
		<div id="aiovg-source-clone" hidden>
			<div class="aiovg-source aiovg-flex aiovg-flex-col aiovg-gap-2">
				<?php
				echo '<div class="aiovg-quality-selector aiovg-flex aiovg-flex-col aiovg-gap-2">';

				echo '<p class="aiovg-no-margin">';
				echo '<span class="aiovg-text-muted dashicons dashicons-video-alt3"></span> ';
				echo esc_html__( 'Select a Quality Level', 'all-in-one-video-gallery' );
				echo '</p>';

				echo '<div class="aiovg-flex aiovg-flex-wrap aiovg-gap-3">';

				echo sprintf( 
					'<label><input type="radio" value=""/>%s</label>',
					esc_html__( 'None', 'all-in-one-video-gallery' )
				);

				foreach ( $quality_levels as $quality ) {
					echo sprintf( 
						'<label><input type="radio" value="%s"/>%s</label>',
						esc_attr( $quality ),
						esc_html( $quality )
					);
				}

				echo '</div>';
				echo '</div>';
				?>
				<div class="aiovg-media-uploader">
					<input type="text" class="widefat" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="" />
					<button type="button" class="aiovg-upload-media button" data-format="mp4">
						<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
					</button>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php wp_nonce_field( 'aiovg_save_video_sources', 'aiovg_video_sources_nonce' ); // Nonce ?>
</div>