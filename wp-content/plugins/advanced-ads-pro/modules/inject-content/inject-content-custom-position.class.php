<?php

use AdvancedAdsPro\Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Injects ads using an output buffer.
 *
 * Since we call `ob_start` during ad injection, we do not specify the callback in order to prevent the
 * "ob_start(): Cannot use output buffering in output buffering display handlers" error.
 */
class Advanced_Ads_Pro_Module_Inject_Content_Custom_Position {
	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( Advanced_Ads_Pro::get_instance()->get_options()['placement-positioning'] !== 'php' ) {
			return;
		}

		add_action( 'wp_head', [ $this, 'start_output_buffering' ], 20 );
		// We need to inject ads (collect Cache Busting output) earlier than Cache Busting outputs its scripts at priority `21`.
		add_action( 'wp_footer', [ $this, 'stop_output_buffering' ], 20 );
	}

	/**
	 * Start output buffering.
	 */
	public function start_output_buffering() {
		ob_start();
	}

	/**
	 * Stop output buffering.
	 */
	public function stop_output_buffering() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- page content should be unescaped.
		echo $this->maybe_inject_placements( ob_get_clean() );
	}

	/**
	 * Maybe inject some custom position placements.
	 *
	 * @param string $content Buffered page content.
	 * @return string
	 */
	public function maybe_inject_placements( $content ) {
		if ( ! class_exists( 'Advanced_Ads_In_Content_Injector' ) ) {
			return $content;
		}

		$placements = Advanced_Ads::get_instance()->get_model()->get_ad_placements_array();

		foreach ( $placements as $placement_id => $placement ) {
			if ( empty( $placement['item'] )
				|| ! isset( $placement['type'] )
				|| ! in_array( $placement['type'], [ 'custom_position', 'post_above_headline' ], true )
			) {
				continue;
			}

			$placement_options = isset( $placement['options'] ) ? $placement['options'] : [];

			if ( $placement['type'] === 'custom_position' ) {
				$xpath_options = $this->get_custom_position_xpath_options( $placement_options );
				if ( ! $xpath_options ) {
					continue;
				}
			}

			if ( $placement['type'] === 'post_above_headline' ) {
				$xpath_options = [
					'tag'      => 'custom',
					'xpath'    => '//h1',
					'position' => 'before',
				];
			}

			$content = Advanced_Ads_In_Content_Injector::inject_in_content(
				$placement_id,
				array_merge( $placement_options, $xpath_options ),
				$content,
				[
					'allowEmpty'   => true,
					'alter_nodes'  => false,
					'itemLimit'    => -1,
					'repeat'       => true,
					'paragraph_id' => 1,
				]
			);
		}

		return $content;
	}

	/**
	 * Get XPath options for Custom Position placement.
	 *
	 * @param array $placement_options Placement options.
	 * @return array|bool XPath options or False on failure.
	 */
	private function get_custom_position_xpath_options( $placement_options ) {
		if (
			( ! isset( $placement_options['inject_by'] )
			|| $placement_options['inject_by'] === 'pro_custom_element'
			)
			&& isset( $placement_options['pro_custom_element'] )
		) {
			// By CSS selector.
			$xpath = $this->css_to_xpath( $placement_options['pro_custom_element'] );
			if ( ! $xpath ) {
				return false;
			}

			$positions = [
				'insertBefore' => 'before',
				'prependTo'    => 'prepend',
				'appendTo'     => 'append',
				'insertAfter'  => 'after',
			];

			return [
				'tag'      => 'custom',
				'xpath'    => $xpath,
				'position' => isset( $placement_options['pro_custom_position'], $positions[ $placement_options['pro_custom_position'] ] )
				? $positions[ $placement_options['pro_custom_position'] ]
				: 'before',
			];
		} elseif ( isset( $placement_options['container_id'] ) ) {
			// By HTML container.
			$xpath = $this->css_to_xpath( $placement_options['container_id'] );
			if ( ! $xpath ) {
				return false;
			}
			return [
				'tag'      => 'custom',
				'xpath'    => $xpath,
				'position' => 'append',
			];
		}

		return false;
	}

	/**
	 * Translate a CSS expression into corresponding XPath expression.
	 *
	 * @param string $css CSS Expression.
	 * @return string XPath Expression.
	 */
	private function css_to_xpath( $css ) {
		if ( ! $css ) {
			return '';
		}

		// Our "frontend picker" adds the `:eq` selector.
		// Since the "css-selector" library does not support it, we replace it with an unique tag.
		$css = preg_replace( '/:eq\((\d+)\)/', ' > advads_eq_selector$1', $css );

		try {
			$query = ( new CssSelectorConverter() )->toXPath( $css );
		} catch ( Exception $e ) {
			return '';
		}

		// Remove the unique tag and implement the functionality of the `:eq` selector.
		do {
			$query = preg_replace_callback( '/(.*?)\/advads_eq_selector(\d+)/', static function( $matches ) {
				return sprintf( '(%s)[%s]', $matches[1], $matches[2] + 1 );
			}, $query, 1, $count );
		} while ( $count === 1 );

		return $query;
	}
}
