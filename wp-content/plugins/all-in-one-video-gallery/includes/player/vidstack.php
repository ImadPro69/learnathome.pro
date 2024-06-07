<?php

/**
 * Vidstack Player.
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
 * AIOVG_Player_Vidstack class.
 *
 * @since 3.5.0
 */
class AIOVG_Player_Vidstack extends AIOVG_Player_Base {

	/**
	 * Array of videos.
	 *
	 * @since  3.5.0
	 * @access private
	 * @var    array	 
	 */
	private $videos = array();

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
 	 * @return string Player HTML.
	 */
	public function get_player() {
		$videos = $this->get_videos();

		// Raw embedcode that contains script tags
		if ( isset( $videos['embedcode'] ) ) {
			return $this->get_player_raw_embed();
		}

		// Iframe embedcode
		if ( isset( $videos['iframe'] ) ) {
			return $this->get_player_lite_embed();
		}

		// Videostack player
		return $this->get_player_vidstack();
	}

	/**
	 * Get the vidstack player HTML.
	 *
	 * @since  3.5.0
	 * @access private
 	 * @return string  Player HTML.
	 */
	public function get_player_vidstack() {
		$player_settings = $this->get_player_settings();
		$privacy_settings = $this->get_privacy_settings();
		$logo_settings = $this->get_logo_settings();

		$videos = $this->get_videos();
		$poster = $this->get_poster();	

		$params = $this->get_params();

		$settings = array(
			'post_id'     => $this->post_id,
			'post_type'   => esc_attr( $this->post_type ),
			'ajax_url'    => esc_attr( admin_url( 'admin-ajax.php' ) ),
			'ajax_nonce'  => esc_attr( wp_create_nonce( 'aiovg_ajax_nonce' ) ),
			'lazyloading' => (int) $player_settings['lazyloading'],
			'player'  => array(
				'volume' => 0.5				
			)
		);		

		// Video Sources	
		$sources = array();

		$formats = array( 'mp4', 'webm', 'ogv', 'hls', 'dash', 'youtube', 'vimeo' );		

		foreach ( $formats as $format ) {
			if ( empty( $videos[ $format ] ) ) {
				continue;
			}

			$mime_type = "video/{$format}";
			$label = '';

			switch ( $format ) {
				case 'mp4':
					$extension = aiovg_get_file_ext( $videos[ $format ] );
					if ( ! in_array( $extension, array( 'webm', 'ogv' ) ) ) {
						$extension = 'mp4';
					}

					$mime_type = "video/{$extension}";

					if ( ! empty( $videos['quality_level'] ) ) {
						$label = $videos['quality_level'];
					}
					break;

				case 'hls':
					$mime_type = 'application/x-mpegurl';
					break;

				case 'dash':
					$mime_type = 'application/dash+xml';
					break;
			}

			$sources[ $format ] = array(
				'type' => $mime_type,
				'src'  => $videos[ $format ]
			);

			if ( ! empty( $label ) ) {
				$sources[ $format ]['label'] = $label;
			}
		}

		if ( isset( $videos['sources'] ) ) {
			foreach ( $videos['sources'] as $source ) {
				if ( ! empty( $source['quality'] ) && ! empty( $source['src'] ) ) {	
					$extension = aiovg_get_file_ext( $source['src'] );
					if ( ! in_array( $extension, array( 'webm', 'ogv' ) ) ) {
						$extension = 'mp4';
					}

					$label = $source['quality'];

					$sources[ $label ] = array(
						'type'  => "video/{$extension}",
						'src'   => $source['src'],
						'label' => $label
					);
				}
			}
		}

		$sources = apply_filters( 'aiovg_vidstack_player_sources', $sources, $params );

		// Video Captions
		$tracks = array();

		if ( ! empty( $player_settings['tracks'] ) ) {	
			$tracks = $this->get_tracks();

			if ( ! empty( $player_settings['cc_load_policy'] ) ) {
				$settings['player']['captions'] = array(
					'active'   => true,
					'language' => 'auto',
					'update'   => false
				); 
			}
		}
		
		$tracks = apply_filters( 'aiovg_vidstack_player_tracks', $tracks, $params );

		// Video Chapters
		if ( ! empty( $player_settings['chapters'] ) ) {	
			$chapters = $this->get_chapters();

			if ( ! empty( $chapters ) ) {
				$settings['player']['markers'] = array(
					'enabled' => true,
					'points'  => $chapters
				); 
			}
		}		

		// Video Attributes
		$attributes = array(
			'id'       => 'aiovg-player-' . $this->reference_id,
			'style'    => 'width: 100%; height: 100%',
			'controls' => '',
			'preload'  => esc_attr( $player_settings['preload'] )
		);

		if ( ! empty( $player_settings['autoplay'] ) ) {
			$attributes['autoplay'] = '';
			$settings['player']['autoplay'] = true;
		}

		if ( ! empty( $player_settings['loop'] ) ) {
			$attributes['loop'] = '';
			$settings['player']['loop'] = array( 'active' => true );
		}

		if ( ! empty( $player_settings['muted'] ) ) {
			$attributes['muted'] = '';
			$settings['player']['muted'] = true;
		}	
		
		if ( ! empty( $player_settings['playsinline'] ) ) {
			$attributes['playsinline'] = '';
			$settings['player']['playsinline'] = true;
		} else {
			$settings['player']['playsinline'] = false;
		}

		if ( ! empty( $poster ) ) {
			$attributes['data-poster'] = aiovg_sanitize_url( $poster );
		}			

		$attributes = apply_filters( 'aiovg_vidstack_player_attributes', $attributes, $params );

		// Player Settings
		$controls = array();
		$controls[] = 'play-large';

		if ( ! empty( $player_settings['playpause'] ) ) {
			$controls[] = 'play';
		}

		if ( ! empty( $player_settings['current'] ) ) {
			$controls[] = 'current-time';
		}
		
		if ( ! empty( $player_settings['progress'] ) ) {
			$controls[] = 'progress';
		}

		if ( ! empty( $player_settings['duration'] ) ) {
			$controls[] = 'duration';
		}

		if ( ! empty( $player_settings['volume'] ) ) {
			$controls[] = 'mute';

			if ( ! wp_is_mobile() ) {
				$controls[] = 'volume';
			}
		}

		if ( ! empty( $player_settings['tracks'] ) ) {
			if ( ! wp_is_mobile() ) {
				$controls[] = 'captions';	
			}		
		}

		if ( ! empty( $player_settings['quality'] ) || ! empty( $player_settings['tracks'] ) || ! empty( $player_settings['speed'] ) ) {
			$controls[] = 'settings';
		
			$settings['player']['settings'] = array();

			if ( ! empty( $player_settings['quality'] ) ) {
				$settings['player']['settings'][] = 'quality';
			}

			if ( ! empty( $player_settings['tracks'] ) ) {
				$settings['player']['settings'][] = 'captions';
			}

			if ( ! empty( $player_settings['speed'] ) ) {
				$settings['player']['settings'][] = 'speed';

				$settings['player']['speed'] = array(
					'selected' => 1,
					'options'  => array( 0.5, 0.75, 1, 1.5, 2 )
				);
			}
		}

		if ( ! empty( $player_settings['download'] ) && $download_url = $this->get_download_url() ) {
			$controls[] = 'download';

			$settings['player']['urls'] = array(
				'download' => esc_url( $download_url )
			);
		}

		if ( ! empty( $player_settings['fullscreen'] ) ) {
			$controls[] = 'fullscreen';
		}
		
		$settings['player']['controls'] = $controls;

		if ( ! empty( $player_settings['hotkeys'] ) ) {
			$settings['player']['keyboard'] = array(
				'focused' => true,
				'global'  => false
			);
		}

		if ( ! empty( $player_settings['share'] ) ) {
			$settings['share'] = 1;
		}

		if ( ! empty( $player_settings['embed'] ) ) {
			$settings['embed'] = 1;
		}			

		if ( ! empty( $logo_settings['show_logo'] ) ) {
			$settings['logo'] = array(
				'image'    => aiovg_sanitize_url( $logo_settings['logo_image'] ),
				'link'     => ! empty( $logo_settings['logo_link'] ) ? esc_url_raw( $logo_settings['logo_link'] ) : 'javascript:void(0)',
				'position' => sanitize_text_field( $logo_settings['logo_position'] ),
				'margin'   => ! empty( $logo_settings['logo_margin'] ) ? (int) $logo_settings['logo_margin'] : 15
			);
		}

		if ( ! empty( $logo_settings['copyright_text'] ) ) {
			$settings['contextmenu'] = array(
				'content' => sanitize_text_field( htmlspecialchars( $logo_settings['copyright_text'] ) )
			);
		}	
		
		if ( ! empty( $privacy_settings['show_consent'] ) ) {
			if ( isset( $sources['youtube'] ) || isset( $sources['vimeo'] ) ) {
				$settings['cookie_consent'] = 1;
			}
		}
		
		if ( isset( $sources['youtube'] ) ) { // YouTube
			$settings['player']['youtube'] = array(
				'noCookie'       => false,
				'rel'            => 0,
				'showinfo'       => 0,
				'iv_load_policy' => 3,
				'modestbranding' => 1
			);

			parse_str( $sources['youtube']['src'], $queries );

			if ( isset( $queries['start'] ) ) {
				$settings['player']['youtube']['start'] = (int) $queries['start'];
			}

			if ( isset( $queries['t'] ) ) {
				$settings['player']['youtube']['start'] = (int) $queries['t'];
			}

			if ( isset( $queries['end'] ) ) {
				$settings['player']['youtube']['end'] = (int) $queries['end'];
			}
		}

		if ( isset( $sources['vimeo'] ) ) { // Vimeo
			$settings['player']['vimeo'] = array(
				'byline'      => false,
				'portrait'    => false,
				'title'       => false,
				'speed'       => true,
				'transparent' => false
			);
		}

		if ( isset( $sources['hls'] ) ) { // HLS
			$settings['hls'] = $sources['hls']['src'];

			if ( ! empty( $player_settings['tracks'] ) ) {
				$settings['player']['captions'] = array(
					'active'   => ! empty( $player_settings['cc_load_policy'] ) ? true : false,
					'language' => 'auto',
					'update'   => true
				); 
			}
		}

		if ( isset( $sources['dash'] ) ) { // Dash
			$settings['dash'] = $sources['dash']['src'];

			if ( ! empty( $player_settings['tracks'] ) ) {
				$settings['player']['captions'] = array(
					'active'   => ! empty( $player_settings['cc_load_policy'] ) ? true : false,
					'language' => 'auto',
					'update'   => true
				); 
			}
		}			

		$settings = apply_filters( 'aiovg_vidstack_player_settings', $settings, $params );

		// Include Dependencies
		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-plyr', 
			AIOVG_PLUGIN_URL . 'vendor/vidstack/plyr.css', 
			array(), 
			'3.7.8', 
			'all' 
		);

