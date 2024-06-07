<?php
/**
 * String utilities
 *
 * @package AdvancedAds\Framework\Utilities
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.0.0
 */

namespace AdvancedAds\Framework\Utilities;

defined( 'ABSPATH' ) || exit;

/**
 * Str class.
 */
class Str {
	/**
	 * Validates whether the passed variable is a empty string.
	 *
	 * @param mixed $variable The variable to validate.
	 *
	 * @return bool Whether or not the passed value is a non-empty string.
	 */
	public static function is_empty( $variable ): bool {
		return ! is_string( $variable ) || empty( $variable );
	}

	/**
	 * Validates whether the passed variable is a non-empty string.
	 *
	 * @param mixed $variable The variable to validate.
	 *
	 * @return bool Whether or not the passed value is a non-empty string.
	 */
	public static function is_non_empty( $variable ): bool {
		return is_string( $variable ) && '' !== $variable;
	}

	/**
	 * Check if the string contains the given value.
	 *
	 * @param string $needle   The sub-string to search for.
	 * @param string $haystack The string to search.
	 *
	 * @return bool
	 */
	public static function contains( $needle, $haystack ): bool {
		return self::is_non_empty( $needle ) ? strpos( $haystack, $needle ) !== false : false;
	}

	/**
	 * Check if the string begins with the given value.
	 *
	 * @param string $needle   The sub-string to search for.
	 * @param string $haystack The string to search.
	 *
	 * @return bool
	 */
	public static function starts_with( $needle, $haystack ): bool {
		return '' === $needle || substr( $haystack, 0, strlen( $needle ) ) === (string) $needle;
	}

	/**
	 * Check if the string end with the given value.
	 *
	 * @param string $needle   The sub-string to search for.
	 * @param string $haystack The string to search.
	 *
	 * @return bool
	 */
	public static function ends_with( $needle, $haystack ): bool {
		return '' === $needle || substr( $haystack, -strlen( $needle ) ) === (string) $needle;
	}

	/**
	 * Generate classnames from arguments
	 *
	 * @return string
	 */
	public static function classnames(): string {
		$args    = func_get_args();
		$data    = array_reduce( $args, [ __CLASS__, 'classnames_reduce' ], [] );
		$classes = array_map(
			[ __CLASS__, 'classnames_mapper' ],
			array_keys( $data ),
			array_values( $data )
		);
		$classes = array_filter( $classes );

		return implode( ' ', $classes );
	}

	/**
	 * Classnames helper function
	 *
	 * @param mixed $carry Holds the return value of the previous iteration.
	 * @param mixed $item  Holds the value of the current iteration.
	 *
	 * @return mixed
	 */
	private static function classnames_reduce( $carry, $item ) {
		if ( is_array( $item ) ) {
			return array_merge( $carry, $item );
		}

		$carry[] = $item;

		return $carry;
	}

	/**
	 * Classnames helper function
	 *
	 * @param mixed $key   Key of array item.
	 * @param mixed $value Value of array item.
	 *
	 * @return array
	 */
	private static function classnames_mapper( $key, $value ) {
		$condition = $value;
		$return    = $key;

		if ( is_int( $key ) ) {
			$condition = null;
			$return    = $value;
		}

		$is_array             = is_array( $return );
		$is_object            = is_object( $return );
		$is_stringable_type   = ! $is_array && ! $is_object;
		$is_stringable_object = $is_object && method_exists( $return, '__toString' );

		if ( ! $is_stringable_type && ! $is_stringable_object ) {
			return null;
		}

		if ( null === $condition ) {
			return $return;
		}

		return $condition ? $return : null;
	}
}
