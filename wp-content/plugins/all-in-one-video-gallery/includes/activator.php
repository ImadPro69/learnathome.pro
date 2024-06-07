<?php

/**
 * Fired during plugin activation.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Activator class.
 *
 * @since 1.0.0
 */
class AIOVG_Activator {

	/**
	 * Called when the plugin is activated.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {	
		// Insert the plugin settings and default values for the first time
		$defaults = aiovg_get_default_settings();

		foreach ( $defaults as $option_name => $values ) {
			if ( false == get_option( $option_name ) ) {	
        		add_option( $option_name, $values );						
    		}
		}
		
		// Add custom AIOVG capabilities
		if ( ! get_option( 'aiovg_version' ) ) {
			$roles = new AIOVG_Roles;
			$roles->add_caps();
		}		

		// Insert the plugin version
		add_option( 'aiovg_version', AIOVG_PLUGIN_VERSION );
	}

}
