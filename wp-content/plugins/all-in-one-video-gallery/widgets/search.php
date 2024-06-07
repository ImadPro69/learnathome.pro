<?php

/**
 * Search Widget.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Widget_Search class.
 *
 * @since 1.0.0
 */
class AIOVG_Widget_Search extends WP_Widget {

	/**
     * Unique identifier for the widget.
     *
     * @since  1.0.0
	 * @access protected
     * @var    string
     */
    protected $widget_slug;
	
	/**
 	 * Get things started.
     *
     * @since 1.0.0
     */
	public function __construct() {
		$this->widget_slug = 'aiovg-widget-search';
		
		$options = array( 
			'classname'   => $this->widget_slug,
			'description' => __( 'A videos search form for your site.', 'all-in-one-video-gallery' ),
		);
		
		parent::__construct( $this->widget_slug, __( 'AIOVG - Search Form', 'all-in-one-video-gallery' ), $options );		
	}

	/**
	 * Display the content of the widget.
	 *
	 * @since 1.0.0
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// Vars
		$page_settings = get_option( 'aiovg_page_settings' );
		
		$attributes = array(
			'template'       => isset( $instance['template'] ) ? sanitize_text_field( $instance['template'] ) : 'vertical',
			'search_page_id' => $page_settings['search'],
			'has_keyword'    => isset( $instance['has_keyword'] ) ? (int) $instance['has_keyword'] : 1,
			'has_category'   => isset( $instance['has_category'] ) ? (int) $instance['has_category'] : 0,
			'has_tag'        => isset( $instance['has_tag'] ) ? (int) $instance['has_tag'] : 0
		);

		if ( ! $attributes['has_category'] && ! $attributes['has_tag'] ) {
			$attributes['template'] = 'compact';
		}
		
		// Enqueue dependencies
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-public' );

		if ( $attributes['has_tag'] ) {
			wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-autocomplete' );
		}

		// Process output
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . 'public/templates/search-form-template-' . sanitize_file_name( $attributes['template'] ) . '.php' );
		
		echo $args['after_widget'];		
	}
	
	/**   
	 * Process widget options on save. 
	 * 
	 * @since 1.0.0
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {	
		$instance = array();
		
		$instance['title']        = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['template']     = isset( $new_instance['template'] ) ? sanitize_text_field( $new_instance['template'] ) : 'vertical';
		$instance['has_keyword']  = isset( $new_instance['has_keyword'] ) ? (int) $new_instance['has_keyword'] : 0;
		$instance['has_category'] = isset( $new_instance['has_category'] ) ? (int) $new_instance['has_category'] : 0;
		$instance['has_tag']      = isset( $new_instance['has_tag'] ) ? (int) $new_instance['has_tag'] : 0;
		
		return $instance;		
	}

	/**
	 * Display the options form on admin.
	 *
	 * @since 1.0.0
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {	
		// Define the array of defaults
		$defaults = array(
			'title'        => __( 'Search Videos', 'all-in-one-video-gallery' ),
			'template'     => 'vertical',
			'has_keyword'  => 1,
			'has_category' => 0,
			'has_tag'      => 0
		);
		
		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array) $instance,
			$defaults
		);
		
        include AIOVG_PLUGIN_DIR . 'widgets/forms/search.php';		                    
	}        
		
}