		if ( isset( $settings['share'] ) ) {
			wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-icons' );
		}
		
		wp_dequeue_style( AIOVG_PLUGIN_SLUG . '-player' );

		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-player', 
			AIOVG_PLUGIN_URL . 'public/assets/css/vidstack.min.css', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			'all' 
		);

		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-plyr', 
			AIOVG_PLUGIN_URL . 'vendor/vidstack/plyr.polyfilled.js', 
			array(), 
			'3.7.8', 
			array( 'strategy' => 'defer' ) 
		);		

		if ( isset( $sources['hls'] ) ) {
			wp_enqueue_script( 
				AIOVG_PLUGIN_SLUG . '-hls', 
				AIOVG_PLUGIN_URL . 'vendor/vidstack/hls.min.js', 
				array(), 
				'1.4.3', 
				array( 'strategy' => 'defer' ) 
			);	
		}

		if ( isset( $sources['dash'] ) ) {
			wp_enqueue_script( 
				AIOVG_PLUGIN_SLUG . '-dash', 
				AIOVG_PLUGIN_URL . 'vendor/vidstack/dash.all.min.js', 
				array(), 
				AIOVG_PLUGIN_VERSION, 
				array( 'strategy' => 'defer' ) 
			);
		}
		
		do_action( 'aiovg_vidstack_player_scripts', $settings, $attributes, $sources, $tracks );

		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-vidstack', 
			AIOVG_PLUGIN_URL . 'public/assets/js/vidstack.min.js', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			array( 'strategy' => 'defer' ) 
		);

		aiovg_add_inline_script(
			AIOVG_PLUGIN_SLUG . '-vidstack',
			'var aiovg_player_' . (int) $this->reference_id . ' = ' . wp_json_encode( $settings ) . ';'
		);		

		// Output
		$player_html = '';

		if ( isset( $sources['youtube'] ) ) { // YouTube
			$video_id = aiovg_get_youtube_id_from_url( $sources['youtube']['src'] );

			$player_html .= sprintf(
				'<div id="%s" style="%s" data-plyr-provider="youtube" data-plyr-embed-id="%s" data-poster="%s"></div>',
				esc_attr( $attributes['id'] ),
				esc_attr( $attributes['style'] ),
				esc_attr( $video_id ),
				( isset( $attributes['data-poster'] ) ? esc_attr( $attributes['data-poster'] ) : '' )
			);
		}

		elseif ( isset( $sources['vimeo'] ) ) { // Vimeo
			$video_id = aiovg_get_vimeo_id_from_url( $sources['vimeo']['src'] );
	
			$player_html .= sprintf(
				'<div id="%s" style="%s" data-plyr-provider="vimeo" data-plyr-embed-id="%s" data-poster="%s"></div>',
				esc_attr( $attributes['id'] ),
				esc_attr( $attributes['style'] ),
				esc_attr( $video_id ),
				( isset( $attributes['data-poster'] ) ? esc_attr( $attributes['data-poster'] ) : '' )
			);
		}

		elseif ( isset( $sources['hls'] ) || isset( $sources['dash'] ) ) { // HLS or Dash
			$player_html .= sprintf( '<video %s></video>', aiovg_combine_video_attributes( $attributes ) );
		}

		elseif ( ! empty( $sources ) ) { // HTML5 Video
			$player_html .= sprintf( '<video %s>', aiovg_combine_video_attributes( $attributes ) );
		
			foreach ( $sources as $source ) { // Sources
				$player_html .= sprintf( 
					'<source type="%s" src="%s" size="%d" />', 
					esc_attr( $source['type'] ), 
					esc_attr( $source['src'] ),
					( isset( $source['label'] ) ? (int) $source['label'] : '' )
				);
			}		
			
			foreach ( $tracks as $index => $track ) { // Tracks
				$player_html .= sprintf( 
					'<track kind="captions" src="%s" label="%s" srclang="%s" />', 
					esc_attr( $track['src'] ),						 
					esc_attr( $track['label'] ),
					esc_attr( $track['srclang'] )
				);
			}

			$player_html .= '</video>';
		}		
		
		if ( isset( $settings['share'] ) || isset( $settings['embed'] ) ) { // Share / Embed
			$player_html .= '<div class="plyr__share-embed-modal" style="display: none;">';
			$player_html .= '<div class="plyr__share-embed-modal-content">';
			
			if ( isset( $settings['share'] ) ) { // Share Buttons
				$share_buttons = $this->get_share_buttons();

				$player_html .= '<div class="plyr__share">';
				foreach ( $share_buttons as $button ) {
					$player_html .= sprintf( 
						'<a href="%s" class="plyr__share-button plyr__share-button-%s" target="_blank"><span class="%s"></span><span class="plyr__sr-only">%s</span></a>',							
						esc_attr( $button['url'] ), 
						esc_attr( $button['service'] ),
						esc_attr( $button['icon'] ),
						esc_attr( $button['text'] )  
					);
				}
				$player_html .= '</div>';
			}
			
			if ( isset( $settings['embed'] ) ) { // Embed Code
				$embedcode = $this->get_embedcode();

				$player_html .= '<div class="plyr__embed">';
				$player_html .= '<label for="plyr__embed-code-input-' . $this->reference_id . '">' . esc_html__( 'Paste this code in your HTML page', 'all-in-one-video-gallery' ) . '</label>';
				$player_html .= '<input type="text" id="plyr__embed-code-input-' . $this->reference_id . '" class="plyr__embed-code-input" value="' . htmlspecialchars( $embedcode ) . '" readonly />';
				$player_html .= '</div>';
			}

			$player_html .= '<button type="button" class="plyr__controls__item plyr__control plyr__share-embed-modal-close-button aiovg-icon-close"><span class="plyr__sr-only">Close</span></button>';

			$player_html .= '</div>';
			$player_html .= '</div>';
		}
		
		if ( isset( $settings['cookie_consent'] ) ) { // Cookie Consent
			$player_html .= sprintf(
				'<div class="aiovg-privacy-wrapper" data-poster="%s"><div class="aiovg-privacy-consent-block"><div class="aiovg-privacy-consent-message">%s</div><button type="button" class="aiovg-privacy-consent-button">%s</button></div></div>',
				( isset( $attributes['data-poster'] ) ? esc_attr( $attributes['data-poster'] ) : '' ),
				wp_kses_post( trim( $privacy_settings['consent_message'] ) ),
				esc_html( $privacy_settings['consent_button_label'] )
			);
		}

		// Return			
		$html = sprintf( 
			'<div class="aiovg-player-container" style="max-width: %s;">', 
			( ! empty( $player_settings['width'] ) ? (int) $player_settings['width'] . 'px' : '100%' )
		);

		$attributes = array(			
			'class' => 'aiovg-player aiovg-player-element',
			'style' => 'padding-bottom: ' . (float) $player_settings['ratio'] . '%;',
			'reference_id' => $this->reference_id
		);		

		if ( isset( $settings['cookie_consent'] ) ) {
			$attributes['cookieconsent'] = '';
		}

		$html .= sprintf( 
			'<aiovg-video %s>%s</aiovg-video>',
			aiovg_combine_video_attributes( $attributes ),
			$player_html
		);

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get the videos.
	 *
	 * @since  3.5.0
 	 * @return array $videos Array of videos.
	 */
	public function get_videos() {
		if ( ! empty( $this->videos ) ) {
			return $this->videos;
		}

		$player_settings = $this->get_player_settings();
		$videos = parent::get_videos();

		// Force native embed when applicable
		if ( ! empty( $videos['youtube'] ) ) {
			$use_native_controls = apply_filters( 'aiovg_use_native_controls', isset( $player_settings['use_native_controls']['youtube'] ), 'youtube' );
			if ( $use_native_controls ) {
				$videos['iframe'] = $this->get_youtube_embed_url( $videos['youtube'] );
			}
		}

		if ( ! empty( $videos['vimeo'] ) ) {
			$use_native_controls = apply_filters( 'aiovg_use_native_controls', isset( $player_settings['use_native_controls']['vimeo'] ), 'vimeo' );
			if ( $use_native_controls ) {
				$videos['iframe'] = $this->get_vimeo_embed_url( $videos['vimeo'] );
			}
		}

		if ( ! empty( $videos['dailymotion'] ) ) {
			$videos['iframe'] = $this->get_dailymotion_embed_url( $videos['dailymotion'] );
		}

		if ( ! empty( $videos['rumble'] ) ) {
			$videos['iframe'] = $this->get_rumble_embed_url( $videos['rumble'] );
		}

		if ( ! empty( $videos['facebook'] ) ) {
			$videos['iframe'] = $this->get_facebook_embed_url( $videos['facebook'] );
		}

		// Set embed URL if available
		if ( isset( $videos['iframe'] ) ) {
			$this->embed_url = $videos['iframe'];
		}

		// Output
		$this->videos = $videos;
		return $this->videos;
	}
		
}
