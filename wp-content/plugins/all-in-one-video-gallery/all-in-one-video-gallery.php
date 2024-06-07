<?php

/**
 * The plugin bootstrap file.
 *
 * @link            https://plugins360.com
 * @since           1.0.0
 * @package         All_In_One_Video_Gallery
 *
 * @wordpress-plugin
 * Plugin Name:     All-in-One Video Gallery
 * Plugin URI:      https://plugins360.com/all-in-one-video-gallery/
 * Description:     An ultimate video player and video gallery plugin – no coding required. Suitable for YouTubers, Video Bloggers, Course Creators, Podcasters, Sales & Marketing Professionals, and anyone using video on a website.
 * Version:         3.7.1
 * Author:          Team Plugins360
 * Author URI:      https://plugins360.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     all-in-one-video-gallery
 * Domain Path:     /languages
 * 
 */
// Exit if accessed directly
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( function_exists( 'aiovg_fs' ) ) {
    aiovg_fs()->set_basename( false, __FILE__ );
    return;
}
if ( !function_exists( 'aiovg_fs' ) ) {
    // Create a helper function for easy SDK access
    function aiovg_fs() {
        global $aiovg_fs;
        if ( !isset( $aiovg_fs ) ) {
            // Activate multisite network integration
            if ( !defined( 'WP_FS__PRODUCT_3213_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_3213_MULTISITE', true );
            }
            // Include Freemius SDK
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $aiovg_fs = fs_dynamic_init( array(
                'id'             => '3213',
                'slug'           => 'all-in-one-video-gallery',
                'type'           => 'plugin',
                'public_key'     => 'pk_e1bed9a9a8957abe8947bb2619ab7',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                    'days'               => 7,
                    'is_require_payment' => false,
                ),
                'menu'           => array(
                    'slug'       => 'all-in-one-video-gallery',
                    'first-path' => 'admin.php?page=all-in-one-video-gallery',
                    'support'    => false,
                ),
                'is_live'        => true,
            ) );
        }
        return $aiovg_fs;
    }

    // Init Freemius
    aiovg_fs();
    // Signal that SDK was initiated
    do_action( 'aiovg_fs_loaded' );
}
// The current version of the plugin
if ( !defined( 'AIOVG_PLUGIN_VERSION' ) ) {
    define( 'AIOVG_PLUGIN_VERSION', '3.7.1' );
}
// The unique identifier of the plugin
if ( !defined( 'AIOVG_PLUGIN_SLUG' ) ) {
    define( 'AIOVG_PLUGIN_SLUG', 'all-in-one-video-gallery' );
}
// Path to the plugin directory
if ( !defined( 'AIOVG_PLUGIN_DIR' ) ) {
    define( 'AIOVG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
// URL of the plugin
if ( !defined( 'AIOVG_PLUGIN_URL' ) ) {
    define( 'AIOVG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
// The plugin file name
if ( !defined( 'AIOVG_PLUGIN_FILE_NAME' ) ) {
    define( 'AIOVG_PLUGIN_FILE_NAME', plugin_basename( __FILE__ ) );
}
// The global plugin variable
$aiovg = array();
if ( !function_exists( 'aiovg_activate' ) ) {
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/activator.php
     */
    function aiovg_activate() {
        require_once AIOVG_PLUGIN_DIR . 'includes/activator.php';
        AIOVG_Activator::activate();
    }

    register_activation_hook( __FILE__, 'aiovg_activate' );
}
if ( !function_exists( 'aiovg_deactivate' ) ) {
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/deactivator.php
     */
    function aiovg_deactivate() {
        require_once AIOVG_PLUGIN_DIR . 'includes/deactivator.php';
        AIOVG_Deactivator::deactivate();
    }

    register_deactivation_hook( __FILE__, 'aiovg_deactivate' );
}
if ( !function_exists( 'aiovg_run' ) ) {
    /**
     * Begins execution of the plugin.
     *
     * @since 1.0.0
     */
    function aiovg_run() {
        require AIOVG_PLUGIN_DIR . 'includes/init.php';
        $plugin = new AIOVG_Init();
        $plugin->run();
    }

    aiovg_run();
}
if ( !function_exists( 'aiovg_uninstall' ) ) {
    /**
     * The code that runs during plugin uninstallation.
     * This action is documented in includes/uninstall.php
     */
    function aiovg_uninstall() {
        require_once AIOVG_PLUGIN_DIR . 'includes/uninstall.php';
        AIOVG_Uninstall::uninstall();
    }

    aiovg_fs()->add_action( 'after_uninstall', 'aiovg_uninstall' );
}