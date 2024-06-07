<?php

/**
 * Video.js Player.
 *
 * @link     https://plugins360.com
 * @since    3.3.1
 *
 * @package All_In_One_Video_Gallery
 */

$settings = array(	
	'uid'            => isset( $_GET['uid'] ) ? sanitize_text_field( $_GET['uid'] ) : 0,
	'post_id'        => $post_id,
	'post_type'      => $post_type,
	'cc_load_policy' => isset( $_GET['cc_load_policy'] ) ? (int) $_GET['cc_load_policy'] : (int) $player_settings['cc_load_policy'],
	'hotkeys'        => isset( $player_settings['hotkeys'] ) && ! empty( $player_settings['hotkeys'] ) ? 1 : 0,	
	'i18n'           => array(
		'stream_not_found' => __( 'This stream is currently not live. Please check back or refresh your page.', 'all-in-one-video-gallery' )
	),
	'player' => array(
		'controlBar'                => array(),
		'playbackRates'             => array( 0.5, 0.75, 1, 1.5, 2 ),
		'suppressNotSupportedError' => true
	)
);

$autoadvance = isset( $_GET['autoadvance'] ) ? (int) $_GET['autoadvance'] : 0;
if ( $autoadvance ) {
	$settings['autoadvance'] = 1;
}

// Video Sources
$sources = array();
$allowed_types = array( 'mp4', 'webm', 'ogv', 'hls', 'dash', 'youtube', 'vimeo', 'dailymotion' );

if ( ! empty( $post_meta ) ) {
	$type = $post_meta['type'][0];

	switch ( $type ) {
		case 'default':
			$types = array( 'mp4', 'webm', 'ogv' );

			foreach ( $types as $type ) {
				if ( ! empty( $post_meta[ $type ][0] ) ) {
					$ext   = $type;
					$label = '';

					if ( 'mp4' == $type ) {
						$ext = aiovg_get_file_ext( $post_meta[ $type ][0] );
						if ( ! in_array( $ext, array( 'webm', 'ogv' ) ) ) {
							$ext = 'mp4';
						}

						if ( ! empty( $post_meta['quality_level'][0] ) ) {
							$label = $post_meta['quality_level'][0];
						}
					}

					$sources[ $type ] = array(
						'type' => "video/{$ext}",
						'src'  => $post_meta[ $type ][0]
					);

					if ( ! empty( $label ) ) {
						$sources[ $type ]['label'] = $label;
					}
				}
			}

			if ( ! empty( $post_meta['sources'][0] ) ) {
				$_sources = unserialize( $post_meta['sources'][0] );

				foreach ( $_sources as $source ) {
					if ( ! empty( $source['quality'] ) && ! empty( $source['src'] ) ) {	
						$ext = aiovg_get_file_ext( $source['src'] );
						if ( ! in_array( $ext, array( 'webm', 'ogv' ) ) ) {
							$ext = 'mp4';
						}

						$label = $source['quality'];

						$sources[ $label ] = array(
							'type'  => "video/{$ext}",
							'src'   => $source['src'],
							'label' => $label
						);
					}
				}
			}
			break;
		case 'adaptive':
			$hls = isset( $post_meta['hls'] ) ? $post_meta['hls'][0] : '';
			if ( ! empty( $hls ) ) {
				$sources['hls'] = array(
					'type' => 'application/x-mpegurl',
					'src'  => $hls
				);
			}

			$dash = isset( $post_meta['dash'] ) ? $post_meta['dash'][0] : '';
			if ( ! empty( $dash ) ) {
				$sources['dash'] = array(
					'type' => 'application/dash+xml',
					'src'  => $dash
				);
			}
			break;
		default:
			if ( in_array( $type, $allowed_types ) && ! empty( $post_meta[ $type ][0] ) ) {
				$src = $post_meta[ $type ][0];

				$sources[ $type ] = array(
					'type' => "video/{$type}",
					'src'  => $src
				);
			}
	}
} else {
	foreach ( $allowed_types as $type ) {		
		if ( isset( $_GET[ $type ] ) && ! empty( $_GET[ $type ] ) ) {
			switch ( $type ) {
				case 'hls': 
					$mime_type = 'application/x-mpegurl'; 
					break;
				case 'dash': 
					$mime_type = 'application/dash+xml'; 
					break;
				default: 
					$mime_type = "video/{$type}";
			}

			$src = aiovg_sanitize_url( $_GET[ $type ] );

			$sources[ $type ] = array(
				'type' => $mime_type,
				'src'  => $src
			);
		}	
	}
}

$sources = apply_filters( 'aiovg_video_sources', $sources ); // Backward compatibility to 3.3.0
$sources = apply_filters( 'aiovg_iframe_videojs_player_sources', $sources );

// Video Tracks
$tracks = array();
$has_tracks = isset( $_GET['tracks'] ) ? (int) $_GET['tracks'] : isset( $player_settings['controls']['tracks'] );

if ( $has_tracks && ! empty( $post_meta['track'] ) ) {
	foreach ( $post_meta['track'] as $track ) {
		$tracks[] = unserialize( $track );
	}
	
	$has_srt_found = 0;

	foreach ( $tracks as $index => $track ) {
        $ext = pathinfo( $track['src'], PATHINFO_EXTENSION );
        if ( 'srt' == strtolower( $ext ) ) {
            $has_srt_found = 1;			
			break;
        }
    }

	if ( $has_srt_found ) {
		$settings['tracks'] = $tracks;
		$tracks = array();
	}
}

$tracks = apply_filters( 'aiovg_video_tracks', $tracks ); // Backward compatibility to 3.3.0
$tracks = apply_filters( 'aiovg_iframe_videojs_player_tracks', $tracks );

// Video Chapters
$has_chapters = isset( $_GET['chapters'] ) ? (int) $_GET['chapters'] : isset( $player_settings['controls']['chapters'] );

if ( $has_chapters && ! empty( $post_meta['chapter'] ) ) {	
	$chapters = array();

	foreach ( $post_meta['chapter'] as $chapter ) {
		$chapter = unserialize( $chapter );

		$chapters[] = array(
			'label' => sanitize_text_field( $chapter['label'] ),
			'time'  => (float) $chapter['time']
		);
	}

	if ( ! empty( $chapters ) ) {
		$settings['chapters'] = $chapters;
	}
}

