<?php
/**
 * The class is responsible for ajax functionality.
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.48.0
 */

namespace AdvancedAds\Modules\OneClick\Admin;

use WP_Error;
use AdvancedAds\Utilities\WordPress;
use AdvancedAds\Modules\OneClick\Helpers;
use AdvancedAds\Modules\OneClick\Options;
use AdvancedAds\Modules\OneClick\AdsTxt\Detector;
use AdvancedAds\Framework\Utilities\Params;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Ajax.
 */
class Ajax implements Integration_Interface {

	/**
	 * Hook into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'wp_ajax_pubguru_connect', [ $this, 'pubguru_connect' ] );
		add_action( 'wp_ajax_pubguru_disconnect', [ $this, 'pubguru_disconnect' ] );
		add_action( 'wp_ajax_pubguru_module_change', [ $this, 'module_status_changed' ] );
		add_action( 'wp_ajax_pubguru_backup_ads_txt', [ $this, 'backup_ads_txt' ] );
	}

	/**
	 * Init hook
	 *
	 * @return void
	 */
	public function init(): void {
		if ( Params::get( 'refresh_ads', false, FILTER_VALIDATE_BOOLEAN ) ) {
			$config = $this->pubguru_api_connect();

			if ( is_wp_error( $config ) ) {
				wp_die(
					$config->get_error_message(), // phpcs:ignore
					esc_html__( 'Refreshing PubGuru Ads', 'advanced-ads' ),
					$config->get_error_data() // phpcs:ignore
				);
			}
		}
	}

	/**
	 * Pubguru Connect
	 *
	 * @return void
	 */
	public function pubguru_connect(): void {
		check_ajax_referer( 'advanced-ads-admin-ajax-nonce', 'nonce' );

		$config = $this->pubguru_api_connect();

		if ( is_wp_error( $config ) ) {
			wp_send_json_error(
				$config->get_error_message(),
				$config->get_error_data()
			);
		}

		wp_send_json_success(
			[
				'message'       => esc_html__( 'Pubguru successfully connected.', 'advanced-ads' ),
				'hasTrafficCop' => Helpers::has_traffic_cop( $config ),
			]
		);
	}

	/**
	 * Pubguru Disconnect
	 *
	 * @return void
	 */
	public function pubguru_disconnect(): void {
		check_ajax_referer( 'advanced-ads-admin-ajax-nonce', 'nonce' );

		Options::pubguru_config( 'delete' );

		wp_send_json_success(
			[
				'message' => esc_html__( 'Pubguru successfully disconnected.', 'advanced-ads' ),
			]
		);
	}

	/**
	 * Handle module status changes
	 *
	 * @return void
	 */
	public function module_status_changed(): void {
		check_ajax_referer( 'pubguru_module_changed', 'security' );

		$module     = Params::post( 'module', [] );
		$status     = Params::post( 'status', false, FILTER_VALIDATE_BOOLEAN );
		$option_key = 'pubguru_module_' . $module;

		update_option( $option_key, $status );

		do_action( 'pubguru_module_status_changed', $module, $status );

		$notice = '';

		if ( 'ads_txt' === $module && $status ) {
			$detector = new Detector();
			if ( $detector->detect_files() ) {
				ob_start();
				$detector->show_notice();
				$notice = ob_get_clean();
			}
		}

		wp_send_json_success( [ 'notice' => $notice ] );
	}

	/**
	 * Handle module status changes
	 *
	 * @return void
	 */
	public function backup_ads_txt(): void {
		check_ajax_referer( 'pubguru_backup_adstxt', 'security' );

		( new Detector() )->backup_file();

		wp_send_json_success();
	}

	/**
	 * Fetch config from PubGuru api
	 *
	 * Development: https://new-stagingtools1.pubguru.com
	 * Production: https://app.pubguru.com
	 *
	 * @return WP_Error|array
	 */
	private function pubguru_api_connect() {
		$domain   = WordPress::get_site_domain();
		$domain   = str_replace( 'www.', '', $domain );
		$response = wp_remote_get(
			'https://app.pubguru.com/domain_configs/?domain=' . $domain,
			[
				'timeout'   => 30,
				'sslverify' => false,
			]
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error(
				'connect_error',
				esc_html__( 'An error has occurred please try again.', 'advanced-ads' ),
				$response_code
			);
		}

		$config = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( 'error' === $config['status'] ) {
			return new WP_Error(
				'domain_not_found',
				'Connection with PubGuru & MonetizeMore was unsuccessful. Please <a href="https://www.monetizemore.com/contact/">click here</a> to contact MonetizeMore Support or email us at <a href="mailto:support@monetizemore.com">support@monetizemore.com</a>',
				404
			);
		}

		Options::pubguru_config( $config );
		Helpers::start_auto_ad_creation();

		return $config;
	}
}
