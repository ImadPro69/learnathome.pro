<?php

/**
 * Class Grimlock_Query_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-query/inc/components
 */
class Grimlock_Query_Component extends Grimlock_Component {
	/**
	 * @var array List of layouts that require swiper.js
	 */
	protected $slider_layouts;

	/**
	 * Setup class.
	 *
	 * @param array $props
	 * @since 1.0.0
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'post_thumbnail_size' => 'large',
			'posts_layout'        => '12-cols-classic',
		) ) );

		$this->slider_layouts = array(
			'12-cols-overlay-slider',
			'6-6-cols-overlay-slider',
			'4-4-4-cols-overlay-slider',
			'3-3-3-3-cols-overlay-slider',
			'12-cols-classic-slider',
			'6-6-cols-classic-slider',
			'4-4-4-cols-classic-slider',
			'3-3-3-3-cols-classic-slider',
		);

		// Enqueue swiper scripts if the layout is a slider
		if ( in_array( $this->props['posts_layout'], $this->slider_layouts ) && ( ! wp_script_is( 'swiper' ) || ! wp_script_is( 'grimlock-swiper' ) ) ) {
			wp_enqueue_style( 'swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/vendor/swiper-bundle.min.css', array(), '4.4.6' );
			wp_enqueue_script( 'swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/vendor/swiper-bundle.min.js', array(), '4.4.6', true );
			wp_enqueue_script( 'grimlock-swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/swiper.js', array( 'swiper', 'jquery' ), GRIMLOCK_VERSION, true );
		}
	}

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes   = parent::get_class( $class );
		$classes[] = 'grimlock-query';
		$classes[] = "grimlock-posts--{$this->props['posts_layout']}";

		if ( isset( $this->props['query'] ) && $this->props['query'] instanceof WP_Query ) {
			$classes[] = $this->props['query']->get( 'posts_per_page' ) > 0 ? "grimlock-posts--per-page-{$this->props['query']->get( 'posts_per_page' )}" : "grimlock-posts--per-page-unlimited";
			$classes[] = "grimlock-posts--type-{$this->props['query']->get( 'post_type' )}";
		}

		return array_unique( $classes );
	}

	/**
	 * Retrieve the classes for a single post in the query as an array.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_post_class( $class = '' ) {
		$classes = $this->parse_array( $class );

		if ( in_array( $this->props['posts_layout'], $this->slider_layouts ) ) {
			$classes[] = 'swiper-slide';
		}

		$classes = apply_filters( 'grimlock_query_post_class', $classes );

		return array_unique( $classes );
	}

	/**
	 * Display the classes for a single post in the query
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	public function render_post_class( $class = '' ) {
		$classes = $this->get_post_class( $class );
		post_class( $classes );
	}

	/**
	 * Get props for the items in the loop
	 */
	public function get_item_props() {
		$item_props = $this->props;

		if ( $this->props['query']->current_post === 0 && $this->props['posts_layout'] === '8-4-cols-featured-grid' ) {
			$item_props['post_thumbnail_size'] = 'thumbnail-12-cols-classic';
		}

		return apply_filters( 'grimlock_query_item_props', $item_props );
	}

	/**
	 * Render HTML before the loop
	 */
	public function render_before_loop() {
		if ( in_array( $this->props['posts_layout'], $this->slider_layouts ) ) : ?>

			<div class="swiper-container">
				<div class="swiper-wrapper">

		<?php endif;
	}

	/**
	 * Render HTML after the loop
	 */
	public function render_after_loop() {
		if ( in_array( $this->props['posts_layout'], $this->slider_layouts ) ) : ?>

					</div><!-- .swiper-wrapper -->

				<?php if ( ! empty( $this->props['slider_pagination'] ) ) : ?>

					<div class="swiper-pagination"></div><!-- swiper-pagination -->

				<?php endif; ?>

			</div><!-- .swiper-container -->


			<?php if ( $this->props['slider_arrows_enabled'] ) : ?>

				<div class="swiper-button-prev"></div>
				<div class="swiper-button-next"></div>

			<?php endif;

		endif;
	}

	/**
	 * Get data attributes as property-values pairs for the component using props.
	 *
	 * @since 1.4.1
	 */
	public function get_data_attributes() {
		$data_attributes = parent::get_data_attributes();

		if ( in_array( $this->props['posts_layout'], $this->slider_layouts ) ) {
			$data_attributes['auto-slide-enabled']    = $this->props['auto_slide_enabled'];
			$data_attributes['slider-arrows-enabled'] = $this->props['slider_arrows_enabled'];
			$data_attributes['slider-pagination']     = $this->props['slider_pagination'];
			$data_attributes['slider-loop-enabled']   = $this->props['slider_loop_enabled'];

			switch ( $this->props['posts_layout'] ) {
				case '12-cols-overlay-slider':
				case '12-cols-classic-slider':
					$data_attributes['slides-per-view'] = 1;
					$data_attributes['slider-transition'] = $this->props['slider_transition'];
					break;
				case '6-6-cols-overlay-slider':
				case '6-6-cols-classic-slider':
					$data_attributes['slides-per-view'] = 2;
					break;
				case '3-3-3-3-cols-overlay-slider':
				case '3-3-3-3-cols-classic-slider':
					$data_attributes['slides-per-view'] = 4;
					break;
				case '4-4-4-cols-overlay-slider':
				case '4-4-4-cols-classic-slider':
				default:
					$data_attributes['slides-per-view'] = 3;
					$data_attributes['slider-transition'] = 'slide';
					break;
			}
		}

		return $data_attributes;
	}

	/**
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {

		$has_query = isset( $this->props['query'] ) && $this->props['query'] instanceof WP_Query;

		$switch_to_blog = is_multisite() && ! empty( $this->props['site'] ) && ! empty( get_site( $this->props['site'] ) );
		if ( $switch_to_blog ) {
			switch_to_blog( $this->props['site'] );
		}

		if ( $this->is_displayed() && $has_query && $this->props['query']->have_posts() ) : ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>

			<?php
			$this->render_before_loop();
			while ( $this->props['query']->have_posts() ) : $this->props['query']->the_post(); ?>

				<article id="post-<?php echo esc_attr( uniqid() ); ?>" <?php $this->render_post_class( is_sticky() ? 'sticky' : '' ); ?>>
					<?php
					$post_type = get_post_type();

					if ( has_action( "grimlock_query_{$post_type}" ) ) :
						do_action( "grimlock_query_{$post_type}", $this->get_item_props() );
					else :
						do_action( 'grimlock_query_post', $this->get_item_props() );
					endif; ?>
				</article><!-- #post-## -->

			<?php
			endwhile;

			if ( function_exists( 'restore_current_blog' ) ) {
				restore_current_blog();
			}

			wp_reset_postdata();
			$this->render_after_loop(); ?>

			</<?php $this->render_el(); ?>><!-- .grimlock-region -->
		<?php endif;
	}
}