// Video Attributes
$attributes = array( 
	'id'       => 'player',
	'class'    => 'vjs-fill',
	'style'    => 'width: 100%; height: 100%;',
	'controls' => '',
	'preload'  =>  esc_attr( $player_settings['preload'] )
);

$autoplay = isset( $_GET['autoplay'] ) ? (int) $_GET['autoplay'] : (int) $player_settings['autoplay'];
if ( $autoplay ) {
	$settings['player']['autoplay'] = true;
}

$loop = isset( $_GET['loop'] ) ? (int) $_GET['loop'] : (int) $player_settings['loop'];
if ( $loop ) {
	$attributes['loop'] = '';
}

$muted = isset( $_GET['muted'] ) ? (int) $_GET['muted'] : (int) $player_settings['muted'];
if ( $muted ) {
	$attributes['muted'] = '';
}

$playsinline = ! empty( $player_settings['playsinline'] ) ? 1 : 0;
if ( $playsinline ) {
	$attributes['playsinline'] = '';
}

$poster = '';
if ( isset( $_GET['poster'] ) ) {
	$poster = $_GET['poster'];
} elseif ( ! empty( $post_meta ) ) {
	$image_data = aiovg_get_image( $post_id, 'large' );
	$poster = $image_data['src'];
}

if ( ! empty( $poster) ) {
	$attributes['poster'] = aiovg_sanitize_url( aiovg_resolve_url( $poster ) );
}

if ( ! empty( $brand_settings ) && ! empty( $brand_settings['copyright_text'] ) ) {
	$attributes['controlsList']  = 'nodownload';
	$attributes['oncontextmenu'] = 'return false;';
}

$attributes = apply_filters( 'aiovg_video_attributes', $attributes );  // Backward compatibility to 3.3.0
$attributes = apply_filters( 'aiovg_iframe_videojs_player_attributes', $attributes ); 

// Player Settings
$controls = array( 
	'playpause'  => 'PlayToggle', 
	'current'    => 'CurrentTimeDisplay', 
	'progress'   => 'progressControl', 
	'duration'   => 'durationDisplay',
	'tracks'     => 'SubtitlesButton',
	'audio'      => 'AudioTrackButton',
	'speed'      => 'PlaybackRateMenuButton', 
	'quality'    => 'qualitySelector',	 
	'volume'     => 'VolumePanel', 
	'fullscreen' => 'fullscreenToggle'
);

foreach ( $controls as $index => $control ) {
	$enabled = isset( $_GET[ $index ] ) ? (int) $_GET[ $index ] : isset( $player_settings['controls'][ $index ] );

	if ( $enabled && 'tracks' == $index ) {
		$player_settings['controls']['audio'] = 1;
	}

	if ( ! $enabled ) {	
		unset( $controls[ $index ] );	
	}	
}

$settings['player']['controlBar']['children'] = array_values( $controls );
if ( empty( $settings['player']['controlBar']['children'] ) ) {
	$attributes['class'] = 'vjs-no-control-bar';
}

// YouTube
if ( isset( $sources['youtube'] ) ) {
	$settings['player']['techOrder'] = array( 'youtube' );
	$settings['player']['youtube']   = array( 
		'iv_load_policy' => 3,
		'playsinline'    => $playsinline
	);

	parse_str( $sources['youtube']['src'], $queries );

	if ( isset( $queries['start'] ) ) {
		$settings['start'] = (int) $queries['start'];
	}

	if ( isset( $queries['t'] ) ) {
		$settings['start'] = (int) $queries['t'];
	}

	if ( isset( $queries['end'] ) ) {
		$settings['end'] = (int) $queries['end'];
	}
}

// Vimeo
if ( isset( $sources['vimeo'] ) ) {
	$settings['player']['techOrder'] = array( 'vimeo2' );
	$settings['player']['vimeo2'] = array( 
		'playsinline' => $playsinline
	);

	if ( strpos( $sources['vimeo']['src'], 'player.vimeo.com' ) !== false ) {
		$video_id = aiovg_get_vimeo_id_from_url( $sources['vimeo']['src'] );
		$sources['vimeo']['src'] = 'https://vimeo.com/' . $video_id;
	}
}

// Dailymotion
if ( isset( $sources['dailymotion'] ) ) {
	$settings['player']['techOrder'] = array( 'dailymotion' );
}

// Share
$has_share = isset( $_GET['share'] ) ? (int) $_GET['share'] : isset( $player_settings['controls']['share'] );
if ( $has_share ) {
	$socialshare_settings = get_option( 'aiovg_socialshare_settings' );

	$share_url = $post_url;

	$share_title = $post_title;
	$share_title = str_replace( ' ', '%20', $share_title );
	$share_title = str_replace( '|', '%7C', $share_title );
	$share_title = str_replace( '@', '%40', $share_title );

	$share_image = isset( $attributes['poster'] ) ? $attributes['poster'] : '';

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

		$share_description = aiovg_get_excerpt( $post_id, 160, '', false ); 
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
	if ( ! empty( $share_buttons ) ) {
		$settings['share'] = 1;
	}
}

// Embed
$has_embed = isset( $_GET['embed'] ) ? (int) $_GET['embed'] : isset( $player_settings['controls']['embed'] );
if ( $has_embed ) {
	$protocol = ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? 'https://' : 'http://';
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = aiovg_remove_query_arg( array( 'uid', 'autoadvance' ), $current_url );

	$embed_code = sprintf(
		'<div style="position:relative;padding-bottom:%s;height:0;overflow:hidden;"><iframe src="%s" title="%s" width="100%%" height="100%%" style="position:absolute;width:100%%;height:100%%;top:0px;left:0px;overflow:hidden" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>',
		( isset( $_GET['ratio'] ) ? (float) $_GET['ratio'] : (float) $player_settings['ratio'] ) . '%',
		esc_url( $current_url ),
		esc_attr( $post_title )
	);

	$settings['embed'] = 1;
}

