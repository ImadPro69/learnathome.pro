<?php
/**
 * Compatibility fixes with other plugins.
 */
class Advanced_Ads_Pro_Compatibility {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ] );

		// Set WPML Language.
		// Note: the "Language filtering for AJAX operations" feature of WPML does not work
		// because it sets cookie later then our ajax requests are sent.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX
			&& defined( 'ICL_SITEPRESS_VERSION' )
			&& ! empty( $_REQUEST[ 'wpml_lang' ] ) ) {
			do_action( 'wpml_switch_language', $_REQUEST[ 'wpml_lang' ] );
		}

		// Weglot plugin.
		if ( function_exists( 'weglot_get_current_full_url' ) ) {
			add_filter( 'advanced-ads-pro-display-condition-url-string', [ $this, 'weglot_get_current_full_url' ], 0 );
		}
	}

	/**
	 * After the theme is loaded.
	 */
	public function after_setup_theme() {
		// Newspaper theme
		if ( defined( 'TD_THEME_NAME' ) && 'Newspaper' === TD_THEME_NAME ) {
			$options = get_option( 'td_011' );
			// Check if lazy load is enabled (non-existent key or '').
			if ( empty( $options['tds_animation_stack'] ) ) {
				add_filter( 'advanced-ads-ad-image-tag-style', [ $this, 'newspaper_theme_disable_lazy_load' ] );
			}
		}
	}

	/**
	 * Newspaper theme: disable lazy load of the theme to prevent conflict with
	 * cache-busting/lazy-load of the Pro add-on.
	 *
	 * @param str $style
	 * @return str $style
	 */
	public function newspaper_theme_disable_lazy_load( $style ) {
		$style .= 'opacity: 1 !important;';
		return $style;
	}

	/**
	 * Weglot plugin: Get the current full url that contains a lauguage.
	 *
	 * @param string $url_parameter Current URI string.
	 * @return string
	 */
	public function weglot_get_current_full_url( $url_parameter ) {
		if ( wp_doing_ajax() ) {
			return $url_parameter;
		}

		$url_parsed    = wp_parse_url( weglot_get_current_full_url() );
		$url_parameter = $url_parsed['path'];
		if ( isset( $url_parsed['query'] ) ) {
			$url_parameter .= '?' . $url_parsed['query'];
		}

		return $url_parameter;
	}
}

