<?php

/**
 * Video Player.
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
 * AIOVG_Player_Base class.
 *
 * @since 3.5.0
 */
class AIOVG_Player_Base {

	/**
	 * Player options.
	 *
	 * @since  3.5.0
	 * @access protected
	 * @var    array	 
	 */
	protected $args;

	/**
	 * Player reference ID.
	 *
	 * @since  3.5.0
	 * @access protected
	 * @var    int	 
	 */
	protected $reference_id;	

	/**
	 * Post ID.
	 *
	 * @since  3.5.0
	 * @access protected
	 * @var    int	 
	 */
	protected $post_id;

	/**
	 * Post Type.
	 *
	 * @since  3.5.0
	 * @access protected
	 * @var    string	 
	 */
	protected $post_type = 'page';

	/**
	 * Post Title.
	 *
	 * @since  3.5.0
	 * @access protected
	 * @var    string	 
	 */
	protected $post_title = '';	

	/**
	 * Player embed URL.
	 *
	 * @since  3.5.0
	 * @access protected
	 * @var    string	 
	 */
	protected $embed_url = '';

	/**
	 * An array of cached player options.
	 *
	 * @since  3.5.0
	 * @access private
	 * @var    array	 
	 */
	private $cache = array();

	/**
	 * Get things started.
	 *
	 * @since 3.5.0
	 * @param int   $post_id      Post ID.
 	 * @param array $args         Player options.
	 * @param int   $reference_id Player reference ID.
	 */
	public function __construct( $post_id, $args, $reference_id ) {	
		$this->post_id = $post_id;
		$this->args = $args;						
		$this->reference_id = $reference_id;			

		if ( $this->post_id > 0 ) {
			$this->post_type = get_post_type( $this->post_id );
			$this->post_title = get_the_title( $this->post_id );
		}
	}

	/**
	 * Get the options that can be passed through the WP filter hooks.
	 *
	 * @since  3.5.0
 	 * @return array Player options.
	 */
	public function get_params() {
		$params = array(
			'uid'        => $this->reference_id,
			'post_id'    => $this->post_id,
			'post_type'  => $this->post_type,
			'post_title' => $this->post_title,
			'embed_url'  => $this->embed_url
		);

		// Output
		return array_merge( $params, $this->args );		
	}	

	/**
	 * Get the videos.
	 *
	 * @since  3.5.0
 	 * @return array $videos Array of videos.
	 */
	public function get_videos() {
		if ( isset( $this->cache['videos'] ) ) {
			return $this->cache['videos'];
		}

		$defaults = array(
			'mp4'         => '',
			'webm'        => '',
			'ogv'         => '',
			'hls'         => '',
			'dash'        => '',
			'youtube'     => '',
			'vimeo'       => '',
			'dailymotion' => '',
			'rumble'      => '',
			'facebook'    => ''
		);

		$videos = shortcode_atts( $defaults, $this->args );

		// Is a video post?
		if ( $this->post_id > 0 && 'aiovg_videos' == $this->post_type ) {
			$source_type = get_post_meta( $this->post_id, 'type', true );
			
			switch ( $source_type ) {
				case 'adaptive':
					$hls = get_post_meta( $this->post_id, 'hls', true );
					if ( ! empty( $hls ) ) {
						$videos['hls'] = $hls;
					}

					$dash = get_post_meta( $this->post_id, 'dash', true );
					if ( ! empty( $dash ) ) {
						$videos['dash'] = $dash;
					}
					break;

				case 'youtube':
				case 'vimeo':
				case 'dailymotion':
				case 'rumble':
				case 'facebook':
					$src = get_post_meta( $this->post_id, $source_type, true );
					if ( ! empty( $src ) ) {
						$videos[ $source_type ] = $src;
					}
					break;
				
				case 'embedcode':
					$embedcode = get_post_meta( $this->post_id, 'embedcode', true );
					if ( ! empty( $embedcode ) ) {
						$iframe_src = aiovg_extract_iframe_src( $embedcode );
						if ( $iframe_src ) {
							$videos['iframe'] = $iframe_src;
						} else {
							$videos['embedcode'] = $embedcode;
						}
					}
					break;

				default:
					$mp4 = get_post_meta( $this->post_id, 'mp4', true );
					if ( ! empty( $mp4 ) ) {
						$videos['mp4'] = $mp4;
					}

					$webm = get_post_meta( $this->post_id, 'webm', true );
					if ( ! empty( $webm ) ) {
						$videos['webm'] = $webm;
					}

					$ogv = get_post_meta( $this->post_id, 'ogv', true );
					if ( ! empty( $ogv ) ) {
						$videos['ogv'] = $ogv;
					}

					$quality_level = get_post_meta( $this->post_id, 'quality_level', true );
					if ( ! empty( $quality_level ) ) {
						$videos['quality_level'] = $quality_level;
					}

					$sources = get_post_meta( $this->post_id, 'sources', true );
					if ( ! empty( $sources ) && is_array( $sources ) ) {
						foreach ( $sources as $index => $source ) {
							$sources[ $index ]['src'] = aiovg_resolve_url( $source['src'] );
						}

						$videos['sources'] = $sources;
					}
					break;
			}
		}

		// Resolve relative file paths as absolute URLs
		if ( ! empty( $videos['mp4'] ) ) {
			$videos['mp4'] = aiovg_resolve_url( $videos['mp4'] );
		}

		if ( ! empty( $videos['webm'] ) ) {
			$videos['webm'] = aiovg_resolve_url( $videos['webm'] );
		}

		if ( ! empty( $videos['ogv'] ) ) {
			$videos['ogv'] = aiovg_resolve_url( $videos['ogv'] );
		}

		// Set embed URL if available
		if ( isset( $videos['iframe'] ) ) {
			$this->embed_url = $videos['iframe'];
		}

		// Output
		$this->cache['videos'] = $videos;
		return $videos;
	}

