<?php

/**
 * Likes / Dislikes
 *
 * @link    https://plugins360.com
 * @since   3.6.1
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Public_Likes class.
 *
 * @since 3.6.1
 */
class AIOVG_Public_Likes {
	
	/**
	 * Get things started.
	 *
	 * @since 3.6.1
	 */
	public function __construct() {
		// Register shortcode(s)
		add_shortcode( 'aiovg_like_button', array( $this, 'run_shortcode_like_button' ) );
	}

	/**
	 * Run the shortcode [aiovg_like_button].
	 *
	 * @since  3.6.1
	 * @param  array  $attributes An associative array of attributes.
	 * @return string             Shortcode output.
	 */
	public function run_shortcode_like_button( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}

		global $post;

		$post_id = 0;
		$content = '';
		
		if ( isset( $post ) ) {
			$post_id = $post->ID;
		}

		if ( isset( $attributes['id'] ) && ! empty( $attributes['id'] ) ) {
			$post_id = $attributes['id'];
		}

		if ( ! empty( $post_id ) ) {
			$post_id   = (int) $post_id;
			$post_type = get_post_type( $post_id );

			if ( 'aiovg_videos' == $post_type ) {
				wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-likes' );
				
				$content = aiovg_get_like_button( $post_id );
			} 
		}

