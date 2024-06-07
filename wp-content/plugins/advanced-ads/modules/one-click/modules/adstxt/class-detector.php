<?php
/**
 * The class is responsible to detect ads.txt.
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.48.0
 */

namespace AdvancedAds\Modules\OneClick\AdsTxt;

use AdvancedAds\Modules\OneClick\Admin\Admin;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Detector.
 */
class Detector implements Integration_Interface {

	/**
	 * Hook into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'current_screen', [ $this, 'conditional_loading' ] );
	}

	/**
	 * Detect ads.txt physical file
	 *
	 * @return void
	 */
	public function conditional_loading(): void {
		if ( ! Admin::is_pubguru_page() ) {
			return;
		}

		if ( $this->detect_files() ) {
			add_action( 'pubguru_notices', [ $this, 'show_notice' ] );
		}
	}

	/**
	 * Detect file exists
	 *
	 * @return bool
	 */
	public function detect_files(): bool {
		$wp_filesystem = $this->get_filesystem();
		if ( null === $wp_filesystem ) {
			return false;
		}

		return $wp_filesystem->exists( ABSPATH . '/ads.txt' );
	}

	/**
	 * Backup file
	 *
	 * @return bool
	 */
	public function backup_file(): bool {
		$wp_filesystem = $this->get_filesystem();
		if ( null === $wp_filesystem ) {
			return false;
		}

		return $wp_filesystem->move( ABSPATH . '/ads.txt', ABSPATH . '/ads.txt.bak' );
	}

	/**
	 * Show notice that physical file exists
	 *
	 * @return void
	 */
	public function show_notice(): void {
		?>
		<div class="flex shadow-lg rounded">
			<div class="bg-red-600 p-3 rounded-l flex items-center">
				<svg focusable="false" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" class="fill-current text-white w-5" viewBox="0 0 32 32" aria-hidden="true"><path d="M2,16H2A14,14,0,1,0,16,2,14,14,0,0,0,2,16Zm23.15,7.75L8.25,6.85a12,12,0,0,1,16.9,16.9ZM8.24,25.16A12,12,0,0,1,6.84,8.27L23.73,25.16a12,12,0,0,1-15.49,0Z"></path><title>Error</title></svg>
			</div>

			<div class="p-3 bg-white rounded-r w-full flex justify-between items-center">
				<div>
					<span class="font-medium"><?php esc_html_e( 'File alert!', 'advanced-ads' ); ?></span> <?php esc_html_e( 'Physical ads.txt found. In order to use PubGuru service you need to delete it.', 'advanced-ads' ); ?>
				</div>
				<button class="js-btn-backup-adstxt px-3 py-2 rounded text-xs bg-blue-600 text-white hover:bg-blue-700" data-text="<?php esc_attr_e( 'Backup the File', 'advanced-ads' ); ?>" data-loading="<?php esc_attr_e( 'Backing Up', 'advanced-ads' ); ?>" data-done="<?php esc_attr_e( 'Backed Up', 'advanced-ads' ); ?>" data-security="<?php echo wp_create_nonce( 'pubguru_backup_adstxt' ); // phpcs:ignore ?>">
					<?php esc_html_e( 'Backup the File', 'advanced-ads' ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Instantiates the WordPress filesystem for use
	 *
	 * @return object
	 */
	public function get_filesystem() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}
}
