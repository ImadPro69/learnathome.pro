<?php

/**
 * Iframe Player.
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
 * AIOVG_Player_Iframe class.
 *
 * @since 3.5.0
 */
class AIOVG_Player_Iframe extends AIOVG_Player_Base {

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
		$player_settings  = $this->get_player_settings();
		$privacy_settings = $this->get_privacy_settings();

		$this->embed_url = aiovg_get_player_page_url( $this->post_id, $this->args );

		// Enqueue dependencies
		if ( ! empty( $privacy_settings['show_consent'] ) ) {
			wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-player' );
		}

		// Output
		$html = sprintf( 
			'<div class="aiovg-player-container" style="max-width: %s;">',
			( ! empty( $player_settings['width'] ) ? (int) $player_settings['width'] . 'px' : '100%' ) 
		);	

		$html .= sprintf( 
			'<div class="aiovg-player aiovg-player-iframe" style="padding-bottom: %s%%;">', 
			(float) $player_settings['ratio']
		);	

		$html .= sprintf( 
			'<iframe src="%s" title="%s" width="560" height="315" frameborder="0" scrolling="no" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', 
			esc_attr( $this->embed_url ),
			esc_attr( $this->post_title ) 
		);	

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
	
}