	/**
	 * Get the video tracks.
	 *
	 * @since  3.5.0
 	 * @return array $tracks Array of video tracks.
	 */
	public function get_tracks() {
		if ( isset( $this->cache['tracks'] ) ) {
			return $this->cache['tracks'];
		}

		$tracks = array();

		if ( $this->post_id > 0 && 'aiovg_videos' == $this->post_type ) {
			$tracks = get_post_meta( $this->post_id, 'track' );
			foreach ( $tracks as $index => $track ) {
				$tracks[ $index ]['src'] = aiovg_resolve_url( $track['src'] );
			}
		}

		// Output
		$this->cache['tracks'] = $tracks;
		return $tracks;
	}

	/**
	 * Get the video chapters.
	 *
	 * @since  3.6.0
 	 * @return array $chapters Array of video chapters.
	 */
	public function get_chapters() {
		if ( isset( $this->cache['chapters'] ) ) {
			return $this->cache['chapters'];
		}

		$chapters = array();

		if ( $this->post_id > 0 && 'aiovg_videos' == $this->post_type ) {
			$chapters = get_post_meta( $this->post_id, 'chapter' );
			
			foreach ( $chapters as $index => $chapter ) {
				$chapters[ $index ]['label'] = sanitize_text_field( $chapter['label'] );
				$chapters[ $index ]['time']  = (float) $chapter['time'];
			}
		}

		// Output
		$this->cache['chapters'] = $chapters;
		return $chapters;
	}

	/**
	 * Get the poster image.
	 *
	 * @since  3.5.0
 	 * @return array $poster Poster image URL.
	 */
	public function get_poster() {
		if ( isset( $this->cache['poster'] ) ) {
			return $this->cache['poster'];
		}

		$poster = '';

		if ( ! empty( $this->args['poster'] ) ) {
			$poster = aiovg_resolve_url( $this->args['poster'] );
		} else {
			// Is a video post?
			if ( $this->post_id > 0 && 'aiovg_videos' == $this->post_type ) {
				$image_data = aiovg_get_image( $this->post_id, 'large' );

				if ( ! empty( $image_data['src'] ) ) {
					$poster = aiovg_resolve_url( $image_data['src'] );
				}
			}
		}

		if ( empty( $poster ) ) {
			$videos = $this->get_videos();

			// YouTube
			if ( ! empty( $videos['youtube'] ) ) {
				$poster = aiovg_get_youtube_image_url( $videos['youtube'] );
			}

			// Vimeo
			if ( ! empty( $videos['vimeo'] ) ) {
				$poster = aiovg_get_vimeo_image_url( $videos['vimeo'] );
			}

			// Dailymotion
			if ( ! empty( $videos['dailymotion'] ) ) {
				$poster = aiovg_get_dailymotion_image_url( $videos['dailymotion'] );
			}

			// Rumble
			if ( ! empty( $videos['rumble'] ) ) {
				$oembed = aiovg_get_rumble_oembed_data( $videos['rumble'] );
				$poster = $oembed['thumbnail_url'];
			}
		}

		// Output
		$this->cache['poster'] = $poster;
		return $poster;
	}	
	
