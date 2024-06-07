<?php

/**
 * Video Player: Iframe Embed Code.
 *
 * @link     https://plugins360.com
 * @since    1.6.0
 *
 * @package All_In_One_Video_Gallery
 */
 
$player_html = '';
$type = '';
$embed_url = '';

if ( ! empty( $post_meta ) ) {
    $type = $post_meta['type'][0];

    if ( 'rumble' == $type ) {
        $embed_url = $post_meta['rumble'][0];
    }

    if ( 'facebook' == $type ) {
        $embed_url = $post_meta['facebook'][0];
    }

    if ( 'embedcode' == $type ) { 
        $iframe_src = aiovg_extract_iframe_src( $post_meta['embedcode'][0] );
		if ( $iframe_src ) { 
            $embed_url = $iframe_src; 
        } else {
            $player_html = $post_meta['embedcode'][0];
        }
	}	
}

if ( isset( $_GET['rumble'] ) ) {
    $type = 'rumble';
    $embed_url = urldecode( $_GET['rumble'] );
}

if ( isset( $_GET['facebook'] ) ) {
    $type = 'facebook';
    $embed_url = urldecode( $_GET['facebook'] );
}

if ( ! empty( $embed_url ) ) {
    // Rumble
    if ( 'rumble' == $type ) {
        $oembed = aiovg_get_rumble_oembed_data( $embed_url );

        $iframe_src = aiovg_extract_iframe_src( $oembed['html'] );
		if ( $iframe_src ) { 
            $embed_url = add_query_arg( 'rel', 0, $iframe_src );	
                    
            $autoplay = isset( $_GET[ 'autoplay' ] ) ? (int) $_GET['autoplay'] : (int) $player_settings['autoplay'];
            if ( ! empty( $autoplay ) ) {
                $embed_url = add_query_arg( 'autoplay', 2, $embed_url );	
            }
        }
    }

    // Facebook
    if ( 'facebook' == $type ) {
        $embed_url = 'https://www.facebook.com/plugins/video.php?href=' . urlencode( $embed_url ) . '&width=560&height=315&show_text=false&appId';
    
        $autoplay = isset( $_GET[ 'autoplay' ] ) ? $_GET['autoplay'] : $player_settings['autoplay'];
        $embed_url = add_query_arg( 'autoplay', (int) $autoplay, $embed_url );

        $loop = isset( $_GET[ 'loop' ] ) ? $_GET['loop'] : $player_settings['loop'];
        $embed_url = add_query_arg( 'loop', (int) $loop, $embed_url );

        $muted = isset( $_GET[ 'muted' ] ) ? $_GET['muted'] : $player_settings['muted'];
        $embed_url = add_query_arg( 'muted', (int) $muted, $embed_url );
    }
}

