<?php

/**
 * Videos Widget.
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
 * AIOVG_Widget_Videos class.
 *
 * @since 1.0.0
 */
class AIOVG_Widget_Videos extends WP_Widget {
	
	/**
     * Unique identifier for the widget.
     *
     * @since  1.0.0
	 * @access protected
     * @var    string
     */
    protected $widget_slug;
	
	/**
     * Widget fields.
     *
     * @since  1.0.0
	 * @access private
     * @var    array
     */
	private $fields;

	/**
     * Excluded widget fields.
     *
     * @since  2.5.8
	 * @access private
     * @var    array
     */
	private $excluded_fields = array();
	
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
		$this->widget_slug = 'aiovg-widget-videos';		
		$this->fields = aiovg_get_shortcode_fields();
		$this->excluded_fields = array( 'ratio', 'title_length', 'excerpt_length' );
		$this->defaults = $this->get_defaults();
		
		parent::__construct(
			$this->widget_slug,
			__( 'AIOVG - Video Gallery', 'all-in-one-video-gallery' ),
			array(
				'classname'   => $this->widget_slug,
				'description' => __( 'Display a video gallery.', 'all-in-one-video-gallery' )
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
			foreach ( $this->excluded_fields as $excluded_field ) {
				if ( isset( $instance[ $excluded_field ] ) ) {
					unset( $instance[ $excluded_field ] ); // Always get this value from the global settings
				}
			}
			
			$attributes = array_merge( $this->defaults, $instance );
		} else {
			$attributes = $this->defaults;
		}
		
		if ( empty( $attributes['more_label'] ) ) {
			$attributes['more_label'] = __( 'Show More', 'all-in-one-video-gallery' );
		}

		// Added for backward compatibility (version < 1.5.7)
		if ( isset( $instance['image_position'] ) && 'left' == $instance['image_position'] ) {
			$attributes['thumbnail_style'] = 'image-left';
		}

		$attributes['uid'] = aiovg_get_uniqid();
		$attributes['show_count'] = 0;

		$orderby = sanitize_text_field( $attributes['orderby'] );
		$order   = sanitize_text_field( $attributes['order'] );
		
		// Define the query
		global $post;	

		$query = array(				
			'post_type'      => 'aiovg_videos',
			'posts_per_page' => ! empty( $attributes['limit'] ) ? (int) $attributes['limit'] : -1,
			'post_status'    => array( 'publish' )
		);
		
		// Taxonomy Parameters
		$tax_queries = array();					

		$category = array_map( 'intval', $attributes['category'] );
		$tag = array_map( 'intval', $attributes['tag'] );		

		$tax_relation = 'AND';		
	
		if ( $attributes['related'] ) {	
			$tax_relation = 'OR';

			if ( is_singular( 'aiovg_videos' ) ) {
				$categories = wp_get_object_terms( $post->ID, 'aiovg_categories', array( 'fields' => 'ids' ) );
				$category = ! empty( $categories ) ? $categories : '';

				$tags = wp_get_object_terms( $post->ID, 'aiovg_tags', array( 'fields' => 'ids' ) );
				$tag = ! empty( $tags ) ? $tags : '';
				
				$query['post__not_in'] = array( $post->ID );
			} else {			
				// Category page
				$term_slug = get_query_var( 'aiovg_category' );				
				if ( ! empty( $term_slug ) ) {		
					$term = get_term_by( 'slug', sanitize_text_field( $term_slug ), 'aiovg_categories' );
					$category = $term->term_id;
				}
				
				// Tag page
				$term_slug = get_query_var( 'aiovg_tag' );				
				if ( ! empty( $term_slug ) ) {		
					$term = get_term_by( 'slug', sanitize_text_field( $term_slug ), 'aiovg_tags' );
					$tag = $term->term_id;
				}
			}			
		}
		
		if ( ! empty( $category ) ) {		
			$tax_queries[] = array(
				'taxonomy'         => 'aiovg_categories',
				'field'            => 'term_id',
				'terms'            => $category,
				'include_children' => false,
			);					
		}
		
		if ( ! empty( $tag ) ) {		
			$tax_queries[] = array(
				'taxonomy'         => 'aiovg_tags',
				'field'            => 'term_id',
				'terms'            => $tag,
				'include_children' => false,
			);					
		}		
		
		$count_tax_queries = count( $tax_queries );
		if ( $count_tax_queries ) {
			$query['tax_query'] = ( $count_tax_queries > 1 ) ? array_merge( array( 'relation' => $tax_relation ), $tax_queries ) : $tax_queries;
		}
	
		// Custom Field (post meta) Parameters
		$meta_queries = array();		

		if ( 'likes' == $orderby ) { // Likes			
			$meta_queries['likes'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'likes',
					'compare' => 'NOT EXISTS'
				),
				array(
					'key'     => 'likes',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS'
				)
			);				
		}