	/**
	 * Get the player settings.
	 *
	 * @since  3.5.0
 	 * @return array $settings Player settings.
	 */
	public function get_player_settings() {
		if ( isset( $this->cache['player_settings'] ) ) {
			return $this->cache['player_settings'];
		}

		$player_settings = get_option( 'aiovg_player_settings' );		

		$defaults = array(
			'width' 			  => $player_settings['width'],
			'ratio' 			  => $player_settings['ratio'],
			'preload'             => $player_settings['preload'],
			'playsinline'         => isset( $player_settings['playsinline'] ) ? $player_settings['playsinline'] : 0,
			'autoplay'            => $player_settings['autoplay'],
			'loop'                => $player_settings['loop'],
			'muted'               => $player_settings['muted'],
			'controls'            => $player_settings['controls'],
			'playpause'           => isset( $player_settings['controls']['playpause'] ),
			'current'             => isset( $player_settings['controls']['current'] ),
			'progress'            => isset( $player_settings['controls']['progress'] ),
			'duration'            => isset( $player_settings['controls']['duration'] ),
			'tracks'              => isset( $player_settings['controls']['tracks'] ),
			'chapters'            => isset( $player_settings['controls']['chapters'] ),
			'speed'               => isset( $player_settings['controls']['speed'] ),
			'quality'             => isset( $player_settings['controls']['quality'] ),			
			'volume'              => isset( $player_settings['controls']['volume'] ),
			'fullscreen'          => isset( $player_settings['controls']['fullscreen'] ),
			'share'			      => isset( $player_settings['controls']['share'] ),
			'embed'			      => isset( $player_settings['controls']['embed'] ),
			'download'			  => isset( $player_settings['controls']['download'] ),
			'hotkeys'             => isset( $player_settings['hotkeys'] ) ? $player_settings['hotkeys'] : 0,
			'cc_load_policy'      => $player_settings['cc_load_policy'],
			'use_native_controls' => $player_settings['use_native_controls'],
			'lazyloading'         => isset( $player_settings['lazyloading'] ) ? $player_settings['lazyloading'] : 0
		);

		$settings = shortcode_atts( $defaults, $this->args );

		if ( empty( $settings['ratio'] ) ) {
			$settings['ratio'] = 56.25;
		}

		// Output
		$this->cache['player_settings'] = $settings;
		return $settings;
	}

	/**
	 * Get the privacy settings.
	 *
	 * @since  3.5.0
 	 * @return array $settings Privacy settings.
	 */
	public function get_privacy_settings() {
		if ( isset( $this->cache['privacy_settings'] ) ) {
			return $this->cache['privacy_settings'];
		}

		if ( isset( $_COOKIE['aiovg_gdpr_consent'] ) ) {
			$settings = array( 
				'show_consent' => false 
			);
		} else {
			$privacy_settings = get_option( 'aiovg_privacy_settings' );			

			$settings = shortcode_atts( $privacy_settings, $this->args );

			if ( $settings['show_consent'] ) {
				$settings['consent_message'] = apply_filters( 'aiovg_translate_strings', $settings['consent_message'], 'consent_message' );
				$settings['consent_button_label'] = apply_filters( 'aiovg_translate_strings', $settings['consent_button_label'], 'consent_button_label' );
			}

			if ( empty( $settings['consent_message'] ) || empty( $settings['consent_button_label'] ) ) {
				$settings['show_consent'] = false;
			}
		}
 
		// Output
		$this->cache['privacy_settings'] = $settings;
		return $settings;
	}

