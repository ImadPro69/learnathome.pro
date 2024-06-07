<?php
/**
 * The class is responsible for locating and loading the autoloader file used in the plugin.
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.47.0
 */

namespace AdvancedAds;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader.
 */
class Autoloader {

	/**
	 * Hold autoloader.
	 *
	 * @var mixed
	 */
	private $autoloader;

	/**
	 * Main instance
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @return Autoloader
	 */
	public static function get(): Autoloader {
		static $instance;

		if ( null === $instance ) {
			$instance = new Autoloader();
		}

		return $instance;
	}

	/**
	 * Get hold autoloader.
	 *
	 * @return mixed
	 */
	public function get_autoloader() {
		return $this->autoloader;
	}

	/**
	 * Runs this initializer.
	 *
	 * @return void
	 */
	public function initialize(): void {
		$locate = $this->locate();

		if ( ! $locate ) {
			add_action( 'admin_notices', [ $this, 'missing_autoloader' ] );
			return;
		}

		$this->autoloader = require_once $locate;
		$this->register_wordpress();
	}

	/**
	 * Locate the autoload file
	 *
	 * This function searches for the autoload file in the packages directory and vendor directory.
	 *
	 * @return bool|string
	 */
	private function locate() {
		$directory   = dirname( ADVADS_FILE );
		$packages    = $directory . '/packages/autoload.php';
		$vendors     = $directory . '/vendor/autoload.php';
		$is_debug    = 'local' === ( function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : $this->get_environment_type() );
		$is_packages = is_readable( $packages );
		$is_vendors  = is_readable( $vendors );

		if ( $is_packages && ( ! $is_debug || ! $is_vendors ) ) {
			return $packages;
		}

		if ( $is_vendors ) {
			return $vendors;
		}

		return false;
	}

	/**
	 * Add WordPress classes to map
	 *
	 * @return void
	 */
	private function register_wordpress(): void {
		$this->autoloader->addClassmap(
			[
				'WP_List_Table'       => ABSPATH . 'wp-admin/includes/class-wp-list-table.php',
				'WP_Terms_List_Table' => ABSPATH . 'wp-admin/includes/class-wp-terms-list-table.php',
			]
		);
	}

	/**
	 * If the autoloader is missing, add an admin notice.
	 *
	 * @return void
	 */
	protected function missing_autoloader(): void {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: 1: is a link to a support document. 2: closing link */
					esc_html__( 'Your installation of Advanced Ads is incomplete. If you installed Advanced Ads from GitHub, %1$s please refer to this document%2$s to set up your development environment.', 'advanced-ads' ),
					'<a href="' . esc_url( 'https://github.com/advanced-ads/advanced-ads/wiki/How-to-set-up-development-environment' ) . '" target="_blank" rel="noopener noreferrer">',
					'</a>'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Retrieves the current environment type.
	 *
	 * @return string
	 */
	public function get_environment_type(): string {
		static $current_env = '';

		if ( ! defined( 'WP_RUN_CORE_TESTS' ) && $current_env ) {
			return $current_env;
		}

		$wp_environments = [
			'local',
			'development',
			'staging',
			'production',
		];

		// Add a note about the deprecated WP_ENVIRONMENT_TYPES constant.
		if ( defined( 'WP_ENVIRONMENT_TYPES' ) && function_exists( '_deprecated_argument' ) ) {
			if ( function_exists( '__' ) ) {
				/* translators: %s: WP_ENVIRONMENT_TYPES */
				$message = sprintf( __( 'The %s constant is no longer supported.', 'advanced-ads' ), 'WP_ENVIRONMENT_TYPES' );
			} else {
				$message = sprintf( 'The %s constant is no longer supported.', 'WP_ENVIRONMENT_TYPES' );
			}

			_deprecated_argument(
				'define()',
				'5.5.1',
				$message // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}

		// Check if the environment variable has been set, if `getenv` is available on the system.
		if ( function_exists( 'getenv' ) ) {
			$has_env = getenv( 'WP_ENVIRONMENT_TYPE' );
			if ( false !== $has_env ) {
				$current_env = $has_env;
			}
		}

		// Fetch the environment from a constant, this overrides the global system variable.
		if ( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE ) {
			$current_env = WP_ENVIRONMENT_TYPE;
		}

		// Make sure the environment is an allowed one, and not accidentally set to an invalid value.
		if ( ! in_array( $current_env, $wp_environments, true ) ) {
			$current_env = 'production';
		}

		return $current_env;
	}
}
