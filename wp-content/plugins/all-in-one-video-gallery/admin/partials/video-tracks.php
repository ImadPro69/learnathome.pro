<?php

/**
 * Videos: "Subtitles" meta box.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div class="aiovg">
	<table id="aiovg-tracks" class="aiovg-table form-table striped">
		<tbody>
			<?php foreach ( $tracks as $key => $track ) : ?>
				<tr class="aiovg-tracks-row">
					<td class="aiovg-handle">
						<span class="aiovg-text-muted dashicons dashicons-sort"></span>
					</td>
					<td>
						<div class="aiovg-track">
							<div class="aiovg-track-src">
								<label class="aiovg-text-small"><?php esc_html_e( 'File URL', 'all-in-one-video-gallery' ); ?></label>                
								<input type="text" name="track_src[]" class="widefat" value="<?php echo esc_attr( $track['src'] ); ?>" />
							</div>

							<div class="aiovg-track-label">
								<label class="aiovg-text-small"><?php esc_html_e( 'Label', 'all-in-one-video-gallery' ); ?></label>				
								<input type="text" name="track_label[]" class="widefat" placeholder="<?php esc_attr_e( 'English', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $track['label'] ); ?>" />
							</div>
			
							<div class="aiovg-track-srclang">
								<label class="aiovg-text-small"><?php esc_html_e( 'Srclang', 'all-in-one-video-gallery' ); ?></label>
								<input type="text" name="track_srclang[]" class="widefat" placeholder="<?php esc_attr_e( 'en', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $track['srclang'] ); ?>" />
							</div>
					
							<div class="aiovg-track-buttons">
								<button type="button" class="aiovg-upload-track button">
									<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
								</button>

								<button type="button" class="aiovg-delete-track button">
									<?php esc_html_e( 'Delete', 'all-in-one-video-gallery' ); ?>
								</button>
							</div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<a href="javascript:;" id="aiovg-add-new-track" class="aiovg-block aiovg-margin-top aiovg-text-small">
		<?php esc_html_e( '[+] Add New File', 'all-in-one-video-gallery' ); ?>
	</a>

	<table id="aiovg-tracks-clone" hidden>
		<tr class="aiovg-tracks-row">
			<td class="aiovg-handle">
				<span class="aiovg-text-muted dashicons dashicons-sort"></span>
			</td>
			<td>
				<div class="aiovg-track">
					<div class="aiovg-track-src">
						<label class="aiovg-text-small"><?php esc_html_e( 'File URL', 'all-in-one-video-gallery' ); ?></label>                
						<input type="text" name="track_src[]" class="widefat" />
					</div>

					<div class="aiovg-track-label">
						<label class="aiovg-text-small"><?php esc_html_e( 'Label', 'all-in-one-video-gallery' ); ?></label>				
						<input type="text" name="track_label[]" class="widefat" placeholder="<?php esc_attr_e( 'English', 'all-in-one-video-gallery' ); ?>" />
					</div>

					<div class="aiovg-track-srclang">
						<label class="aiovg-text-small"><?php esc_html_e( 'Srclang', 'all-in-one-video-gallery' ); ?></label>
						<input type="text" name="track_srclang[]" class="widefat" placeholder="<?php esc_attr_e( 'en', 'all-in-one-video-gallery' ); ?>" />
					</div>
			
					<div class="aiovg-track-buttons">
						<button type="button" class="aiovg-upload-track button">
							<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
						</button>

						<button type="button" class="aiovg-delete-track button">
							<?php esc_html_e( 'Delete', 'all-in-one-video-gallery' ); ?>
						</button>
					</div>
				</div>
			</td>
		</tr>		
	</table>

	<?php wp_nonce_field( 'aiovg_save_video_tracks', 'aiovg_video_tracks_nonce' ); // Nonce ?>
</div>