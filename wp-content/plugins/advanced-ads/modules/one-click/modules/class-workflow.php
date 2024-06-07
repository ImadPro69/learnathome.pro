<?php
/**
 * The class is responsible for the one-click module workflow.
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.48.0
 */

namespace AdvancedAds\Modules\OneClick;

use AdvancedAds\Modules\OneClick\Helpers;
use AdvancedAds\Modules\OneClick\Options;
use AdvancedAds\Framework\Interfaces\Integration_Interface;
use AdvancedAds\Framework\Utilities\Params;
use AdvancedAds\Modules\OneClick\AdsTxt\AdsTxt;
use AdvancedAds\Modules\OneClick\Traffic_Cop;

defined( 'ABSPATH' ) || exit;

/**
 * Workflow.
 */
class Workflow implements Integration_Interface {

	/**
	 * Flush rules option key.
	 *
	 * @var string
	 */
	const FLUSH_KEY = 'pubguru_flush_rewrite_rules';

	/**
	 * Hook into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'init', [ $this, 'flush_rewrite_rules' ], 999 );
		add_action( 'pubguru_module_status_changed', [ $this, 'module_status_changed' ], 10, 1 );
		add_action( 'advanced-ads-pghb-auto-ad-creation', [ $this, 'auto_ad_creation' ], 10, 0 );

		if ( false !== Options::pubguru_config() && ! is_admin() ) {
			add_action( 'wp', [ $this, 'init' ] );

			if ( Helpers::is_module_enabled( 'ads_txt' ) ) {
				( new AdsTxt() )->hooks();
			}
		}
	}

	/**
	 * Init workflow
	 *
	 * @return void
	 */
	public function init(): void {
		// Early bail!!
		$is_debugging = Params::get( 'aa-debug', false, FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_debugging && Helpers::is_ad_disabled() ) {
			return;
		}
		Page_Parser::get_instance();

		if ( $is_debugging || Helpers::is_module_enabled( 'header_bidding' ) ) {
			( new Header_Bidding() )->hooks();
		}

		if ( $is_debugging || Helpers::is_module_enabled( 'tag_conversion' ) ) {
			( new Tags_Conversion() )->hooks();
		}

		if ( Helpers::is_module_enabled( 'traffic_cop' ) && Helpers::has_traffic_cop() ) {
			if ( ! Helpers::is_module_enabled( 'header_bidding' ) ) {
				( new Header_Bidding() )->hooks();
			}

			( new Traffic_Cop() )->hooks();
		}
	}

	/**
	 * Handle module status change
	 *
	 * @param string $module Module name.
	 *
	 * @return void
	 */
	public function module_status_changed( $module ): void {
		if ( 'ads_txt' === $module ) {
			update_option( self::FLUSH_KEY, 1 );
		}
	}

	/**
	 * Flush the rewrite rules once if the pubguru_flush_rewrite_rules option is set
	 *
	 * @return void
	 */
	public function flush_rewrite_rules(): void {
		if ( get_option( self::FLUSH_KEY ) ) {
			flush_rewrite_rules();
			delete_option( self::FLUSH_KEY );
		}
	}

	/**
	 * Start auto ad creation process
	 *
	 * @return void
	 */
	public function auto_ad_creation(): void {
		$ads = Helpers::get_ads_from_config();
		// ( new Auto_Ads() )->run( $ads );
	}
}