		return $content;		
	}	

	/**
	 * Get likes / dislikes info.
	 *
	 * @since 3.6.1
	 */
	public function ajax_callback_get_likes_dislikes_info() {
		check_ajax_referer( 'aiovg_ajax_nonce', 'security' );

		// Proceed safe
		$response = array( 
			'status'   => 'error', 
			'message'  => '', 
			'likes'    => 0, 			
			'dislikes' => 0, 
			'liked'    => false, 			
			'disliked' => false
		);
		
		$post_id = isset( $_REQUEST['post_id'] ) ? (int) $_REQUEST['post_id'] : 0;
		$user_id = isset( $_REQUEST['user_id'] ) ? (int) $_REQUEST['user_id'] : 0;
			
		if ( $post_id > 0 ) {
			$response['status']   = 'success';
			$response['likes']    = (int) get_post_meta( $post_id, 'likes', true );
			$response['dislikes'] = (int) get_post_meta( $post_id, 'dislikes', true );

			$liked    = array();	
			$disliked = array();			

			if ( $user_id > 0 ) {
				$liked    = (array) get_user_meta( $user_id, 'aiovg_videos_likes' );	
				$disliked = (array) get_user_meta( $user_id, 'aiovg_videos_dislikes' );		
			} else {
				$likes_settings = get_option( 'aiovg_likes_settings' );

				if ( empty( $likes_settings['login_required_to_vote'] ) ) {
					if ( isset( $_COOKIE['aiovg_videos_likes'] ) ) {
						$liked = explode( '|', $_COOKIE['aiovg_videos_likes'] );
						$liked = array_map( 'intval', $liked );
					}
		
					if ( isset( $_COOKIE['aiovg_videos_dislikes'] ) ) {
						$disliked = explode( '|', $_COOKIE['aiovg_videos_dislikes'] );
						$disliked = array_map( 'intval', $disliked );
					}
				}
			}
		
			if ( in_array( $post_id, $liked ) ) {
				$response['liked'] = true;
			} elseif ( in_array( $post_id, $disliked ) ) {
				$response['disliked'] = true;
			}
		}
		
		// Output
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Toggle likes.
	 *
	 * @since 3.6.1
	 */
	public function ajax_callback_toggle_likes() {
		check_ajax_referer( 'aiovg_ajax_nonce', 'security' );

		// Proceed safe
		$response = array( 
			'status'   => 'error', 
			'message'  => '', 
			'likes'    => 0, 			
			'dislikes' => 0, 
			'liked'    => false, 			
			'disliked' => false
 		);
		
		$post_id = isset( $_REQUEST['post_id'] ) ? (int) $_REQUEST['post_id'] : 0;
		$user_id = isset( $_REQUEST['user_id'] ) ? (int) $_REQUEST['user_id'] : 0;
		$action  = isset( $_REQUEST['context'] ) ? sanitize_text_field( $_REQUEST['context'] ) : '';		
			
		if ( $post_id > 0 ) {			
			$response['likes']    = (int) get_post_meta( $post_id, 'likes', true );
			$response['dislikes'] = (int) get_post_meta( $post_id, 'dislikes', true );

			// Add to Likes
			if ( 'add_to_likes' == $action ) {
				$likes = $this->add_to_likes( $response['likes'], $post_id, $user_id );

				if ( $likes != $response['likes'] ) {
					$response['status'] = 'success';
					$response['likes']  = $likes;		
					$response['liked']  = true;	
 				}
			} 
			
			// Remove from Likes
			if ( 'remove_from_likes' == $action ) {
				$likes = $this->remove_from_likes( $response['likes'], $post_id, $user_id );

				if ( $likes != $response['likes'] ) {
					$response['status'] = 'success';
					$response['likes']  = $likes;
				}
			}
			
			// Remove from Dislikes
			if ( 'success' == $response['status'] ) {
				$response['dislikes'] = $this->remove_from_dislikes( $response['dislikes'], $post_id, $user_id );
			}
		}
		
		// Output
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Toggle dislikes.
	 *
	 * @since 3.6.1
	 */
	public function ajax_callback_toggle_dislikes() {
		check_ajax_referer( 'aiovg_ajax_nonce', 'security' );

		// Proceed safe
		$response = array( 
			'status'   => 'error', 
			'message'  => '', 
			'likes'    => 0, 			
			'dislikes' => 0, 
			'liked'    => false, 			
			'disliked' => false
		);
		
		$post_id = isset( $_REQUEST['post_id'] ) ? (int) $_REQUEST['post_id'] : 0;
		$user_id = isset( $_REQUEST['user_id'] ) ? (int) $_REQUEST['user_id'] : 0;
		$action  = isset( $_REQUEST['context'] ) ? sanitize_text_field( $_REQUEST['context'] ) : '';	

		if ( $post_id > 0 ) {			
			$response['likes']    = (int) get_post_meta( $post_id, 'likes', true );
			$response['dislikes'] = (int) get_post_meta( $post_id, 'dislikes', true );

			// Add to Dislikes
			if ( 'add_to_dislikes' == $action ) {
				$dislikes = $this->add_to_dislikes( $response['dislikes'], $post_id, $user_id );

				if ( $dislikes != $response['dislikes'] ) {
					$response['status']   = 'success';
					$response['dislikes'] = $dislikes;	
					$response['disliked'] = true;			
				}
			} 
			
			// Remove from Dislikes
			if ( 'remove_from_dislikes' == $action ) {
				$dislikes = $this->remove_from_dislikes( $response['dislikes'], $post_id, $user_id );

				if ( $dislikes != $response['dislikes'] ) {
					$response['status']   = 'success';
					$response['dislikes'] = $dislikes;
				}
			}
			
			// Remove from Likes
			if ( 'success' == $response['status'] ) {
				$response['likes'] = $this->remove_from_likes( $response['likes'], $post_id, $user_id );
			}
		}
		
		// Output
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Add to likes.
	 *
	 * @since  3.6.1
	 * @access private
	 * @param  int     $likes   Current likes count.
	 * @param  int     $post_id Video post ID.
	 * @param  int     $user_id Current user ID.
	 * @return int     $likes   Updated likes count.
	 */
	private function add_to_likes( $likes, $post_id, $user_id ) {
		if ( $user_id > 0 ) {			
			$liked = (array) get_user_meta( $user_id, 'aiovg_videos_likes' );		

			if ( ! in_array( $post_id, $liked ) ) {
				add_user_meta( $user_id, 'aiovg_videos_likes', $post_id );

				$likes = $likes + 1;
				update_post_meta( $post_id, 'likes', $likes );
			}
		} else {
			$likes_settings = get_option( 'aiovg_likes_settings' );

			if ( empty( $likes_settings['login_required_to_vote'] ) ) {
				$liked = array();

				if ( isset( $_COOKIE['aiovg_videos_likes'] ) ) {
					$liked = explode( '|', $_COOKIE['aiovg_videos_likes'] );
					$liked = array_map( 'intval', $liked );
				}

				if ( ! in_array( $post_id, $liked ) ) {
					$likes = $likes + 1;
					update_post_meta( $post_id, 'likes', $likes );
		
					// SetCookie
					$liked[] = $post_id;
					setcookie( 'aiovg_videos_likes', implode( '|', $liked ), time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN );
				}	
			}
		}

		return $likes;
	}

	/**
	 * Add to dislikes.
	 *
	 * @since  3.6.1
	 * @access private
	 * @param  int     $dislikes Current dislikes count.
	 * @param  int     $post_id  Video post ID.
	 * @param  int     $user_id  Current user ID.
	 * @return int     $dislikes Total dislikes count.
	 */
	private function add_to_dislikes( $dislikes, $post_id, $user_id ) {
		if ( $user_id > 0 ) {			
			$disliked = (array) get_user_meta( $user_id, 'aiovg_videos_dislikes' );		

			if ( ! in_array( $post_id, $disliked ) ) {
				add_user_meta( $user_id, 'aiovg_videos_dislikes', $post_id );
				
				$dislikes = $dislikes + 1;
				update_post_meta( $post_id, 'dislikes', $dislikes );
			}
		} else {
			$likes_settings = get_option( 'aiovg_likes_settings' );

			if ( empty( $likes_settings['login_required_to_vote'] ) ) {
				$disliked = array();

				if ( isset( $_COOKIE['aiovg_videos_dislikes'] ) ) {
					$disliked = explode( '|', $_COOKIE['aiovg_videos_dislikes'] );
					$disliked = array_map( 'intval', $disliked );
				}

				if ( ! in_array( $post_id, $disliked ) ) {
					$dislikes = $dislikes + 1;
					update_post_meta( $post_id, 'dislikes', $dislikes );
		
					// SetCookie
					$disliked[] = $post_id;
					setcookie( 'aiovg_videos_dislikes', implode( '|', $disliked ), time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN );
				}	
			}
		}

		return $dislikes;
	}

	/**
	 * Remove from likes.
	 *
	 * @since  3.6.1
	 * @access private
	 * @param  int     $likes   Current likes count.
	 * @param  int     $post_id Video post ID.
	 * @param  int     $user_id Current user ID.
	 * @return int     $likes   Total likes count.
	 */
	private function remove_from_likes( $likes, $post_id, $user_id ) {
		if ( $user_id > 0 ) {			
			$liked = (array) get_user_meta( $user_id, 'aiovg_videos_likes' );		

			if ( in_array( $post_id, $liked ) ) {
				delete_user_meta( $user_id, 'aiovg_videos_likes', $post_id );
				
				$likes = max( 0, $likes - 1 );
				update_post_meta( $post_id, 'likes', $likes );
			}
		} else {
			$likes_settings = get_option( 'aiovg_likes_settings' );

			if ( empty( $likes_settings['login_required_to_vote'] ) ) {
				$liked = array();

				if ( isset( $_COOKIE['aiovg_videos_likes'] ) ) {
					$liked = explode( '|', $_COOKIE['aiovg_videos_likes'] );
					$liked = array_map( 'intval', $liked );
				}

				if ( in_array( $post_id, $liked ) ) {
					$likes = max( 0, $likes - 1 );
					update_post_meta( $post_id, 'likes', $likes );
		
					// SetCookie
					if ( ( $key = array_search( $post_id, $liked ) ) !== false ) {
						unset( $liked[ $key ] );
					}

					setcookie( 'aiovg_videos_likes', implode( '|', $liked ), time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN );
				}	
			}
		}

		return $likes;
	}

	/**
	 * Remove from dislikes.
	 *
	 * @since  3.6.1
	 * @access private
	 * @param  int     $dislikes Current dislikes count.
	 * @param  int     $post_id  Video post ID.
	 * @param  int     $user_id  Current user ID.
	 * @return int     $dislikes Total dislikes count.
	 */
	private function remove_from_dislikes( $dislikes, $post_id, $user_id ) {
		if ( $user_id > 0 ) {			
			$disliked = (array) get_user_meta( $user_id, 'aiovg_videos_dislikes' );		

			if ( in_array( $post_id, $disliked ) ) {
				delete_user_meta( $user_id, 'aiovg_videos_dislikes', $post_id );
				
				$dislikes = max( 0, $dislikes - 1 );
				update_post_meta( $post_id, 'dislikes', $dislikes );
			}
		} else {
			$likes_settings = get_option( 'aiovg_likes_settings' );

			if ( empty( $likes_settings['login_required_to_vote'] ) ) {
				$disliked = array();

				if ( isset( $_COOKIE['aiovg_videos_dislikes'] ) ) {
					$disliked = explode( '|', $_COOKIE['aiovg_videos_dislikes'] );
					$disliked = array_map( 'intval', $disliked );
				}

				if ( in_array( $post_id, $disliked ) ) {
					$dislikes = max( 0, $dislikes - 1 );
					update_post_meta( $post_id, 'dislikes', $dislikes );
		
					// SetCookie
					if ( ( $key = array_search( $post_id, $disliked ) ) !== false ) {
						unset( $disliked[ $key ] );
					}

					setcookie( 'aiovg_videos_dislikes', implode( '|', $disliked ), time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN );
				}	
			}
		}

		return $dislikes;
	}
	
}