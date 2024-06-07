<?php
/**
 * Background ads class.
 */
class Advanced_Ads_Pro_Module_Background_Ads {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_footer', [ $this, 'footer_injection' ], 20 );

		// Register output change hook.
		add_filter( 'advanced-ads-output-final', [ $this, 'ad_output' ], 20, 3 );
	}

	public function footer_injection(){
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
		    return;
		}

		// get placements
		$placements = get_option( 'advads-ads-placements', [] );
		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( isset($_placement['type']) && 'background' == $_placement['type'] ){
					// display the placement content with placement options
					$_options = isset( $_placement['options'] ) ? $_placement['options'] : [];
					echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, 'placement', $_options );
				}
			}
		}
	}

	/**
	 * Change ad output.
	 *
	 * @param string          $output Ad output.
	 * @param Advanced_Ads_Ad $ad Ad object.
	 * @param array           $output_options Output options.
	 * @return string
	 */
	public function ad_output( $output, $ad, $output_options ) {
		if ( ! isset( $ad->args['placement_type'] ) || 'background' !== $ad->args['placement_type'] ) {
			return $output;
		}

		if( !isset( $ad->type ) || 'image' !== $ad->type ){
			return $output;
		}

		// get background color
		$bg_color = isset( $ad->args['bg_color'] ) ? sanitize_text_field( $ad->args['bg_color'] ) : false;

		// get prefix and generate new body class
		$prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
		$class = $prefix . 'body-background';

		// get image
		if( isset( $ad->output['image_id'] ) ){
		    $image = wp_get_attachment_image_src( $ad->output['image_id'], 'full' );
		    if ( $image ) {
			list( $image_url, $image_width, $image_height ) = $image;
		    }
		}

		if( empty( $image_url ) ){
		    return $output;
		}

		$selector = apply_filters( 'advanced-ads-pro-background-selector', 'body' );
		$is_amp   = function_exists( 'advads_is_amp' ) && advads_is_amp();
		$link     = ! empty( $ad->url ) ? $ad->url : '';
		/**
		 * Filter the background placement URL.
		 *
		 * @param string $link The URL.
		 * @param Advanced_Ads_Ad $ad The current ad object.
		 */
		$link     = (string) apply_filters( 'advanced-ads-pro-background-url', $link, $ad );

		if ( method_exists( 'Advanced_Ads_Tracking_Util', 'get_target' ) ) {
			$target = Advanced_Ads_Tracking_Util::get_target( $ad, true );
		} else {
			$options = Advanced_Ads::get_instance()->options();
			$target  = isset( $options['target-blank'] ) ? '_blank' : '';
		}
		$target = $target !== '' ? $target : '_self';

		ob_start();
		?><style><?php echo $selector; ?> {
			    background: url(<?php echo $image_url; ?>) no-repeat fixed;
			    background-size: 100% auto;
			<?php if( $bg_color ) : ?>
			    background-color: <?php echo $bg_color; ?>;
			<?php endif; ?>
		    }
			<?php if ( $link && ! $is_amp ) : ?>
		    <?php /**
		    * We should not use links and other tags that should have cursor: pointer as direct childs of the $selector.
		    * That is, we need a nested container (e.g. body > div > a) to make it work corretly. */
		    echo $selector; ?> { cursor: pointer; } <?php echo $selector; ?> > * { cursor: default; }
		<?php endif; ?>
		</style>
		<?php
		/**
		 * Don't load any javascript on amp.
		 * Javascript output can be prevented by disabling click tracking and empty url field on ad level.
		 */
		if ( ! $is_amp ) :
			?>
			<script>
				( window.advanced_ads_ready || document.readyState === 'complete' ).call( null, function () {
					document.querySelector( '<?php echo esc_attr( $selector ); ?>' ).classList.add( '<?php echo esc_attr( $class ); ?>' );
					<?php if ( $link ) : ?>
					// Use event delegation because $selector may be not in the DOM yet.
					document.addEventListener( 'click', function ( e ) {
						if ( e.target.matches( '<?php echo $selector; ?>' ) ) {
							<?php
							$script = '';
							/**
							 * Add additional script output.
							 *
							 * @param string          $script The URL.
							 * @param Advanced_Ads_Ad $ad     The current ad object.
							 */
							$script = (string) apply_filters( 'advanced-ads-pro-background-click-matches-script', $script, $ad );
							// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- this is our own JS code, escaping will break it
							echo $script;
							?>
							// Open url in new tab.
							window.open( '<?php echo esc_url( $link ); ?>', '<?php echo esc_attr( $target ); ?>' );
						}
					} );
					<?php endif; ?>
				} );
			</script>
		<?php endif; ?>
		<?php

		// add content of Custom Code option here since the normal hook can’t be used.
		$output_options = $ad->options( 'output' );

		if ( ! empty( $output_options['custom-code'] ) ) {
			echo $output_options['custom-code'];
		}

		return ob_get_clean();

		//return $output;

	}
}
