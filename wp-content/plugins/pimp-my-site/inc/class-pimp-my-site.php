<?php
/**
 * Class Pimp_My_Site
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package pimp-my-site/inc
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pimp_My_Site
 */
class Pimp_My_Site {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'pimp-my-site', false, 'pimp-my-site/languages' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_footer', array( $this, 'stickers' ) );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() {
		/** @var Pimp_My_Site_Admin $pimp_my_site_admin */
		global $pimp_my_site_admin;

		if ( ! $pimp_my_site_admin->settings->get_option( 'enabled' ) || ( wp_is_mobile() && ! $pimp_my_site_admin->settings->get_option( 'mobile_enabled' ) ) ) {
			return;
		}

		/**
		 * Styles
		 */
		wp_enqueue_style( 'pimp-my-site', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/css/style.css', array(), PIMP_MY_SITE_VERSION );

		$style                           = '';
		$cursor                          = $pimp_my_site_admin->settings->get_option( 'cursor' );
		$scrollbar_customization_enabled = $pimp_my_site_admin->settings->get_option( 'scrollbar_customization_enabled' );
		$scrollbar_handle_color          = $pimp_my_site_admin->settings->get_option( 'scrollbar_handle_color' );
		$scrollbar_track_color           = $pimp_my_site_admin->settings->get_option( 'scrollbar_track_color' );

		// Cursor style
		if ( ! empty( $cursor ) ) {
			$cursor_url_svg = PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/cursors/{$cursor}.svg";
			$cursor_url_png = PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/cursors/{$cursor}.png";
			$style .= "
				body {
					cursor: url(\"{$cursor_url_svg}\"), url(\"{$cursor_url_png}\"), auto;
				}
			";
		}

		// Scrollbar style
		if ( ! empty( $scrollbar_customization_enabled ) ) {
			$style .= "
				::-webkit-scrollbar {
					width: 10px;
				}
			";

			if ( ! empty( $scrollbar_handle_color ) ) {
				$style .= "
					::-webkit-scrollbar-thumb {
						background-color: {$scrollbar_handle_color};
					}
				";
			}

			if ( ! empty( $scrollbar_track_color ) ) {
				$style .= "
					::-webkit-scrollbar-track {
						background-color: {$scrollbar_track_color};
					}
				";
			}
		}

		wp_add_inline_style( 'pimp-my-site', $style );

		/**
		 * Scripts
		 */
		wp_enqueue_script( 'tsparticles', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/vendor/tsparticles.bundle.min.js', array(), '2.1.3', true );
		wp_enqueue_script( 'jquery-particles', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/vendor/jquery.particles.js', array( 'jquery', 'tsparticles' ), '2.1.3', true );
		wp_add_inline_script( 'jquery-particles', "$ = jQuery;", 'before' );
		wp_add_inline_script( 'jquery-particles', "jQuery.noConflict();", 'after' );

		wp_enqueue_script( 'pimp-my-site', PIMP_MY_SITE_PLUGIN_DIR_URL . 'assets/js/main.js', array( 'jquery', 'jquery-particles' ), PIMP_MY_SITE_VERSION, true );
		wp_localize_script( 'pimp-my-site', 'pimpMySite', array(
			'pluginUrl'      => PIMP_MY_SITE_PLUGIN_DIR_URL,
			'enabled'        => ! empty( $pimp_my_site_admin->settings->get_option( 'particles_enabled' ) ),
			'colors'         => $pimp_my_site_admin->settings->get_option( 'particles_colors' ),
			'opacity'        => $pimp_my_site_admin->settings->get_option( 'particles_opacity' ),
			'shapes'         => $pimp_my_site_admin->settings->get_option( 'particles_shapes' ),
			'size'           => $pimp_my_site_admin->settings->get_option( 'particles_size' ),
			'direction'      => $pimp_my_site_admin->settings->get_option( 'particles_direction' ),
			'speed'          => $pimp_my_site_admin->settings->get_option( 'particles_speed' ),
			'lifetime'       => $pimp_my_site_admin->settings->get_option( 'particles_lifetime' ),
			'density'        => $pimp_my_site_admin->settings->get_option( 'particles_density' ),
		) );
	}

	/**
	 * Display stickers
	 */
	public function stickers() {
		/** @var Pimp_My_Site_Admin $pimp_my_site_admin */
		global $pimp_my_site_admin;

		if ( ! $pimp_my_site_admin->settings->get_option( 'enabled' ) || ( wp_is_mobile() && ! $pimp_my_site_admin->settings->get_option( 'mobile_enabled' ) ) ) {
			return;
		}

		$top_left_sticker     = $pimp_my_site_admin->settings->get_option( 'top_left_sticker' );
		$top_right_sticker    = $pimp_my_site_admin->settings->get_option( 'top_right_sticker' );
		$bottom_left_sticker  = $pimp_my_site_admin->settings->get_option( 'bottom_left_sticker' );
		$bottom_right_sticker = $pimp_my_site_admin->settings->get_option( 'bottom_right_sticker' );
		$top_sticker          = $pimp_my_site_admin->settings->get_option( 'top_sticker' );
		$bottom_sticker       = $pimp_my_site_admin->settings->get_option( 'bottom_sticker' );

		?>
		<div class="pimp-my-site-sticker-board">

			<?php if ( ! empty( $top_left_sticker ) ) : ?>
				<div class="pimp-my-site-sticker-board__top-left-sticker-wrapper">
					<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/stickers/{$top_left_sticker}.svg" ) ?>" alt="<?php esc_attr_e( 'Decoration sticker', 'pimp-my-site' ); ?>" class="pimp-my-site-sticker-board__top-left-sticker">
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $top_right_sticker ) ) : ?>
				<div class="pimp-my-site-sticker-board__top-right-sticker-wrapper">
					<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/stickers/{$top_right_sticker}.svg" ) ?>" alt="<?php esc_attr_e( 'Decoration sticker', 'pimp-my-site' ); ?>" class="pimp-my-site-sticker-board__top-right-sticker">
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $bottom_left_sticker ) ) : ?>
				<div class="pimp-my-site-sticker-board__bottom-left-sticker-wrapper">
					<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/stickers/{$bottom_left_sticker}.svg" ) ?>" alt="<?php esc_attr_e( 'Decoration sticker', 'pimp-my-site' ); ?>" class="pimp-my-site-sticker-board__bottom-left-sticker">
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $bottom_right_sticker ) ) : ?>
				<div class="pimp-my-site-sticker-board__bottom-right-sticker-wrapper">
					<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/stickers/{$bottom_right_sticker}.svg" ) ?>" alt="<?php esc_attr_e( 'Decoration sticker', 'pimp-my-site' ); ?>" class="pimp-my-site-sticker-board__bottom-right-sticker">
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $top_sticker ) ) : ?>
			<div class="pimp-my-site-sticker-board__top-sticker-wrapper">
				<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/stickers/{$top_sticker}.svg" ) ?>" alt="<?php esc_attr_e( 'Decoration sticker', 'pimp-my-site' ); ?>" class="pimp-my-site-sticker-board__top-sticker">
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $bottom_sticker ) ) : ?>
				<div class="pimp-my-site-sticker-board__bottom-sticker-wrapper">
					<img loading="lazy" src="<?php echo esc_url( PIMP_MY_SITE_PLUGIN_DIR_URL . "assets/images/stickers/{$bottom_sticker}.svg" ) ?>" alt="<?php esc_attr_e( 'Decoration sticker', 'pimp-my-site' ); ?>" class="pimp-my-site-sticker-board__bottom-sticker">
				</div>
			<?php endif; ?>

		</div>
		<?php
	}
}
