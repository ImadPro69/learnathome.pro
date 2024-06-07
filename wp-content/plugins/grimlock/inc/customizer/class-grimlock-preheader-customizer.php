<?php
/**
 * Grimlock_Preheader_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer preheader class.
 */
class Grimlock_Preheader_Customizer extends Grimlock_Region_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'preheader';
		$this->section = 'grimlock_preheader_customizer_section';
		$this->title   = esc_html__( 'Pre Header', 'grimlock' );

		add_action( 'after_setup_theme',                                                      array( $this, 'add_customizer_fields'                       ), 20    );

		add_filter( 'grimlock_customizer_controls_js_data',                                   array( $this, 'add_customizer_controls_js_data'             ), 10, 1 );
		add_filter( 'grimlock_preheader_args',                                                array( $this, 'add_args'                                    ), 10, 1 );

		add_filter( 'grimlock_navigation_customizer_sub_menu_item_background_color_elements', array( $this, 'add_sub_menu_item_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_sub_menu_item_color_elements',            array( $this, 'add_sub_menu_item_color_elements'            ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',                                             array( $this, 'add_dynamic_css'                             ), 10, 1 );

		add_action( 'grimlock_navbar_nav_menu',                                               array( $this, 'add_preheader_to_navbar'                     ), 0,  0 );
	}

	/**
	 * Add elements to use the background color applied to the Navigation sub menu items.
	 *
	 * @param $elements
	 *
	 * @return array
	 */
	public function add_sub_menu_item_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.grimlock-preheader .menu > .menu-item .sub-menu',
			'.grimlock-preheader .wpml-ls-sub-menu',
		) );
	}

	/**
	 * Add elements to use the color applied to the Navigation sub menu items.
	 *
	 * @param $elements
	 *
	 * @return array
	 */
	public function add_sub_menu_item_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.grimlock-preheader .menu > .menu-item .sub-menu',
			'.grimlock-preheader .menu > .menu-item .sub-menu .menu-item > a',
			'.grimlock-preheader .wpml-ls-sub-menu li',
			'.grimlock-preheader .wpml-ls-sub-menu li a',
		) );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data = parent::add_customizer_controls_js_data( $js_data );

		$js_data['tabs'][ $this->section ][0]['controls'][] = "{$this->id}_mobile_navigation_displayed";

		return $js_data;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_preheader_customizer_defaults', array(
			'preheader_background_image'            => '',
			'preheader_background_image_width'      => get_custom_header()->width,
			'preheader_background_image_height'     => get_custom_header()->height,
			'preheader_layout'                      => '6-6-cols-left-right',
			'preheader_container_layout'            => 'classic',
			'preheader_padding_y'                   => 0, // %
			'preheader_mobile_displayed'            => true,
			'preheader_mobile_navigation_displayed' => false,

			'preheader_background_color'            => 'rgba(255,255,255,0)',
			'preheader_heading_color'               => GRIMLOCK_BODY_COLOR,
			'preheader_color'                       => GRIMLOCK_BODY_COLOR,
			'preheader_link_color'                  => GRIMLOCK_LINK_COLOR,
			'preheader_link_hover_color'            => GRIMLOCK_LINK_HOVER_COLOR,
			'preheader_border_top_color'            => GRIMLOCK_BORDER_COLOR,
			'preheader_border_top_width'            => 0, // px
			'preheader_border_bottom_color'         => GRIMLOCK_BORDER_COLOR,
			'preheader_border_bottom_width'         => 0, // px
		) );

		$this->add_section(                           array( 'priority' => 50  ) );

		$this->add_layout_field(                      array( 'priority' => 10  ) );
		$this->add_divider_field(                     array( 'priority' => 20  ) );
		$this->add_container_layout_field(            array( 'priority' => 20  ) );
		$this->add_divider_field(                     array( 'priority' => 30  ) );
		$this->add_heading_field(                     array(
			'label'    => esc_html__( 'Mobile Display', 'grimlock' ),
			'priority' => 30,
		) );
		$this->add_mobile_displayed_field(            array( 'priority' => 30  ) );
		$this->add_mobile_navigation_displayed_field( array( 'priority' => 40  ) );

		$this->add_background_image_field(            array( 'priority' => 100 ) );
		$this->add_divider_field(                     array( 'priority' => 110 ) );
		$this->add_padding_y_field(                   array( 'priority' => 110 ) );
		$this->add_divider_field(                     array( 'priority' => 120 ) );
		$this->add_background_color_field(            array( 'priority' => 120 ) );
		$this->add_divider_field(                     array( 'priority' => 130 ) );
		$this->add_border_top_width_field(            array( 'priority' => 130 ) );
		$this->add_border_top_color_field(            array( 'priority' => 140 ) );
		$this->add_divider_field(                     array( 'priority' => 150 ) );
		$this->add_border_bottom_width_field(         array( 'priority' => 150 ) );
		$this->add_border_bottom_color_field(         array( 'priority' => 160 ) );
		$this->add_divider_field(                     array( 'priority' => 170 ) );
		$this->add_heading_color_field(               array( 'priority' => 170 ) );
		$this->add_color_field(                       array( 'priority' => 180 ) );
		$this->add_link_color_field(                  array( 'priority' => 190 ) );
		$this->add_link_hover_color_field(            array( 'priority' => 200 ) );
	}

	/**
	 * Add a Kirki checkbox field to set the component display for mobile in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_mobile_navigation_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display inside the collapsible navigation on mobile', 'grimlock' ),
				'settings' => "{$this->id}_mobile_navigation_displayed",
				'default'  => $this->get_default( "{$this->id}_mobile_navigation_displayed" ),
				'priority' => 10,
				'active_callback' => array(
					array(
						'setting'  => "{$this->id}_mobile_displayed",
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_mobile_navigation_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add the preheader to the navbar to display on mobile if the option is enabled in the customizer
	 */
	public function add_preheader_to_navbar() {
		if ( ! empty( $this->get_theme_mod( "{$this->id}_mobile_displayed" ) ) && ! empty( $this->get_theme_mod( "{$this->id}_mobile_navigation_displayed" ) ) ) {
			?>
			<div class="grimlock-preheader-mobile d-block d-md-none">
				<?php grimlock_widget_areas( 'preheader', 4 ); ?>
			</div>
			<?php
		}
	}

	/**
	 * Add custom styles based on theme mods.
	 *
	 * @param string $styles The styles printed by Kirki
	 *
	 * @return string
	 */
	public function add_dynamic_css( $styles ) {
		if ( empty( $this->get_theme_mod( "{$this->id}_mobile_displayed" ) ) || ! empty( $this->get_theme_mod( "{$this->id}_mobile_navigation_displayed" ) ) ) {
			$styles .= "
			@media (max-width: 768px) {
				.grimlock-{$this->id} {
					display: none;
				}
			}";
		}

		return $styles;
	}
}

return new Grimlock_Preheader_Customizer();
