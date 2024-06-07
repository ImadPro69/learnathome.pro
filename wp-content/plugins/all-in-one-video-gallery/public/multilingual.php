<?php

/**
 * Multilingual Compatibility.
 *
 * @link    https://plugins360.com
 * @since   3.0.0
 *
 * @package All_In_One_Video_Gallery
 */
 
// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Public_Multilingual class.
 *
 * @since 3.0.0
 */
class AIOVG_Public_Multilingual {

	/**
	 * [Polylang] Filter the 'aiovg_page_settings' option.
	 *
	 * @since  3.0.0
	 * @param  array $settings Default settings array.
	 * @return array $settings Filtered array of settings.
	 */
	public function filter_page_settings_for_polylang( $settings ) {
		if ( ! function_exists( 'pll_get_post' ) ) {
			return $settings;
		}

		foreach ( $settings as $key => $value ) {
			if ( $value > 0 ) {				
				$id = pll_get_post( $value );

				if ( ! empty( $id ) ) {
					$settings[ $key ] = $id;
				}
			}
		}

		return $settings;
	}	
	
}
