<?php

/**
 * Class Grimlock_Animate_Query_Section_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-query/inc/components
 */
class Grimlock_Animate_Query_Section_Component extends Grimlock_Query_Section_Component {
	/**
	 * Setup class.
	 *
	 * @param array $props
	 * @since 1.0.0
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'posts_reveal_selector'    => '.section__header, article, .section__footer',
			'posts_reveal'             => 'none',
			'posts_reveal_duration'    => 750,
			'posts_reveal_interval'    => 50,
			'posts_reveal_distance'    => '80px',
			'posts_reveal_delay'       => 500,
			'posts_reveal_rotate_x'    => 0,
			'posts_reveal_rotate_y'    => 0,
			'posts_reveal_rotate_z'    => 0,
			'posts_reveal_opacity'     => 0,
			'posts_reveal_scale'       => 1,
			'posts_reveal_easing'      => 'cubic-bezier(0.6, 0.2, 0.1, 1)',
			'posts_reveal_view_factor' => 0.5,
			'reveal_mobile'            => true,
			'reveal_reset'             => false,
		) ) );
	}

	/**
	 * Generate data-attributes to enable scroll reveal on the given element
	 *
	 * @param String $element The name of an element from the component
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_element_reveal_data_attributes( $element ) {
		$data_attributes = array();
		$reveal_options  = array();
		switch ( $this->props["{$element}_reveal"] ) {
			case 'bottom':
			case 'top':
			case 'left':
			case 'right':
				$reveal_options['origin']   = $this->props["{$element}_reveal"];
				$reveal_options['distance'] = $this->props["{$element}_reveal_distance"];
				break;
			case 'fade':
				$reveal_options['origin']   = 'top';
				$reveal_options['distance'] = 0;
				break;
		}

		if ( !empty( $reveal_options ) ) {
			$reveal_options['selector']   = $this->props["{$element}_reveal_selector"];
			$reveal_options['duration']   = $this->props["{$element}_reveal_duration"];
			$reveal_options['interval']   = $this->props["{$element}_reveal_interval"];
			$reveal_options['delay']      = $this->props["{$element}_reveal_delay"];
			$reveal_options['rotate']     = array(
				'x' => $this->props["{$element}_reveal_rotate_x"],
				'y' => $this->props["{$element}_reveal_rotate_y"],
				'z' => $this->props["{$element}_reveal_rotate_z"],
			);
			$reveal_options['opacity']    = $this->props["{$element}_reveal_opacity"];
			$reveal_options['scale']      = $this->props["{$element}_reveal_scale"];
			$reveal_options['easing']     = $this->props["{$element}_reveal_easing"];
			$reveal_options['viewFactor'] = $this->props["{$element}_reveal_view_factor"];
			$reveal_options['mobile']     = $this->props['reveal_mobile'];
			$reveal_options['reset']      = $this->props['reveal_reset'];

			$data_attributes['grimlock-animate-scroll-reveal'] = esc_attr( wp_json_encode( $reveal_options ) );
		}

		return $data_attributes;
	}

	/**
	 * Get data attributes as property-values pairs for the component using props.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_data_attributes() {
		$data_attributes = parent::get_data_attributes();
		return array_merge( $data_attributes, $this->get_element_reveal_data_attributes( 'posts' ) );
	}

	/**
	 * Generate a class to add to the component if the given element has a reveal effect
	 *
	 * @since 1.0.0
	 *
	 * @param $element
	 *
	 * @return string
	 */
	public function get_element_reveal_class( $element ) {
		switch ( $this->props["{$element}_reveal"] ) {
			case 'bottom':
			case 'top':
			case 'left':
			case 'right':
			case 'fade':
				return "section_{$element}_reveal";
			default:
				return '';
		}
	}

	/**
	 * Retrieve the classes for the query posts as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_posts_class( $class = '' ) {
		$classes   = parent::get_posts_class( $class );
		$classes[] = $this->get_element_reveal_class( 'posts' );

		return array_unique( $classes );
	}
}