	/**
	 * Get the logo settings.
	 *
	 * @since  3.5.0
 	 * @return array $settings Logo settings.
	 */
	public function get_logo_settings() {
		if ( isset( $this->cache['logo_settings'] ) ) {
			return $this->cache['logo_settings'];
		}

		$brand_settings = get_option( 'aiovg_brand_settings', array() );			

		$settings = shortcode_atts( $brand_settings, $this->args );

		if ( ! empty( $settings['logo_image'] ) ) {
			$settings['logo_image'] = aiovg_resolve_url( $settings['logo_image'] );
		} else {
			$settings['show_logo'] = false;
		}

		if ( ! empty( $settings['copyright_text'] ) ) {
			$settings['copyright_text'] = apply_filters( 'aiovg_translate_strings', $settings['copyright_text'], 'copyright_text' );
		}
 
		// Output
		$this->cache['logo_settings'] = $settings;
		return $settings;
	}

	/**
	 * Get the share buttons.
	 *
	 * @since  3.5.0
 	 * @return array Share buttons.
	 */
	public function get_share_buttons() {
		if ( isset( $this->cache['share_buttons'] ) ) {
			return $this->cache['share_buttons'];
		}

		$socialshare_settings = get_option( 'aiovg_socialshare_settings' );

		$share_url = get_permalink( $this->post_id );
			
		$share_title = $this->post_title;
		$share_title = str_replace( ' ', '%20', $share_title );
		$share_title = str_replace( '|', '%7C', $share_title );
		$share_title = str_replace( '@', '%40', $share_title );
	
		$share_image = $this->get_poster();
	
		$share_buttons = array();
			
		if ( isset( $socialshare_settings['services']['facebook'] ) ) {
			$share_buttons[] = array(
				'service' => 'facebook',		
				'url'     => "https://www.facebook.com/sharer/sharer.php?u={$share_url}",
				'icon'    => 'aiovg-icon-facebook',
				'text'    => __( 'Facebook', 'all-in-one-video-gallery' )				
			);
		}
	
		if ( isset( $socialshare_settings['services']['twitter'] ) ) {
			$share_buttons[] = array(
				'service' => 'twitter',			
				'url'     => "https://twitter.com/intent/tweet?text={$share_title}&amp;url={$share_url}",
				'icon'    => 'aiovg-icon-twitter',
				'text'    => __( 'Twitter', 'all-in-one-video-gallery' )
			);
		}		
	
		if ( isset( $socialshare_settings['services']['linkedin'] ) ) {
			$share_buttons[] = array(
				'service' => 'linkedin',			
				'url'     => "https://www.linkedin.com/shareArticle?url={$share_url}&amp;title={$share_title}",
				'icon'    => 'aiovg-icon-linkedin',
				'text'    => __( 'Linkedin', 'all-in-one-video-gallery' )
			);
		}
	
		if ( isset( $socialshare_settings['services']['pinterest'] ) ) {
			$pinterest_url = "https://pinterest.com/pin/create/button/?url={$share_url}&amp;description={$share_title}";
	
			if ( ! empty( $share_image ) ) {
				$pinterest_url .= "&amp;media={$share_image}";
			}
	
			$share_buttons[] = array(
				'service' => 'pinterest',			
				'url'     => $pinterest_url,
				'icon'    => 'aiovg-icon-pinterest',
				'text'    => __( 'Pinterest', 'all-in-one-video-gallery' )
			);
		}
	
		if ( isset( $socialshare_settings['services']['tumblr'] ) ) {
			$tumblr_url = "https://www.tumblr.com/share/link?url={$share_url}&amp;name={$share_title}";
	
			$share_description = aiovg_get_excerpt( $this->post_id, 160, '', false ); 
			if ( ! empty( $share_description ) ) {
				$share_description = str_replace( ' ', '%20', $share_description );
				$share_description = str_replace( '|', '%7C', $share_description );	
				$share_description = str_replace( '@', '%40', $share_description );
	
				$tumblr_url .= "&amp;description={$share_description}";
			}
	
			$share_buttons[] = array(	
				'service' => 'tumblr',		
				'url'     => $tumblr_url,
				'icon'    => 'aiovg-icon-tumblr',
				'text'    => __( 'Tumblr', 'all-in-one-video-gallery' )
			);
		}
	
		if ( isset( $socialshare_settings['services']['whatsapp'] ) ) {
			if ( wp_is_mobile() ) {
				$whatsapp_url = "whatsapp://send?text={$share_title} " . rawurlencode( $share_url );
			} else {
				$whatsapp_url = "https://api.whatsapp.com/send?text={$share_title}&nbsp;{$share_url}";
			}
	
			$share_buttons[] = array(
				'service' => 'whatsapp',				
				'url'     => $whatsapp_url,
				'icon'    => 'aiovg-icon-whatsapp',
				'text'    => __( 'WhatsApp', 'all-in-one-video-gallery' )
			);
		}

		$share_buttons = apply_filters( 'aiovg_player_socialshare_buttons', $share_buttons );
	
		// Output
		$this->cache['share_buttons'] = $share_buttons;
		return $share_buttons;
	}

