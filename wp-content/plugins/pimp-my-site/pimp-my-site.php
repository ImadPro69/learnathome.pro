<?php
/*
 * Plugin Name: Pimp my Site
 * Description: Add fun effects to your site for every special occasion: christmas, thanksgiving, halloween, birthday, and much more!
 * Author:      Themosaurus
 * Author URI:  https://themosaurus.com/
 * Version:     1.2.2
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pimp-my-site
 * Domain Path: /languages
 *
 * @package Pimp_My_Site
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PIMP_MY_SITE_VERSION',         '1.2.2' );
define( 'PIMP_MY_SITE_PLUGIN_FILE',     __FILE__ );
define( 'PIMP_MY_SITE_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'PIMP_MY_SITE_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );
define( 'PIMP_MY_SITE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once 'inc/class-pimp-my-site.php';
require_once 'admin/class-pimp-my-site-admin.php';

global $pimp_my_site;
$pimp_my_site = new Pimp_My_Site();

global $pimp_my_site_admin;
$pimp_my_site_admin = new Pimp_My_Site_Admin();

do_action( 'pimp_my_site_loaded' );
