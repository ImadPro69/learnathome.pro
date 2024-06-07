<?php

/**
 * Videos: "Chapters" meta box.
 *
 * @link    https://plugins360.com
 * @since   3.6.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div class="aiovg">
	<table id="aiovg-chapters" class="aiovg-table form-table striped">
		<tbody>
			<?php foreach ( $chapters as $key => $chapter ) : ?>
				<tr class="aiovg-chapters-row">
					<td class="aiovg-handle">
						<span class="aiovg-text-muted dashicons dashicons-sort"></span>
					</td>
					<td>
						<div class="aiovg-chapter">
							<div class="aiovg-chapter-label">
								<label class="aiovg-text-small"><?php esc_html_e( 'Label', 'all-in-one-video-gallery' ); ?></label>				
								<input type="text" name="chapter_label[]" class="widefat" placeholder="<?php esc_attr_e( 'Chapter Title', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $chapter['label'] ); ?>" />
							</div>

							<div class="aiovg-chapter-time">
								<label class="aiovg-text-small"><?php esc_html_e( 'Time', 'all-in-one-video-gallery' ); ?></label>				
								<input type="text" name="chapter_time[]" class="widefat" placeholder="<?php esc_attr_e( 'Start time in seconds', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $chapter['time'] ); ?>" />
							</div>							
					
							<div class="aiovg-chapter-buttons">
								<button type="button" class="aiovg-delete-chapter button">
									<?php esc_html_e( 'Delete', 'all-in-one-video-gallery' ); ?>
								</button>
							</div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<a href="javascript:;" id="aiovg-add-new-chapter" class="aiovg-block aiovg-margin-top aiovg-text-small">
		<?php esc_html_e( '[+] Add New Chapter', 'all-in-one-video-gallery' ); ?>
	</a>

	<table id="aiovg-chapters-clone" hidden>
		<tr class="aiovg-chapters-row">
			<td class="aiovg-handle">
				<span class="aiovg-text-muted dashicons dashicons-sort"></span>
			</td>
			<td>
				<div class="aiovg-chapter">
					<div class="aiovg-chapter-label">
						<label class="aiovg-text-small"><?php esc_html_e( 'Label', 'all-in-one-video-gallery' ); ?></label>				
						<input type="text" name="chapter_label[]" class="widefat" placeholder="<?php esc_attr_e( 'Chapter Title', 'all-in-one-video-gallery' ); ?>" />
					</div>

					<div class="aiovg-chapter-time">
						<label class="aiovg-text-small"><?php esc_html_e( 'Time', 'all-in-one-video-gallery' ); ?></label>				
						<input type="text" name="chapter_time[]" class="widefat" placeholder="<?php esc_attr_e( 'Start time in seconds', 'all-in-one-video-gallery' ); ?>" />
					</div>	
			
					<div class="aiovg-chapter-buttons">
						<button type="button" class="aiovg-delete-chapter button">
							<?php esc_html_e( 'Delete', 'all-in-one-video-gallery' ); ?>
						</button>
					</div>
				</div>
			</td>
		</tr>		
	</table>

	<?php wp_nonce_field( 'aiovg_save_video_chapters', 'aiovg_video_chapters_nonce' ); // Nonce ?>
</div>