// Download
if ( isset( $sources['mp4'] ) ) {
	$has_download = isset( $player_settings['controls']['download'] );
	$download_url = '';

	if ( ! empty( $post_meta ) ) {
		if ( isset( $post_meta['download'] ) && empty( $post_meta['download'][0] ) ) {
			$has_download = 0;
		}

		$download_url = home_url( '?vdl=' . $post_id );
	}

	if ( isset( $_GET['download'] ) ) {
		$has_download = (int) $_GET['download'];
	}

	if ( $has_download ) {
		if ( empty( $download_url ) ) {
			$download_url = home_url( '?vdl=' . aiovg_get_temporary_file_download_id( $sources['mp4']['src'] ) );
		}

		$settings['download'] = array(
			'url' => esc_url( $download_url )
		);
	}
}

// Logo
if ( ! empty( $brand_settings ) ) {
	$has_logo = ! empty( $brand_settings['logo_image'] ) ? (int) $brand_settings['show_logo'] : 0;
	if ( $has_logo ) {
		$settings['logo'] = array(
			'image'    => aiovg_sanitize_url( aiovg_resolve_url( $brand_settings['logo_image'] ) ),
			'link'     => ! empty( $brand_settings['logo_link'] ) ? esc_url_raw( $brand_settings['logo_link'] ) : 'javascript:void(0)',
			'position' => sanitize_text_field( $brand_settings['logo_position'] ),
			'margin'   => ! empty( $brand_settings['logo_margin'] ) ? (int) $brand_settings['logo_margin'] : 15
		);
	}

	$has_contextmenu = ! empty( $brand_settings['copyright_text'] ) ? 1 : 0;
	if ( $has_contextmenu ) {
		$settings['contextmenu'] = array(
			'content' => apply_filters( 'aiovg_translate_strings', sanitize_text_field( $brand_settings['copyright_text'] ), 'copyright_text' )
		);
	}
}

