<?php

/**
 * Video Player.
 *
 * @link     https://plugins360.com
 * @since    1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
 
$player_settings  = get_option( 'aiovg_player_settings' );
$privacy_settings = get_option( 'aiovg_privacy_settings' );
$brand_settings   = get_option( 'aiovg_brand_settings', array() );

$post_id    = (int) get_query_var( 'aiovg_video', 0 );
$post_type  = 'page';
$post_title = '';
$post_url   = '';
$post_meta  = array();

$embedded_sources = array( 'youtube', 'vimeo', 'dailymotion' );
$player_template  = ( 'vidstack' == $player_settings['player'] ) ? 'vidstack' : 'videojs';

if ( $post_id > 0 ) {
	$post_type  = get_post_type( $post_id );
	$post_title = get_the_title( $post_id );
    $post_url   = get_permalink( $post_id );
		
	if ( 'aiovg_videos' == $post_type ) {
		$post_meta = get_post_meta( $post_id );

		if ( 'rumble' == $post_meta['type'][0] ) {
			$player_template = 'iframe';			
		}

		if ( 'facebook' == $post_meta['type'][0] ) {
			$player_template = 'iframe';			
		}

		if ( 'embedcode' == $post_meta['type'][0] ) {
			$player_template = 'iframe';			
		}
	}
}

if ( isset( $_GET['rumble'] ) ) {
	$player_template = 'iframe';
}	

if ( isset( $_GET['facebook'] ) ) {
	$player_template = 'iframe';
}	

foreach ( $embedded_sources as $source ) {
	$use_native_controls = isset( $player_settings['use_native_controls'][ $source ] );

	if ( 'vidstack' == $player_template && 'dailymotion' == $source ) {
		$use_native_controls = true;
	}

	$use_native_controls = apply_filters( 'aiovg_use_native_controls', $use_native_controls, $source );

	if ( $use_native_controls ) {
		if ( ! empty( $post_meta ) ) {
			if ( $source == $post_meta['type'][0] ) {
				$player_template = 'iframe';
			}			
		} elseif ( isset( $_GET[ $source ] ) ) {
			$player_template = 'iframe';
		}		
	}
}

if ( ! isset( $_COOKIE['aiovg_gdpr_consent'] ) && ! empty( $privacy_settings['show_consent'] ) && ! empty( $privacy_settings['consent_message'] ) && ! empty( $privacy_settings['consent_button_label'] ) ) {		
	if ( 'iframe' == $player_template ) {
		$player_template = 'gdpr';
	} else {
		foreach ( $embedded_sources as $source ) {
			if ( ! empty( $post_meta ) ) {
				if ( $source == $post_meta['type'][0] ) {
					$player_template = 'gdpr';
				}			
			} elseif ( isset( $_GET[ $source ] ) ) {
				$player_template = 'gdpr';
			}		
		}
	}
}

include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/player-{$player_template}.php" );