if ( ! in_array( $type, array( 'embedcode', 'rumble', 'facebook' ) ) ) {
    foreach ( $embedded_sources as $source ) {
        $is_src_found = 0;

        if ( ! empty( $post_meta ) ) {
			if ( $source == $type ) {
                $is_src_found = 1;
                $embed_url = $post_meta[ $type ][0];
			}			
		} elseif ( isset( $_GET[ $source ] ) ) {
            $is_src_found = 1;
            $embed_url = urldecode( $_GET[ $source ] );
        }
        
        if ( $is_src_found ) {            
            switch ( $source ) {
                case 'youtube':
                    parse_str( $embed_url, $queries );
                    
                    $embed_url = 'https://www.youtube.com/embed/' . aiovg_get_youtube_id_from_url( $embed_url ) . '?showinfo=0&rel=0&iv_load_policy=3';	                    
                    
                    if ( isset( $queries['start'] ) ) {
                        $embed_url = add_query_arg( 'start', (int) $queries['start'], $embed_url );
                    }

                    if ( isset( $queries['t'] ) ) {
                        $embed_url = add_query_arg( 'start', (int) $queries['t'], $embed_url );
                    }
                    
                    if ( isset( $queries['end'] ) ) {
                        $embed_url = add_query_arg( 'end', (int) $queries['end'], $embed_url );
                    }

                    $embed_url = add_query_arg( 'cc_load_policy', (int) $player_settings['cc_load_policy'], $embed_url );

                    $playsinline = ! empty( $player_settings['playsinline'] ) ? 1 : 0;
                    $embed_url = add_query_arg( 'playsinline', $playsinline, $embed_url );
                    break;
                case 'vimeo':
                    $oembed = aiovg_get_vimeo_oembed_data( $embed_url );
                    $embed_url = 'https://player.vimeo.com/video/' . $oembed['video_id'] . '?title=0&byline=0&portrait=0';

                    if ( ! empty( $oembed['html'] ) ) {
                        $iframe_src = aiovg_extract_iframe_src( $oembed['html'] );
		                if ( $iframe_src ) {
                            $parsed_url = parse_url( $iframe_src, PHP_URL_QUERY );
				            parse_str( $parsed_url, $queries );

                            if ( isset( $queries['h'] ) ) {
                                $embed_url = add_query_arg( 'h', $queries['h'], $embed_url );
                            }

                            if ( isset( $queries['app_id'] ) ) {
                                $embed_url = add_query_arg( 'app_id', $queries['app_id'], $embed_url );
                            }
                        }
                    }

                    $playsinline = ! empty( $player_settings['playsinline'] ) ? 1 : 0;
                    $embed_url = add_query_arg( 'playsinline', $playsinline, $embed_url );
                    break;				
                case 'dailymotion':
                    $embed_url = 'https://www.dailymotion.com/embed/video/' . aiovg_get_dailymotion_id_from_url( $embed_url ) . '?queue-autoplay-next=0&queue-enable=0&sharing-enable=0&ui-logo=0&ui-start-screen-info=0';
                    break;
            }
    
            $features = array( 'playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen' );
            $controls = array();
            
            foreach ( $features as $feature ) {	
                if ( isset( $_GET[ $feature ] ) ) {	
                    if ( 1 == (int) $_GET[ $feature ] ) {
                        $controls[] = $feature;
                    }		
                } else {	
                    if ( isset( $player_settings['controls'][ $feature ] ) ) {
                        $controls[] = $feature;
                    }		
                }	
            }
    
            if ( empty( $controls ) ) {
                $embed_url = add_query_arg( 'controls', 0, $embed_url );
            } else {
                if ( ! in_array( 'fullscreen', $controls ) ) {
                    $embed_url = add_query_arg( 'fs', 0, $embed_url );
                }
            }
    
            $autoplay = isset( $_GET[ 'autoplay' ] ) ? $_GET['autoplay'] : $player_settings['autoplay'];
            $embed_url = add_query_arg( 'autoplay', (int) $autoplay, $embed_url );
    
            $loop = isset( $_GET[ 'loop' ] ) ? $_GET['loop'] : $player_settings['loop'];
            $embed_url = add_query_arg( 'loop', (int) $loop, $embed_url );

            $muted = isset( $_GET[ 'muted' ] ) ? $_GET['muted'] : $player_settings['muted'];
            $embed_url = add_query_arg( 'muted', (int) $muted, $embed_url );
        }
    }
}

if ( ! empty( $embed_url ) ) {
    $player_html = sprintf(
        '<iframe src="%s" title="%s" width="560" height="315" frameborder="0" scrolling="no" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
        esc_url_raw( $embed_url ),
        esc_attr( $post_title )
    );
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
          
    <?php if ( $post_id > 0 ) : ?>    
        <title><?php echo wp_kses_post( $post_title ); ?></title>    
        <link rel="canonical" href="<?php echo esc_url( $post_url ); ?>" />
    <?php endif; ?>
    
	<style type="text/css">
        html, 
        body, 
        iframe {            
            margin: 0 !important; 
            padding: 0 !important; 
            width: 100% !important;
            height: 100% !important;
            overflow: hidden;
        }
    </style>

    <?php do_action( 'aiovg_player_iframe_head' ); ?>
</head>
<body>    
    <?php echo $player_html; ?>

    <?php if ( 'aiovg_videos' == $post_type ) : ?>
        <script type="text/javascript">
            /**
             * Update video views count.
             */
            function ajaxSubmit() {
                var xmlhttp;

                if ( window.XMLHttpRequest ) {
                    xmlhttp = new XMLHttpRequest();
                } else {
                    xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
                }
                
                xmlhttp.onreadystatechange = function() {				
                    if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 && xmlhttp.responseText ) {					
                        // console.log( xmlhttp.responseText );						
                    }					
                }

                xmlhttp.open( 'GET', '<?php echo admin_url( 'admin-ajax.php' ); ?>?action=aiovg_update_views_count&post_id=<?php echo $post_id; ?>&security=<?php echo wp_create_nonce( 'aiovg_ajax_nonce' ); ?>', true );
                xmlhttp.send();							
            }

            ajaxSubmit();		
        </script>
    <?php endif; ?>

    <?php do_action( 'aiovg_player_iframe_footer' ); ?>
</body>
</html>