	/**
	 * Get the video embedcode.
	 *
	 * @since  3.5.0
 	 * @return string $embedcode Video embedcode.
	 */
	public function get_embedcode() {
		if ( isset( $this->cache['embedcode'] ) ) {
			return $this->cache['embedcode'];
		}

		$player_settings = $this->get_player_settings();

		$embedcode = sprintf(
			'<div style="position:relative;padding-bottom:%s%%;height:0;overflow:hidden;"><iframe src="%s" title="%s" width="100%%" height="100%%" style="position:absolute;width:100%%;height:100%%;top:0px;left:0px;overflow:hidden;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>',
			(float) $player_settings['ratio'],
			esc_url( aiovg_get_player_page_url( $this->post_id, $this->args ) ),
			esc_attr( $this->post_title )
		);

		// Output
		$this->cache['embedcode'] = $embedcode;
		return $embedcode;
	}

	/**
	 * Get the video file download URL.
	 *
	 * @since  3.5.0
 	 * @return string Video file download URL.
	 */
	public function get_download_url() {	
		if ( isset( $this->cache['download_url'] ) ) {
			return $this->cache['download_url'];
		}

		$videos = $this->get_videos();
		$download_url = '';

		if ( ! empty( $videos['mp4'] ) ) {
			$can_download = 1;

			// Is a video post?
			if ( $this->post_id > 0 && 'aiovg_videos' == $this->post_type ) {
				if ( metadata_exists( 'post', $this->post_id, 'download' ) ) {
					$can_download = (int) get_post_meta( $this->post_id, 'download', true );
				}

				if ( $can_download ) {
					$download_url =  home_url( '?vdl=' . $this->post_id );
				}
			}

			if ( empty( $download_url ) && $can_download ) {
				$download_url = home_url( '?vdl=' . aiovg_get_temporary_file_download_id( $videos['mp4'] ) );
			}
		}

		// Output
		$this->cache['download_url'] = $download_url;
		return $download_url;
	}			

	/**
	 * Get the raw player embedcode.
	 *
	 * @since  3.5.0
 	 * @return string $html Player HTML.
	 */
	public function get_player_raw_embed() {
		$videos = $this->get_videos();

		wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-player' );

		$settings = array(
			'post_id'   => $this->post_id,
			'post_type' => 'aiovg_videos'
		);

		$html = sprintf(
			'<div class="aiovg-player-raw" data-params=\'%s\'>%s</div>',
			wp_json_encode( $settings ),
			$videos['embedcode']			
		);

		return $html;
	}

	/**
	 * Get the web component based lite player embed code.
	 * 
	 * @since  3.5.0
 	 * @return string $html Player HTML.
	 */
	public function get_player_lite_embed() {	
		$player_settings = $this->get_player_settings();
		$privacy_settings = $this->get_privacy_settings();

		$videos = $this->get_videos();
		$poster = $this->get_poster();				

		// Enqueue dependencies
		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-embed', 
			AIOVG_PLUGIN_URL . 'public/assets/js/embed.min.js', 
			array(), 
			AIOVG_PLUGIN_VERSION,
			array( 'strategy' => 'defer' )
		);
		
