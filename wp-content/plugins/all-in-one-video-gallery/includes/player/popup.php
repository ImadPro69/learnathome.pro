<?php

/**
 * Popup Player.
 *
 * @link    https://plugins360.com
 * @since   3.5.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Player_Popup class.
 *
 * @since 3.5.0
 */
class AIOVG_Player_Popup extends AIOVG_Player_Base {

	/**
	 * Get things started.
	 *
	 * @since 3.5.0
	 * @param int   $post_id      Post ID.
 	 * @param array $args         Player options.
	 * @param int   $reference_id Player reference ID.
	 */
	public function __construct( $post_id, $args, $reference_id ) {	
		parent::__construct( $post_id, $args, $reference_id );	
	}	

	/**
	 * Get the player HTML.
	 *
	 * @since  3.5.0
 	 * @return string $html Player HTML.
	 */
	public function get_player() {		
		$player_settings = $this->get_player_settings();
		
		$popup_content = __( 'Open Popup', 'all-in-one-video-gallery' );

		if ( ! isset( $this->args['content'] ) ) {
			$videos = $this->get_videos();
			$poster = $this->get_poster();

			if ( ! empty( $poster ) ) {
				$popup_content = sprintf( '<img src="%s" alt="" />', esc_url( $poster ) );
			}
		} else {
			$popup_content = trim( $this->args['content'] );

			if ( ! filter_var( $popup_content, FILTER_VALIDATE_URL ) === FALSE ) {
				$popup_content = sprintf( '<img src="%s" alt="" />', esc_url( $popup_content ) );
			}
		}

		// Enqueue dependencies
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-magnific-popup' );
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-premium-public' );

		wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-magnific-popup' );
		wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-template-popup' );
	
		// Process output
		$this->args['autoplay'] = 1;
		$this->embed_url = aiovg_get_player_page_url( $this->post_id, $this->args ); 

		$html = sprintf( 
			'<a href="javascript: void(0);" class="aiovg-video-template-popup" data-mfp-src="%s" data-player_ratio="%s">%s</a>',
			esc_url( $this->embed_url ),
			(float) $player_settings['ratio'] . '%',
			$popup_content
		);

		return $html;
	}
	
}