		if ( 'dislikes' == $orderby ) { // Dislikes			
			$meta_queries['dislikes'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'dislikes',
					'compare' => 'NOT EXISTS'
				),
				array(
					'key'     => 'dislikes',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS'
				)
			);				
		}

		if ( ! empty( $attributes['featured'] ) ) {			
			$meta_queries['featured'] = array(
				'key'     => 'featured',
				'value'   => 1,
				'compare' => '='
			);				
		}

		$count_meta_queries = count( $meta_queries );
		if ( $count_meta_queries ) {
			$query['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : $meta_queries;
		}		
	
		// Order & Orderby Parameters
		switch ( $orderby ) {
			case 'likes':
			case 'dislikes':
				$query['orderby'] = array(
					$orderby => $order,
					'date'   => 'DESC'
				);
				break;

			case 'views':
				$query['meta_key'] = $orderby;
				$query['orderby']  = 'meta_value_num';
				
				$query['order']    = $order;
				break;

			case 'rand':
				$seed = aiovg_get_orderby_rand_seed();
				$query['orderby']  = 'RAND(' . $seed . ')';
				break;
				
			default:
				$query['orderby'] = $orderby;
				$query['order']   = $order;
		}
		
		$query = apply_filters( 'aiovg_query_args', $query, $attributes );
		$aiovg_query = new WP_Query( $query );
		
		// Enqueue dependencies
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-public' );
		
		// Process output
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		if ( $aiovg_query->have_posts() ) {			
			unset( $attributes['title'] );
				
			include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . 'public/templates/videos-template-classic.php', $attributes );		
		} else {		
			printf(
				'<div class="aiovg-widget-videos aiovg-no-items-found">%s</div>',
				esc_html( aiovg_get_message( 'videos_empty' ) )
			);
		}
		
		echo $args['after_widget'];
	}
	
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since 1.0.0
	 * @param array	$new_instance The new instance of values to be generated via the update.
	 * @param array $old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;		

		foreach ( $this->fields['videos']['sections'] as $section ) {
			foreach ( $section['fields'] as $field ) {
				$field_name = $field['name'];

				if ( in_array( $field_name, $this->excluded_fields ) ) {
					continue;
				}

				if ( 'categories' == $field['type'] ) {
					$instance['category'] = isset( $new_instance['category'] ) ? array_map( 'intval', $new_instance['category'] ) : array();
				} elseif ( 'tags' == $field['type'] ) {
					$instance['tag'] = isset( $new_instance['tag'] ) ? array_map( 'intval', $new_instance['tag'] ) : array();
				} elseif ( 'number' == $field['type'] ) {
					if ( ! empty( $new_instance[ $field_name ] ) ) {
						$instance[ $field_name ] = false === strpos( $new_instance[ $field_name ], '.' ) ? (int) $new_instance[ $field_name ] : (float) $new_instance[ $field_name ];
					} else {
						$instance[ $field_name ] = 0;
					}
				} elseif ( 'url' == $field['type'] ) {
					$instance[ $field_name ] = ! empty( $new_instance[ $field_name ] ) ? esc_url_raw( $new_instance[ $field_name ] ) : '';
				} elseif ( 'checkbox' == $field['type'] ) {
					$instance[ $field_name ] = isset( $new_instance[ $field_name ] ) ? (int) $new_instance[ $field_name ] : 0;
				} else {
					$instance[ $field_name ] = ! empty( $new_instance[ $field_name ] ) ? sanitize_text_field( $new_instance[ $field_name ] ) : '';
				}
			}
		}
		
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
		
		// Added for backward compatibility (version < 1.5.7)
		if ( isset( $instance['image_position'] ) && 'left' == $instance['image_position'] ) {
			$instance['thumbnail_style'] = 'image-left';
		}
			
		// Display the admin form
		include AIOVG_PLUGIN_DIR . 'widgets/forms/videos.php';
	}

	/**
	 * Get the default shortcode attribute values.
	 *
	 * @since  1.0.0
	 * @return array $atts An associative array of attributes.
	 */
	public function get_defaults() {
		$pagination_settings = get_option( 'aiovg_pagination_settings', array() ); 

		$defaults = array();

		foreach ( $this->fields['videos']['sections'] as $section ) {
			foreach ( $section['fields'] as $field ) {
				$defaults[ $field['name'] ] = $field['value'];
			}
		}

		foreach ( $this->fields['categories']['sections']['general']['fields'] as $field ) {
			if ( 'orderby' == $field['name'] || 'order' == $field['name'] ) {
				$defaults[ 'categories_' . $field['name'] ] = $field['value'];
			}
		}

		$defaults['source'] = 'videos';
		$defaults['count'] = 0;
		$defaults['paged'] = 1;
		$defaults['pagination_ajax'] = isset( $pagination_settings['ajax'] ) && ! empty( $pagination_settings['ajax'] ) ? 1 : 0;

		$defaults = array_merge(
			$defaults,
			array(
				'title'              => __( 'Video Gallery', 'all-in-one-video-gallery' ),
				'columns'            => 1,
				'thumbnail_style'    => 'image-left',				
				'ratio'              => ! empty( $defaults['ratio'] ) ? (float) $defaults['ratio'] . '%' : '56.25%',
				'show_pagination'    => 0			
			)
		);

		return $defaults;
	}
	
}