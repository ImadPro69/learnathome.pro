<?php

/**
 * Class Grimlock_Term_Query_Component
 *
 * @author  themosaurus
 * @package grimlock/inc/components
 */
class Grimlock_Term_Query_Component extends Grimlock_Component {

	/**
	 * @var array List of layouts that require swiper.js
	 */
	protected $slider_layouts;

	/**
	 * Setup class.
	 *
	 * @param array $props
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'term_thumbnail_size' => 'large',
			'terms_layout'        => '12-cols-classic',
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
		if ( in_array( $this->props['terms_layout'], $this->slider_layouts ) && ( ! wp_script_is( 'swiper' ) || ! wp_script_is( 'grimlock-swiper' ) ) ) {
			wp_enqueue_style( 'swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/vendor/swiper-bundle.min.css', array(), '4.4.6' );
			wp_enqueue_script( 'swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/vendor/swiper-bundle.min.js', array(), '4.4.6', true );
			wp_enqueue_script( 'grimlock-swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/swiper.js', array( 'swiper', 'jquery' ), GRIMLOCK_VERSION, true );
		}
	}

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes   = parent::get_class( $class );
		$classes[] = 'grimlock-term-query';
		$classes[] = "grimlock-terms--{$this->props['terms_layout']}";

		if ( isset( $this->props['query'] ) && $this->props['query'] instanceof WP_Term_Query ) {
			$classes[] = $this->props['query']->query_vars['number'] > 0 ? "grimlock-terms--per-page-{$this->props['query']->query_vars['number']}" : "grimlock-terms--per-page-unlimited";
			$classes[] = "grimlock-terms--taxonomy-{$this->props['query']->query_vars['taxonomy'][0]}";
		}

		return array_unique( $classes );
	}

	/**
	 * Retrieve the classes for a single term in the query as an array.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_term_class( $class = '' ) {
		$classes = $this->parse_array( $class );

		$classes[] = 'term';

		if ( in_array( $this->props['terms_layout'], $this->slider_layouts ) ) {
			$classes[] = 'swiper-slide';
		}

		$classes = apply_filters( 'grimlock_query_term_class', $classes );

		return array_unique( $classes );
	}

	/**
	 * Display the classes for a single term in the query
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	public function render_term_class( $class = '' ) {
		$classes = $this->get_term_class( $class );
		$this->output_class( $classes );
	}

	/**
	 * Get data attributes as property-values pairs for the component using props.
	 */
	public function get_data_attributes() {
		$data_attributes = parent::get_data_attributes();

		if ( in_array( $this->props['terms_layout'], $this->slider_layouts ) ) {
			$data_attributes['auto-slide-enabled']    = $this->props['auto_slide_enabled'];
			$data_attributes['slider-arrows-enabled'] = $this->props['slider_arrows_enabled'];
			$data_attributes['slider-pagination']     = $this->props['slider_pagination'];
			$data_attributes['slider-loop-enabled']   = $this->props['slider_loop_enabled'];

			switch ( $this->props['terms_layout'] ) {
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
	 * Render HTML before the loop
	 */
	public function render_before_loop() {
		if ( in_array( $this->props['terms_layout'], $this->slider_layouts ) ) : ?>

			<div class="swiper-container">
				<div class="swiper-wrapper">

		<?php endif;
	}

	/**
	 * Render HTML after the loop
	 */
	public function render_after_loop() {
		if ( in_array( $this->props['terms_layout'], $this->slider_layouts ) ) : ?>

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
	 * Display the current component.
	 */
	public function render() {
		$has_query = isset( $this->props['query'] ) && $this->props['query'] instanceof WP_Term_Query;
		if ( $this->is_displayed() && $has_query && ! empty( $terms = $this->props['query']->get_terms() ) ) : ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>

				<?php $this->render_before_loop(); ?>
				<?php foreach ( $terms as $term ) :
					$term_classes = array( "term-{$term->term_id}", "term--{$term->taxonomy}" );
					if ( ! empty( grimlock_get_term_thumbnail_id( $term->term_id ) ) ) {
						$term_classes[] = 'term--has-thumbnail';
					} ?>

					<article id="term-<?php echo esc_attr( uniqid() ); ?>" <?php $this->render_term_class( $term_classes ) ?>>
						<?php
						$props = array_merge ( (array) $term, array(
							'term_thumbnail_size' => $this->props['term_thumbnail_size'],
						) );

						if ( has_action( "grimlock_term_query_{$term->taxonomy}" ) ) :
							do_action( "grimlock_term_query_{$term->taxonomy}", $props );
						else :
							do_action( 'grimlock_term_query_category', $props );
						endif; ?>
					</article><!-- #term-## -->

				<?php endforeach; ?>
				<?php $this->render_after_loop(); ?>

			</<?php $this->render_el(); ?>>
		<?php endif;
	}
}