		// Vars
		$provider = 'embed';
		if ( ! empty( $videos['youtube'] ) ) {					
			$provider = 'youtube';
		} elseif ( ! empty( $videos['vimeo'] ) ) {
			$provider = 'vimeo';
		} elseif ( ! empty( $videos['dailymotion'] ) ) {
			$provider = 'dailymotion';
		}

		$attributes = array(
			'class'       => 'aiovg-player-element',
			'title'       => esc_attr( $this->post_title ),
			'src'         => esc_attr( $videos['iframe'] ),
			'poster'      => esc_attr( $poster ),
			'ratio'       => (float) $player_settings['ratio'],
			'post_id'     => (int) $this->post_id,
			'post_type'   => esc_attr( $this->post_type )
		);
		
		if ( ! empty( $player_settings['lazyloading'] ) ) {
			$attributes['lazyloading'] = '';
		}

		if ( ! empty( $privacy_settings['show_consent'] ) ) {
			$attributes['cookieconsent'] = '';
		}

		if ( ( $this->post_id > 0 && 'aiovg_videos' == $this->post_type ) || isset( $attributes['cookieconsent'] ) ) {
			$attributes['ajax_url'] = esc_attr( admin_url( 'admin-ajax.php' ) );
			$attributes['ajax_nonce'] = esc_attr( wp_create_nonce( 'aiovg_ajax_nonce' ) );
		}

		// Player
		$html = sprintf( 
			'<div class="aiovg-player-container" style="max-width: %s;">', 
			( ! empty( $player_settings['width'] ) ? (int) $player_settings['width'] . 'px' : '100%' )
		);

		$html .= sprintf( 
			'<aiovg-%s %s>', 
			$provider, 
			aiovg_combine_video_attributes( $attributes )
		);

		if ( isset( $attributes['cookieconsent'] ) ) { // Cookie consent			
			$html .= sprintf( '<div slot="cookieconsent-message">%s</div>', wp_kses_post( trim( $privacy_settings['consent_message'] ) ) );
			$html .= sprintf( '<span slot="cookieconsent-button-label">%s</span>', esc_attr( $privacy_settings['consent_button_label'] ) );
		}

		$html .= sprintf( '</aiovg-%s>', $provider );

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get the YouTube embed URL.
	 *
	 * @since  3.5.0
	 * @param  string $url YouTube video URL.
 	 * @return string $url Embed URL.
	 */
	public function get_youtube_embed_url( $url ) {
		$player_settings = $this->get_player_settings();

		parse_str( $url, $queries );

		$url = 'https://www.youtube.com/embed/' . aiovg_get_youtube_id_from_url( $url ) . '?modestbranding=1&showinfo=0&rel=0&iv_load_policy=3';									
		
		if ( isset( $queries['start'] ) ) {
			$url = add_query_arg( 'start', (int) $queries['start'], $url );
		}

		if ( isset( $queries['t'] ) ) {
			$url = add_query_arg( 'start', (int) $queries['t'], $url );
		}

		if ( isset( $queries['end'] ) ) {
			$url = add_query_arg( 'end', (int) $queries['end'], $url );
		}

		if ( empty( $player_settings['controls'] ) ) {
			$url = add_query_arg( 'controls', 0, $url );
		}

		if ( empty( $player_settings['fullscreen'] ) ) {
			$url = add_query_arg( 'fs', 0, $url );
		}

		if ( ! empty( $player_settings['autoplay'] ) ) {
			$url = add_query_arg( 'autoplay', 1, $url );
		}

		if ( ! empty( $player_settings['loop'] ) ) {
			$url = add_query_arg( 'loop', 1, $url );
		}

		if ( ! empty( $player_settings['muted'] ) ) {
			$url = add_query_arg( 'muted', 1, $url );
		}

		$url = add_query_arg( 'cc_load_policy', (int) $player_settings['cc_load_policy'], $url );

		$url = add_query_arg( 'playsinline', (int) $player_settings['playsinline'], $url );

		return $url;
	}

