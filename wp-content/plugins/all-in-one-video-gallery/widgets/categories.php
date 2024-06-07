<?php

/**
 * Categories Widget.
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
 * AIOVG_Widget_Categories class.
 *
 * @since 1.0.0
 */
class AIOVG_Widget_Categories extends WP_Widget {
	
	/**
     * Unique identifier for the widget.
     *
     * @since  1.0.0
	 * @access protected
     * @var    string
     */
    protected $widget_slug;
	
	/**
     * Default settings.
     *
     * @since  1.0.0
	 * @access private
     * @var    array
     */
    private $defaults;
	
	/**
	 * Get things started.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {		
		$this->widget_slug = 'aiovg-widget-categories';

		$categories_settings = get_option( 'aiovg_categories_settings' );
		$images_settings     = get_option( 'aiovg_images_settings' );
		
		$this->defaults = array(
			'title'            => __( 'Video Categories', 'all-in-one-video-gallery' ),
			'template'         => 'list',			
			'child_of'         => 0,			
			'columns'          => 1,
			'limit'            => 0,
			'ratio'            => ! empty( $images_settings['ratio'] ) ? (float) $images_settings['ratio'] . '%' : '56.25%',
			'orderby'          => $categories_settings['orderby'],
            'order'            => $categories_settings['order'],
			'hierarchical'     => 1,	
			'show_description' => ! empty( $categories_settings['show_description'] ) ? 1 : 0,
			'show_count'       => $categories_settings['show_count'],
			'hide_empty'       => $categories_settings['hide_empty'],
			'show_more'        => 0,
			'more_label'	   => __( 'Show More', 'all-in-one-video-gallery' ),
			'more_link'        => ''			
		);
		
		parent::__construct( 
			$this->widget_slug, __( 'AIOVG - Video Categories', 'all-in-one-video-gallery' ), 
			array( 
				'classname'   => $this->widget_slug,
				'description' => __( 'Display a list of video categories.', 'all-in-one-video-gallery' ),
			)
		);		
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.0.0
	 * @param array	$args	  The array of form elements.
	 * @param array $instance The current instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// Merge incoming $instance array with $defaults
		if ( count( $instance ) ) {
			$attributes = array_merge( $this->defaults, $instance );
		} else {
			$attributes = $this->defaults;
		}

		$attributes['id'] = $attributes['child_of'];
		unset( $attributes['title'] );

		$template = sanitize_file_name( $attributes['template'] );	

		// Enqueue dependencies
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-public' );
		
		if ( 'dropdown' == $template ) {
			wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-public' );
		}

		// Process output
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}			

		if ( 'list' == $template ) {
			$query = array(
				'taxonomy'         => 'aiovg_categories',
				'orderby'          => sanitize_text_field( $attributes['orderby'] ),
				'order'            => sanitize_text_field( $attributes['order'] ),
				'hide_empty'       => (int) $attributes['hide_empty'], 
				'hierarchical'     => (int) $attributes['hierarchical'],                
				'show_count'       => (int) $attributes['show_count'], 
				'show_option_none' => '',   
				'title_li'         => '',
				'echo'             => 0
			);

			if ( $query['hierarchical'] ) {
				$query['child_of'] = (int) $attributes['id'];
			} else {
				$query['parent'] = (int) $attributes['id'];
			}
			
			$query = apply_filters( 'aiovg_categories_args', $query, $attributes );
			$categories_li = wp_list_categories( $query ); 

			if ( ! empty( $categories_li ) ) {
				include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/categories-template-list.php" );
			} else {
				printf(
					'<div class="aiovg-widget-categories aiovg-no-items-found">%s</div>',
					esc_html( aiovg_get_message( 'categories_empty' ) )
				);
			}
		} elseif ( 'dropdown' == $template ) {
			include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/categories-template-dropdown.php" );
		} else {
			$query = array(	
				'taxonomy'	   => 'aiovg_categories',
				'parent'       => (int) $attributes['id'],
				'orderby'      => sanitize_text_field( $attributes['orderby'] ), 
				'order'        => sanitize_text_field( $attributes['order'] ),
				'hide_empty'   => (int) $attributes['hide_empty'],
				'hierarchical' => false
			);	

			$query = apply_filters( 'aiovg_categories_args', $query, $attributes );
			$terms = get_terms( $query );		
			
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$attributes['uid'] = aiovg_get_uniqid();
				$attributes['source'] = 'categories';
				$attributes['count'] = 0;
				$attributes['max_num_pages'] = 1;

				$limit = (int) $attributes['limit'];
				if ( ! empty( $limit ) ) {					
					$attributes['count'] = count( $terms );
					$attributes['max_num_pages'] = ceil( $attributes['count'] / $limit );
					
					$terms = array_slice( $terms, 0, $limit );
				}

				include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/categories-template-grid.php" );
			} else {
				printf(
					'<div class="aiovg-widget-categories aiovg-no-items-found">%s</div>',
					esc_html( aiovg_get_message( 'categories_empty' ) )
				);
			}		
		}
	
		echo $args['after_widget'];
	}
	
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since 1.0.0
	 * @param array $new_instance The new instance of values to be generated via the update.
	 * @param array $old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title']            = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';	
		$instance['template']         = isset( $new_instance['template'] ) ? sanitize_text_field( $new_instance['template'] ) : 'grid';	
		$instance['child_of']         = isset( $new_instance['child_of'] ) ? (int) $new_instance['child_of'] : 0;		
		$instance['columns']          = isset( $new_instance['columns'] ) ? (int) $new_instance['columns'] : 1;
		$instance['limit']            = isset( $new_instance['limit'] ) ? (int) $new_instance['limit'] : 0;
		$instance['orderby']          = isset( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : 'name';
		$instance['order']            = isset( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : 'asc';
		$instance['hierarchical']     = isset( $new_instance['hierarchical'] ) ? (int) $new_instance['hierarchical'] : 0;
		$instance['show_description'] = isset( $new_instance['show_description'] ) ? (int) $new_instance['show_description'] : 0;
		$instance['show_count']       = isset( $new_instance['show_count'] ) ? (int) $new_instance['show_count'] : 0;
		$instance['hide_empty']       = isset( $new_instance['hide_empty'] ) ? (int) $new_instance['hide_empty'] : 0;
		$instance['show_more']        = isset( $new_instance['show_more'] ) ? (int) $new_instance['show_more'] : 0;
		$instance['more_label']       = isset( $new_instance['more_label'] ) ? sanitize_text_field( $new_instance['more_label'] ) : '';	
		$instance['more_link']        = isset( $new_instance['more_link'] ) ? esc_url_raw( $new_instance['more_link'] ) : '';		
		
		return $instance;
	}
	
	/**
	 * Generates the administration form for the widget.
	 *
	 * @since 1.0.0
	 * @param array $instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {		
		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array) $instance,
			$this->defaults
		);
			
		// Display the admin form
		include AIOVG_PLUGIN_DIR . 'widgets/forms/categories.php';
	}
	
}
