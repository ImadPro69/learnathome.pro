<?php
/**
 * Options.
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.48.0
 */

namespace AdvancedAds\Modules\OneClick;

defined( 'ABSPATH' ) || exit;

/**
 * Options.
 */
class Options {

	const CONFIG_KEY = '_advads_pubguru_connect_config';

	/**
	 * Read and Write pubguru config
	 *
	 * @param array $data Array of pubguru configuration.
	 *
	 * @return bool|array
	 */
	public static function pubguru_config( $data = null ) {
		if ( null === $data ) {
			return get_option( self::CONFIG_KEY );
		}

		if ( 'delete' === $data ) {
			return delete_option( self::CONFIG_KEY );
		}

		return update_option( self::CONFIG_KEY, $data );
	}
}
