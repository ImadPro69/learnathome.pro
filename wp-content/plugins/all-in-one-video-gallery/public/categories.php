<?php

/**
 * Categories
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
 * AIOVG_Public_Categories class.
 *
 * @since 1.0.0
 */
class AIOVG_Public_Categories {

	/**
	 * The detault shortcode attribute values.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array     $defaults An associative array of defaults.
	 */
	protected $defaults = array();
	
	/**
	 * Get things started.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Register shortcode(s)
		add_shortcode( "aiovg_categories", array( $this, "run_shortcode_categories" ) );
	}

	/**
	 * Load more categories.
	 *
	 * @since 2.5.1
	 */
	public function ajax_callback_load_more_categories() {
		// Security check
		check_ajax_referer( 'aiovg_ajax_nonce', 'security' );

		// Proceed safe
		$json = array();
		$json['html'] = $this->run_shortcode_categories( $_POST );
		
		wp_send_json_success( $json );
	}
	
	/**
	 * Run the shortcode [aiovg_categories].
	 *
	 * @since 1.0.0
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_categories( $atts ) {	
		// Vars
		$attributes = shortcode_atts( $this->get_defaults(), $atts, 'aiovg_categories' );		
		$attributes['shortcode'] = 'aiovg_categories';	
		$attributes['ratio'] = ! empty( $attributes['ratio'] ) ? (float) $attributes['ratio'] . '%' : '56.25%';
		
		$template = sanitize_file_name( $attributes['template'] );

		// Enqueue dependencies
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-public' );

		if ( 'dropdown' == $template ) {
			wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-public' );
		}
		
		// Process output
		if ( 'list' == $template ) {
			$args = array(
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
			
			if ( ! empty( $attributes['include'] ) ) { // Include category IDs
				$args['include'] = array_map( 'intval', explode( ',', $attributes['include'] ) );
			} else {
				if ( $args['hierarchical'] ) {
					$args['child_of'] = (int) $attributes['id'];
				} else {
					$args['parent'] = (int) $attributes['id'];
				}				
			}

			if ( ! empty( $attributes['exclude'] ) ) { // Exclude category IDs
				$args['exclude'] = array_map( 'intval', explode( ',', $attributes['exclude'] ) );
			}
			
			$args = apply_filters( 'aiovg_categories_args', $args, $attributes );
			$categories_li = wp_list_categories( $args ); 

			if ( ! empty( $categories_li ) ) {
				ob_start();
				include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/categories-template-list.php" );
				return ob_get_clean();				
			} else {
				return sprintf(
					'<div class="aiovg-shortcode-categories aiovg-no-items-found">%s</div>',
					esc_html( aiovg_get_message( 'categories_empty' ) )
				);
			}
		} elseif ( 'dropdown' == $template ) {		
			ob_start();
			include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/categories-template-dropdown.php" );
			return ob_get_clean();	
		} else {
			$args = array(			
				'taxonomy'     => 'aiovg_categories',
				'orderby'      => sanitize_text_field( $attributes['orderby'] ), 
				'order'        => sanitize_text_field( $attributes['order'] ),
				'hide_empty'   => (int) $attributes['hide_empty'],
				'hierarchical' => false
			);

			if ( ! empty( $attributes['include'] ) ) { // Include category IDs
				$args['include'] = array_map( 'intval', explode( ',', $attributes['include'] ) );
			} else {
				$args['child_of'] = (int) $attributes['id'];
				$args['pad_counts'] = true;
			}

			if ( ! empty( $attributes['exclude'] ) ) { // Exclude category IDs
				$args['exclude'] = array_map( 'intval', explode( ',', $attributes['exclude'] ) );
			}

			$args = apply_filters( 'aiovg_categories_args', $args, $attributes );
			$terms = get_terms( $args );			
			
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				if ( isset( $args['child_of'] ) ) {
					foreach ( $terms as $key => $term ) {
						if ( $term->parent != $args['child_of'] ) {
							unset( $terms[ $key ] );
						}
					}

					$terms = array_values( $terms );
				}

				// Pagination				
				$attributes['terms_per_page'] = (int) $attributes['limit'];

				if ( $attributes['terms_per_page'] > 0 ) {
					$offset = 0;

					if ( ! empty( $attributes['show_pagination'] ) ) {
						$attributes['count'] = count( $terms );
						$attributes['max_num_pages'] = ceil( $attributes['count'] / $attributes['terms_per_page'] );	
						
						$offset = ( $attributes['paged'] - 1 ) * $attributes['terms_per_page'];
					}
					
					$terms = array_slice( $terms, $offset, $attributes['terms_per_page'] );
				} else {
					$attributes['show_pagination'] = 0;
				}

				ob_start();
				include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/categories-template-grid.php" );
				return ob_get_clean();		
			} else {
				return sprintf(
					'<div class="aiovg-shortcode-categories aiovg-no-items-found">%s</div>',
					esc_html( aiovg_get_message( 'categories_empty' ) )
				);
			}			
		}	
	}	

	/**
	 * Get the default shortcode attribute values.
	 *
	 * @since  1.0.0
	 * @return array $atts An associative array of attributes.
	 */
	public function get_defaults() {	
		if ( empty( $this->defaults ) ) {
			$pagination_settings = get_option( 'aiovg_pagination_settings', array() ); 

			$fields = aiovg_get_shortcode_fields();

			foreach ( $fields['categories']['sections'] as $section ) {
				foreach ( $section['fields'] as $field ) {
					$this->defaults[ $field['name'] ] = $field['value'];
				}
			}			
			
			$this->defaults['source'] = 'categories';
			$this->defaults['count'] = 0;
			$this->defaults['paged'] = aiovg_get_page_number();
			$this->defaults['pagination_ajax'] = isset( $pagination_settings['ajax'] ) && ! empty( $pagination_settings['ajax'] ) ? 1 : 0;
		}

		$this->defaults['uid'] = aiovg_get_uniqid();
		
		return $this->defaults;
	}
	
}
