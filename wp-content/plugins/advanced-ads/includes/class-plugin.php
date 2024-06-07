<?php
/**
 * The plugin bootstrap.
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.47.0
 */

namespace AdvancedAds;

use AdvancedAds\Admin;
use AdvancedAds\Groups;
use AdvancedAds\Framework;
use AdvancedAds\Framework\Loader;
use AdvancedAds\Installation\Install;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin.
 */
class Plugin extends Loader {

	/**
	 * Main instance
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @return Plugin
	 */
	public static function get(): Plugin {
		static $instance;

		if ( null === $instance ) {
			$instance = new Plugin();
			$instance->setup();
		}

		return $instance;
	}

	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	public function get_version(): string {
		return ADVADS_VERSION;
	}

	/**
	 * Bootstrap plugin.
	 *
	 * @return void
	 */
	private function setup(): void {
		$this->define_constants();
		$this->includes();

		/**
		 * Old loading strategy
		 *
		 * TODO: need to remove it in future.
		 */

		// Load public functions (might be used by modules, other plugins or theme).
		require_once ADVADS_ABSPATH . 'includes/functions.php';
		require_once ADVADS_ABSPATH . 'includes/cap_map.php';

		// Public-Facing and Core Functionality.
		\Advanced_Ads::get_instance();
		\Advanced_Ads_ModuleLoader::loadModules( ADVADS_ABSPATH . 'modules/' ); // enable modules, requires base class.

		// Dashboard and Administrative Functionality.
		if ( is_admin() ) {
			\Advanced_Ads_Admin::get_instance();
		}

		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], -1 );
		$this->load();
	}

	/**
	 * When WordPress has loaded all plugins, trigger the `advanced-ads-loaded` hook.
	 *
	 * @since 1.47.0
	 *
	 * @return void
	 */
	public function on_plugins_loaded(): void {
		/**
		 * Action trigger after loading finished.
		 *
		 * @since 1.47.0
		 */
		do_action( 'advanced-ads-loaded' );
	}

	/**
	 * Define Advanced Ads constant
	 *
	 * @return void
	 */
	private function define_constants(): void {
		$this->define( 'ADVADS_ABSPATH', dirname( ADVADS_FILE ) . '/' );
		$this->define( 'ADVADS_PLUGIN_BASENAME', plugin_basename( ADVADS_FILE ) );
		$this->define( 'ADVADS_BASE_URL', plugin_dir_url( ADVADS_FILE ) );
		$this->define( 'ADVADS_SLUG', 'advanced-ads' );

		// Deprecated Constants.
		/**
		 * ADVADS_BASE
		 *
		 * @deprecated 1.47.0 use ADVADS_PLUGIN_BASENAME now.
		 */
		define( 'ADVADS_BASE', ADVADS_PLUGIN_BASENAME );

		/**
		 * ADVADS_BASE_PATH
		 *
		 * @deprecated 1.47.0 use ADVADS_ABSPATH now.
		 */
		define( 'ADVADS_BASE_PATH', ADVADS_ABSPATH );

		/**
		 * ADVADS_BASE_DIR
		 *
		 * @deprecated 1.47.0 Avoid global declaration of the constant used exclusively in `load_text_domain` function; use localized declaration instead.
		 */
		define( 'ADVADS_BASE_DIR', dirname( ADVADS_PLUGIN_BASENAME ) );

		/**
		 * ADVADS_URL
		 *
		 * @deprecated 1.47.0 Deprecating the constant in favor of using the direct URL to circumvent costly `esc_url` function; please update code accordingly.
		 */
		define( 'ADVADS_URL', 'https://wpadvancedads.com/' );
	}

	/**
	 * Includes core files used in admin and on the frontend.
	 *
	 * @return void
	 */
	private function includes(): void {
		$this->register_initializer( Install::class );
		$this->register_integration( Entities::class );
		$this->register_integration( Assets_Registry::class );
		$this->register_integration( Framework\JSON::class, 'json', [ 'advancedAds' ] );
		$this->register_integration( Groups\Manager::class, 'group_manager' );

		// Only in admin area.
		if ( is_admin() ) {
			$this->register_integration( Admin\Action_Links::class );
			$this->register_integration( Admin\Assets::class );
			$this->register_integration( Admin\Header::class );
			$this->register_integration( Admin\TinyMCE::class );
			$this->register_integration( Admin\Admin_Menu::class );
		}
	}
}
