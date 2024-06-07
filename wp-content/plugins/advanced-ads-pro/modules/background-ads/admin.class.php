<?php

/**
 * Background ads placement module.
 */
class Advanced_Ads_Pro_Module_Background_Ads_Admin {

	public function __construct() {
	    // stop, if main plugin doesn’t exist
	    if ( ! class_exists( 'Advanced_Ads', false ) ) {
		return;
	    }

	    // add background ads placement
	    add_action( 'advanced-ads-placement-types', [ $this, 'add_placement' ] );
	    // content of background ads placement
	    add_action( 'advanced-ads-placement-options-after-advanced', [ $this, 'placement_options' ], 10, 2 );

	    add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
	    add_action( 'advanced-ads-placements-list-after', [ $this, 'placements_list_after' ] );

	}

	/**
	 * Add the placement definition to the placement's repository.
	 *
	 * @param array $types Existing placement definitions.
	 *
	 * @return array
	 */
	public function add_placement( $types ) {
		$types['background'] = [
			'title'       => __( 'Background Ad', 'advanced-ads-pro' ),
			'description' => __( 'Background of the website behind the main wrapper.', 'advanced-ads-pro' ),
			'image'       => AAP_BASE_URL . 'modules/background-ads/assets/img/background.png',
			'order'       => 70,
			'options'     => [
				'allowed_ad_types' => [ 'image', 'plain' ],
			],
		];

		return $types;
	}

	public function placement_options( $placement_slug = '', $placement = [] ){
	    if( 'background' === $placement['type'] ){
		    $bg_color = ( isset($placement['options']['bg_color']) ) ? $placement['options']['bg_color'] : '';
		    $option_content = '<input type="text" value="'. $bg_color .'" class="advads-bg-color-field" name="advads[placements]['. $placement_slug . '][options][bg_color]"/>';
		    $description = __( 'Select a background color in case the background image is not high enough to cover the whole screen.', 'advanced-ads-pro' );
		    if( class_exists( 'Advanced_Ads_Admin_Options' ) ){
			Advanced_Ads_Admin_Options::render_option(
				'placement-background-color',
				__( 'background', 'advanced-ads-pro' ),
				$option_content,
				$description );
		    }
	    }

	}

	/**
	 * add color picker script to placements page
	 *
	 * @since 1.8
	 */
	function admin_scripts( ) {

	    if( ! class_exists( 'Advanced_Ads_Admin' ) ) {
		    return;
	    };

	    $screen = get_current_screen();
	    if ( 'advanced-ads_page_advanced-ads-placements' === $screen->id ){
		    // add color picker script
		    wp_enqueue_style( 'wp-color-picker' );
		    wp_enqueue_script( 'wp-color-picker' );
	    }
	}

	/**
	 * render content after the placements list
	 *  activate color picker fields
	 *
	 * @since 1.8
	 * @param type $placements array with placements
	 */
	public function placements_list_after( $placements = [] ){
		?><script>
		jQuery(document).ready(function($){
			jQuery( '.advads-bg-color-field' ).wpColorPicker( {
				change: ( e, ui ) => {
					e.target.value = ui.color.toString();
					e.target.dispatchEvent( new Event( 'change' ) );
				},
				clear: e => {
					if ( e.type === 'change' ) {
						return;
					}

					jQuery( e.target ).parent().find( '.advads-bg-color-field' )[0].dispatchEvent( new Event( 'change' ) );
				},
			} );
		});
		</script><?php
	}
}
