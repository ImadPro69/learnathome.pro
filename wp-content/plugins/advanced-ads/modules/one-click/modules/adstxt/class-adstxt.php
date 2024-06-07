<?php
/**
 * The class is responsible to redirect ads.txt to centralized location.
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.48.0
 */

namespace AdvancedAds\Modules\OneClick\AdsTxt;

use AdvancedAds\Utilities\WordPress;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * AdsTxt.
 */
class AdsTxt implements Integration_Interface {

	/**
	 * Hook into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		remove_action( 'advanced-ads-plugin-loaded', 'advanced_ads_ads_txt_init' );

		add_action( 'init', [ $this, 'add_rewrite_rules' ] );
		add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
		add_action( 'template_redirect', [ $this, 'handle_redirect' ] );
		add_filter( 'allowed_redirect_hosts', [ $this, 'allowed_redirect_hosts' ] );

		if ( is_admin() ) {
			( new Detector() )->hooks();
		}
	}

	/**
	 * Add rewrite rules
	 *
	 * @return void
	 */
	public function add_rewrite_rules(): void {
		global $wp_rewrite;

		if ( ! $wp_rewrite->using_permalinks() ) {
			return;
		}

		add_rewrite_rule( 'ads\.txt$', $wp_rewrite->index . '?adstxt=1', 'top' );
	}

	/**
	 * Add query var
	 *
	 * @param array $vars Array to hold query variables.
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ): array {
		$vars[] = 'adstxt';
		return $vars;
	}

	/**
	 * Handle redirect
	 *
	 * @return void
	 */
	public function handle_redirect(): void {
		if ( empty( get_query_var( 'adstxt' ) ) ) {
			return;
		}

		$redirect = sprintf( 'https://adstxt.pubguru.net/%s/ads.txt', WordPress::get_site_domain() );
		wp_safe_redirect( $redirect, 301 );
		exit;
	}

	/**
	 * Allowed redirect hosts
	 *
	 * @param array $hosts Array to hold allowed hosts.
	 *
	 * @return array
	 */
	public function allowed_redirect_hosts( $hosts ): array {
		$hosts[] = 'adstxt.pubguru.net';

		return $hosts;
	}
}
