<?php
/**
 * Plugin name: Verified Member for BuddyPress
 * Plugin URI:  https://wordpress.org/plugins/bp-verified-member/
 * Description: This plugins allows admins to add a "Verified" badge for specific members.
 * Author:      Themosaurus
 * Author URI:  https://themosaurus.com/
 * Version:     1.2.6
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bp-verified-member
 * Domain Path: /languages
 *
 * @package Verified Member for BuddyPress
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BP_VERIFIED_MEMBER_VERSION',         '1.2.6' );
define( 'BP_VERIFIED_MEMBER_PLUGIN_FILE',     __FILE__ );
define( 'BP_VERIFIED_MEMBER_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BP_VERIFIED_MEMBER_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );
define( 'BP_VERIFIED_MEMBER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once 'inc/class-bp-verified-member.php';
require_once 'admin/class-bp-verified-member-admin.php';

register_activation_hook( __FILE__, array( BP_Verified_Member::class, 'create_admin_verification_request_email' ) );

/**
 * Show notice if BuddyPress is not installed
 */
function bp_verified_member_dependency_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			// translators: placeholders are opening and closing <a> tag, linking to BuddyPress plugin
			printf( esc_html__( 'Verified Member for BuddyPress requires %1$sBuddyPress%2$s to be installed and activated.', 'bp-verified-member' ), '<a href="https://wordpress.org/plugins/buddypress/" target="_blank">', '</a>' );
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'bp_verified_member_dependency_notice' );

/**
 * Load plugin.
 */
function bp_verified_member_loaded() {
	// Remove admin notice if plugin is able to load
	remove_action( 'admin_notices', 'bp_verified_member_dependency_notice' );

	global $bp_verified_member;
	$bp_verified_member = new BP_Verified_Member();

	global $bp_verified_member_admin;
	$bp_verified_member_admin = new BP_Verified_Member_Admin();

	do_action( 'bp_verified_member_loaded' );
}
add_action( 'bp_include', 'bp_verified_member_loaded' );
