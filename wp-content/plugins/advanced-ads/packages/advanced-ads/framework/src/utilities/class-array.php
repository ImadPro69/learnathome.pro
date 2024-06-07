<?php
/**
 * Array utilities
 *
 * @package AdvancedAds\Framework\Utilities
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.0.0
 */

namespace AdvancedAds\Framework\Utilities;

use ArrayAccess;

defined( 'ABSPATH' ) || exit;

/**
 * Arr class.
 */
class Arr {

	/**
	 * Determine whether the given value is array accessible.
	 *
	 * @param mixed $value Value to check.
	 *
	 * @return bool
	 */
	public static function accessible( $value ) {
		return is_array( $value ) || $value instanceof ArrayAccess;
	}

	/**
	 * Determine if the given key exists in the provided array.
	 *
	 * @param ArrayAccess|array $arr Array to check key in.
	 * @param string|int        $key Key to check for.
	 *
	 * @return bool
	 */
	public static function exists( $arr, $key ) {
		if ( $arr instanceof ArrayAccess ) {
			return $arr->offsetExists( $key );
		}

		return array_key_exists( $key, $arr );
	}

	/**
	 * Insert a single array item inside another array at a set position
	 *
	 * @param array $arr      Array to modify. Is passed by reference, and no return is needed.
	 * @param array $new      New array to insert.
	 * @param int   $position Position in the main array to insert the new array.
	 */
	public static function insert( &$arr, $new, $position ) {
		$before = array_slice( $arr, 0, $position - 1 );
		$after  = array_diff_key( $arr, $before );
		$arr    = array_merge( $before, $new, $after );
	}
}
