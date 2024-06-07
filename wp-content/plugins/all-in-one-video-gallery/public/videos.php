<?php

/**
 * Videos
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
 * AIOVG_Public_Videos class.
 *
 * @since 1.0.0
 */
class AIOVG_Public_Videos {

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
		add_shortcode( "aiovg_videos", array( $this, "run_shortcode_videos" ) );
		add_shortcode( "aiovg_category", array( $this, "run_shortcode_category" ) );
		add_shortcode( "aiovg_tag", array( $this, "run_shortcode_tag" ) );
		add_shortcode( "aiovg_search", array( $this, "run_shortcode_search" ) );
		add_shortcode( "aiovg_related_videos", array( $this, "run_shortcode_related_videos" ) );
		add_shortcode( "aiovg_user_videos", array( $this, "run_shortcode_user_videos" ) );
		add_shortcode( "aiovg_liked_videos", array( $this, "run_shortcode_liked_videos" ) );
		add_shortcode( "aiovg_disliked_videos", array( $this, "run_shortcode_disliked_videos" ) );
	}

	/**
	 * Run the shortcode [aiovg_videos].
	 *
	 * @since 1.0.0
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_videos( $atts ) {		
		$attributes = shortcode_atts( $this->get_defaults(), $atts, 'aiovg_videos' );
		$attributes['shortcode'] = 'aiovg_videos';
				
		if ( ! empty( $attributes['related'] ) ) {
			// Category page
			if ( $term_slug = get_query_var( 'aiovg_category' ) ) {         
				$term = get_term_by( 'slug', sanitize_text_field( $term_slug ), 'aiovg_categories' );
				$attributes['category'] = $term->term_id;
			}

			// Tag page
			if ( $term_slug = get_query_var( 'aiovg_tag' ) ) {         
				$term = get_term_by( 'slug', sanitize_text_field( $term_slug ), 'aiovg_tags' );
				$attributes['tag'] = $term->term_id;
			}
		}
		
		$content = $this->get_content( $attributes );
		
		if ( empty( $content ) ) {
			$content = sprintf(
				'<div class="aiovg-shortcode-videos aiovg-no-items-found">%s</div>',
				esc_html( aiovg_get_message( 'videos_empty' ) )
			);
		}
		
		return $content;		
	}
	
	/**
	 * Run the shortcode [aiovg_category].
	 *
	 * @since 1.0.0
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_category( $atts ) {	
		$term_slug = get_query_var( 'aiovg_category' );
		
		if ( ! empty( $term_slug ) ) {
			$term = get_term_by( 'slug', sanitize_title( $term_slug ), 'aiovg_categories' );
		} elseif ( ! empty( $atts['id'] ) ) {			
			$term = get_term_by( 'id', (int) $atts['id'], 'aiovg_categories' );
		}
		
		if ( isset( $term ) && ! empty( $term ) ) {
			$categories_settings = get_option( 'aiovg_categories_settings' );

			$content = '';
			
			if ( ! empty( $categories_settings['back_button'] ) ) {
				$page_settings = get_option( 'aiovg_page_settings' );

				$back_button_url  = get_permalink( $page_settings['category'] );
				$back_button_text = __( 'All Categories', 'all-in-one-video-gallery' );

				if ( $term->parent > 0 ) {
					$parent_term = get_term_by( 'id', $term->parent, 'aiovg_categories' );

					$back_button_url  = aiovg_get_category_page_url( $parent_term );
					$back_button_text = $parent_term->name;
				}

				$back_button_url = apply_filters( 'aiovg_back_to_categories_link', $back_button_url );

				if ( ! empty( $back_button_url ) ) {
					$icon = '<svg xmlns="http://www.w3.org/2000/svg" width="12px" height="12px" viewBox="0 0 20 20" fill="currentColor" class="aiovg-flex-shrink-0">
						<path fill-rule="evenodd" d="M4.72 9.47a.75.75 0 0 0 0 1.06l4.25 4.25a.75.75 0 1 0 1.06-1.06L6.31 10l3.72-3.72a.75.75 0 1 0-1.06-1.06L4.72 9.47Zm9.25-4.25L9.72 9.47a.75.75 0 0 0 0 1.06l4.25 4.25a.75.75 0 1 0 1.06-1.06L11.31 10l3.72-3.72a.75.75 0 0 0-1.06-1.06Z" clip-rule="evenodd" />
					</svg>';

					$content .= sprintf( 
						'<div class="aiovg aiovg-categories-nav"><a href="%s" class="aiovg-flex aiovg-gap-1 aiovg-items-center">%s %s</a></div>',
						esc_url( $back_button_url ),
						$icon,
						esc_html( $back_button_text )
					);						
				}
			}

			if ( ! empty( $term->description ) ) {
				$content .= sprintf( '<div class="aiovg aiovg-category-description">%s</div>', wp_kses_post( nl2br( $term->description ) ) );
			}

			// Videos
			$attributes = shortcode_atts( $this->get_defaults(), $atts, 'aiovg_category' );
			$attributes['shortcode'] = 'aiovg_category';
			$attributes['category'] = $term->term_id;

			$videos = $this->get_content( $attributes );
			
			// Sub Categories
			$_attributes = array();
			$_attributes[] = 'id="' . $term->term_id . '"';
			$_attributes[] = 'limit="0"';

			if ( ! empty( $videos ) ) {
				$_attributes[] = 'title="' . __( 'Sub Categories', 'all-in-one-video-gallery' ) . '"';
			}
			
			$sub_categories = do_shortcode( '[aiovg_categories ' . implode( ' ', $_attributes ) . ']' );				
			if ( strip_tags( $sub_categories ) == aiovg_get_message( 'categories_empty' ) ) {
				$sub_categories = '';
			}
			
			// ...
			if ( empty( $videos ) && empty( $sub_categories ) ) {
				$content .= sprintf(
					'<div class="aiovg-shortcode-category aiovg-no-items-found">%s</div>',
					esc_html( aiovg_get_message( 'videos_empty' ) )
				);
			} else {
				$content .= $videos;

				if ( ! empty( $videos ) && ! empty( $sub_categories ) ) {
					$content .= '<br />';
				}

				$content .= $sub_categories;
			}

			return $content;
		}
		
		return do_shortcode( '[aiovg_categories]' );	
	}

	/**
	 * Run the shortcode [aiovg_tag].
	 *
	 * @since 2.4.3
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_tag( $atts ) {	
		$term_slug = get_query_var( 'aiovg_tag' );
		
		if ( ! empty( $term_slug ) ) {
			$term = get_term_by( 'slug', sanitize_title( $term_slug ), 'aiovg_tags' );
		} elseif ( ! empty( $atts['id'] ) ) {			
			$term = get_term_by( 'id', (int) $atts['id'], 'aiovg_tags' );
		}
		
		if ( isset( $term ) && ! empty( $term ) ) {
			$page_settings = get_option( 'aiovg_page_settings' );
			$content = '';
	
			if ( ! empty( $term->description ) ) {
				$content .= sprintf( '<p class="aiovg-tag-description">%s</p>', wp_kses_post( nl2br( $term->description ) ) );
			}

			// Videos
			$attributes = shortcode_atts( $this->get_defaults(), $atts, 'aiovg_tag' );
			$attributes['shortcode'] = 'aiovg_tag';
			$attributes['tag'] = $term->term_id;

			$content .= $this->get_content( $attributes );

			if ( empty( $content ) ) {
				$content = sprintf(
					'<div class="aiovg-shortcode-tag aiovg-no-items-found">%s</div>',
					esc_html( aiovg_get_message( 'videos_empty' ) )
				);
			}
	
			return $content;
		} else {
			$args = array( 
				'taxonomy' => 'aiovg_tags',
				'number'   => 0, 
				'format'   => 'array',
				'echo'     => false 
			);

			$args = apply_filters( 'aiovg_tags_args', $args, $atts );
			$tags = wp_tag_cloud( $args );

			if ( ! empty( $tags ) ) {
				return sprintf( 
					'<div class="aiovg aiovg-tags-list">%s</div>',
					implode( "\n", $tags )
				);
			}
		}
		
		return sprintf(
			'<div class="aiovg-shortcode-tag aiovg-no-items-found">%s</div>',
			esc_html( aiovg_get_message( 'tags_empty' ) )
		);
	}
	
	/**
	 * Run the shortcode [aiovg_search].
	 *
	 * @since 1.0.0
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_search( $atts ) {	
		$attributes = shortcode_atts( $this->get_defaults(), $atts, 'aiovg_search' );
		$attributes['shortcode'] = 'aiovg_search';
		
		if ( isset( $_GET['vi'] ) ) {
			$attributes['search_query'] = $_GET['vi'];
		}
		
		if ( isset( $_GET['ca'] ) ) {
			$attributes['category'] = $_GET['ca'];
		}

		if ( ! isset( $_GET['ca'] ) || ( isset( $_GET['ca'] ) && empty( $_GET['ca'] ) ) ) {
			$categories_excluded = get_terms( array(
				'taxonomy'   => 'aiovg_categories',
				'hide_empty' => false,
				'fields'     => 'ids',
				'meta_key'   => 'exclude_search_form',
				'meta_value' => 1
			) );

			if ( ! empty( $categories_excluded ) && ! is_wp_error( $categories_excluded ) ) {
				$attributes['category_exclude'] = $categories_excluded;
			}
		}

		if ( isset( $_GET['ta'] ) ) {
			$attributes['tag'] = $_GET['ta'];
		}
		
		$content = $this->get_content( $attributes );
		
		if ( empty( $content ) ) {
			$content = sprintf(
				'<div class="aiovg-shortcode-search aiovg-no-items-found">%s</div>',
				esc_html( aiovg_get_message( 'videos_empty' ) )
			);
		}
		
		return $content;		
	}
	
	/**
	 * Run the shortcode [aiovg_related_videos].
	 *
	 * @since 3.6.4
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_related_videos( $atts ) {
		global $post;

		if ( ! isset( $post ) || ! is_singular( 'aiovg_videos' ) ) {
			return '';
		}

		$related_videos_settings = get_option( 'aiovg_related_videos_settings' );

		$post_id = (int) $post->ID;

		$attributes = array(
			'related'         => 1,
			'exclude'         => $post_id,
			'show_count'      => 0,
			'columns'         => (int) $related_videos_settings['columns'],
			'limit'           => (int) $related_videos_settings['limit'],
			'orderby'         => sanitize_text_field( $related_videos_settings['orderby'] ),
			'order'           => sanitize_text_field( $related_videos_settings['order'] ),
			'show_pagination' => isset( $related_videos_settings['display']['pagination'] )
		);

		$categories = wp_get_object_terms( $post_id, 'aiovg_categories', array( 'fields' => 'ids' ) );
		if ( ! empty( $categories ) ) {
			$attributes['category'] = $categories;
		}

		$tags = wp_get_object_terms( $post_id, 'aiovg_tags', array( 'fields' => 'ids' ) );
		if ( ! empty( $tags ) ) {
			$attributes['tag'] = $tags;
		}	
		
		if ( is_array( $atts ) ) {
			$attributes = array_merge( $attributes, $atts );
		}

		$attributes = shortcode_atts( $this->get_defaults(), $attributes, 'aiovg_related_videos' );
		$attributes['shortcode'] = 'aiovg_related_videos';

		$content = $this->get_content( $attributes );

		if ( empty( $content ) ) {
			$content = sprintf(
				'<div class="aiovg-shortcode-related-videos aiovg-no-items-found">%s</div>',
				esc_html( aiovg_get_message( 'videos_empty' ) )
			);
		}

		return $content;
	}

	/**
	 * Run the shortcode [aiovg_user_videos].
	 *
	 * @since 1.0.0
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_user_videos( $atts ) {	
		$user_slug = get_query_var( 'aiovg_user' );
		$content   = '';		
		
		if ( empty( $user_slug ) ) {
			if ( ! empty( $atts['id'] ) ) {
				$user_slug = get_the_author_meta( 'user_nicename', (int) $atts['id'] );	
			} elseif ( is_user_logged_in() ) {
				$user_slug = get_the_author_meta( 'user_nicename', get_current_user_id() );				
			}
		}
		
		if ( ! empty( $user_slug ) ) {		
			$attributes = shortcode_atts( $this->get_defaults(), $atts, 'aiovg_user_videos' );
			$attributes['shortcode'] = 'aiovg_user_videos';
			$attributes['user_slug'] = $user_slug;

			$content = $this->get_content( $attributes );		
		} else {
			$content = do_shortcode( '[aiovg_videos]' );	
		}
		
		if ( empty( $content ) ) {
			$content = sprintf(
				'<div class="aiovg-shortcode-user-videos aiovg-no-items-found">%s</div>',
				esc_html( aiovg_get_message( 'videos_empty' ) )
			);
		}
		
		return $content;	
	}

	/**
	 * Run the shortcode [aiovg_liked_videos].
	 *
	 * @since 3.6.1
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_liked_videos( $atts ) {
		$likes_settings = get_option( 'aiovg_likes_settings' );

		$user_id = get_current_user_id();
		$liked   = array();
		$content = '';

		if ( $user_id > 0 ) {
			$liked = (array) get_user_meta( $user_id, 'aiovg_videos_likes' );	
		} else {
			$likes_settings = get_option( 'aiovg_likes_settings' );

			if ( empty( $likes_settings['login_required_to_vote'] ) ) {
				if ( isset( $_COOKIE['aiovg_videos_likes'] ) ) {
					$liked = explode( '|', $_COOKIE['aiovg_videos_likes'] );
					$liked = array_map( 'intval', $liked );
				}
			} else {
				if ( function_exists( 'aiovg_premium_login_form' ) ) {
					wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-premium-public' );
					return aiovg_premium_login_form();
				} else {
					return sprintf(
						'<div class="aiovg-shortcode-liked-videos aiovg-login-required">%s</div>',
						esc_html( aiovg_get_message( 'login_required' ) )
					);
				}				
			}
		}			
		
		if ( ! empty( $liked ) ) {		
			$attributes = shortcode_atts( $this->get_defaults(), $atts, 'aiovg_liked_videos' );
			$attributes['shortcode'] = 'aiovg_liked_videos';
			$attributes['include'] = $liked;
			$attributes['limit'] = -1;

			$content = $this->get_content( $attributes );		
		}
		
		if ( empty( $content ) ) {
			$content = sprintf(
				'<div class="aiovg-shortcode-liked-videos aiovg-no-items-found">%s</div>',
				esc_html( aiovg_get_message( 'videos_empty' ) )
			);
		}
		
		return $content;	
	}

	/**
	 * Run the shortcode [aiovg_disliked_videos].
	 *
	 * @since 3.6.1
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_disliked_videos( $atts ) {	
		$likes_settings = get_option( 'aiovg_likes_settings' );

		$user_id  = get_current_user_id();
		$disliked = array();
		$content  = '';				

		if ( $user_id > 0 ) {
			$disliked = (array) get_user_meta( $user_id, 'aiovg_videos_dislikes' );		
		} else {
			$likes_settings = get_option( 'aiovg_likes_settings' );

			if ( empty( $likes_settings['login_required_to_vote'] ) ) {
				if ( isset( $_COOKIE['aiovg_videos_dislikes'] ) ) {
					$disliked = explode( '|', $_COOKIE['aiovg_videos_dislikes'] );
					$disliked = array_map( 'intval', $disliked );
				}
			} else {
				if ( function_exists( 'aiovg_premium_login_form' ) ) {
					wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-premium-public' );
					return aiovg_premium_login_form();
				} else {
					return sprintf(
						'<div class="aiovg-shortcode-disliked-videos aiovg-login-required">%s</div>',
						esc_html( aiovg_get_message( 'login_required' ) )
					);
				}	
			}
		}			
		
		if ( ! empty( $disliked ) ) {		
			$attributes = shortcode_atts( $this->get_defaults(), $atts, 'aiovg_disliked_videos' );
			$attributes['shortcode'] = 'aiovg_disliked_videos';
			$attributes['include'] = $disliked;
			$attributes['limit'] = -1;

			$content = $this->get_content( $attributes );		
		}
		
		if ( empty( $content ) ) {
			$content = sprintf(
				'<div class="aiovg-shortcode-disliked-videos aiovg-no-items-found">%s</div>',
				esc_html( aiovg_get_message( 'videos_empty' ) )
			);
		}
		
		return $content;	
	}

	/**
	 * Load more videos.
	 *
	 * @since 2.5.1
	 */
	public function ajax_callback_load_more_videos() {
		// Security check
		check_ajax_referer( 'aiovg_ajax_nonce', 'security' );

		// Proceed safe
		$attributes = array();

		foreach ( $_POST as $key => $value ) {
			if ( $value == 'false' ) {
				$attributes[ $key ] = 0;
			} elseif ( $value == 'true' ) {
				$attributes[ $key ] = 1;
			} else {
				$attributes[ $key ] = is_array( $value ) ? array_map( 'intval', $value ) : sanitize_text_field( $value );
			}
		}

		$json = array();
		$json['html'] = $this->get_content( $attributes );

		wp_send_json_success( $json );
	}
	
	/**
	 * Get the html output.
	 *
	 * @since  1.0.0
	 * @param  array  $atts    An associative array of attributes.
	 * @return string $content HTML output.
	 */
	public function get_content( $attributes ) {		
		$attributes['ratio'] = ! empty( $attributes['ratio'] ) ? (float) $attributes['ratio'] . '%' : '56.25%';
		
		$orderby = sanitize_text_field( $attributes['orderby'] );
		$order   = sanitize_text_field( $attributes['order'] );
		
		// Enqueue style dependencies
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-public' );
		
		// Define the query
		$args = array(				
			'post_type'      => 'aiovg_videos',
			'posts_per_page' => ! empty( $attributes['limit'] ) ? (int) $attributes['limit'] : -1,
			'post_status'    => array( 'publish' )
		);

		if ( ! empty( $attributes['show_pagination'] ) || ! empty( $attributes['show_more'] ) ) { // Pagination
			$args['paged'] = (int) $attributes['paged'];
		}
		
		if ( ! empty( $attributes['search_query'] ) ) { // Search
			$args['s'] = sanitize_text_field( $attributes['search_query'] );
		}	
		
		if ( ! empty( $attributes['user_slug'] ) ) { // User
			$args['author_name'] = sanitize_text_field( $attributes['user_slug'] );
		}		
		
		if ( ! empty( $attributes['include'] ) ) { // Include video IDs
			$args['post__in'] = is_array( $attributes['include'] ) ? array_map( 'intval', $attributes['include'] ) : array_map( 'intval', explode( ',', $attributes['include'] ) );
		}

		if ( ! empty( $attributes['exclude'] ) ) { // Exclude video IDs
			$args['post__not_in'] = is_array( $attributes['exclude'] ) ? array_map( 'intval', $attributes['exclude'] ) : array_map( 'intval', explode( ',', $attributes['exclude'] ) );
		}

		// Taxonomy Parameters
		$tax_queries = array();		

		if ( ! empty( $attributes['category'] ) ) { // Category
			$tax_queries[] = array(
				'taxonomy'         => 'aiovg_categories',
				'field'            => 'term_id',
				'terms'            => is_array( $attributes['category'] ) ? array_map( 'intval', $attributes['category'] ) : array_map( 'intval', explode( ',', $attributes['category'] ) ),
				'include_children' => false
			);
		}

		if ( ! empty( $attributes['category_exclude'] ) ) { // Exclude categories
			$tax_queries[] = array(
				'taxonomy'         => 'aiovg_categories',
				'field'            => 'term_id',
				'terms'            => is_array( $attributes['category_exclude'] ) ? array_map( 'intval', $attributes['category_exclude'] ) : array_map( 'intval', explode( ',', $attributes['category_exclude'] ) ),
				'include_children' => false,
				'operator'         => 'NOT IN'
			);
		}

		if ( ! empty( $attributes['tag'] ) ) { // Tag
			$tax_queries[] = array(
				'taxonomy'         => 'aiovg_tags',
				'field'            => 'term_id',
				'terms'            => is_array( $attributes['tag'] ) ? array_map( 'intval', $attributes['tag'] ) : array_map( 'intval', explode( ',', $attributes['tag'] ) ),
				'include_children' => false
			);
		}
		
		$count_tax_queries = count( $tax_queries );
		if ( $count_tax_queries ) {
			$tax_relation = 'AND';
			if ( ! empty( $attributes['related'] ) ) {		
				$tax_relation = 'OR';
			}

			$args['tax_query'] = ( $count_tax_queries > 1 ) ? array_merge( array( 'relation' => $tax_relation ), $tax_queries ) : $tax_queries;
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

		if ( ! empty( $attributes['featured'] ) ) { // Featured			
			$meta_queries['featured'] = array(
				'key'     => 'featured',
				'value'   => 1,
				'compare' => '='
			);				
		}		

		$count_meta_queries = count( $meta_queries );
		if ( $count_meta_queries ) {
			$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : $meta_queries;
		}				
		
		// Order & Orderby Parameters
		switch ( $orderby ) {
			case 'likes':
			case 'dislikes':
				$args['orderby'] = array(
					$orderby => $order,
					'date'   => 'DESC'
				);			
				break;

			case 'views':
				$args['meta_key'] = $orderby;
				$args['orderby']  = 'meta_value_num';
			
				$args['order']    = $order;
				break;

			case 'rand':
				$seed = aiovg_get_orderby_rand_seed( (int) $attributes['paged'] );
				$args['orderby']  = 'RAND(' . $seed . ')';
				break;

			default:
				$args['orderby']  = $orderby;
				$args['order']    = $order;
		}
	
		$args = apply_filters( 'aiovg_query_args', $args, $attributes );
		$aiovg_query = new WP_Query( $args );
		
		// Start the loop
		global $post;
		
		// Process output
		$content = '';
		
		if ( $aiovg_query->have_posts() ) {		
			if ( ( is_front_page() && is_home() ) || empty( $attributes['show_pagination'] ) ) {
				$attributes['count'] = $aiovg_query->post_count;
			} else {
				$attributes['count'] = $aiovg_query->found_posts;
			}
			
			ob_start();
			include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . 'public/templates/videos-template-classic.php', $attributes );
			$content = ob_get_clean();			
		}
		
		return $content;	
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

			foreach ( $fields['videos']['sections'] as $section ) {
				foreach ( $section['fields'] as $field ) {
					$this->defaults[ $field['name'] ] = $field['value'];
				}
			}

			foreach ( $fields['categories']['sections']['general']['fields'] as $field ) {
				if ( 'orderby' == $field['name'] || 'order' == $field['name'] ) {
					$this->defaults[ 'categories_' . $field['name'] ] = $field['value'];
				}
			}			
			
			$this->defaults['source'] = 'videos';
			$this->defaults['count'] = 0;
			$this->defaults['paged'] = aiovg_get_page_number();	
			$this->defaults['pagination_ajax'] = isset( $pagination_settings['ajax'] ) && ! empty( $pagination_settings['ajax'] ) ? 1 : 0;		
		}

		$this->defaults['uid'] = aiovg_get_uniqid();
		
		return $this->defaults;
	}
	
}
