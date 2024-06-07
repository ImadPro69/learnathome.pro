<?php

/**
 * Fixes for third-party plugin/theme conflict.
 *
 * @link    https://plugins360.com
 * @since   2.4.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Public_Conflict class.
 *
 * @since 1.0.0
 */
class AIOVG_Public_Conflict {
	
	/**
	 * Stop autoptimize from optimizing the player page.
	 *
	 * @since 2.4.0
	 * @param bool  "true" to stop optimizing, "false" to optimize.
	 */
	public function noptimize() {
		$page_settings = get_option( 'aiovg_page_settings' );

		if ( $page_settings['player'] > 0 ) {
			$post = get_post( $page_settings['player'] );
			
			if ( strpos( $_SERVER['REQUEST_URI'], $post->post_name ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Skip AIOVG iframes from lazy loading.
	 *
	 * @since  2.4.2
	 * @param  bool   $skip Should skip? Default: false.
	 * @param  string $src  Iframe url.
	 * @return bool
	 */
	public function smush( $skip, $src ) {
		$page_settings = get_option( 'aiovg_page_settings' );
		$post_id = (int) get_query_var( 'aiovg_video', 0 );

		if ( $page_settings['player'] > 0 ) {
			$post = get_post( $page_settings['player'] );
			
			if ( strpos( $src, $post->post_name ) !== false ) {
				return true;
			}
		}
		
		if ( $post_id > 0 ) {
			$post_type = get_post_type( $post_id );
				
			if ( 'aiovg_videos' == $post_type ) {
				return true;
			}
		}

		return $skip;
	}

	/**
	 * Set thumbnailUrl field in VideoObject Schema.
	 *
	 * @since  2.4.3
	 * @param  array $entity Snippet Data
	 * @return array
	 */
	public function rank_math( $entity ) {
		if ( is_singular( 'aiovg_videos' ) ) {
			global $post;
		   
			$image_data = aiovg_get_image( $post->ID, 'large' );
			$image = $image_data['src'];
	
			if ( ! empty( $image ) ) {
				$entity['thumbnailUrl'] = $image;
			}      
		}
	   
		return $entity;
	}
	
}
