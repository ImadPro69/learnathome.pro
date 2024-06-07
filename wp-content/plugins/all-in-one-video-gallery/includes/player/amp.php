<?php

/**
 * AMP Player.
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
 * AIOVG_Player_AMP class.
 *
 * @since 3.5.0
 */
class AIOVG_Player_AMP extends AIOVG_Player_Base {

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

		// Iframe Embedcode
		if ( isset( $videos['iframe'] ) ) {
			return $this->get_player_iframe();
		}

		// YouTube
		if ( ! empty( $videos['youtube'] ) ) {
			return $this->get_player_youtube();
		}
		
		// Vimeo
		if ( ! empty( $videos['vimeo'] ) ) {
			return $this->get_player_vimeo();
		}
		
		// Dailymotion
		if ( ! empty( $videos['dailymotion'] ) ) {
			return $this->get_player_dailymotion();
		}

		// MP4, WebM, OGV, HLS & Dash		            
		return $this->get_player_html5();
	}

	/**
	 * Get the Iframe player.
	 *
	 * @since  3.5.0
	 * @access private
 	 * @return string  $html Player HTML.
	 */
	private function get_player_iframe() {	
		$player_settings = $this->get_player_settings();

		$videos = $this->get_videos();	
		$poster = $this->get_poster();			

		$width  = ! empty( $player_settings['width'] ) ? (int) $player_settings['width'] : 640;
		$ratio  = (float) $player_settings['ratio'];
		$height = ( $width * $ratio ) / 100;

		$attributes = array(
			'title'  => esc_attr( $this->post_title ),
			'width'  => $width,
			'height' => $height,
			'layout' => 'responsive'
		);		

		$attributes['src'] = esc_attr( $videos['iframe'] );

		$attributes['sandbox'] = 'allow-scripts allow-same-origin allow-popups';
		$attributes['allowfullscreen'] = '';
		$attributes['frameborder'] = '0';

		$placeholder = '';
		if ( ! empty( $poster ) ) {
			$placeholder = sprintf(
				'<amp-img src="%s" layout="fill" placeholder></amp-img>',
				esc_attr( $poster )
			);
		}

		return sprintf(
			'<amp-iframe %s>%s</amp-iframe>',
			aiovg_combine_video_attributes( $attributes ),
			$placeholder
		);		
	}

	/**
	 * Get the YouTube player.
	 *
	 * @since  3.5.0
 	 * @return string Player HTML.
	 */
	public function get_player_youtube() {
		$player_settings = $this->get_player_settings();
		$videos = $this->get_videos();

		$width  = ! empty( $player_settings['width'] ) ? (int) $player_settings['width'] : 640;
		$ratio  = (float) $player_settings['ratio'];
		$height = ( $width * $ratio ) / 100;

		$attributes = array(
			'width'  => $width,
			'height' => $height,
			'layout' => 'responsive'
		);

		$src = esc_url_raw( $videos['youtube'] );
		$attributes['data-videoid'] = aiovg_get_youtube_id_from_url( $src );

		$attributes['data-param-showinfo'] = 0;
		$attributes['data-param-rel'] = 0;
		$attributes['data-param-iv_load_policy'] = 3;

		if ( empty( $player_settings['controls'] ) ) {
			$attributes['data-param-controls'] = 0;
		}

		if ( empty( $player_settings['fullscreen'] ) ) {
			$attributes['data-param-fs'] = 0;
		}

		if ( ! empty( $player_settings['autoplay'] ) ) {
			$attributes['autoplay'] = '';
		}

		if ( ! empty( $player_settings['loop'] ) ) {
			$attributes['loop'] = '';
		}  

		return sprintf( '<amp-youtube %s></amp-youtube>', aiovg_combine_video_attributes( $attributes ) );		
	}

	/**
	 * Get the Vimeo player.
	 *
	 * @since  3.5.0
 	 * @return string Player HTML.
	 */
	public function get_player_vimeo() {
		$player_settings = $this->get_player_settings();
		$videos = $this->get_videos();

		$width  = ! empty( $player_settings['width'] ) ? (int) $player_settings['width'] : 640;
		$ratio  = (float) $player_settings['ratio'];
		$height = ( $width * $ratio ) / 100;

		$attributes = array(
			'width'  => $width,
			'height' => $height,
			'layout' => 'responsive'
		);

		$src = esc_url_raw( $videos['vimeo'] );
		$attributes['data-videoid'] = aiovg_get_vimeo_id_from_url( $src );

		if ( ! empty( $player_settings['autoplay'] ) ) {
			$attributes['autoplay'] = '';
		}

		return sprintf( '<amp-vimeo %s></amp-vimeo>', aiovg_combine_video_attributes( $attributes ) );		
	}

	/**
	 * Get the dailymotion player.
	 *
	 * @since  3.5.0
 	 * @return string Player HTML.
	 */
	public function get_player_dailymotion() {
		$player_settings = $this->get_player_settings();
		$videos = $this->get_videos();

		$width  = ! empty( $player_settings['width'] ) ? (int) $player_settings['width'] : 640;
		$ratio  = (float) $player_settings['ratio'];
		$height = ( $width * $ratio ) / 100;

		$attributes = array(
			'width'  => $width,
			'height' => $height,
			'layout' => 'responsive'
		);

		$src = esc_url_raw( $videos['dailymotion'] );
		$attributes['data-videoid'] = aiovg_get_dailymotion_id_from_url( $src );

		if ( empty( $player_settings['controls'] ) ) {
			$attributes['data-param-controls'] = 'false';
		}

		if ( ! empty( $player_settings['autoplay'] ) ) {
			$attributes['autoplay'] = '';
		}

		if ( ! empty( $player_settings['muted'] ) ) {
			$attributes['mute'] = 'true';
		}

		$attributes['data-endscreen-enable'] = 'false';
		$attributes['data-sharing-enable'] = 'false';
		$attributes['data-ui-logo'] = 'false';

		$attributes['data-param-queue-autoplay-next'] = 0;
		$attributes['data-param-queue-enable'] = 0;

		return sprintf( '<amp-dailymotion %s></amp-dailymotion>', aiovg_combine_video_attributes( $attributes ) );
	}

	/**
	 * Get the standard HTML5 player.
	 *
	 * @since  3.5.0
 	 * @return string Player HTML.
	 */
	public function get_player_html5() {
		$player_settings = $this->get_player_settings();

		$videos = $this->get_videos();		
		$poster = $this->get_poster();			

		$width  = ! empty( $player_settings['width'] ) ? (int) $player_settings['width'] : 640;
		$ratio  = (float) $player_settings['ratio'];
		$height = ( $width * $ratio ) / 100;

		// Attributes
		$attributes = array(
			'width'  => $width,
			'height' => $height,
			'layout' => 'responsive'
		);   
		
		if ( ! empty( $player_settings['autoplay'] ) ) {
			$attributes['autoplay'] = '';
		}

		if ( ! empty( $player_settings['loop'] ) ) {
			$attributes['loop'] = '';
		}            

		if ( ! empty( $poster ) ) {
			$attributes['poster'] = esc_attr( $poster );
		}

		// Videos
		$sources = array();

		$formats = array( 'mp4', 'webm', 'ogv', 'hls', 'dash' );		

		foreach ( $formats as $format ) {
			if ( empty( $videos[ $format ] ) ) {
				continue;
			}

			$mime_type = "video/{$format}";
			if ( 'hls' == $format ) $mime_type = 'application/x-mpegurl';
			if ( 'dash' == $format ) $mime_type = 'application/dash+xml';

			$src = str_replace( 'http://', '//', $videos[ $format ] );

			$sources[] = sprintf(
				'<source type="%s" src="%s" />',
				$mime_type,
				esc_attr( $src )
			);
		}			

		// Tracks
		if ( ! empty( $player_settings['tracks'] ) ) {
			$tracks = $this->get_tracks();

			foreach ( $tracks as $track ) {
				$src = str_replace( 'http://', '//', $track['src'] );

				$sources[] = sprintf( 
					'<track src="%s" kind="subtitles" srclang="%s" label="%s">', 
					esc_attr( $src ), 
					esc_attr( $track['srclang'] ), 
					esc_attr( $track['label'] ) 
				);
			}
		}		

		return sprintf(
			'<amp-video %s>%s</amp-video>',
			aiovg_combine_video_attributes( $attributes ),
			implode( '', $sources )
		);
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

		$videos = parent::get_videos();

		// Force native embed when applicable
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
