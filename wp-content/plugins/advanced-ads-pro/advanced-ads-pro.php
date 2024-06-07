<?php
/**
 * Advanced Ads Pro
 *
 * @wordpress-plugin
 * Plugin Name:         Advanced Ads Pro
 * Plugin URI:          https://wpadvancedads.com/add-ons/advanced-ads-pro/
 * Description:         Advanced features to boost your ad revenue.
 * Version:             2.21.2
 * Author:              Advanced Ads GmbH
 * Author URI:          https://wpadvancedads.com
 * Text Domain:         advanced-ads-pro
 * Domain Path:         /languages
 */

if ( defined( 'AAP_SLUG' ) ) {
	return;
}

$aap_licenses = [];
$aap_keys = [ 'pro', 'geo', 'layer', 'responsive', 'sticky', 'gam', 'tracking', 'selling' ];
foreach ( $aap_keys as $aap_key ) {
    $aap_licenses[ $aap_key ] = '**********';
}
update_option( 'advanced-ads-licenses', $aap_licenses );

$aap_slugs = [ 'advanced-ads-pro', 'advanced-ads-geo', 'advanced-ads-layer', 'advanced-ads-responsive', 'advanced-ads-sticky', 'advanced-ads-gam', 'advanced-ads-tracking', 'advanced-ads-selling' ];
foreach ( $aap_slugs as $aap_slug ) {
    update_option( $aap_slug . '-license-expires', 'lifetime' );
    update_option( $aap_slug . '-license-status',  'valid' );
}

define( 'AAP_SLUG', 'advanced-ads-pro' );
define( 'AAP_PATH', __DIR__ );
define( 'AAP_BASE', plugin_basename( __FILE__ ) ); // Plugin base as used by WordPress to identify it.
define( 'AAP_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'AAP_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'AAP_BASE_DIR', dirname( AAP_BASE ) ); // Directory of the plugin without any paths.
define( 'AAP_VERSION', '2.21.2' );
define( 'AAP_PLUGIN_NAME', 'Advanced Ads Pro' );

require_once AAP_BASE_PATH . 'lib/autoload.php';

// Autoload and activate.
Advanced_Ads_Pro::get_instance();

register_activation_hook( __FILE__, [ 'Advanced_Ads_Pro', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'Advanced_Ads_Pro', 'deactivate' ] );
add_action( 'wpmu_new_blog', [ 'Advanced_Ads_Pro', 'activate_new_site' ] );