	/**
	 * Get the Vimeo embed URL.
	 *
	 * @since  3.5.0
	 * @param  string $url Vimeo video URL.
 	 * @return string $url Embed URL.
	 */
	public function get_vimeo_embed_url( $url ) {
		$player_settings = $this->get_player_settings();

		$oembed = aiovg_get_vimeo_oembed_data( $url );
		$url = 'https://player.vimeo.com/video/' . $oembed['video_id'] . '?title=0&byline=0&portrait=0';

		if ( ! empty( $oembed['html'] ) ) {
			$iframe_src = aiovg_extract_iframe_src( $oembed['html'] );
			if ( $iframe_src ) {
				$parsed_url = parse_url( $iframe_src, PHP_URL_QUERY );
				parse_str( $parsed_url, $queries );

				if ( isset( $queries['h'] ) ) {
					$url = add_query_arg( 'h', $queries['h'], $url );
				}

				if ( isset( $queries['app_id'] ) ) {
					$url = add_query_arg( 'app_id', $queries['app_id'], $url );
				}
			}
		}

		if ( ! empty( $player_settings['autoplay'] ) ) {
			$url = add_query_arg( 'autoplay', 1, $url );
		}

		if ( ! empty( $player_settings['loop'] ) ) {
			$url = add_query_arg( 'loop', 1, $url );
		}

		if ( ! empty( $player_settings['muted'] ) ) {
			$url = add_query_arg( 'mute', 1, $url );
		}

		$url = add_query_arg( 'playsinline', (int) $player_settings['playsinline'], $url );

		return $url;
	}

	/**
	 * Get the Dailymotion embed URL.
	 *
	 * @since  3.5.0
	 * @param  string $url Dailymotion video URL.
 	 * @return string $url Embed URL.
	 */
	public function get_dailymotion_embed_url( $url ) {
		$player_settings = $this->get_player_settings();

		$url = 'https://www.dailymotion.com/embed/video/' . aiovg_get_dailymotion_id_from_url( $url ) . '?queue-autoplay-next=0&queue-enable=0&sharing-enable=0&ui-logo=0&ui-start-screen-info=0';

		if ( ! empty( $player_settings['autoplay'] ) ) {
			$url = add_query_arg( 'autoplay', 1, $url );
		}

		if ( ! empty( $player_settings['loop'] ) ) {
			$url = add_query_arg( 'loop', 1, $url );
		}

		if ( ! empty( $player_settings['muted'] ) ) {
			$url = add_query_arg( 'muted', 1, $url );
		}

		return $url;
	}

	/**
	 * Get the Rumble embed URL.
	 *
	 * @since  3.5.0
	 * @param  string $url Rumble video URL.
 	 * @return string $url Embed URL.
	 */
	public function get_rumble_embed_url( $url ) {
		$player_settings = $this->get_player_settings();

		$oembed = aiovg_get_rumble_oembed_data( $url );

		if ( ! empty( $oembed['html'] ) ) {
			$iframe_src = aiovg_extract_iframe_src( $oembed['html'] );
			if ( $iframe_src ) {
				$url = add_query_arg( 'rel', 0, $iframe_src );	

				if ( ! empty( $player_settings['autoplay'] ) ) {
					$url = add_query_arg( 'autoplay', 2, $url );
				}
			}
		}

		return $url;
	}

	/**
	 * Get the Facebook embed URL.
	 *
	 * @since  3.5.0
	 * @param  string $url Facebook video URL.
 	 * @return string $url Embed URL.
	 */
	public function get_facebook_embed_url( $url ) {
		$player_settings = $this->get_player_settings();

		$url = 'https://www.facebook.com/plugins/video.php?href=' . urlencode( $url ) . '&width=560&height=315&show_text=false&appId';

		if ( ! empty( $player_settings['autoplay'] ) ) {
			$url = add_query_arg( 'autoplay', 1, $url );
		}

		if ( ! empty( $player_settings['loop'] ) ) {
			$url = add_query_arg( 'loop', 1, $url );
		}

		if ( ! empty( $player_settings['muted'] ) ) {
			$url = add_query_arg( 'muted', 1, $url );
		}

		return $url;
	}
		
}
