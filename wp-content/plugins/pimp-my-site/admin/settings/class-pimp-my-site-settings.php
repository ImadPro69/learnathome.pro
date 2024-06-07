<?php
/**
 * Class Pimp_My_Site_Settings
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package pimp-my-site/admin/settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pimp_My_Site_Settings' ) ) :
	/**
	 * Class Pimp_My_Site_Settings
	 *
	 * @author themosaurus
	 * @package pimp-my-site/admin/settings
	 */
	class Pimp_My_Site_Settings {

		/**
		 * Option page slug.
		 *
		 * @var string
		 */
		private $page_slug = 'pimp-my-site';

		/**
		 * Option group slug.
		 *
		 * @var string
		 */
		private $option_group = 'pimp_my_site';

		/**
		 * Options presets
		 *
		 * @var array
		 */
		public $presets = array();

		/**
		 * Settings fields
		 *
		 * @var array
		 */
		private $settings = array();

		/**
		 * Pimp_My_Site_Settings constructor.
		 */
		public function __construct() {
			add_filter( 'plugin_action_links_' . PIMP_MY_SITE_PLUGIN_BASENAME, array( $this, 'add_plugin_page_settings_link' ), 10, 1 );

			add_action( 'admin_menu', array( $this, 'add_option_page' ) );
			add_action( 'admin_init', array( $this, 'page_init'       ) );
			add_action( 'admin_init', array( $this, 'process_preset'  ), 9 );

			$this->presets = array(
				'christmas'    => array(
					'label'   => esc_html__( 'Christmas', ' pimp-my-site' ),
					'options' => array(
						'christmas1' => array(
							'label'   => esc_html__( 'Christmas 1', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'christmas/corner-5',
								"{$this->option_group}_top_right_sticker"               => 'christmas/corner-6',
								"{$this->option_group}_bottom_left_sticker"             => 'christmas/corner-3',
								"{$this->option_group}_bottom_right_sticker"            => 'christmas/corner-4',
								"{$this->option_group}_top_sticker"                     => 'christmas/decoration-9',
								"{$this->option_group}_bottom_sticker"                  => 'christmas/decoration-18',
								"{$this->option_group}_cursor"                          => 'christmas/cursor-4',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#97bac4',
								"{$this->option_group}_scrollbar_track_color"           => '#d0dde4',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array( 'sparkle' ),
								"{$this->option_group}_particles_colors"                => array(
									'#a8cad4',
									'#d0dde4',
									'#9cd7e2',
									'#e5f3fc',
									'#ffffff'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '10'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '30',
									'max' => '130'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'christmas2' => array(
							'label'   => esc_html__( 'Christmas 2', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'christmas/decoration-17',
								"{$this->option_group}_top_right_sticker"               => 'christmas/decoration-16',
								"{$this->option_group}_bottom_left_sticker"             => 'christmas/decoration-12',
								"{$this->option_group}_bottom_right_sticker"            => 'christmas/decoration-15',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'christmas/cursor-2',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#97bac4',
								"{$this->option_group}_scrollbar_track_color"           => '#d0dde4',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'sparkle3',
									1 => 'snowflake',
									2 => 'snowflake2',
									3 => 'snowflake3'
								),
								"{$this->option_group}_particles_colors"                => array(
									'#a8cad4',
									'#d0dde4',
									'#9cd7e2',
									'#e5f3fc',
									'#ffffff'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '15'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '30',
									'max' => '130'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '44',
							),
						),
						'christmas3' => array(
							'label'   => esc_html__( 'Christmas 3', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'christmas/corner-13',
								"{$this->option_group}_top_right_sticker"               => 'christmas/corner-10',
								"{$this->option_group}_bottom_left_sticker"             => 'christmas/decoration-27',
								"{$this->option_group}_bottom_right_sticker"            => 'christmas/decoration-25',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'christmas/cursor-3',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#315c0f',
								"{$this->option_group}_scrollbar_track_color"           => '#60922e',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									'sparkle',
									'sparkle4'
								),
								"{$this->option_group}_particles_colors"                => array( '#d3a13b' ),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '80',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '2',
									'max' => '12'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '60',
									'max' => '170'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '40',
							),
						),
						'christmas4' => array(
							'label'   => esc_html__( 'Christmas 4', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'christmas/decoration-1',
								"{$this->option_group}_top_right_sticker"               => 'christmas/corner-2',
								"{$this->option_group}_bottom_left_sticker"             => 'christmas/corner-7',
								"{$this->option_group}_bottom_right_sticker"            => 'christmas/decoration-14',
								"{$this->option_group}_top_sticker"                     => 'christmas/decoration-7',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'christmas/cursor-4',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#2a9f6a',
								"{$this->option_group}_scrollbar_track_color"           => '#a8e5b2',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'sparkle',
									1 => 'sparkle2',
								),
								"{$this->option_group}_particles_colors"                => array(
									0 => '#d8d2ce',
									1 => '#2a9f6a',
									2 => '#a8e5b2',
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '80',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '15',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '60',
									'max' => '170',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '44',
							),
						),
						'christmas5' => array(
							'label'   => esc_html__( 'Christmas 5', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => '',
								"{$this->option_group}_top_right_sticker"               => '',
								"{$this->option_group}_bottom_left_sticker"             => 'christmas/decoration-4',
								"{$this->option_group}_bottom_right_sticker"            => 'christmas/decoration-5',
								"{$this->option_group}_top_sticker"                     => 'christmas/decoration-8',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'christmas/cursor-6',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#ffac30',
								"{$this->option_group}_scrollbar_track_color"           => '#ffea65',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array( 0 => 'circle', ),
								"{$this->option_group}_particles_colors"                => array( 0 => '#ffffff', ),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '100',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '3',
									'max' => '8',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '40',
									'max' => '140',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '3',
									'max' => '6',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '80',
							),
						),
						'christmas6' => array(
							'label'   => esc_html__( 'Christmas 6', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'christmas/decoration-1',
								"{$this->option_group}_top_right_sticker"               => 'christmas/decoration-3',
								"{$this->option_group}_bottom_left_sticker"             => 'christmas/decoration-6',
								"{$this->option_group}_bottom_right_sticker"            => 'christmas/decoration-4',
								"{$this->option_group}_top_sticker"                     => 'christmas/decoration-2',
								"{$this->option_group}_bottom_sticker"                  => 'christmas/decoration-5',
								"{$this->option_group}_cursor"                          => 'christmas/cursor-1',
								"{$this->option_group}_scrollbar_customization_enabled" => '1',
								"{$this->option_group}_scrollbar_handle_color"          => '#FFAC30',
								"{$this->option_group}_scrollbar_track_color"           => '#ffea65',
								"{$this->option_group}_particles_enabled"               => '1',
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'circle',
									1 => 'sparkle',
								),
								"{$this->option_group}_particles_colors"                => array( 0 => '#ffffff', ),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '80',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '3',
									'max' => '7',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '20',
									'max' => '160',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '3',
									'max' => '6',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '100',
							),
						),
						'christmas7' => array(
							'label'   => esc_html__( 'Christmas 7', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'christmas/corner-12',
								"{$this->option_group}_top_right_sticker"               => 'christmas/corner-13',
								"{$this->option_group}_bottom_left_sticker"             => 'christmas/decoration-23',
								"{$this->option_group}_bottom_right_sticker"            => 'christmas/decoration-21',
								"{$this->option_group}_top_sticker"                     => 'christmas/decoration-27',
								"{$this->option_group}_bottom_sticker"                  => 'christmas/decoration-29',
								"{$this->option_group}_cursor"                          => 'christmas/cursor-3',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#c7d6de',
								"{$this->option_group}_scrollbar_track_color"           => '#6e9985',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array( 0 => 'snowflake', ),
								"{$this->option_group}_particles_colors"                => array( 0 => '#f4f1d7', ),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '100',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '6',
									'max' => '12',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '40',
									'max' => '140',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '3',
									'max' => '6',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '44',
							),
						),
						'christmas8' => array(
							'label'   => esc_html__( 'Christmas 8', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'christmas/corner-5',
								"{$this->option_group}_top_right_sticker"               => 'christmas/corner-6',
								"{$this->option_group}_bottom_left_sticker"             => 'christmas/decoration-11',
								"{$this->option_group}_bottom_right_sticker"            => 'christmas/decoration-13',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'christmas/cursor-2',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#c7d6de',
								"{$this->option_group}_scrollbar_track_color"           => '#6e9985',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'sparkle',
									1 => 'sparkle4',
								),
								"{$this->option_group}_particles_colors"                => array( 0 => '#a8cad4', ),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '100',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '6',
									'max' => '12',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '40',
									'max' => '140',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '3',
									'max' => '6',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '44',
							),
						),
					)
				),
				'halloween'    => array(
					'label'   => esc_html__( 'Halloween', 'pimp-my-site' ),
					'options' => array(
						'halloween1' => array(
							'label'   => esc_html__( 'Halloween 1', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'halloween/decoration-21',
								"{$this->option_group}_top_right_sticker"               => 'halloween/decoration-14',
								"{$this->option_group}_bottom_left_sticker"             => 'halloween/decoration-4',
								"{$this->option_group}_bottom_right_sticker"            => 'halloween/decoration-1',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'halloween/cursor-2',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#392555',
								"{$this->option_group}_scrollbar_track_color"           => '#725291',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'ghost',
									1 => 'ghost2',
								),
								"{$this->option_group}_particles_colors"                => array(
									'#735292',
									'#502B79',
									'#2a161c',
									'#411f29'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '15'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '220',
									'max' => '320'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'halloween2' => array(
							'label'   => esc_html__( 'Halloween 2', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'halloween/corner-1',
								"{$this->option_group}_top_right_sticker"               => 'halloween/decoration-21',
								"{$this->option_group}_bottom_left_sticker"             => 'halloween/decoration-6',
								"{$this->option_group}_bottom_right_sticker"            => 'halloween/decoration-24',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'halloween/cursor-1',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#CF622C',
								"{$this->option_group}_scrollbar_track_color"           => '#15132A',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array( 0 => 'bat' ),
								"{$this->option_group}_particles_colors"                => array(
									'#CF622C',
									'#000000',
									'#333333',
									'#666666'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '15'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '220',
									'max' => '320'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'halloween3' => array(
							'label'   => esc_html__( 'Halloween 3', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'halloween/corner-1',
								"{$this->option_group}_top_right_sticker"               => 'halloween/decoration-14',
								"{$this->option_group}_bottom_left_sticker"             => 'halloween/decoration-2',
								"{$this->option_group}_bottom_right_sticker"            => 'halloween/decoration-19',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'halloween/cursor-5',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#1E131B',
								"{$this->option_group}_scrollbar_track_color"           => '#449143',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'candy',
									1 => 'candy2'
								),
								"{$this->option_group}_particles_colors"                => array(
									'#735292',
									'#449143',
									'#cf622c',
									'#ff0000'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '15'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '220',
									'max' => '320'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'halloween4' => array(
							'label'   => esc_html__( 'Halloween 4', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'halloween/decoration-15',
								"{$this->option_group}_top_right_sticker"               => 'halloween/decoration-14',
								"{$this->option_group}_bottom_left_sticker"             => 'halloween/decoration-7',
								"{$this->option_group}_bottom_right_sticker"            => 'halloween/decoration-20',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'halloween/cursor-5',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#724287',
								"{$this->option_group}_scrollbar_track_color"           => '#b17ed1',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'dot',
									1 => 'bat',
									2 => 'ghost'
								),
								"{$this->option_group}_particles_colors"                => array(
									'#735292',
									'#b17ed1',
									'#16101c'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '7',
									'max' => '20'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '280',
									'max' => '350'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '4',
									'max' => '7'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'halloween5' => array(
							'label'   => esc_html__( 'Halloween 5', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'halloween/decoration-12',
								"{$this->option_group}_top_right_sticker"               => 'halloween/decoration-22',
								"{$this->option_group}_bottom_left_sticker"             => 'halloween/decoration-11',
								"{$this->option_group}_bottom_right_sticker"            => 'halloween/decoration-8',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'halloween/cursor-6',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#f2ad4c',
								"{$this->option_group}_scrollbar_track_color"           => '#FBE6D1',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'ghost2',
									1 => 'bat',
									2 => 'ghost'
								),
								"{$this->option_group}_particles_colors"                => array(
									'#f2ad4c',
									'#fbe6d1',
									'#c12a2b'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '7',
									'max' => '20'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '280',
									'max' => '350'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '4',
									'max' => '7'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'halloween6' => array(
							'label'   => esc_html__( 'Halloween 6', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'halloween/decoration-14',
								"{$this->option_group}_top_right_sticker"               => 'halloween/decoration-9',
								"{$this->option_group}_bottom_left_sticker"             => 'halloween/decoration-1',
								"{$this->option_group}_bottom_right_sticker"            => 'halloween/decoration-3',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'halloween/cursor-4',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#f2ad4c',
								"{$this->option_group}_scrollbar_track_color"           => '#2A161C',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'ghost2',
									1 => 'bat',
									2 => 'ghost'
								),
								"{$this->option_group}_particles_colors"                => array( '#222222' ),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '7',
									'max' => '20'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '280',
									'max' => '350'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '4',
									'max' => '7'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'halloween7' => array(
							'label'   => esc_html__( 'Halloween 7', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'halloween/corner-1',
								"{$this->option_group}_top_right_sticker"               => 'halloween/corner-1',
								"{$this->option_group}_bottom_left_sticker"             => 'halloween/decoration-5',
								"{$this->option_group}_bottom_right_sticker"            => 'halloween/decoration-10',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => 'halloween/decoration-24',
								"{$this->option_group}_cursor"                          => 'halloween/cursor-3',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#222222',
								"{$this->option_group}_scrollbar_track_color"           => '#F1F1F1',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array( 0 => 'ghost' ),
								"{$this->option_group}_particles_colors"                => array( '#222222' ),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '7',
									'max' => '20'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '280',
									'max' => '350'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '4',
									'max' => '7'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
					)
				),
				'thanksgiving' => array(
					'label'   => esc_html__( 'Thanksgiving', 'pimp-my-site' ),
					'options' => array(
						'thanksgiving1' => array(
							'label'   => esc_html__( 'Thanksgiving 1', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'thanksgiving/corner-1',
								"{$this->option_group}_top_right_sticker"               => 'thanksgiving/decoration-2',
								"{$this->option_group}_bottom_left_sticker"             => 'thanksgiving/decoration-1',
								"{$this->option_group}_bottom_right_sticker"            => 'thanksgiving/decoration-3',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => 'thanksgiving/decoration-5',
								"{$this->option_group}_cursor"                          => 'thanksgiving/cursor-4',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#5B2622',
								"{$this->option_group}_scrollbar_track_color"           => '#BF8F8B',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'leaf',
									1 => 'leaf2',
									2 => 'leaf3',
									3 => 'leaf4'
								),
								"{$this->option_group}_particles_colors"                => array(
									'#5B2622',
									'#C13E2B',
									'#DD612A',
									'#F3B428'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '7',
									'max' => '20'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '90',
									'max' => '100'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '4',
									'max' => '7'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'thanksgiving2' => array(
							'label'   => esc_html__( 'Thanksgiving 2', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'thanksgiving/decoration-1',
								"{$this->option_group}_top_right_sticker"               => 'thanksgiving/decoration-3',
								"{$this->option_group}_bottom_left_sticker"             => 'thanksgiving/decoration-11',
								"{$this->option_group}_bottom_right_sticker"            => 'thanksgiving/corner-1',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => 'thanksgiving/decoration-12',
								"{$this->option_group}_cursor"                          => 'thanksgiving/cursor-2',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#C13E2B',
								"{$this->option_group}_scrollbar_track_color"           => '#FDD3CD',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'leaf4',
									1 => 'leaf5',
									2 => 'leaf6'
								),
								"{$this->option_group}_particles_colors"                => array(
									'#5B2622',
									'#C13E2B',
									'#DD612A',
									'#F3B428'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '7',
									'max' => '20'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '90',
									'max' => '100'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '4',
									'max' => '7'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'thanksgiving3' => array(
							'label'   => esc_html__( 'Thanksgiving 3', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'thanksgiving/decoration-3',
								"{$this->option_group}_top_right_sticker"               => 'thanksgiving/decoration-4',
								"{$this->option_group}_bottom_left_sticker"             => 'thanksgiving/decoration-4',
								"{$this->option_group}_bottom_right_sticker"            => 'thanksgiving/decoration-9',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => 'thanksgiving/decoration-10',
								"{$this->option_group}_cursor"                          => 'thanksgiving/cursor-1',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#C13E2B',
								"{$this->option_group}_scrollbar_track_color"           => '#FDD3CD',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array(
									0 => 'leaf7',
									1 => 'leaf8'
								),
								"{$this->option_group}_particles_colors"                => array(
									'#5B2622',
									'#C13E2B',
									'#DD612A',
									'#F3B428'
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '7',
									'max' => '20'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '90',
									'max' => '100'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '4',
									'max' => '7'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '45',
							),
						),
						'thanksgiving4' => array(
							'label'   => esc_html__( 'Thanksgiving 4', 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'thanksgiving/decoration-7',
								"{$this->option_group}_top_right_sticker"               => 'thanksgiving/decoration-7',
								"{$this->option_group}_bottom_left_sticker"             => 'thanksgiving/decoration-5',
								"{$this->option_group}_bottom_right_sticker"            => 'thanksgiving/decoration-6',
								"{$this->option_group}_top_sticker"                     => 'thanksgiving/decoration-1',
								"{$this->option_group}_bottom_sticker"                  => 'thanksgiving/decoration-11',
								"{$this->option_group}_cursor"                          => 'thanksgiving/cursor-3',
								"{$this->option_group}_scrollbar_customization_enabled" => true,
								"{$this->option_group}_scrollbar_handle_color"          => '#F3B428',
								"{$this->option_group}_scrollbar_track_color"           => '#F0E0BC',
								"{$this->option_group}_particles_enabled"               => true,
								"{$this->option_group}_particles_shapes"                => array( 0 => 'sparkle' ),
								"{$this->option_group}_particles_colors"                => array( '#F3B428' ),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '20',
									'max' => '80'
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '12'
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '50',
									'max' => '90'
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '2',
									'max' => '5'
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0'
								),
								"{$this->option_group}_particles_density"               => '20',
							),
						),
					),
				),
				'valentine'    => array(
					'label'   => esc_html__( "Valentine's Day", 'pimp-my-site' ),
					'options' => array(
						'valentine1' => array(
							'label'   => esc_html__( "Valentine's Day 1", 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'valentine/corner-1',
								"{$this->option_group}_top_right_sticker"               => 'valentine/decoration-5',
								"{$this->option_group}_bottom_left_sticker"             => 'valentine/decoration-10',
								"{$this->option_group}_bottom_right_sticker"            => 'valentine/decoration-6',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'valentine/cursor-6',
								"{$this->option_group}_scrollbar_customization_enabled" => '1',
								"{$this->option_group}_scrollbar_handle_color"          => '#e13248',
								"{$this->option_group}_scrollbar_track_color"           => '#ffdfcb',
								"{$this->option_group}_particles_enabled"               => '1',
								"{$this->option_group}_particles_shapes"                => array(
									'heart',
									'heart2',
									'heart3',
									'heart4',
									'heart5',
								),
								"{$this->option_group}_particles_colors"                => array(
									'#e13248',
									'#ffdfcb',
									'#f798a0',
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '10',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '270',
									'max' => '320',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '50',
							),
						),
						'valentine2' => array(
							'label'   => esc_html__( "Valentine's Day 2", 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'valentine/decoration-4',
								"{$this->option_group}_top_right_sticker"               => 'valentine/decoration-5',
								"{$this->option_group}_bottom_left_sticker"             => 'valentine/decoration-18',
								"{$this->option_group}_bottom_right_sticker"            => 'valentine/decoration-19',
								"{$this->option_group}_top_sticker"                     => 'valentine/decoration-13',
								"{$this->option_group}_bottom_sticker"                  => 'valentine/decoration-7',
								"{$this->option_group}_cursor"                          => 'valentine/cursor-3',
								"{$this->option_group}_scrollbar_customization_enabled" => '1',
								"{$this->option_group}_scrollbar_handle_color"          => '#b90a03',
								"{$this->option_group}_scrollbar_track_color"           => '#5b1d29',
								"{$this->option_group}_particles_enabled"               => '1',
								"{$this->option_group}_particles_shapes"                => array(
									'heart-balloon',
									'heart-balloon2',
									'heart-balloon3',
									'heart-balloon4',
								),
								"{$this->option_group}_particles_colors"                => array(
									'#86303d',
									'#5b1d29',
									'#b90a03',
									'#7f0505',
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '15',
									'max' => '20',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '240',
									'max' => '300',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '50',
							),
						),
						'valentine3' => array(
							'label'   => esc_html__( "Valentine's Day 3", 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'valentine/decoration-2',
								"{$this->option_group}_top_right_sticker"               => '',
								"{$this->option_group}_bottom_left_sticker"             => 'valentine/decoration-18',
								"{$this->option_group}_bottom_right_sticker"            => 'valentine/decoration-17',
								"{$this->option_group}_top_sticker"                     => '',
								"{$this->option_group}_bottom_sticker"                  => '',
								"{$this->option_group}_cursor"                          => 'valentine/cursor-2',
								"{$this->option_group}_scrollbar_customization_enabled" => '1',
								"{$this->option_group}_scrollbar_handle_color"          => '#b90a03',
								"{$this->option_group}_scrollbar_track_color"           => '#5b1d29',
								"{$this->option_group}_particles_enabled"               => '1',
								"{$this->option_group}_particles_shapes"                => array(
									'leaf5',
									'heart3',
									'heart4',
									'heart5',
								),
								"{$this->option_group}_particles_colors"                => array(
									'#b90a03',
									'#7f0505',
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '50',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '7',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '40',
									'max' => '85',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '30',
							),
						),
						'valentine4' => array(
							'label'   => esc_html__( "Valentine's Day 4", 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => 'valentine/decoration-9',
								"{$this->option_group}_top_right_sticker"               => 'valentine/decoration-7',
								"{$this->option_group}_bottom_left_sticker"             => 'valentine/decoration-1',
								"{$this->option_group}_bottom_right_sticker"            => 'valentine/decoration-16',
								"{$this->option_group}_top_sticker"                     => 'valentine/decoration-13',
								"{$this->option_group}_bottom_sticker"                  => 'valentine/decoration-2',
								"{$this->option_group}_cursor"                          => 'valentine/cursor-5',
								"{$this->option_group}_scrollbar_customization_enabled" => '1',
								"{$this->option_group}_scrollbar_handle_color"          => '#70111d',
								"{$this->option_group}_scrollbar_track_color"           => '#fe786c',
								"{$this->option_group}_particles_enabled"               => '1',
								"{$this->option_group}_particles_shapes"                => array(
									'circle',
									'heart3',
									'heart5',
								),
								"{$this->option_group}_particles_colors"                => array(
									'#fd9393',
									'#f74b55',
									'#b90a03',
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '70',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '5',
									'max' => '7',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '280',
									'max' => '340',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '54',
							),
						),
						'valentine5' => array(
							'label'   => esc_html__( "Valentine's Day 5", 'pimp-my-site' ),
							'options' => array(
								"{$this->option_group}_top_left_sticker"                => '',
								"{$this->option_group}_top_right_sticker"               => '',
								"{$this->option_group}_bottom_left_sticker"             => 'valentine/decoration-14',
								"{$this->option_group}_bottom_right_sticker"            => 'valentine/decoration-19',
								"{$this->option_group}_top_sticker"                     => 'valentine/decoration-13',
								"{$this->option_group}_bottom_sticker"                  => 'valentine/decoration-3',
								"{$this->option_group}_cursor"                          => 'valentine/cursor-1',
								"{$this->option_group}_scrollbar_customization_enabled" => '1',
								"{$this->option_group}_scrollbar_handle_color"          => '#ffb878',
								"{$this->option_group}_scrollbar_track_color"           => '#ffdfcb',
								"{$this->option_group}_particles_enabled"               => '1',
								"{$this->option_group}_particles_shapes"                => array(
									'heart-balloon',
									'heart-balloon2',
									'heart-balloon3',
									'heart-balloon4',
								),
								"{$this->option_group}_particles_colors"                => array(
									'#f49ba2',
									'#f35e75',
									'#e83648',
								),
								"{$this->option_group}_particles_opacity"               => array(
									'min' => '100',
									'max' => '100',
								),
								"{$this->option_group}_particles_size"                  => array(
									'min' => '15',
									'max' => '30',
								),
								"{$this->option_group}_particles_direction"             => array(
									'min' => '280',
									'max' => '300',
								),
								"{$this->option_group}_particles_speed"                 => array(
									'min' => '1',
									'max' => '2',
								),
								"{$this->option_group}_particles_lifetime"              => array(
									'min' => '0',
									'max' => '0',
								),
								"{$this->option_group}_particles_density"               => '17',
							),
						),
					),
				),
			);

			$selected_preset_name = get_option( "{$this->option_group}_selected_preset", false );
			$selected_preset      = $this->get_preset( $selected_preset_name );

			$this->settings = array(
				"{$this->option_group}_general_section"        => array(
					'title'  => esc_html__( 'General', 'pimp-my-site' ),
					'fields' => array(
						"{$this->option_group}_enabled"        => array(
							'label'   => esc_html__( 'Enable effects', 'pimp-my-site' ),
							'type'    => 'checkbox',
							'default' => true,
						),
						"{$this->option_group}_mobile_enabled" => array(
							'label'   => esc_html__( 'Enable effects on mobile', 'pimp-my-site' ),
							'type'    => 'checkbox',
							'default' => true,
						),
					),
				),
				"{$this->option_group}_preset_options_section" => array(
					// translators: %1$s = preset name
					'title'  => ! empty( $selected_preset ) ? sprintf( esc_html__( '%1$s Preset Options', 'pimp-my-site' ), $selected_preset['label'] ) : esc_html__( 'Preset Options', 'pimp-my-site' ),
					'fields' => array(),
				),
				"{$this->option_group}_stickers_section"       => array(
					'title'  => esc_html__( 'Stickers', 'pimp-my-site' ),
					'fields' => array(
						"{$this->option_group}_top_left_sticker" => array(
							'label'        => esc_html__( 'Top Left Corner', 'pimp-my-site' ),
							'button_label' => esc_html__( 'Choose Sticker', 'pimp-my-site' ),
							'type'         => 'selector',
							'options_dir'  => 'assets/images/stickers',
							'exclude'      => array(
								'valentine/corner-2.svg',
								'valentine/decoration-10.svg',
								'valentine/decoration-15.svg',
								'valentine/decoration-16.svg',
								'valentine/decoration-20.svg',
							),
							'default'      => '',
						),
						"{$this->option_group}_top_right_sticker"    => array(
							'label'        => esc_html__( 'Top Right Corner', 'pimp-my-site' ),
							'button_label' => esc_html__( 'Choose Sticker', 'pimp-my-site' ),
							'type'         => 'selector',
							'options_dir'  => 'assets/images/stickers',
							'exclude'      => array(
								'valentine/corner-1.svg',
								'valentine/decoration-13.svg',
								'valentine/decoration-10.svg',
								'valentine/decoration-15.svg',
								'valentine/decoration-16.svg',
								'valentine/decoration-20.svg',
							),
							'default'      => '',
						),
						"{$this->option_group}_bottom_left_sticker"  => array(
							'label'        => esc_html__( 'Bottom Left Corner', 'pimp-my-site' ),
							'button_label' => esc_html__( 'Choose Sticker', 'pimp-my-site' ),
							'type'         => 'selector',
							'options_dir'  => 'assets/images/stickers',
							'exclude'      => array(
								'valentine/corner-1.svg',
								'valentine/corner-2.svg',
								'valentine/decoration-13.svg',
								'valentine/decoration-16.svg',
								'valentine/decoration-20.svg',
							),
							'default'      => '',
						),
						"{$this->option_group}_bottom_right_sticker" => array(
							'label'        => esc_html__( 'Bottom Right Corner', 'pimp-my-site' ),
							'button_label' => esc_html__( 'Choose Sticker', 'pimp-my-site' ),
							'type'         => 'selector',
							'options_dir'  => 'assets/images/stickers',
							'exclude'      => array(
								'valentine/corner-1.svg',
								'valentine/corner-2.svg',
								'valentine/decoration-10.svg',
								'valentine/decoration-13.svg',
								'valentine/decoration-15.svg',
							),
							'default'      => '',
						),
						"{$this->option_group}_top_sticker"          => array(
							'label'        => esc_html__( 'Top of page', 'pimp-my-site' ),
							'button_label' => esc_html__( 'Choose Sticker', 'pimp-my-site' ),
							'type'         => 'selector',
							'options_dir'  => 'assets/images/stickers',
							'exclude'      => array(
								'valentine/corner-2.svg',
								'valentine/decoration-10.svg',
								'valentine/decoration-15.svg',
								'valentine/decoration-16.svg',
								'valentine/decoration-20.svg',
							),
							'default'      => '',
						),
						"{$this->option_group}_bottom_sticker"       => array(
							'label'        => esc_html__( 'Bottom of page', 'pimp-my-site' ),
							'button_label' => esc_html__( 'Choose Sticker', 'pimp-my-site' ),
							'type'         => 'selector',
							'options_dir'  => 'assets/images/stickers',
							'exclude'      => array(
								'valentine/corner-1.svg',
								'valentine/corner-2.svg',
								'valentine/decoration-13.svg',
								'valentine/decoration-16.svg',
							),
							'default'      => '',
						),
					),
				),
				"{$this->option_group}_cursor_section"         => array(
					'title'  => esc_html__( 'Cursor', 'pimp-my-site' ),
					'fields' => array(
						"{$this->option_group}_cursor" => array(
							'label'        => esc_html__( 'Replace cursor', 'pimp-my-site' ),
							'button_label' => esc_html__( 'Choose Cursor', 'pimp-my-site' ),
							'type'         => 'selector',
							'options_dir'  => 'assets/images/cursors',
							'default'      => '',
						),
					),
				),
				"{$this->option_group}_scrollbar_section"      => array(
					'title'  => esc_html__( 'Scrollbar', 'pimp-my-site' ),
					'fields' => array(
						"{$this->option_group}_scrollbar_customization_enabled" => array(
							'label'   => esc_html__( 'Enable Scrollbar Customization', 'pimp-my-site' ),
							'type'    => 'checkbox',
							'default' => false,
						),
						"{$this->option_group}_scrollbar_handle_color"          => array(
							'label'   => esc_html__( 'Scrollbar handle color', 'pimp-my-site' ),
							'type'    => 'color',
							'default' => '#333',
						),
						"{$this->option_group}_scrollbar_track_color"           => array(
							'label'   => esc_html__( 'Scrollbar track color', 'pimp-my-site' ),
							'type'    => 'color',
							'default' => '#ccc',
						),
					),
				),
				"{$this->option_group}_particles_section"      => array(
					'title'  => esc_html__( 'Particles', 'pimp-my-site' ),
					'fields' => array(
						"{$this->option_group}_particles_enabled"   => array(
							'label'   => esc_html__( 'Enable Particle Effects', 'pimp-my-site' ),
							'type'    => 'checkbox',
							'default' => false,
						),
						"{$this->option_group}_particles_shapes"    => array(
							'label'   => esc_html__( 'Particles Shape', 'pimp-my-site' ),
							'type'    => 'check_image',
							'options' => array(
								'circle'         => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/circle.svg',
								'circle2'        => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/circle2.svg',
								'snowflake'      => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/snowflake.svg',
								'snowflake2'     => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/snowflake2.svg',
								'snowflake3'     => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/snowflake3.svg',
								'sparkle'        => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/sparkle.svg',
								'sparkle2'       => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/sparkle2.svg',
								'sparkle3'       => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/sparkle3.svg',
								'sparkle4'       => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/sparkle4.svg',
								'sparkle5'       => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/sparkle5.svg',
								'star'           => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/star.svg',
								'candy'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/candy.svg',
								'candy2'         => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/candy2.svg',
								'bat'            => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/bat.svg',
								'ghost'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/ghost.svg',
								'ghost2'         => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/ghost2.svg',
								'leaf'           => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/leaf.svg',
								'leaf2'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/leaf2.svg',
								'leaf3'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/leaf3.svg',
								'leaf4'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/leaf4.svg',
								'leaf5'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/leaf5.svg',
								'leaf6'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/leaf6.svg',
								'leaf7'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/leaf7.svg',
								'leaf8'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/leaf8.svg',
								'heart'          => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart.svg',
								'heart2'         => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart2.svg',
								'heart3'         => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart3.svg',
								'heart4'         => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart4.svg',
								'heart5'         => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart5.svg',
								'heart-balloon'  => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart-balloon.svg',
								'heart-balloon2' => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart-balloon2.svg',
								'heart-balloon3' => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart-balloon3.svg',
								'heart-balloon4' => PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/particles/heart-balloon4.svg',
							),
							'default' => array( 'snowflake' ),
						),
						"{$this->option_group}_particles_colors"    => array(
							'label'       => esc_html__( 'Particles Color', 'pimp-my-site' ),
							'description' => esc_html__( 'Add multiple colors to make each particle a random color from this set', 'pimp-my-site' ),
							'type'        => 'multi_color',
							'default'     => array( '#ffffff' ),
						),
						"{$this->option_group}_particles_opacity"   => array(
							'label'       => esc_html__( 'Particles Opacity', 'pimp-my-site' ),
							'description' => esc_html__( 'Opacity goes from 0 (transparent) to 100 (opaque).', 'pimp-my-site' ),
							'type'        => 'min_max',
							'min'         => 0,
							'max'         => 100,
							'default'     => array( 'min' => 100, 'max' => 100 ),
						),
						"{$this->option_group}_particles_size"      => array(
							'label'   => esc_html__( 'Particles Size', 'pimp-my-site' ),
							'type'    => 'min_max',
							'min'     => 0,
							'max'     => 999,
							'default' => array( 'min' => 1, 'max' => 10 ),
						),
						"{$this->option_group}_particles_direction" => array(
							'label'       => esc_html__( 'Particles Direction', 'pimp-my-site' ),
							'description' => esc_html__( 'Direction goes from 0 to 360 degrees. 0 is pointing right and the values go clockwise.', 'pimp-my-site' ),
							'type'        => 'min_max',
							'min'         => 0,
							'max'         => 360,
							'default'     => array( 'min' => 45, 'max' => 135 ),
						),
						"{$this->option_group}_particles_speed"     => array(
							'label'   => esc_html__( 'Particles Speed', 'pimp-my-site' ),
							'type'    => 'min_max',
							'min'     => 0,
							'max'     => 999,
							'default' => array( 'min' => 5, 'max' => 10 ),
						),
						"{$this->option_group}_particles_lifetime"  => array(
							'label'       => esc_html__( 'Particles Lifetime', 'pimp-my-site' ),
							'description' => esc_html__( 'Particles lifetime in seconds. 0 means they never disappear unless they move outside the screen.', 'pimp-my-site' ),
							'type'        => 'min_max',
							'min'         => 0,
							'max'         => 999,
							'default'     => array( 'min' => 0, 'max' => 0 ),
						),
						"{$this->option_group}_particles_density"   => array(
							'label'       => esc_html__( 'Particles Density', 'pimp-my-site' ),
							'description' => esc_html__( 'Changes the amount of particles on screen.', 'pimp-my-site' ),
							'type'        => 'slider',
							'min'         => 0,
							'max'         => 100,
							'default'     => '50',
						),
					),
				),
			);
		}

		/**
		 * Register and add settings and settings fields.
		 */
		public function page_init() {
			$selected_preset_name = get_option( "{$this->option_group}_selected_preset", false );

			foreach ( $this->settings as $section_id => $section ) {
				$add_section = false;

				foreach ( $section['fields'] as $field_id => $field ) {
					if ( isset( $field['presets'] ) && ! in_array( $selected_preset_name, $field['presets'] ) ) {
						continue;
					}

					$add_section = true;

					register_setting(
						$this->option_group, // Option group
						$field_id, // Option name
						array( $this, "sanitize_{$field['type']}" ) // Sanitize
					);

					add_settings_field(
						$field_id, // ID
						$field['label'], // Title
						array( $this, "render_{$field['type']}_field" ), // Callback
						$this->page_slug, // Page
						$section_id, // Section
						array_merge( array( 'id' => $field_id ), $field ) // Callback args
					);
				}

				if ( $add_section ) {
					add_settings_section(
						$section_id, // ID
						$section['title'], // Title
						'__return_false', // Callback
						$this->page_slug // Page
					);
				}
			}

			// Prevent save meta box order
			delete_user_meta( get_current_user_id(), "meta-box-order_settings_page_{$this->page_slug}" );
		}

		/**
		 * Sanitize checkbox field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return int The sanitized checkbox value
		 */
		public function sanitize_checkbox( $input ) {
			return ! empty( $input ) ? 1 : 0;
		}

		/**
		 * Sanitize multi checkbox field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return array The sanitized checkbox value
		 */
		public function sanitize_multi_checkbox( $input ) {
			if ( ! is_array( $input ) ) {
				return array();
			}

			return array_map( 'sanitize_text_field', $input );
		}

		/**
		 * Sanitize color field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return string The sanitized color
		 */
		public function sanitize_color( $input ) {
			return sanitize_hex_color( $input );
		}

		/**
		 * Sanitize multi color field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return array The sanitized color
		 */
		public function sanitize_multi_color( $input ) {
			return array_map( 'sanitize_hex_color', $input );
		}

		/**
		 * Sanitize radio image field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return string The sanitized radio image value
		 */
		public function sanitize_radio_image( $input ) {
			return sanitize_text_field( $input );
		}

		/**
		 * Sanitize selector field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return string The sanitized selector value
		 */
		public function sanitize_selector( $input ) {
			return sanitize_text_field( $input );
		}

		/**
		 * Sanitize check image field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return array The sanitized check image value
		 */
		public function sanitize_check_image( $input ) {
			return array_map( 'sanitize_text_field', $input );
		}

		/**
		 * Sanitize text field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return string The sanitized text
		 */
		public function sanitize_text( $input ) {
			return sanitize_text_field( $input );
		}

		/**
		 * Sanitize slider field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return string The sanitized text
		 */
		public function sanitize_slider( $input ) {
			return sanitize_text_field( $input );
		}

		/**
		 * Sanitize textarea field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return string The sanitized text
		 */
		public function sanitize_textarea( $input ) {
			return wp_kses_post( $input );
		}

		/**
		 * Sanitize min max field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return array The sanitized field
		 */
		public function sanitize_min_max( $input ) {
			return array( 'min' => sanitize_text_field( $input['min'] ), 'max' => sanitize_text_field( $input['max'] ) );
		}

		/**
		 * Sanitize select field.
		 *
		 * @param mixed $input Contains the setting value.
		 *
		 * @return string Sanitized select value
		 */
		public function sanitize_select( $input ) {
			return sanitize_text_field( $input );
		}

		/**
		 * Render a checkbox field.
		 *
		 * @param array $args Field args.
		 */
		public function render_checkbox_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			$checked = ! empty( $this->get_option( $args['id'] ) ) ? 'checked' : '';
			?>
			<input type="checkbox" id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" <?php echo esc_attr( $checked ); ?> />
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['description'] ); ?></label>
			<?php endif;
		}

		/**
		 * Render a multi checkbox field.
		 *
		 * @param array $args Field args.
		 */
		public function render_multi_checkbox_field( $args ) {
			if ( empty( $args['id'] ) || ! isset( $args['options'] ) ) {
				return;
			}

			if ( ! empty( $args['description'] ) ) : ?>
				<p><?php echo wp_kses_post( $args['description'] ); ?></p>
			<?php endif;

			foreach ( $args['options'] as $value => $label ) : ?>
				<br>
				<label>
					<input type="checkbox" <?php checked( in_array( esc_attr( $value ), $this->get_option( $args['id'] ) ), true ); ?> name="<?php echo esc_attr( $args['id'] ); ?>[]" value="<?php echo esc_attr( $value ); ?>">
					<?php echo esc_html( $label ); ?>
				</label>
			<?php endforeach;
		}

		/**
		 * Render a radio image field.
		 *
		 * @param array $args Field args.
		 */
		public function render_radio_image_field( $args ) {
			if ( empty( $args['id'] ) || ! isset( $args['options'] ) ) {
				return;
			}

			$args = wp_parse_args( $args, array(
				'options'   => array(),
				'img_style' => '',
			) );

			if ( ! empty( $args['description'] ) ) : ?>
				<p><?php echo wp_kses_post( $args['description'] ); ?></p>
			<?php endif;

			$is_categorized = ! empty( $args['options'] ) && is_array( reset( $args['options'] ) );

			if ( $is_categorized ) : ?>
				<ul class="pimp-my-site-radio-image-control">
					<li>
						<input type="radio" name="<?php echo esc_attr( $args['id'] ); ?>" value="" id="<?php echo esc_attr( $args['id'] . 'none' ); ?>" <?php checked( $this->get_option( $args['id'] ), '' ); ?> />
						<label for="<?php echo esc_attr( $args['id'] . 'none' ); ?>">
							<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/icon-none.svg' ); ?>" style="<?php echo esc_attr( $args['img_style'] ); ?>">
						</label>
					</li>
				</ul>

				<div id="input_<?php echo esc_attr( $args['id'] ); ?>" class="pimp-my-site-options-categories">

					<?php foreach ( $args['options'] as $category ) : ?>

						<div class="pimp-my-site-options-category" id="<?php echo esc_attr( $args['id'] . '-' . sanitize_title( $category['label'] ) ); ?>">
							<h4><?php echo esc_html( $category['label'] ); ?></h4>

							<ul class="pimp-my-site-radio-image-control">
								<?php foreach ( $category['options'] as $value => $image ) : ?>
									<li>
										<input type="radio" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $value ); ?>" id="<?php echo esc_attr( $args['id'] . $value ); ?>" <?php checked( $this->get_option( $args['id'] ), esc_attr( $value ) ); ?> />
										<label for="<?php echo esc_attr( $args['id'] . $value ); ?>">
											<img loading="lazy" src="<?php echo esc_url( $image ); ?>" style="<?php echo esc_attr( $args['img_style'] ); ?>">
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>

					<?php endforeach; ?>

				</div>

			<?php else : ?>

				<ul id="input_<?php echo esc_attr( $args['id'] ); ?>" class="pimp-my-site-radio-image-control">
					<li>
						<input type="radio" name="<?php echo esc_attr( $args['id'] ); ?>" value="" id="<?php echo esc_attr( $args['id'] . 'none' ); ?>" <?php checked( $this->get_option( $args['id'] ), 'none' ); ?> />
						<label for="<?php echo esc_attr( $args['id'] . 'none' ); ?>">
							<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/icon-none.svg' ); ?>" style="<?php echo esc_attr( $args['img_style'] ); ?>">
						</label>
					</li>
					<?php foreach ( $args['options'] as $value => $image ) : ?>
						<li>
							<input type="radio" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $value ); ?>" id="<?php echo esc_attr( $args['id'] . $value ); ?>" <?php checked( $this->get_option( $args['id'] ), esc_attr( $value ) ); ?> />
							<label for="<?php echo esc_attr( $args['id'] . $value ); ?>">
								<img loading="lazy" src="<?php echo esc_url( $image ); ?>" style="<?php echo esc_attr( $args['img_style'] ); ?>">
							</label>
						</li>
					<?php endforeach; ?>
				</ul>

			<?php endif;
		}

		/**
		 * Render a check image field.
		 *
		 * @param array $args Field args.
		 */
		public function render_check_image_field( $args ) {
			if ( empty( $args['id'] ) || ! isset( $args['options'] ) ) {
				return;
			}

			if ( ! empty( $args['description'] ) ) : ?>
				<p><?php echo wp_kses_post( $args['description'] ); ?></p>
			<?php endif;

			$is_categorized = ! empty( $args['options'] ) && is_array( reset( $args['options'] ) );

			if ( $is_categorized ) : ?>

				<div id="input_<?php echo esc_attr( $args['id'] ); ?>" class="pimp-my-site-options-categories">

					<?php foreach ( $args['options'] as $category ) : ?>

						<div class="pimp-my-site-options-category" id="<?php echo esc_attr( $args['id'] . '-' . $category['label'] ); ?>">
							<h4><?php echo esc_html( $category['label'] ); ?></h4>

							<ul class="pimp-my-site-check-image-control">
								<?php foreach ( $category['options'] as $value => $image ) : ?>
									<li>
										<input type="checkbox" name="<?php echo esc_attr( $args['id'] ); ?>[]" value="<?php echo esc_attr( $value ); ?>" id="<?php echo esc_attr( $args['id'] . $value ); ?>" <?php checked( in_array( esc_attr( $value ), $this->get_option( $args['id'] ) ) ); ?> />
										<label for="<?php echo esc_attr( $args['id'] . $value ); ?>">
											<img loading="lazy" src="<?php echo esc_url( $image ); ?>">
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>

					<?php endforeach; ?>

				</div>

			<?php else : ?>

				<ul id="input_<?php echo esc_attr( $args['id'] ); ?>" class="pimp-my-site-check-image-control">
					<?php foreach ( $args['options'] as $value => $image ) : ?>
						<li>
							<input type="checkbox" name="<?php echo esc_attr( $args['id'] ); ?>[]" value="<?php echo esc_attr( $value ); ?>" id="<?php echo esc_attr( $args['id'] . $value ); ?>" <?php checked( in_array( esc_attr( $value ), $this->get_option( $args['id'] ) ) ); ?> />
							<label for="<?php echo esc_attr( $args['id'] . $value ); ?>">
								<img loading="lazy" src="<?php echo esc_url( $image ); ?>">
							</label>
						</li>
					<?php endforeach; ?>
				</ul>

			<?php endif;
		}

		/**
		 * Render a selector field to select an item from an extensive list of options through the help of a modal
		 *
		 * @param array $args Field args.
		 */
		public function render_selector_field( $args ) {
			if ( empty( $args['id'] ) || ( ! isset( $args['options'] ) && ! isset( $args['options_dir'] ) ) ) {
				return;
			}

			$categories = array(
				'christmas'    => esc_html__( 'Christmas', 'pimp-my-site' ),
				'halloween'    => esc_html__( 'Halloween', 'pimp-my-site' ),
				'thanksgiving' => esc_html__( 'Thanksgiving', 'pimp-my-site' ),
				'valentine'    => esc_html__( "Valentine's day", 'pimp-my-site' ),
			);

			if ( ! empty( $args['options_dir'] ) ) {
				$options  = array();
				$dir_path = trailingslashit( PIMP_MY_SITE_PLUGIN_DIR_PATH . $args['options_dir'] );
				$dir_url  = trailingslashit( PIMP_MY_SITE_PLUGIN_DIR_URL . $args['options_dir'] );
				$files    = array_diff( scandir( $dir_path ), array( '.', '..', '.DS_Store' ) );

				foreach ( $files as $file ) {
					if ( is_dir( $dir_path . $file ) ) {
						$options[ $file ] = array(
							'label'   => $categories[ $file ],
							'options' => array(),
						);

						$subfiles = array_diff( scandir( $dir_path . $file ), array( '.', '..', '.DS_Store' ) );

						foreach ( $subfiles as $subfile ) {
							if ( ! empty( $args['exclude'] ) && in_array( $file . '/' . $subfile, $args['exclude'] ) ) {
								continue;
							}

							$subfile_pathinfo = pathinfo( $subfile );

							if ( $subfile_pathinfo['extension'] !== 'svg' ) {
								continue;
							}

							$options[ $file ]['options'][ $file . '/' . $subfile_pathinfo['filename'] ] = $dir_url . $file . '/' . $subfile;
						}
					}
				}

				$args['options'] = $options;
			}

			$is_categorized = ! empty( $args['options'] ) && is_array( reset( $args['options'] ) );

			$selected = $this->get_option( $args['id'] );
			if ( $is_categorized ) {
				foreach ( $args['options'] as $category ) {
					if ( isset( $category['options'][ $selected ] ) ) {
						$selected_image = $category['options'][ $selected ];
						break;
					}
				}
			}
			else {
				$selected_image = $args['options'][ $selected ];
			}
			?>

			<div class="pimp-my-site-selector-field pimp-my-site-selector-field--<?php echo esc_attr( $args['id'] ) ?>">

				<div class="pimp-my-site-selector-field__selected-option">
					<?php if ( ! empty( $selected ) ) : ?>
						<img loading="lazy" src="<?php echo esc_url( $selected_image ); ?>" style="<?php echo esc_attr( $args['img_style'] ); ?>">
					<?php else : ?>
						<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/images/icon-none.svg' ); ?>" style="<?php echo esc_attr( $args['img_style'] ); ?>">
					<?php endif; ?>
				</div>

				<a href="#" data-micromodal-trigger="pimp-my-site-selector-field__modal-<?php echo esc_attr( $args['id'] ); ?>" class="pimp-my-site-selector-field__modal-button">
					<?php echo esc_html( $args['button_label'] ); ?>
				</a>

				<div class="modal pimp-my-site-modal pimp-my-site-selector-field__modal" id="pimp-my-site-selector-field__modal-<?php echo esc_attr( $args['id'] ); ?>" aria-hidden="true">
					<div class="modal__overlay" tabindex="-1" data-micromodal-close>
						<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="pimp-my-site-selector-field__modal-title">

							<header class="modal__header">
								<h3 class="modal__title" id="pimp-my-site-selector-field__modal-title">
									<?php esc_html_e( 'Choose an option', 'pimp-my-site' ); ?>
								</h3>
								<button class="modal__close" aria-label="<?php esc_attr_e( 'Close modal', 'pimp-my-site' ); ?>" data-micromodal-close></button>
							</header>

							<main class="modal__content">

								<?php if ( $is_categorized ) : ?>
									<div class="modal__sidebar">

										<div class="modal__sidebar-content">

											<ul class="pimp-my-site-selector-field__filters">
												<li><a href="" class="active"><?php esc_html_e( 'All', 'pimp-my-site' ); ?></a></li>
												<?php foreach ( $args['options'] as $category ) : ?>
													<li><a href="#<?php echo esc_attr( $args['id'] . '-' . sanitize_title( $category['label'] ) ); ?>"><?php echo esc_html( $category['label'] ); ?></a></li>
												<?php endforeach; ?>
											</ul>

										</div>

									</div>
								<?php endif; ?>

								<div class="modal__entry">

									<?php $this->render_radio_image_field( $args ); ?>

								</div>

							</main>

						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Render a color field.
		 *
		 * @param array $args Field args.
		 */
		public function render_color_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			?>
			<input type="text" id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $this->get_option( $args['id'] ) ); ?>" class="pimp-my-site-color-picker" />
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['description'] ); ?></label>
			<?php endif;
		}

		/**
		 * Render a multi color field.
		 *
		 * @param array $args Field args.
		 */
		public function render_multi_color_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			if ( ! empty( $args['description'] ) ) : ?>
				<small><?php echo esc_html( $args['description'] ); ?></small>
			<?php endif; ?>

			<div class="pimp-my-site-multi-color-field" data-field-id="<?php echo esc_attr( $args['id'] ); ?>" data-color-values="<?php echo esc_attr( wp_json_encode( $this->get_option( $args['id'] ) ) ); ?>">
				<button class="pimp-my-site-multi-color-field__add-color button alt"><i class="dashicons dashicons-plus-alt2"></i> <?php esc_html_e( 'Add a color', 'pimp-my-site' ); ?></button>
			</div>

			<?php
		}

		/**
		 * Render a text field.
		 *
		 * @param array $args Field args.
		 */
		public function render_text_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			?>
			<input type="text" id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $this->get_option( $args['id'] ) ); ?>" />
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo wp_kses_post( $args['description'] ); ?></label>
			<?php endif;
		}

		/**
		 * Render a slider field.
		 *
		 * @param array $args Field args.
		 */
		public function render_slider_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			?>
			<input type="range" id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $this->get_option( $args['id'] ) ); ?>" min="<?php echo esc_attr( $args['min'] ); ?>" max="<?php echo esc_attr( $args['max'] ); ?>" class="pimp-my-site-slider" />
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo wp_kses_post( $args['description'] ); ?></label>
			<?php endif;
		}

		/**
		 * Render a textarea field.
		 *
		 * @param array $args Field args.
		 */
		public function render_textarea_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			?>
			<textarea id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" rows="2"><?php echo wp_kses_post( $this->get_option( $args['id'] ) ); ?></textarea>
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<br>
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo wp_kses_post( $args['description'] ); ?></label>
			<?php endif;
		}

		/**
		 * Render a min max field.
		 *
		 * @param array $args Field args
		 */
		public function render_min_max_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			?>
			<div class="pimp-my-site-min-max-field">
				<button class="pimp-my-site-min-max-field__merge-toggle button alt"><i class="dashicons dashicons-editor-unlink"></i></button>

				<div class="pimp-my-site-min-max-field__min" style="display: none;">
					<label for="<?php echo esc_attr( $args['id'] ); ?>-min"><?php esc_html_e( 'Min', 'pimp-my-site' ); ?></label>
					<input type="number" min="<?php echo esc_attr( $args['min'] ); ?>" max="<?php echo esc_attr( $args['max'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>-min" name="<?php echo esc_attr( $args['id'] ); ?>[min]" value="<?php echo esc_attr( $this->get_option( $args['id'] )['min'] ); ?>" />
				</div>

				<div class="pimp-my-site-min-max-field__max" style="display: none;">
					<label for="<?php echo esc_attr( $args['id'] ); ?>-max"><?php esc_html_e( 'Max', 'pimp-my-site' ); ?></label>
					<input type="number" min="<?php echo esc_attr( $args['min'] ); ?>" max="<?php echo esc_attr( $args['max'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>-max" name="<?php echo esc_attr( $args['id'] ); ?>[max]" value="<?php echo esc_attr( $this->get_option( $args['id'] )['max'] ); ?>" />
				</div>

				<div class="pimp-my-site-min-max-field__merged" style="display: inline-block;">
					<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Value', 'pimp-my-site' ); ?></label>
					<input type="number" min="<?php echo esc_attr( $args['min'] ); ?>" max="<?php echo esc_attr( $args['max'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $this->get_option( $args['id'] )['min'] ); ?>" />
				</div>

				<?php if ( ! empty( $args['description'] ) ) : ?>
					<label><?php echo wp_kses_post( $args['description'] ); ?></label>
				<?php endif; ?>

			</div>
			<?php
		}

		/**
		 * Render a select field.
		 *
		 * @param array $args Field args.
		 */
		public function render_select_field( $args ) {
			if ( empty( $args['id'] ) || empty( $args['options'] ) ) {
				return;
			}

			?>
			<select id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $this->get_option( $args['id'] ) ); ?>">
				<?php foreach ( $args['options'] as $value => $label ) : ?>
					<option <?php selected( esc_attr( $this->get_option( $args['id'] ) ), esc_attr( $value ) ); ?> value="<?php echo esc_attr( $value ); ?>">
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo wp_kses_post( $args['description'] ); ?></label>
			<?php endif; ?>
			<?php
		}

		/**
		 * Apply the selected preset
		 */
		public function process_preset() {
			if ( isset( $_POST["{$this->option_group}_preset"] ) && ! empty( $preset = sanitize_text_field( $_POST["{$this->option_group}_preset"] ) ) && check_admin_referer( 'pimp_my_site_apply_preset' ) ) {
				$selected_preset = $this->get_preset( $preset );
				if ( ! empty( $selected_preset ) && ! empty( $selected_preset['options'] ) ) {
					foreach ( $selected_preset['options'] as $option_name => $option_value ) {
						update_option( $option_name, $option_value );
					}

					update_option( "{$this->option_group}_selected_preset", $preset );

					add_settings_error( 'general', 'settings_updated', esc_html__( 'The selected preset has been applied!', 'pimp-my-site' ), 'success' );
				}
			}
		}

		/**
		 * Get a preset by name
		 *
		 * @param string $preset_name
		 *
		 * @return false|array
		 */
		private function get_preset( $preset_name ) {
			$selected_preset = false;
			foreach ( $this->presets as $category ) {
				if ( isset( $category['options'][ $preset_name ] ) ) {
					$selected_preset = $category['options'][ $preset_name ];
					break;
				}
			}

			return $selected_preset;
		}

		/**
		 * Add plugin settings link in plugin page
		 *
		 * @param array $links The plugin page links for this plugin
		 *
		 * @return array The modified list of links for this plugin
		 */
		public function add_plugin_page_settings_link( $links ) {
			if ( current_user_can( 'manage_options' ) ) {
				$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=pimp-my-site' ) ) . '">' . esc_html__( 'Settings', 'pimp-my-site' ) . '</a>';
			}
			return $links;
		}

		/**
		 * Add options page.
		 */
		public function add_option_page() {
			add_options_page(
				esc_html__( 'Pimp my Site Settings', 'pimp-my-site' ),
				esc_html__( 'Pimp my Site', 'pimp-my-site' ),
				'manage_options',
				$this->page_slug,
				array( $this, 'render_settings_page' )
			);
		}

		/**
		 * Get an option with default value.
		 *
		 * @param string $option_name The requested option name.
		 *
		 * @return mixed The requested option.
		 */
		public function get_option( $option_name ) {
			// Prefix $option_name if not already prefixed
			if ( substr( $option_name, 0, strlen( $this->option_group ) ) !== $this->option_group ) {
				$option_name = $this->option_group . '_' . $option_name;
			}

			$setting_args = array();
			foreach ( $this->settings as $settings_group ) {
				if ( isset( $settings_group['fields'][ $option_name ] ) ) {
					$setting_args = $settings_group['fields'][ $option_name ];
					break;
				}
			}

			$selected_preset = get_option( "{$this->option_group}_selected_preset", false );
			if ( isset( $setting_args['presets'] ) && ! in_array( $selected_preset, $setting_args['presets'] ) ) {
				return false;
			}

			return get_option( $option_name, $setting_args['default'] );
		}

		/**
		 * Check if the given preset is different from the current settings
		 *
		 * @param string $preset_name
		 *
		 * @return bool
		 */
		private function is_preset_customized( $preset_name ) {
			$preset = $this->get_preset( $preset_name );
			if ( empty( $preset ) || empty( $preset['options'] ) ) {
				return false;
			}

			foreach ( $preset['options'] as $option_name => $preset_value ) {
				$option_value = $this->get_option( $option_name );
				if ( is_array( $option_value ) && is_array( $preset_value ) ) {
					if ( array_diff( $option_value, $preset_value ) ) {
						return true;
					}
				}
				else if ( $option_value != $preset_value ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Options page callback.
		 */
		public function render_settings_page() {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'glider.js', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/css/vendor/glider.min.css', array(), '1.7.4' );
			wp_enqueue_style( 'pimp-my-site-settings', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/css/settings.css', array(), PIMP_MY_SITE_VERSION );

			wp_dequeue_script( 'autosave' );
			wp_enqueue_script( 'post' );
			wp_enqueue_script( 'postbox' );
			wp_enqueue_script( 'freezeframe', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/vendor/freezeframe.min.js', array(), '5.0.2', true );
			wp_enqueue_script( 'wp-color-picker-alpha', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/vendor/wp-color-picker-alpha.js', array( 'wp-color-picker' ), '3.0.2', true );
			wp_enqueue_script( 'micromodal', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/vendor/micromodal.min.js', array(), '0.4.10', true );
			wp_enqueue_script( 'glider-js', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/vendor/glider.min.js', array(), '1.7.4', true );
			wp_enqueue_script( 'isotope-layout', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/vendor/isotope.pkgd.min.js', array(), '3.0.6', true );
			wp_enqueue_script( 'pimp-my-site-settings', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/settings.js', array( 'jquery', 'wp-color-picker', 'glider-js', 'isotope-layout', 'freezeframe', 'micromodal' ), PIMP_MY_SITE_VERSION, true );
			wp_localize_script( 'pimp-my-site-settings', 'pimpMySiteSettings', array( 'confirmPresetMessage' => esc_html__( "Are you sure you want to apply this preset? \nThis will override your current settings.", 'pimp-my-site' ) ) );

			$selected_preset_name = get_option( "{$this->option_group}_selected_preset", false );
			$selected_preset = $this->get_preset( $selected_preset_name );
			?>

			<div class="wrap pimp-my-site-wrap">

				<div class="pimp-my-site-headerbar">

					<strong class="pimp-my-site-headerbar__title"><?php esc_html_e( 'Pimp my Site', 'pimp-my-site' ); ?></strong>

					<div class="pimp-my-site-headerbar__actions">
					</div>

				</div>

				<h1 class="notifications-trap"></h1>

				<div class="pimp-my-site-settings-container">

					<div class="pimp-my-site-settings-main">

						<form class="pimp-my-site-presets-form" method="post">

							<?php wp_nonce_field( 'pimp_my_site_apply_preset' ); ?>

							<h1 class="wp-heading-inline"><?php esc_html_e( 'Presets', 'pimp-my-site' ); ?></h1>

							<div class="pimp-my-site-presets">

								<?php if ( ! empty( $selected_preset_name ) ) : ?>
									<div class="pimp-my-site-preset pimp-my-site-selected-preset">

										<?php if ( $this->is_preset_customized( $selected_preset_name ) ) :?>
											<span class="pimp-my-site-selected-preset__customized">
												<?php esc_html_e( 'Customized', 'pimp-my-site' ); ?>
											</span>
										<?php endif; ?>

										<span class="pimp-my-site-selected-preset__title">
											<strong><?php echo esc_html( $selected_preset['label'] ); ?></strong>
										</span>

										<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/presets/{$selected_preset_name}.gif" ); ?>">

									</div>
								<?php else : ?>
									<p><?php esc_html_e( 'Choose one of our handmade presets to apply to your website.', 'pimp-my-site' ); ?></p>
								<?php endif; ?>

								<a href="#" data-micromodal-trigger="pimp-my-site-presets-modal" class="pimp-my-site-presets-modal-button">
									<?php if ( ! empty( $selected_preset_name ) ) : ?>
										<?php esc_html_e( 'Choose another preset', 'pimp-my-site' ); ?>
									<?php else : ?>
										<?php esc_html_e( 'Choose your preset', 'pimp-my-site' ); ?>
									<?php endif; ?>
								</a>

							</div>

							<div class="modal pimp-my-site-modal pimp-my-site-presets-modal" id="pimp-my-site-presets-modal" aria-hidden="true">
								<div class="modal__overlay" tabindex="-1" data-micromodal-close>
									<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="pimp-my-site-presets-modal__title">

										<header class="modal__header">
											<h2 class="modal__title" id="pimp-my-site-presets-modal__title">
												<?php esc_html_e( 'Choose your preset', 'pimp-my-site' ); ?>
											</h2>
											<button class="modal__close" aria-label="<?php esc_attr_e( 'Close modal', 'pimp-my-site' ); ?>" data-micromodal-close></button>
										</header>

										<main class="modal__content">

											<div class="modal__sidebar">

												<div class="modal__sidebar-content">

													<ul class="pimp-my-site-presets-modal__filters">
														<li><a href="" class="active"><?php esc_html_e( 'All', 'pimp-my-site' ); ?></a></li>
														<?php foreach ( $this->presets as $category_name => $category ) : ?>
															<li><a href="#<?php echo esc_attr( 'presets-' . $category_name ); ?>"><?php echo esc_html( $category['label'] ); ?></a></li>
														<?php endforeach; ?>
													</ul>

												</div>

											</div>

											<div class="modal__entry">

												<div id="input_presets" class="pimp-my-site-options-categories">

													<?php foreach ( $this->presets as $category_name => $category ) : ?>

														<div class="pimp-my-site-options-category" id="<?php echo esc_attr( 'presets-' . $category_name ); ?>">
															<h4><?php echo esc_html( $category['label'] ); ?></h4>

															<ul class="pimp-my-site-presets-control">
																<?php foreach ( $category['options'] as $value => $preset ) : ?>
																	<li class="pimp-my-site-preset <?php echo ( $value === $selected_preset_name ) ? esc_attr( 'pimp-my-site-selected-preset' ) : ''; ?>">

																		<?php if ( $value === $selected_preset_name ) : ?>
																			<span class="pimp-my-site-selected-preset__selected">
																				<?php esc_html_e( 'Selected', 'pimp-my-site' ); ?>
																			</span>
																		<?php endif; ?>

																		<span class="pimp-my-site-selected-preset__title">
																			<?php echo esc_html( $preset['label'] ); ?>
																		</span>

																		<input type="submit" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( "{$this->option_group}_preset" ); ?>" id="<?php echo esc_attr( "{$this->option_group}_preset_{$value}" ); ?>">
																		<label for="<?php echo esc_attr( "{$this->option_group}_preset_{$value}" ); ?>">
																			<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/presets/{$value}.gif" ); ?>">
																		</label>

																	</li>
																<?php endforeach; ?>
															</ul>
														</div>

													<?php endforeach; ?>

												</div>

											</div>

										</main>

									</div>
								</div>
							</div>

						</form>

						<h1 class="wp-heading-inline"><?php esc_html_e( 'Customize Preset', 'pimp-my-site' ); ?></h1>

						<form class="pimp-my-site-settings-form" method="post" action="options.php">
							<div id="poststuff">
								<div id="post-body" class="metabox-holder columns-2">

									<div id="postbox-container-2" class="postbox-container">
										<div id="normal-sortables" class="meta-box-sortables ui-sortable">

											<?php
											// This prints out all hidden setting fields
											settings_fields( $this->option_group );
											wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
											wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

											$this->render_settings_sections();
											?>

										</div>
									</div>

									<?php submit_button(); ?>

								</div>
							</div>
						</form>

					</div>

					<div class="pimp-my-site-settings-sidebar">

						<br /><br />

						<div class="themosaurus-promo">
							<h2><?php esc_html_e( 'Like Pimp my Site?', 'pimp-my-site' ); ?></h2>
							<h3><?php esc_html_e( 'Check out our premium, 100% compatible themes!', 'pimp-my-site' ); ?></h3>
							<div class="glider-contain">
								<button aria-label="Previous" class="glider-prev">
									<i class="dashicons dashicons-arrow-left-alt2"></i>
								</button>

								<div class="glider">
									<div class="gwangi">
										<a target="_blank" href="https://themeforest.net/item/gwangi-dating-community-theme/21115855">
											<img loading="lazy" src="https://files.themosaurus.com/bp-verified-member/gwangi-promo.png" alt="Gwangi Promo Image">
										</a>
									</div>
									<div class="cera">
										<a target="_blank" href="https://themeforest.net/item/cera-intranet-community-theme/24872621">
											<img loading="lazy" src="https://files.themosaurus.com/bp-verified-member/cera-promo.png" alt="Cera Promo Image">
										</a>
									</div>
									<div class="gorgo">
										<a target="_blank" href="https://themeforest.net/item/gorgo-minimal-content-focused-blog-and-magazine/23091367">
											<img loading="lazy" src="https://files.themosaurus.com/bp-verified-member/gorgo-promo.png" alt="Gorgo Promo Image">
										</a>
									</div>
									<div class="stego">
										<a target="_blank" href="https://themeforest.net/item/stego-food-truck-restaurant-theme/29935711">
											<img loading="lazy" src="https://files.themosaurus.com/bp-verified-member/stego-promo.png" alt="Stego Promo Image">
										</a>
									</div>
									<div class="armadon">
										<a target="_blank" href="https://themeforest.net/item/armadon-gaming-community-wordpress-theme/27957394">
											<img loading="lazy" src="https://files.themosaurus.com/bp-verified-member/armadon-promo.png" alt="Armadon Promo Image">
										</a>
									</div>
									<div class="sinclair">
										<a target="_blank" href="https://themeforest.net/item/sinclair-political-donations-wordpress-theme/31136760">
											<img loading="lazy" src="https://files.themosaurus.com/bp-verified-member/sinclair-promo.png" alt="Sinclair Promo Image">
										</a>
									</div>
								</div>

								<button aria-label="Next" class="glider-next">
									<i class="dashicons dashicons-arrow-right-alt2"></i>
								</button>
							</div>
							<div role="tablist" class="dots"></div>
						</div>

					</div>

				</div>
			</div>

			<?php
		}

		/**
		 * Render settings section
		 */
		private function render_settings_sections() {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections[ $this->page_slug ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_sections[ $this->page_slug ] as $section ) : ?>

				<?php add_meta_box( $section['id'], $section['title'], array( $this, 'render_settings_fields' ), null, 'normal', 'default', array( 'section_id' => $section['id'] ) ); ?>

			<?php endforeach;

			do_meta_boxes( null, 'normal', null );
		}

		/**
		 * Render the settings fields
		 *
		 * @param array $args Unused parameter (passed by do_meta_boxes)
		 * @param array $meta_box Meta box args
		 */
		public function render_settings_fields( $args, $meta_box ) {
			$section_id = $meta_box['args']['section_id'];

			global $wp_settings_fields;

			if ( ! isset( $wp_settings_fields[ $this->page_slug ][ $section_id ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_fields[ $this->page_slug ][ $section_id ] as $field ) {
				$class = '';

				if ( ! empty( $field['args']['class'] ) ) {
					$class = $field['args']['class'];
				}
				?>

				<div class="pimp-my-site-field <?php echo esc_attr( $class ); ?>">

					<div class="pimp-my-site-label">
						<?php if ( ! empty( $field['args']['label_for'] ) ) : ?>
							<label for="<?php echo esc_attr( $field['args']['label_for'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
						<?php else : ?>
							<label>
								<?php echo esc_html( $field['title'] ); ?>
							</label>
						<?php endif; ?>
					</div>

					<div class="pimp-my-site-input">
						<?php call_user_func( $field['callback'], $field['args'] ); ?>
					</div>

				</div>

				<?php
			}
		}
	}

endif;

return new Pimp_My_Site_Settings();
