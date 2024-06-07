<?php

$rest_api = new \Advanced_Ads_Pro\Rest_Api\Rest_Api( Advanced_Ads_Pro::get_instance() );

if ( is_admin() ) {
	$rest_admin = new \Advanced_Ads_Pro\Rest_Api\Admin_UI( $rest_api );
	add_action( 'advanced-ads-settings-init', [ $rest_admin, 'settings_init' ] );
}

if ( ! $rest_api->is_enabled() ) {
	return;
}

add_action( 'rest_api_init', [ $rest_api, 'register_rest_routes' ] );