$settings = apply_filters( 'aiovg_video_settings', $settings ); // Backward compatibility to 3.3.0
$settings = apply_filters( 'aiovg_iframe_videojs_player_settings', $settings );
?>
<!DOCTYPE html>
<html translate="no">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">

    <?php if ( $post_id > 0 ) : ?>    
        <title><?php echo wp_kses_post( $post_title ); ?></title>    
        <link rel="canonical" href="<?php echo esc_url( $post_url ); ?>" />
    <?php endif; ?>

	<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/video-js.min.css?v=7.21.1" />

	<?php if ( in_array( 'qualitySelector', $settings['player']['controlBar']['children'] ) ) : ?>
		<?php if ( isset( $sources['mp4'] ) || isset( $sources['webm'] ) || isset( $sources['ogv'] ) ) : ?>
			<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/quality-selector/quality-selector.min.css?v=1.2.5" />
		<?php endif; ?>

		<?php if ( isset( $sources['hls'] ) || isset( $sources['dash'] ) ) : ?>
			<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/videojs-quality-menu/videojs-quality-menu.min.css?v=2.0.1" />
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( isset( $settings['share'] ) || isset( $settings['embed'] ) || isset( $settings['download'] ) || isset( $settings['logo'] ) ) : ?>
		<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/overlay/videojs-overlay.min.css?v=2.1.5" />
	<?php endif; ?>

	<style type="text/css">
        html, 
        body,
		.aiovg-player .video-js {
			margin: 0; 
            padding: 0; 
            width: 100%;
            height: 100%;            
            overflow: hidden;
        }

		@font-face {
			font-family: 'aiovg-icons';
			src: url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.eot?2afx3z');
			src: url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.eot?2afx3z#iefix') format('embedded-opentype'),
				url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.ttf?2afx3z') format('truetype'),
				url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.woff?2afx3z') format('woff'),
				url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.svg?2afx3z#aiovg-icons') format('svg');
			font-weight: normal;
			font-style: normal;
			font-display: swap;
		}
		
		[class^="aiovg-icon-"],
		[class*=" aiovg-icon-"] {
			text-transform: none;
			line-height: 1;
			color: #fff;
			font-family: 'aiovg-icons' !important;
			speak: none;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			font-weight: normal;
			font-variant: normal;
			font-style: normal;
		}

		.aiovg-icon-download:before {
			content: "\e9c7";
		}

		.aiovg-icon-share:before {
			content: "\ea82";
		}

		.aiovg-icon-facebook:before {
			content: "\ea90";
		}

		.aiovg-icon-twitter:before {
			content: "\e900";
		}

		.aiovg-icon-linkedin:before {
			content: "\eaca";
		}

		.aiovg-icon-pinterest:before {
			content: "\ead1";
		}

		.aiovg-icon-tumblr:before {
			content: "\eab9";
		}

		.aiovg-icon-whatsapp:before {
			content: "\ea93";
		}

		.aiovg-player .video-js.vjs-youtube-mobile .vjs-poster {
			display: none;
		}

		.aiovg-player .video-js.vjs-ended .vjs-poster {
			display: block;
		}		

		.aiovg-player .video-js:not(.vjs-has-started) .vjs-text-track-display {
			display: none;
		}

		.aiovg-player .video-js.vjs-ended .vjs-text-track-display {
			display: none;
		}

		.aiovg-player.vjs-waiting .vjs-loading-spinner {
			display: block !important;
		}

		.aiovg-player .vjs-waiting.vjs-paused .vjs-loading-spinner {
			display: none;
		}

		.aiovg-player .video-js .vjs-big-play-button {
			top: 50%;
			left: 50%;
			transform: translate3d(-50%, -50%, 0);
			transition: all 0.2s cubic-bezier(0, 0, 0.2, 1); 			
			border: 0;
			border-radius: 40px;
			background-color: rgba(13, 13, 13, 0.6);
			background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M8.56047 5.09337C8.34001 4.9668 8.07015 4.96875 7.85254 5.10019C7.63398 5.23162 7.5 5.47113 7.5 5.73011L7.5 18.2698C7.5 18.5298 7.63398 18.7693 7.85254 18.9007C7.96372 18.9669 8.0882 19 8.21268 19C8.33241 19 8.45309 18.9688 8.56047 18.9075L18.1351 12.6377C18.3603 12.5082 18.5 12.2648 18.5 12C18.5 11.7361 18.3603 11.4917 18.1351 11.3632L8.56047 5.09337Z' fill='%23fff'%3E%3C/path%3E%3C/svg%3E");
			background-position: center;
			background-size: 64px;
			width: 80px;
			height: 80px;
		}

		.aiovg-player .video-js:hover .vjs-big-play-button,
		.aiovg-player .video-js .vjs-big-play-button:focus {
			opacity: 0.8;
			background-color: rgba(13, 13, 13, 0.6);			
		}

		.aiovg-player .video-js .vjs-big-play-button .vjs-icon-placeholder:before {
			content: "";
		}
				
		.aiovg-player.vjs-waiting .vjs-big-play-button {
			display: none !important;
		}

		.aiovg-player .vjs-waiting.vjs-paused .vjs-big-play-button,
		.aiovg-player .video-js.vjs-ended .vjs-big-play-button {
			display: block;
		}

		.aiovg-player .video-js.vjs-no-control-bar .vjs-control-bar {
			display: none;
		}		

		.aiovg-player .video-js.vjs-ended .vjs-control-bar {
			display: none;
		}

		.aiovg-player .video-js .vjs-current-time,
		.aiovg-player .video-js .vjs-duration {
			display: block;
		}

		.aiovg-player .video-js .vjs-subtitles-button .vjs-icon-placeholder:before {
			content: "\f10d";
		}

		.aiovg-player .video-js .vjs-marker {
			position: absolute;
			top: 0;
			bottom: 0;
			z-index: 1;
			background: #ffff00;
			width: 4px;	
		}

		.aiovg-player .video-js .vjs-slider-bar::before {
			z-index: 2;
		}

		.aiovg-player .video-js .vjs-progress-control .vjs-mouse-display {
			z-index: 2;
		}

		.aiovg-player .vjs-progress-control:hover .vjs-mouse-display .vjs-time-tooltip {
			display: flex;
			left: 50%;
			gap: 0.2em;
			transform: translateX(-50%);
			width: max-content;
		}

		.aiovg-player .video-js .vjs-menu li {
			text-transform: Capitalize;
		}

		.aiovg-player .video-js .vjs-menu li.vjs-selected:focus,
		.aiovg-player .video-js .vjs-menu li.vjs-selected:hover {
			background-color: #fff;
			color: #2b333f;
		}

		.aiovg-player .video-js.vjs-quality-menu .vjs-quality-menu-button-4K-flag:after, 
		.aiovg-player .video-js.vjs-quality-menu .vjs-quality-menu-button-HD-flag:after {
			background-color: #F00;
		}

		.aiovg-player .video-js .vjs-quality-selector .vjs-quality-menu-item-sub-label {			
			position: absolute;
			right: 0;
			width: 4em;
			text-align: center;
			text-transform: none;	
			font-size: 75%;
			font-weight: bold;					
		}

		.aiovg-player .video-js.vjs-4k .vjs-quality-selector:after, 
		.aiovg-player .video-js.vjs-hd .vjs-quality-selector:after {
			pointer-events: none; 
			position: absolute;
			top: 0.5em;
			right: 0;			
			border-radius: 2em;	
			background-color: #f00;
			padding: 0;
			width: 2.2em;
			height: 2.2em;
			text-align: center; 
			letter-spacing: 0.1em;
			line-height: 2.2em;
			color: inherit;								 
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			font-size: 0.7em;
			font-weight: 300;   
			content: "";			
		}

		.aiovg-player .video-js.vjs-4k .vjs-quality-selector:after {
			content: "4K";
		}

		.aiovg-player .video-js.vjs-hd .vjs-quality-selector:after {
			content: "HD";
		}	
		
		.aiovg-player .video-js .vjs-playback-rate .vjs-playback-rate-value {
			line-height: 2.6em;
			font-size: 1.2em;
		}

		.aiovg-player .video-js .vjs-share {
			margin: 5px;
		}	

		.aiovg-player .video-js .vjs-share button {
			display: flex;
			margin: 0;
			border: 0;		
			border-radius: 2px;
			box-shadow: none;
			background: rgba(0, 0, 0, 0.5);
			cursor: pointer;	
			padding: 10px;
			line-height: 1;
			color: #fff;
			font-size: 15px;
		}

		.aiovg-player .video-js .vjs-share:hover button {
			background-color: rgba(0, 0, 0, 0.7);
		}

		.aiovg-player .video-js .vjs-share .vjs-icon-share {
			line-height: 1;
		}

		.aiovg-player .video-js.vjs-has-started .vjs-share {
			display: block;			
			transition: visibility .1s,opacity .1s;
			visibility: visible;
			opacity: 1;
		}

		.aiovg-player .video-js.vjs-has-started.vjs-user-inactive.vjs-playing .vjs-share {			
			transition: visibility 1s,opacity 1s;
			visibility: visible;
			opacity: 0;
		}		

		.aiovg-player .video-js .vjs-modal-dialog-share-embed {
            background: #222 !important;
        }

		.aiovg-player .video-js .vjs-modal-dialog-share-embed .vjs-close-button {
            margin: 7px;
        }

		.aiovg-player .video-js .vjs-share-embed {
            display: flex !important;
            flex-direction: column;            
            align-items: center;
			justify-content: center;
            width: 100%;
            height: 100%;   
        }

		.aiovg-player .video-js .vjs-share-embed-content {
            width: 100%;
        }

		.aiovg-player .video-js .vjs-share-buttons {
            text-align: center;
        }

		.aiovg-player .video-js .vjs-share-button {
            display: inline-block;
			margin: 2px;
			border-radius: 2px;
            width: 40px;
			height: 40px;            
			vertical-align: middle;
			line-height: 1;
        }       

        .aiovg-player .video-js .vjs-share-button,
        .aiovg-player .video-js .vjs-share-button:hover,
        .aiovg-player .video-js .vjs-share-button:focus {
            text-decoration: none;
        } 

		.aiovg-player .video-js .vjs-share-button:hover {
            opacity: 0.9;
        }

        .aiovg-player .video-js .vjs-share-button-facebook {
            background-color: #3B5996;
        }   
		
		.aiovg-player .video-js .vjs-share-button-twitter {
            background-color: #55ACEE;
        }

        .aiovg-player .video-js .vjs-share-button-linkedin {
            background-color: #006699;
        }

        .aiovg-player .video-js .vjs-share-button-pinterest {
            background-color: #C00117;
        }

        .aiovg-player .video-js .vjs-share-button-tumblr {
            background-color: #28364B;
        } 
		
		.aiovg-player .video-js .vjs-share-button-whatsapp {
            background-color: #25d366;
        }  

        .aiovg-player .video-js .vjs-share-button span {            
			line-height: 40px;
			color: #fff;
            font-size: 24px;
        }

        .aiovg-player .video-js .vjs-embed-code {
            margin: 20px;
        }

        .aiovg-player .video-js .vjs-embed-code label {
			display: block;
			margin: 0 0 7px 0;			
            text-align: center;
			text-transform: uppercase;
			font-size: 11px;
        }

        .aiovg-player .video-js .vjs-embed-code input {            
            border: 1px solid #fff;
			border-radius: 1px;
			background: #fff;
			padding: 7px;
			width: 100%;            
			line-height: 1;
			color: #000;
        }

        .aiovg-player .video-js .vjs-embed-code input:focus {
            border: 1px solid #fff;
        }

		.aiovg-player .video-js .vjs-download {
			margin: 5px;
			cursor: pointer;
		}

		.aiovg-player .video-js .vjs-has-share.vjs-download {
			margin-top: 50px;
		}

		.aiovg-player .video-js .vjs-download a {
			display: flex;
			margin: 0;
			border-radius: 2px;
			background-color: rgba(0, 0, 0, 0.5);	
			padding: 10px;
			line-height: 1;
			color: #fff;
			font-size: 15px;
		}	
		
		.aiovg-player .video-js .vjs-download:hover a {
			background-color: rgba(0, 0, 0, 0.7);
		}

		.aiovg-player .video-js .vjs-download .aiovg-icon-download {
			color: inherit;
		}		

		.aiovg-player .video-js.vjs-has-started .vjs-download {
			display: block;			
			transition: visibility .1s,opacity .1s;
			visibility: visible;
			opacity: 1;
		}

		.aiovg-player .video-js.vjs-has-started.vjs-user-inactive.vjs-playing .vjs-download {
			transition: visibility 1s,opacity 1s;
			visibility: visible;
			opacity: 0;
		}	

		.aiovg-player .video-js .vjs-logo {
			opacity: 0;
		}

		.aiovg-player .video-js.vjs-has-started .vjs-logo {
			transition: opacity 0.1s;
			opacity: 0.6;
		}

		.aiovg-player .video-js.vjs-has-started .vjs-logo:hover {
			opacity: 1;
		}

		.aiovg-player .video-js.vjs-has-started.vjs-user-inactive.vjs-playing .vjs-logo {
			transition: opacity 1s;
			opacity: 0;
		}

		.aiovg-player .video-js .vjs-logo a {
			line-height: 1;
		}

		.aiovg-player .video-js .vjs-logo img {
			max-width: 100%;
		}

		.aiovg-player .video-js.vjs-ended .vjs-logo {
			display: none;
		}	

		.aiovg-player .video-js .vjs-error-display {
			background: #222 !important;
		}

		#aiovg-contextmenu {
            position: absolute;
            top: 0;
            left: 0;
			z-index: 9999999999; /* make sure it shows on fullscreen */
            margin: 0;
			border-radius: 2px;
            background-color: #2B333F;
  			background-color: rgba(43, 51, 63, 0.7);
            padding: 0;			
        }
        
        #aiovg-contextmenu .aiovg-contextmenu-content {
            margin: 0;
			cursor: pointer;
            padding: 8px 12px;
			white-space: nowrap;
			color: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
    </style>

	<?php do_action( 'aiovg_player_head', $settings, $attributes, $sources, $tracks ); // Backward compatibility to 3.3.0 ?>
	<?php do_action( 'aiovg_iframe_videojs_player_head', $settings, $attributes, $sources, $tracks ); ?>
</head>
<body id="body" class="aiovg-player vjs-waiting">
    <video-js <?php the_aiovg_video_attributes( $attributes ); ?>>
        <?php 
		// Video Sources
		foreach ( $sources as $source ) {
			printf( 
				'<source type="%s" src="%s" label="%s" />', 
				esc_attr( $source['type'] ), 
				esc_attr( aiovg_resolve_url( $source['src'] ) ),
				( isset( $source['label'] ) ? esc_attr( $source['label'] ) : '' ) 
			);
		}
		
		// Video Tracks
		foreach ( $tracks as $index => $track ) {
        	printf( 
				'<track kind="subtitles" src="%s" label="%s" srclang="%s" %s/>', 
				esc_attr( aiovg_resolve_url( $track['src'] ) ), 				
				esc_attr( $track['label'] ),
				esc_attr( $track['srclang'] ), 
				( 0 == $index && 1 == $settings['cc_load_policy'] ? 'default' : '' )
			);
		}
       ?>       
	</video-js>

	<?php if ( ! empty( $settings['share'] ) || ! empty( $settings['embed'] ) ) : ?>
		<div id="vjs-share-embed" class="vjs-share-embed" style="display: none;">
			<div class="vjs-share-embed-content">
				<?php if ( isset( $settings['share'] ) ) : ?>
					<!-- Share Buttons -->
					<div class="vjs-share-buttons">
						<?php
						foreach ( $share_buttons as $button ) {
							printf( 
								'<a href="%1$s" class="vjs-share-button vjs-share-button-%2$s" title="%3$s" target="_blank"><span class="%4$s" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">%3$s</span></a>',							
								esc_attr( $button['url'] ), 
								esc_attr( $button['service'] ),
								esc_attr( $button['text'] ),
								esc_attr( $button['icon'] )
							);
						}
						?>
					</div>
				<?php endif; ?>

				<?php if ( isset( $settings['embed'] ) ) : ?>
					<!-- Embed Code -->
					<div class="vjs-embed-code">
						<label for="vjs-copy-embed-code"><?php esc_html_e( 'Paste this code in your HTML page', 'all-in-one-video-gallery' ); ?></label>
						<input type="text" id="vjs-copy-embed-code" value="<?php echo htmlspecialchars( $embed_code ); ?>" readonly />
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( isset( $settings['contextmenu'] ) ) : ?>
		<div id="aiovg-contextmenu" style="display: none;">
            <div class="aiovg-contextmenu-content"><?php echo esc_html( $settings['contextmenu']['content'] ); ?></div>
        </div>
	<?php endif; ?>
    
	<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/video.min.js?v=7.21.1" type="text/javascript" defer></script>

	<?php if ( in_array( 'qualitySelector', $settings['player']['controlBar']['children'] ) ) : ?>
		<?php if ( isset( $sources['mp4'] ) || isset( $sources['webm'] ) || isset( $sources['ogv'] ) ) : ?>
			<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/quality-selector/silvermine-videojs-quality-selector.min.js?v=1.2.5" type="text/javascript" defer></script>
		<?php endif; ?>

		<?php if ( isset( $sources['hls'] ) || isset( $sources['dash'] ) ) : ?>
			<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/videojs-quality-menu/videojs-quality-menu.min.js?v=2.0.1" type="text/javascript" defer></script>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( isset( $sources['youtube'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/youtube/Youtube.min.js?v=2.6.1" type="text/javascript" defer></script>
	<?php endif; ?>

	<?php if ( isset( $settings['start'] ) || isset( $settings['end'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/offset/videojs-offset.min.js?v=2.1.3" type="text/javascript" defer></script>
	<?php endif; ?>

	<?php if ( isset( $sources['vimeo'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/vimeo/videojs-vimeo2.min.js?v=2.0.0" type="text/javascript" defer></script>
	<?php endif; ?>

	<?php if ( isset( $sources['dailymotion'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/dailymotion/videojs-dailymotion.min.js?v=2.0.0" type="text/javascript" defer></script>
	<?php endif; ?>

	<?php if ( isset( $settings['share'] ) || isset( $settings['embed'] ) || isset( $settings['download'] ) || isset( $settings['logo'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/overlay/videojs-overlay.min.js?v=2.1.5" type="text/javascript" defer></script>
	<?php endif; ?>

	<?php if ( ! empty( $settings['hotkeys'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/plugins/hotkeys/videojs.hotkeys.min.js?v=0.2.28" type="text/javascript" defer></script>
	<?php endif; ?> 

	<?php do_action( 'aiovg_player_footer', $settings, $attributes, $sources, $tracks ); // Backward compatibility to 3.3.0 ?>
	<?php do_action( 'aiovg_iframe_videojs_player_footer', $settings, $attributes, $sources, $tracks ); ?>

    <script type="text/javascript">
		'use strict';			
			
		// Vars
		var settings = <?php echo json_encode( $settings ); ?>;		
		
		var body = document.getElementById( 'body' );
		var playButton = null;
		var overlays = [];
		var hasVideoStarted = false;				

		/**
		 * Called when the big play button in the player is clicked.
		 */
		function onPlayClicked() {
			if ( ! hasVideoStarted ) {
				body.className += ' vjs-waiting';
			}

			playButton.removeEventListener( 'click', onPlayClicked );
		}

		/**
		 * Add SRT Text Track.
		 */
		function addSrtTextTrack( player, track, mode ) {
			var xmlhttp;

			if ( window.XMLHttpRequest ) {
				xmlhttp = new XMLHttpRequest();
			} else {
				xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
			}
			
			xmlhttp.onreadystatechange = function() {				
				if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 && xmlhttp.responseText ) {					
					var text = srtToWebVTT( xmlhttp.responseText );

					if ( text ) {
						var blob = new Blob([ text ], { type : 'text/vtt' });
						var src  = URL.createObjectURL( blob );

						var obj = {
							src: src,
							srclang: track.srclang,
							label: track.label,
							kind: 'subtitles'
						};

						if ( mode ) {
							obj.mode = mode;
						}

						player.addRemoteTextTrack( obj, true );
					} 
				}					
			};	

			xmlhttp.open( 'GET', track.src, true );
			xmlhttp.send();							
		}

		/**
		 * Convert SRT to WebVTT.
		 */
		function srtToWebVTT( data ) {
          	// Remove dos newlines.
          	var srt = data.replace( /\r+/g, '' );

          	// Trim white space start and end.
          	srt = srt.replace( /^\s+|\s+$/g, '' );

          	// Get cues.
          	var cuelist = srt.split( '\n\n' );
          	var result  = '';

          	if ( cuelist.length > 0 ) {
            	result += "WEBVTT\n\n";

            	for ( var i = 0; i < cuelist.length; i++ ) {
            		result += convertSrtCue( cuelist[ i ] );
            	}
          	}

          	return result;
        }

		function convertSrtCue( caption ) {
          	// Remove all html tags for security reasons.
          	// srt = srt.replace( /<[a-zA-Z\/][^>]*>/g, '' );

          	var cue = '';
          	var s   = caption.split( /\n/ );

          	// Concatenate muilt-line string separated in array into one.
          	while ( s.length > 3 ) {
              	for ( var i = 3; i < s.length; i++ ) {
                  	s[2] += "\n" + s[ i ];
              	}

              	s.splice( 3, s.length - 3 );
          	}

          	var line = 0;

          	// Detect identifier.
          	if ( ! s[0].match( /\d+:\d+:\d+/ ) && s[1].match( /\d+:\d+:\d+/ ) ) {
            	cue  += s[0].match( /\w+/ ) + "\n";
            	line += 1;
          	}

          	// Get time strings.
          	if ( s[ line ].match( /\d+:\d+:\d+/ ) ) {
            	// Convert time string.
            	var m = s[1].match( /(\d+):(\d+):(\d+)(?:,(\d+))?\s*--?>\s*(\d+):(\d+):(\d+)(?:,(\d+))?/ );

            	if ( m ) {
              		cue  += m[1] + ":" + m[2] + ":" + m[3] + "." + m[4] + " --> " + m[5] + ":" + m[6] + ":" + m[7] + "." + m[8] + "\n";
              		line += 1;
            	} else {
              		// Unrecognized timestring.
              		return '';
            	}
          	} else {
            	// File format error or comment lines.
            	return '';
          	}

          	// Get cue text.
          	if ( s[ line ] ) {
            	cue += s[ line ] + "\n\n";
          	}

          	return cue;
        }	
		
		/**
		 * Helper functions for chapters.
		 */
		function addMarkers( player, markers ) {
			var total   = player.duration();
			var seekBar = document.querySelector( '.vjs-progress-control .vjs-progress-holder' );

			if ( seekBar !== null ) {
				for ( var i = 0; i < markers.length; i++ ) {
					var elem = document.createElement( 'div' );
					elem.className = 'vjs-marker';
					elem.style.left = ( markers[ i ].time / total ) * 100 + '%';

					seekBar.appendChild( elem );
				}
			}
		}

		function formatedTimeToSeconds( time ) {
			var timeSplit = time.split( ':' );
			var seconds   = +timeSplit.pop();
	
			return timeSplit.reduce(( acc, curr, i, arr ) => {
				if ( arr.length === 2 && i === 1 ) return acc + +curr * 60 ** 2;
				else return acc + +curr * 60;
			}, seconds);
		}

		function timeEl( time ) {
			return videojs.dom.createEl( 'span', undefined, undefined, '(' + time + ')' );
		}

		function labelEl( label ) {
			return videojs.dom.createEl( 'strong', undefined, undefined, label );
		}

		/**
		 * Update video views count.
		 */
		function updateViewsCount() {
			var xmlhttp;

			if ( window.XMLHttpRequest ) {
				xmlhttp = new XMLHttpRequest();
			} else {
				xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
			}
			
			xmlhttp.onreadystatechange = function() {				
				if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 && xmlhttp.responseText ) {					
					/** console.log( xmlhttp.responseText ); */				
				}					
			};

			xmlhttp.open( 'GET', '<?php echo admin_url( 'admin-ajax.php' ); ?>?action=aiovg_update_views_count&post_id=<?php echo $post_id; ?>&security=<?php echo wp_create_nonce( 'aiovg_ajax_nonce' ); ?>', true );
			xmlhttp.send();							
		}		

		/**
		 * Merge attributes.
		 */
		function combineAttributes( attributes ) {
			var str = '';

			for ( var key in attributes ) {
				str += ( key + '="' + attributes[ key ] + '" ' );
			}

			return str;
		}

		/**
		 * Init player.
		 */		
		function initPlayer() {
			settings.html5 = {
				vhs: {
					overrideNative: ! videojs.browser.IS_ANY_SAFARI,
				}
			};

			var player = videojs( 'player', settings.player );			

			// Maintained for backward compatibility.
			if ( typeof window['onPlayerInitialized'] === 'function' ) {
				window.onPlayerInitialized( player );
			}

			// Dispatch an event.
			var evt = document.createEvent( 'CustomEvent' );
			evt.initCustomEvent( 'player.init', false, false, { player: player, settings: settings } );
			window.dispatchEvent( evt );

			// On player ready.
			player.ready(function() {
				body.className = 'aiovg-player';
				
				playButton = document.querySelector( '.vjs-big-play-button' );
				if ( playButton !== null ) {
					playButton.addEventListener( 'click', onPlayClicked );
				}
			});

			// On metadata loaded.
			player.one( 'loadedmetadata', function() {
				// Standard quality selector.
				var qualitySelector = document.querySelector( '.vjs-quality-selector' );

				if ( qualitySelector !== null ) {
					var nodes = qualitySelector.querySelectorAll( '.vjs-menu-item' );

					for ( var i = 0; i < nodes.length; i++ ) {
						var node = nodes[ i ];

						var textNode   = node.querySelector( '.vjs-menu-item-text' );
						var resolution = textNode.innerHTML.replace( /\D/g, '' );

						if ( resolution >= 2160 ) {
							node.innerHTML += '<span class="vjs-quality-menu-item-sub-label">4K</span>';
						} else if ( resolution >= 720 ) {
							node.innerHTML += '<span class="vjs-quality-menu-item-sub-label">HD</span>';
						}
					}
				}

				// Add support for SRT.
				if ( settings.hasOwnProperty( 'tracks' ) ) {
					for ( var i = 0, max = settings.tracks.length; i < max; i++ ) {
						var track = settings.tracks[ i ];

						var mode = '';
						if ( i == 0 && settings.cc_load_policy == 1 ) {
							mode = 'showing';
						}

						if ( /srt/.test( track.src.toLowerCase() ) ) {
							addSrtTextTrack( player, track, mode );
						} else {
							var obj = {
								kind: 'subtitles',
								src: track.src,								
								label: track.label,
								srclang: track.srclang
							};

							if ( mode ) {
								obj.mode = mode;
							}

							player.addRemoteTextTrack( obj, true ); 
						}					               
					}  
				}  
				
				// Chapters
				if ( settings.hasOwnProperty( 'chapters' ) ) {
					addMarkers( player, settings.chapters );
				}
			});

			// Chapters
			if ( settings.hasOwnProperty( 'chapters' ) ) {
				try {
					player.getDescendant([
						'controlBar',
						'progressControl',
						'seekBar',
						'mouseTimeDisplay',
						'timeTooltip',
					]).update = function( seekBarRect, seekBarPoint, time ) {
						var markers = settings.chapters;
						var markerIndex = markers.findIndex(({ time: markerTime }) => markerTime == formatedTimeToSeconds( time ));
				
						if ( markerIndex > -1 ) {
							var label = markers[ markerIndex ].label;
					
							videojs.dom.emptyEl( this.el() );
							videojs.dom.appendContent( this.el(), [labelEl( label ), timeEl( time )] );
					
							return false;
						}
				
						this.write( time );
					};
				} catch ( error ) { 
					/** console.log( error ); */	
				}
			}

			// Fired the first time a video is played.
			player.one( 'play', function() {
				hasVideoStarted = true;
				body.className = 'aiovg-player';

				if ( settings.post_type == 'aiovg_videos' ) {
					updateViewsCount();
				}
			});

			player.on( 'playing', function() {
				player.trigger( 'controlsshown' );
			});

			var hasMessageSent = false;
			
			player.on( 'ended', function() {
				player.trigger( 'controlshidden' );

				// Autoplay next video.
				if ( settings.hasOwnProperty( 'autoadvance' ) ) {
					if ( ! hasMessageSent ) {
						hasMessageSent = true;

						window.parent.postMessage({ 				
							message: 'aiovg-video-ended',			
							id: settings.uid
						}, '*'); 
					}
				}
			});

			// Standard quality selector.
			player.on( 'qualitySelected', function( event, source ) {
				var resolution = source.label.replace( /\D/g, '' );

				player.removeClass( 'vjs-4k' );
				player.removeClass( 'vjs-hd' );

				if ( resolution >= 2160 ) {
					player.addClass( 'vjs-4k' );
				} else if ( resolution >= 720 ) {
					player.addClass( 'vjs-hd' );
				}
			});

			// HLS quality selector.
			var src = player.src();

			if ( /.m3u8/.test( src ) || /.mpd/.test( src ) ) {
				if ( settings.player.controlBar.children.indexOf( 'qualitySelector' ) !== -1 ) {
					player.qualityMenu();
				}
			}

			// Offset
			var offset = {};

			if ( settings.hasOwnProperty( 'start' ) ) {
				offset.start = settings.start;
			}

			if ( settings.hasOwnProperty( 'end' ) ) {
				offset.end = settings.end;
			}
			
			if ( Object.keys( offset ).length > 1 ) {
				offset.restart_beginning = false;
				player.offset( offset );
			}			

			// Share / Embed.
			if ( settings.hasOwnProperty( 'share' ) || settings.hasOwnProperty( 'embed' ) ) {
				overlays.push({
					content: '<button type="button" id="vjs-share-embed-button" class="vjs-share-embed-button" title="Share"><span class="vjs-icon-share" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">Share</span></button>',
					class: 'vjs-share',
					align: 'top-right',
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});					
			}

			// Download
			if ( settings.hasOwnProperty( 'download' ) ) {
				var className = 'vjs-download';

				if ( settings.hasOwnProperty( 'share' ) || settings.hasOwnProperty( 'embed' ) ) {
					className += ' vjs-has-share';
				}

				overlays.push({
					content: '<a href="' + settings.download.url + '" id="vjs-download-button" class="vjs-download-button" title="Download" target="_blank" style="text-decoration:none;"><span class="aiovg-icon-download" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">Download</span></a>',
					class: className,
					align: 'top-right',
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});
			}

			// Logo
			if ( settings.hasOwnProperty( 'logo' ) ) {
				var attributes = [];
				attributes['src'] = settings.logo.image;

				if ( settings.logo.margin ) {
					settings.logo.margin = settings.logo.margin - 5;
				}

				var align = 'bottom-left';
				attributes['style'] = 'margin: ' + settings.logo.margin + 'px;';

				switch ( settings.logo.position ) {
					case 'topleft':
						align = 'top-left';
						attributes['style'] = 'margin: ' + settings.logo.margin + 'px;';
						break;

					case 'topright':
						align = 'top-right';
						attributes['style'] = 'margin: ' + settings.logo.margin + 'px;';
						break;		

					case 'bottomright':
						align = 'bottom-right';
						attributes['style'] = 'margin: ' + settings.logo.margin + 'px;';
						break;				
				}

				var logo = '<img ' +  combineAttributes( attributes ) + ' alt="" />';
				if ( settings.logo.link ) {
					logo = '<a href="' + settings.logo.link + '" style="text-decoration:none;">' + logo + '<span class="vjs-control-text" aria-live="polite">Logo</span></a>';
				}

				overlays.push({
					content: logo,
					class: 'vjs-logo',
					align: align,
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});
			}

			// Overlay
			if ( overlays.length > 0 ) {
				player.overlay({
					content: '',
					overlays: overlays
				});

				if ( settings.hasOwnProperty( 'share' ) || settings.hasOwnProperty( 'embed' ) ) {
					var options = {};
					options.content = document.getElementById( 'vjs-share-embed' );
					options.temporary = false;

					var ModalDialog = videojs.getComponent( 'ModalDialog' );
					var modal = new ModalDialog( player, options );
					modal.addClass( 'vjs-modal-dialog-share-embed' );

					player.addChild( modal );

					var wasPlaying = true;
					document.getElementById( 'vjs-share-embed-button' ).addEventListener( 'click', function() {
						wasPlaying = ! player.paused;
						modal.open();						
					});

					modal.on( 'modalclose', function() {
						if ( wasPlaying ) {
							player.play();
						}						
					});
				}

				if ( settings.hasOwnProperty( 'embed' ) ) {
					document.getElementById( 'vjs-copy-embed-code' ).addEventListener( 'focus', function() {
						this.select();	
						document.execCommand( 'copy' );					
					});
				}
			}

			// Keyboard hotkeys.
			if ( settings.hotkeys ) {
				player.hotkeys();
			}

			// Custom contextmenu.
			if ( settings.hasOwnProperty( 'contextmenu' ) ) {
				var contextmenu = document.getElementById( 'aiovg-contextmenu' );
				var timeoutHandler = '';
				
				document.addEventListener( 'contextmenu', function( event ) {						
					if ( event.keyCode == 3 || event.which == 3 ) {
						event.preventDefault();
						event.stopPropagation();
						
						var width = contextmenu.offsetWidth,
							height = contextmenu.offsetHeight,
							x = event.pageX,
							y = event.pageY,
							doc = document.documentElement,
							scrollLeft = ( window.pageXOffset || doc.scrollLeft ) - ( doc.clientLeft || 0 ),
							scrollTop = ( window.pageYOffset || doc.scrollTop ) - ( doc.clientTop || 0 ),
							left = x + width > window.innerWidth + scrollLeft ? x - width : x,
							top = y + height > window.innerHeight + scrollTop ? y - height : y;
				
						contextmenu.style.display = '';
						contextmenu.style.left = left + 'px';
						contextmenu.style.top = top + 'px';
						
						clearTimeout( timeoutHandler );

						timeoutHandler = setTimeout(function() {
							contextmenu.style.display = 'none';
						}, 1500);				
					}														 
				});
				
				if ( settings.hasOwnProperty( 'logo' ) && settings.logo.link ) {
					contextmenu.addEventListener( 'click', function() {
						top.window.location.href = settings.logo.link;
					});
				}
				
				document.addEventListener( 'click', function() {
					contextmenu.style.display = 'none';								 
				});	
			}

			// Custom error.
			videojs.hook( 'beforeerror', function( player, error ) {
				// Prevent current error from being cleared out.
				if ( error == null ) {
					return player.error();
				}

				// But allow changing to a new error.
				if ( error.code == 2 || error.code == 4 ) {
					var src = player.src();

					if ( /.m3u8/.test( src ) || /.mpd/.test( src ) ) {
						return {
							code: error.code,
							message: settings.i18n.stream_not_found
						}
					}
				}
				
				return error;
			});
		}

		document.addEventListener( 'DOMContentLoaded', function() {
			initPlayer();
		});
    </script>	
</body>
</html>