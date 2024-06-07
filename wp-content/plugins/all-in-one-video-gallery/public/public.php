<?php

/**
 * The public-facing functionality of the plugin.
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
 * AIOVG_Public class.
 *
 * @since 1.0.0
 */
class AIOVG_Public {
	
	/**
	 * Remove 'redirect_canonical' hook to fix secondary loop pagination issue on single video 
	 * pages.
	 *
	 * @since 1.5.5
	 */
	public function template_redirect() {	
		if ( is_singular( 'aiovg_videos' ) ) {		
			global $wp_query;
			
			$page = (int) $wp_query->get( 'page' );
			if ( $page > 1 ) {
		  		// Convert 'page' to 'paged'
		 	 	$wp_query->set( 'page', 1 );
		 	 	$wp_query->set( 'paged', $page );
			}
			
			// Prevent redirect
			remove_action( 'template_redirect', 'redirect_canonical' );		
	  	}	
	}
	
	/**
	 * Add rewrite rules, set necessary plugin cookies.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		global $aiovg;

		$page_settings = get_option( 'aiovg_page_settings' );
		$privacy_settings = get_option( 'aiovg_privacy_settings' );

		$url = home_url();
		
		// Single category page
		$id = $page_settings['category'];
		if ( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );
			$link = urldecode( $link );		
			
			add_rewrite_rule( "$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id=' . $id . '&aiovg_category=$matches[1]&paged=$matches[2]', 'top' );
			add_rewrite_rule( "$link/([^/]+)/?$", 'index.php?page_id=' . $id . '&aiovg_category=$matches[1]', 'top' );
		}

		// Single tag page
		if ( isset( $page_settings['tag'] ) ) {
			$id = $page_settings['tag'];
			if ( $id > 0 ) {
				$link = str_replace( $url, '', get_permalink( $id ) );			
				$link = trim( $link, '/' );
				$link = urldecode( $link );		
				
				add_rewrite_rule( "$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id=' . $id . '&aiovg_tag=$matches[1]&paged=$matches[2]', 'top' );
				add_rewrite_rule( "$link/([^/]+)/?$", 'index.php?page_id=' . $id . '&aiovg_tag=$matches[1]', 'top' );
			}
		}
		
		// User videos page
		$id = $page_settings['user_videos'];
		if ( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );
			$link = urldecode( $link );		
			
			add_rewrite_rule( "$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id=' . $id . '&aiovg_user=$matches[1]&paged=$matches[2]', 'top' );
			add_rewrite_rule( "$link/([^/]+)/?$", 'index.php?page_id=' . $id . '&aiovg_user=$matches[1]', 'top' );
		}
		
		// Player page
		$id = $page_settings['player'];
		if ( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );
			$link = urldecode( $link );		
			
			add_rewrite_rule( "$link/id/([^/]+)/?$", 'index.php?page_id=' . $id . '&aiovg_type=id&aiovg_video=$matches[1]', 'top' );
		}
		
		// Rewrite tags
		add_rewrite_tag( '%aiovg_category%', '([^/]+)' );
		add_rewrite_tag( '%aiovg_tag%', '([^/]+)' );
		add_rewrite_tag( '%aiovg_user%', '([^/]+)' );
		add_rewrite_tag( '%aiovg_type%', '([^/]+)' );
		add_rewrite_tag( '%aiovg_video%', '([^/]+)' );
		
		// Set MySQL's RAND function seed value in a cookie
		if ( isset( $privacy_settings['disable_cookies'] ) && isset( $privacy_settings['disable_cookies']['aiovg_rand_seed'] ) ) {
			unset( $aiovg['rand_seed'] );
			return; // Disable the random ordering
		}
		
		if ( isset( $_COOKIE['aiovg_rand_seed'] ) ) {
			$aiovg['rand_seed'] = sanitize_text_field( $_COOKIE['aiovg_rand_seed'] );
			$transient_seed = get_transient( 'aiovg_rand_seed_' . $aiovg['rand_seed'] );

			if ( ! empty( $transient_seed ) ) {
				delete_transient( 'aiovg_rand_seed_' . $aiovg['rand_seed'] );

				$aiovg['rand_seed'] = sanitize_text_field( $transient_seed );
				@setcookie( 'aiovg_rand_seed', $aiovg['rand_seed'], time() + ( 86400 * 1 ), COOKIEPATH, COOKIE_DOMAIN );				
			}
		} else {
			$aiovg['rand_seed'] = wp_rand();
			@setcookie( 'aiovg_rand_seed', $aiovg['rand_seed'], time() + ( 86400 * 1 ), COOKIEPATH, COOKIE_DOMAIN );
		}
	}
	
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function register_styles() {
		$player_settings = get_option( 'aiovg_player_settings' );

		wp_register_style( 
			AIOVG_PLUGIN_SLUG . '-magnific-popup', 
			AIOVG_PLUGIN_URL . 'vendor/magnific-popup/magnific-popup.min.css', 
			array(), 
			'1.1.0', 
			'all' 
		);
		
		wp_register_style( 
			AIOVG_PLUGIN_SLUG . '-icons', 
			AIOVG_PLUGIN_URL . 'public/assets/css/icons.min.css', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			'all' 
		);
		
		if ( 'vidstack' == $player_settings['player'] ) {
			wp_register_style( 
				AIOVG_PLUGIN_SLUG . '-player', 
				AIOVG_PLUGIN_URL . 'public/assets/css/vidstack.min.css', 
				array(), 
				AIOVG_PLUGIN_VERSION, 
				'all' 
			);
		} else {
			wp_register_style( 
				AIOVG_PLUGIN_SLUG . '-player', 
				AIOVG_PLUGIN_URL . 'public/assets/css/videojs.min.css', 
				array(), 
				AIOVG_PLUGIN_VERSION, 
				'all' 
			);
		}

		wp_register_style( 
			AIOVG_PLUGIN_SLUG . '-public', 
			AIOVG_PLUGIN_URL . 'public/assets/css/public.min.css', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function register_scripts() {
		$likes_settings = get_option( 'aiovg_likes_settings' );

		$ajax_url   = admin_url( 'admin-ajax.php' );
		$ajax_nonce = wp_create_nonce( 'aiovg_ajax_nonce' );
		$user_id    = get_current_user_id();

		$scroll_to_top_offset = 20;
		if ( is_admin_bar_showing() ) $scroll_to_top_offset += 32;
		$scroll_to_top_offset = apply_filters( 'aiovg_scroll_to_top_offset', $scroll_to_top_offset );

		// Register the dependencies
		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-magnific-popup', 
			AIOVG_PLUGIN_URL . 'vendor/magnific-popup/magnific-popup.min.js', 
			array( 'jquery' ), 
			'1.1.0', 
			array( 'strategy' => 'defer' ) 
		);		
		
		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-pagination', 
			AIOVG_PLUGIN_URL . 'public/assets/js/pagination.min.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			array( 'strategy' => 'defer' ) 
		);			

		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-pagination', 
			'aiovg_pagination', 
			array(
				'ajax_url'             => $ajax_url,
				'ajax_nonce'           => $ajax_nonce,
				'scroll_to_top_offset' => $scroll_to_top_offset
			) 
		);

		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-autocomplete', 
			AIOVG_PLUGIN_URL . 'public/assets/js/autocomplete.min.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			array( 'strategy' => 'defer' ) 
		);			

		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-autocomplete', 
			'aiovg_autocomplete', 
			array(
				'ajax_url'   => $ajax_url,
				'ajax_nonce' => $ajax_nonce,
				'i18n'       => array(				
					'no_tags_found' => __( 'No tags found', 'all-in-one-video-gallery' )
				)
			) 
		);

		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-likes', 
			AIOVG_PLUGIN_URL . 'public/assets/js/likes.min.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			array( 'strategy' => 'defer' )
		);

		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-likes', 
			'aiovg_likes', 
			array(
				'ajax_url'               => $ajax_url,
				'ajax_nonce'             => $ajax_nonce,
				'user_id'                => $user_id,
				'show_like_button'       => ( ! empty( $likes_settings['like_button'] ) ? 1 : 0 ),
				'show_dislike_button'    => ( ! empty( $likes_settings['dislike_button'] ) ? 1 : 0 ),
				'login_required_to_vote' => ( ! empty( $likes_settings['login_required_to_vote'] ) ? 1 : 0 ),
				'i18n'                   => array(				
					'likes'                => __( 'Likes', 'all-in-one-video-gallery' ),
					'dislikes'             => __( 'Dislikes', 'all-in-one-video-gallery' ),
					'alert_login_required' => __( 'Sorry, you must login to vote.', 'all-in-one-video-gallery' )
				)
			) 
		);

		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-player', 
			AIOVG_PLUGIN_URL . 'public/assets/js/videojs.min.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			array( 'strategy' => 'defer' ) 
		);	
		
		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-player', 
			'aiovg_player', 
			array(
				'ajax_url'   => $ajax_url,
				'ajax_nonce' => $ajax_nonce,
				'i18n'       => array(				
					'stream_not_found' => __( 'This stream is currently not live. Please check back or refresh your page.', 'all-in-one-video-gallery' ),
				)
			) 
		);
		
		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-public', 
			AIOVG_PLUGIN_URL . 'public/assets/js/public.min.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			array( 'strategy' => 'defer' ) 
		);			

		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-public', 
			'aiovg_public', 
			array(
				'plugin_url'           => AIOVG_PLUGIN_URL,
				'plugin_version'       => AIOVG_PLUGIN_VERSION,
				'ajax_url'             => $ajax_url,
				'ajax_nonce'           => $ajax_nonce,
				'scroll_to_top_offset' => $scroll_to_top_offset,
				'i18n'                 => array(				
					'no_tags_found' => __( 'No tags found', 'all-in-one-video-gallery' )
				)
			) 
		);		
	}	

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @since 3.6.1
	 */
	public function enqueue_block_editor_assets() {
		// Styles
		$this->register_styles();

		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-public' );

		// Scripts
		$this->register_scripts();
		
		wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-likes' );
	}

	/**
	 * Flush rewrite rules when it's necessary.
	 *
	 * @since 1.0.0
	 */
	 public function maybe_flush_rules() {
		$general_settings = get_option( 'aiovg_general_settings' );

		if ( empty( $general_settings['maybe_flush_rewrite_rules'] ) ) {
			return false;
		}

		$rewrite_rules = get_option( 'rewrite_rules' );
				
		if ( $rewrite_rules ) {		
			global $wp_rewrite;
			
			foreach ( $rewrite_rules as $rule => $rewrite ) {
				$rewrite_rules_array[ $rule ]['rewrite'] = $rewrite;
			}
			$rewrite_rules_array = array_reverse( $rewrite_rules_array, true );
		
			$maybe_missing = $wp_rewrite->rewrite_rules();
			$missing_rules = false;		
		
			foreach ( $maybe_missing as $rule => $rewrite ) {
				if ( ! array_key_exists( $rule, $rewrite_rules_array ) ) {
					$missing_rules = true;
					break;
				}
			}
		
			if ( true === $missing_rules ) {
				flush_rewrite_rules();
			}		
		}	
	}	
	
	/**		 
	 * Override the default page/post title.
	 *		
	 * @since  1.0.0
	 * @param  string $title       The document title.	 
     * @param  string $sep         Title separator.
     * @param  string $seplocation Location of the separator (left or right).		 
	 * @return string              The filtered title.		 
	*/
	public function wp_title( $title, $sep, $seplocation ) {		
		global $post;
		
		if ( ! isset( $post ) ) return $title;
		
		$page_settings = get_option( 'aiovg_page_settings' );
		$site_name     = sanitize_text_field( get_bloginfo( 'name' ) );
		$custom_title  = '';		
		
		// Get category page title
		if ( $post->ID == $page_settings['category'] ) {			
			if ( $slug = get_query_var( 'aiovg_category' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_categories' ) ) {
					$custom_title = $term->name;
				}			
			}				
		}

		// Get tag page title
		if ( $post->ID == $page_settings['tag'] ) {			
			if ( $slug = get_query_var( 'aiovg_tag' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {
					$custom_title = $term->name;
				}			
			}				
		}
		
		// Get user videos page title
		if ( $post->ID == $page_settings['user_videos'] ) {		
			if ( $slug = get_query_var( 'aiovg_user' ) ) {
				$user = get_user_by( 'slug', $slug );
				$custom_title = $user->display_name;		
			}			
		}
		
		// ...
		if ( ! empty( $custom_title ) ) {
			$title = ( 'left' == $seplocation ) ? "$site_name $sep $custom_title" : "$custom_title $sep $site_name";
		}
		
		return $title;		
	}
	
	/**
	 * Override the default post/page title depending on the AIOVG view.
	 *
	 * @since  1.0.0
	 * @param  array $title The document title parts.
	 * @return              Filtered title parts.
	 */
	public function document_title_parts( $title ) {	
		global $post;
		
		if ( ! isset( $post ) ) return $title;
		
		$page_settings = get_option( 'aiovg_page_settings' );
		
		// Get category page title
		if ( $post->ID == $page_settings['category'] ) {			
			if ( $slug = get_query_var( 'aiovg_category' ) ) {
				$term = get_term_by( 'slug', $slug, 'aiovg_categories' );
				$title['title'] = $term->name;			
			}				
		}

		// Get tag page title
		if ( $post->ID == $page_settings['tag'] ) {			
			if ( $slug = get_query_var( 'aiovg_tag' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {
					$title['title'] = $term->name;	
				}		
			}				
		}
		
		// Get user videos page title
		if ( $post->ID == $page_settings['user_videos'] ) {		
			if ( $slug = get_query_var( 'aiovg_user' ) ) {
				$user = get_user_by( 'slug', $slug );
				$title['title'] = $user->display_name;		
			}			
		}
		
		// Return
		return $title;	
	}

	/**
	 * Construct Yoast SEO title for our category and user videos pages.
	 *
	 * @since  1.5.6
	 * @param  array $title The Yoast title.
	 * @return              Filtered title.
	 */
	public function wpseo_title( $title ) {	
		global $post;

		if ( ! isset( $post ) ) {
			return $title;
		}

		$page_settings = get_option( 'aiovg_page_settings' );

		if ( $post->ID != $page_settings['category'] && $post->ID != $page_settings['tag'] && $post->ID != $page_settings['user_videos'] ) {
			return $title;
		}

		$wpseo_titles = get_option( 'wpseo_titles' );

		$sep_options = WPSEO_Option_Titles::get_instance()->get_separator_options();

		if ( isset( $wpseo_titles['separator'] ) && isset( $sep_options[ $wpseo_titles['separator'] ] ) ) {
			$sep = $sep_options[ $wpseo_titles['separator'] ];
		} else {
			$sep = '-'; // Setting default separator if Admin didn't set it from backed
		}

		$replacements = array(
			'%%sep%%'              => $sep,						
			'%%page%%'             => '',
			'%%primary_category%%' => '',
			'%%sitename%%'         => sanitize_text_field( get_bloginfo( 'name' ) )
		);

		$title_template = '';
		
		// Category page
		if ( $post->ID == $page_settings['category'] ) {			
			if ( $slug = get_query_var( 'aiovg_category' ) ) {
				// Get Archive SEO title
				if ( array_key_exists( 'title-tax-aiovg_categories', $wpseo_titles ) ) {
					$title_template = $wpseo_titles['title-tax-aiovg_categories'];
				}

				// Get Term SEO title
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_categories' ) ) {		
					$replacements['%%term_title%%'] = $term->name;
					
					$meta = get_option( 'wpseo_taxonomy_meta' );

					if ( array_key_exists( 'aiovg_categories', $meta ) ) {
						if ( array_key_exists( $term->term_id, $meta['aiovg_categories'] ) ) {
							if ( array_key_exists( 'wpseo_title', $meta['aiovg_categories'][ $term->term_id ] ) ) {
								$title_template = $meta['aiovg_categories'][ $term->term_id ]['wpseo_title'];
							}
						}
					}
				}
			}				
		}

		// Tag page
		if ( $post->ID == $page_settings['tag'] ) {			
			if ( $slug = get_query_var( 'aiovg_tag' ) ) {
				// Get Archive SEO title
				if ( array_key_exists( 'title-tax-aiovg_tags', $wpseo_titles ) ) {
					$title_template = $wpseo_titles['title-tax-aiovg_tags'];
				}

				// Get Term SEO title
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {		
					$replacements['%%term_title%%'] = $term->name;
					
					$meta = get_option( 'wpseo_taxonomy_meta' );

					if ( array_key_exists( 'aiovg_tags', $meta ) ) {
						if ( array_key_exists( $term->term_id, $meta['aiovg_tags'] ) ) {
							if ( array_key_exists( 'wpseo_title', $meta['aiovg_tags'][ $term->term_id ] ) ) {
								$title_template = $meta['aiovg_tags'][ $term->term_id ]['wpseo_title'];
							}
						}
					}
				}
			}				
		}
		
		// User videos page
		if ( $post->ID == $page_settings['user_videos'] ) {		
			if ( $slug = get_query_var( 'aiovg_user' ) ) {
				$user = get_user_by( 'slug', $slug );
				$replacements['%%title%%'] = $user->display_name;
				
				// Get Archive SEO title
				if ( array_key_exists( 'title-page', $wpseo_titles ) ) {
					$title_template = $wpseo_titles['title-page'];
				}		
				
				// Get page meta title
				$meta = get_post_meta( $post->ID, '_yoast_wpseo_title', true );

				if ( ! empty( $meta ) ) {
					$title_template = $meta;
				}
			}			
		}

		// Return
		if ( ! empty( $title_template ) ) {
			$title = strtr( $title_template, $replacements );
		}

		return $title;	
	}

	/**
	 * Construct Yoast SEO description for our category and user videos pages.
	 *
	 * @since  1.5.6
	 * @param  array $desc The Yoast description.
	 * @return             Filtered description.
	 */
	public function wpseo_metadesc( $desc ) {	
		global $post;

		if ( ! isset( $post ) ) {
			return $desc;
		}

		$page_settings = get_option( 'aiovg_page_settings' );
		
		if ( $post->ID != $page_settings['category'] && $post->ID != $page_settings['tag'] && $post->ID != $page_settings['user_videos'] ) {
			return $desc;
		}

		$wpseo_titles = get_option( 'wpseo_titles' );

		$sep_options = WPSEO_Option_Titles::get_instance()->get_separator_options();

		if ( isset( $wpseo_titles['separator'] ) && isset( $sep_options[ $wpseo_titles['separator'] ] ) ) {
			$sep = $sep_options[ $wpseo_titles['separator'] ];
		} else {
			$sep = '-'; // Setting default separator if Admin didn't set it from backed
		}

		$replacements = array(
			'%%sep%%'              => $sep,						
			'%%page%%'             => '',
			'%%title%%'            => '',
			'%%primary_category%%' => '',
			'%%sitename%%'         => sanitize_text_field( get_bloginfo( 'name' ) )
		);

		$desc_template = '';

		// Category page
		if ( $post->ID == $page_settings['category'] ) {			
			if ( $slug = get_query_var( 'aiovg_category' ) ) {
				// Get Archive SEO desc
				if ( array_key_exists( 'metadesc-tax-aiovg_categories', $wpseo_titles ) ) {
					$desc_template = $wpseo_titles['metadesc-tax-aiovg_categories'];
				}

				// Get Term SEO desc
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_categories' ) ) {
					$replacements['%%term_title%%'] = $term->name;
					
					$meta = get_option( 'wpseo_taxonomy_meta' );

					if ( array_key_exists( 'aiovg_categories', $meta ) ) {
						if ( array_key_exists( $term->term_id, $meta['aiovg_categories'] ) ) {
							if ( array_key_exists( 'wpseo_desc', $meta['aiovg_categories'][ $term->term_id ] ) ) {
								$desc_template = $meta['aiovg_categories'][ $term->term_id ]['wpseo_desc'];
							}
						}
					}
				}
			}				
		}

		// Tag page
		if ( $post->ID == $page_settings['tag'] ) {			
			if ( $slug = get_query_var( 'aiovg_tag' ) ) {
				// Get Archive SEO desc
				if ( array_key_exists( 'metadesc-tax-aiovg_tags', $wpseo_titles ) ) {
					$desc_template = $wpseo_titles['metadesc-tax-aiovg_tags'];
				}

				// Get Term SEO desc
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {
					$replacements['%%term_title%%'] = $term->name;	
					
					$meta = get_option( 'wpseo_taxonomy_meta' );

					if ( array_key_exists( 'aiovg_tags', $meta ) ) {
						if ( array_key_exists( $term->term_id, $meta['aiovg_tags'] ) ) {
							if ( array_key_exists( 'wpseo_desc', $meta['aiovg_tags'][ $term->term_id ] ) ) {
								$desc_template = $meta['aiovg_tags'][ $term->term_id ]['wpseo_desc'];
							}
						}
					}
				}
			}				
		}
		
		// User videos page
		if ( $post->ID == $page_settings['user_videos'] ) {		
			if ( $slug = get_query_var( 'aiovg_user' ) ) {
				$user = get_user_by( 'slug', $slug );
				$replacements['%%title%%'] = $user->display_name;
				
				// Get Archive SEO desc				
				if ( array_key_exists( 'metadesc-page', $wpseo_titles ) ) {
					$desc_template = $wpseo_titles['metadesc-page'];
				}		
				
				// Get page meta desc
				$meta = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );

				if ( ! empty( $meta ) ) {
					$desc_template = $meta;
				}
			}			
		}
		
		// Return
		if ( ! empty( $desc_template ) ) {
			$desc = strtr( $desc_template, $replacements );
		}

		return $desc;	
	}

	/**
	 * Override the Yoast SEO canonical URL on our category and user videos pages.
	 *
	 * @since  1.5.6
	 * @param  array $url The Yoast canonical URL.
	 * @return            Filtered canonical URL.
	 */
	public function wpseo_canonical( $url ) {	
		global $post;

		if ( ! isset( $post ) ) {
			return $url;
		}
		
		$page_settings = get_option( 'aiovg_page_settings' );

		// Category page
		if ( $post->ID == $page_settings['category'] ) {			
			if ( $slug = get_query_var( 'aiovg_category' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_categories' ) ) {
					$url = aiovg_get_category_page_url( $term );
				}
			}				
		}

		// Tag page
		if ( $post->ID == $page_settings['tag'] ) {			
			if ( $slug = get_query_var( 'aiovg_tag' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {
					$url = aiovg_get_tag_page_url( $term );
				}
			}				
		}
		
		// User videos page
		if ( $post->ID == $page_settings['user_videos'] ) {		
			if ( $slug = get_query_var( 'aiovg_user' ) ) {
				$user = get_user_by( 'slug', $slug );
				$url = aiovg_get_user_videos_page_url( $user->ID );
			}			
		}
		
		// Return
		return $url;	
	}

	/**
	 * Override the Yoast SEO Open Graph image URLs on our plugin pages.
	 *
	 * @since  1.6.5
	 * @param  array $url The Yoast image URL.
	 * @return            Filtered image URL.
	 */
	public function wpseo_opengraph_image( $url ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $url;
		}
		
		if ( is_singular( 'aiovg_videos' ) ) {
			$image = get_post_meta( $post->ID, '_yoast_wpseo_opengraph-image', true );

			if ( empty( $image ) ) {
				$image_data = aiovg_get_image( $post->ID, 'large' );
				$image = $image_data['src'];
			}
			
			if ( ! empty( $image ) ) {
				$url = $image;
			}			
		} else {
			$page_settings = get_option( 'aiovg_page_settings' );

			// Category page
			if ( $post->ID == $page_settings['category'] ) {			
				if ( $slug = get_query_var( 'aiovg_category' ) ) {
					if ( $term = get_term_by( 'slug', $slug, 'aiovg_categories' ) ) {
						$meta  = get_option( 'wpseo_taxonomy_meta' );
						$image = '';

						if ( array_key_exists( 'aiovg_categories', $meta ) ) { // Get custom share image from Yoast
							if ( array_key_exists( $term->term_id, $meta['aiovg_categories'] ) ) {
								if ( array_key_exists( 'wpseo_opengraph-image', $meta['aiovg_categories'][ $term->term_id ] ) ) {
									$image = $meta['aiovg_categories'][ $term->term_id ]['wpseo_opengraph-image'];
								}
							}
						}

						if ( empty( $image ) ) {
							$image_data = aiovg_get_image( $term->term_id, 'large', 'term' );
							$image = $image_data['src'];
						}

						if ( ! empty( $image ) ) {
							$url = $image;
						}
					}
				}				
			}

			// Tag page
			if ( $post->ID == $page_settings['tag'] ) {			
				if ( $slug = get_query_var( 'aiovg_tag' ) ) {
					if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {
						$meta  = get_option( 'wpseo_taxonomy_meta' );
						$image = '';

						if ( array_key_exists( 'aiovg_tags', $meta ) ) { // Get custom share image from Yoast
							if ( array_key_exists( $term->term_id, $meta['aiovg_tags'] ) ) {
								if ( array_key_exists( 'wpseo_opengraph-image', $meta['aiovg_tags'][ $term->term_id ] ) ) {
									$image = $meta['aiovg_tags'][ $term->term_id ]['wpseo_opengraph-image'];
								}
							}
						}

						if ( ! empty( $image ) ) {
							$url = $image;
						}
					}
				}				
			}
		}

		// Return
		return $url;
	}

	/**
	 * Add custom Twitter Social Share images for Yoast SEO.
	 *
	 * @since  1.6.5
	 * @param  array $url The Yoast image URL.
	 * @return            Filtered image URL.
	 */
	public function wpseo_twitter_image( $url ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $url;
		}
		
		if ( is_singular( 'aiovg_videos' ) ) {
			$image = get_post_meta( $post->ID, '_yoast_wpseo_twitter-image', true );

			if ( empty( $image ) ) {
				$image_data = aiovg_get_image( $post->ID, 'large' );
				$image = $image_data['src'];
			}
			
			if ( ! empty( $image ) ) {
				$url = $image;
			}			
		} else {
			$page_settings = get_option( 'aiovg_page_settings' );

			// Category page
			if ( $post->ID == $page_settings['category'] ) {			
				if ( $slug = get_query_var( 'aiovg_category' ) ) {
					if ( $term = get_term_by( 'slug', $slug, 'aiovg_categories' ) ) {
						$meta  = get_option( 'wpseo_taxonomy_meta' );
						$image = '';

						if ( array_key_exists( 'aiovg_categories', $meta ) ) { // Get custom share image from Yoast
							if ( array_key_exists( $term->term_id, $meta['aiovg_categories'] ) ) {
								if ( array_key_exists( 'wpseo_twitter-image', $meta['aiovg_categories'][ $term->term_id ] ) ) {
									$image = $meta['aiovg_categories'][ $term->term_id ]['wpseo_twitter-image'];
								}
							}
						}

						if ( empty( $image ) ) {
							$image_data = aiovg_get_image( $term->term_id, 'large', 'term' );
							$image = $image_data['src'];
						}

						if ( ! empty( $image ) ) {
							$url = $image;
						}	
					}				
				}				
			}

			// Tag page
			if ( $post->ID == $page_settings['tag'] ) {			
				if ( $slug = get_query_var( 'aiovg_tag' ) ) {
					if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {
						$meta  = get_option( 'wpseo_taxonomy_meta' );
						$image = '';

						if ( array_key_exists( 'aiovg_tags', $meta ) ) { // Get custom share image from Yoast
							if ( array_key_exists( $term->term_id, $meta['aiovg_tags'] ) ) {
								if ( array_key_exists( 'wpseo_twitter-image', $meta['aiovg_tags'][ $term->term_id ] ) ) {
									$image = $meta['aiovg_tags'][ $term->term_id ]['wpseo_twitter-image'];
								}
							}
						}

						if ( ! empty( $image ) ) {
							$url = $image;	
						}
					}				
				}				
			}
		}

		// Return
		return $url;
	}

	/**
	 * Filter Yoast SEO breadcrumbs.
	 *
	 * @since  1.6.2
	 * @param  array $crumbs Array of crumbs.
	 * @return array $crumbs Filtered array of crumbs.
	 */
	public function wpseo_breadcrumb_links( $crumbs ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $crumbs;
		}

		if ( is_singular( 'aiovg_videos' ) ) {
			foreach ( $crumbs as $index => $crumb ) {
				if ( ! empty( $crumb['ptarchive'] ) && 'aiovg_videos' == $crumb['ptarchive'] ) {
					$obj = get_post_type_object( 'aiovg_videos' );

					$crumbs[ $index ] = array(
						'text' => $obj->labels->name,
						'url'  => aiovg_get_search_page_url()
					);
				}
			}
		} else {
			$page_settings = get_option( 'aiovg_page_settings' );

			if ( $post->ID == $page_settings['category'] ) {
				if ( $slug = get_query_var( 'aiovg_category' ) ) {
					if ( $term = get_term_by( 'slug', $slug, 'aiovg_categories' ) ) {
						$crumbs[] = array(
							'text' => $term->name
						);	
					}		
				}
			}

			if ( $post->ID == $page_settings['tag'] ) {
				if ( $slug = get_query_var( 'aiovg_tag' ) ) {
					if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {
						$crumbs[] = array(
							'text' => $term->name
						);	
					}		
				}
			}
			
			if ( $post->ID == $page_settings['user_videos'] ) {				
				if ( $slug = get_query_var( 'aiovg_user' ) ) {
					$user = get_user_by( 'slug', $slug );
					$crumbs[] = array(
						'text' => $user->display_name
					);			
				}	
			}
		}

		return $crumbs;
	}

	/**
	 * Filter Yoast video sitemap entry details.
	 *
	 * @since  2.5.8
	 * @param  array $details Array of sitemap entry details.
	 * @return array $details Filtered array of sitemap entry details.
	 */
	public function wpseo_video_sitemap_entry( $details ) {
		if ( isset( $details['post_id'] ) && ! empty( $details['post_id'] ) ) {
			$post_id   = (int) $details['post_id'];
			$post_type = get_post_type( $post_id );
	
			if ( 'aiovg_videos' == $post_type ) {
				$thumbnail_loc = get_post_meta(  $post_id, 'image', true );
	
				if ( ! empty( $thumbnail_loc ) ) {
					$details['thumbnail_loc'] = $thumbnail_loc;
				}
			}
		}
	
		return $details;
	}
	
	/**
	 * Adds the custom common CSS code, Facebook OG tags, and Twitter Cards.
	 *
	 * @since 1.0.0
	 */
	public function wp_head() {	
		global $post;
			
		// Facebook OG tags & Twitter Cards
		if ( isset( $post ) && is_singular( 'aiovg_videos' ) ) {
			$video_settings = get_option( 'aiovg_video_settings' );
			$socialshare_settings = get_option( 'aiovg_socialshare_settings' );

			if ( isset( $video_settings['display']['share'] ) && ! empty( $socialshare_settings['open_graph_tags'] ) ) {
				$site_name = get_bloginfo( 'name' );
				$page_url = get_permalink();
				$video_title = get_the_title();
				$video_description = aiovg_get_excerpt( $post->ID, 160, '', false );
				$video_url = aiovg_get_player_page_url( $post->ID );
				$twitter_username = $socialshare_settings['twitter_username'];

				$image_data = aiovg_get_image( $post->ID, 'large' );
				$image_url = $image_data['src'];

				printf( '<meta property="og:site_name" content="%s" />', esc_attr( $site_name ) );
				printf( '<meta property="og:url" content="%s" />', esc_url( $page_url ) );
				echo '<meta property="og:type" content="video" />';
				printf( '<meta property="og:title" content="%s" />', esc_attr( $video_title ) );

				if ( ! empty( $video_description ) ) {
					printf( '<meta property="og:description" content="%s" />', esc_attr( $video_description ) );
				}

				if ( ! empty( $image_url ) ) {
					printf( '<meta property="og:image" content="%s" />', esc_url( $image_url ) );
				}

				printf( '<meta property="og:video:url" content="%s" />', esc_url( $video_url ) );

				if ( stripos( $page_url, 'https://' ) === 0 ) {
					printf( '<meta property="og:video:secure_url" content="%s" />', esc_url( $video_url ) );
				}

				echo '<meta property="og:video:type" content="text/html">';
				echo '<meta property="og:video:width" content="1280">';
				echo '<meta property="og:video:height" content="720">';

				printf( '<meta name="twitter:card" content="%s">', ( ! empty( $twitter_username ) ? 'player' : 'summary' ) );

				if ( ! empty( $twitter_username ) ) {
					if ( strpos( $twitter_username, '@' ) === false ) {
						$twitter_username = '@' . $twitter_username;
					}
					
					printf( '<meta name="twitter:site" content="%s" />', esc_attr( $twitter_username ) );
				}

				printf( '<meta name="twitter:title" content="%s" />', esc_attr( $video_title ) );

				if ( ! empty( $video_desc ) ) {
					printf( '<meta name="twitter:description" content="%s" />', esc_attr( $video_desc ) );
				}

				if ( ! empty( $image_url ) ) {
					printf( '<meta name="twitter:image" content="%s" />', esc_url( $image_url ) );
				}

				if ( ! empty( $twitter_username ) ) {
					printf( '<meta name="twitter:player" content="%s" />', esc_url( $video_url ) );
					echo '<meta name="twitter:player:width" content="1280">';
					echo '<meta name="twitter:player:height" content="720">';
				}				
			}				
		}
		
		// Custom common CSS code
		echo '<style type="text/css">
			.aiovg-player {
				display: block;
				position: relative;
				padding-bottom: 56.25%;
				width: 100%;
				height: 0;	
				overflow: hidden;
			}
			
			.aiovg-player iframe,
			.aiovg-player .video-js,
			.aiovg-player .plyr {
				position: absolute;
				inset: 0;	
				margin: 0;
				padding: 0;
				width: 100%;
				height: 100%;
			}
		</style>';
	}
	
	/**
	 * Change the current page title if applicable.
	 *
	 * @since  1.0.0
	 * @param  string $title   Current page title.
	 * @param  int    $post_id The post ID.
	 * @return string $title   Filtered page title.
	 */
	public function the_title( $title, $id = 0 ) {
		if ( ! in_the_loop() || ! is_main_query() ) {
			return $title;
		}

		global $post;

		if ( ! empty( $id ) ) {
			if ( $id != $post->ID ) {
				return $title;
			}
		}
		
		$page_settings = get_option( 'aiovg_page_settings' );
		
		// Change category page title
		if ( $post->ID == $page_settings['category'] ) {		
			if ( $slug = get_query_var( 'aiovg_category' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_categories' ) ) {
					$title = $term->name;	
				}		
			}			
		}

		// Change tag page title
		if ( $post->ID == $page_settings['tag'] ) {		
			if ( $slug = get_query_var( 'aiovg_tag' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'aiovg_tags' ) ) {
					$title = $term->name;
				}			
			}			
		}
		
		// Change search page title
		if ( $post->ID == $page_settings['search'] ) {		
			$queries = array();
			
			if ( ! empty( $_GET['vi'] ) ) {
				$queries[] = sanitize_text_field( stripslashes( $_GET['vi'] ) );				
			}
			
			if ( ! empty( $_GET['ca'] ) ) {
				if ( $term = get_term_by( 'id', (int) $_GET['ca'], 'aiovg_categories' ) ) {
					$queries[] = $term->name;		
				}		
			}

			if ( isset( $_GET['ta'] ) ) {
				$tags = array_map( 'intval', $_GET['ta'] );
				$tags = array_filter( $tags );

				if ( ! empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						if ( $term = get_term_by( 'id', $tag, 'aiovg_tags' ) ) {
							$queries[] = $term->name;	
						}
					}	
				}						
			}
			
			if ( ! empty( $queries ) ) {
				$title = sprintf( __( 'Showing results for "%s"', 'all-in-one-video-gallery' ), implode( ', ', $queries ) );	
			}			
		}
		
		// Change user videos page title
		if ( $post->ID == $page_settings['user_videos'] ) {		
			if ( $slug = get_query_var( 'aiovg_user' ) ) {
				$user = get_user_by( 'slug', $slug );
				$title = $user->display_name;		
			}			
		}
		
		return $title;	
	}

	/**
	 * Filters whether a video post has a thumbnail.
	 *
	 * @since  2.4.0
	 * @param bool             $has_thumbnail true if the post has a post thumbnail, otherwise false.
	 * @param int|WP_Post|null $post          Post ID or WP_Post object. Default is global `$post`.
	 * @param int|string       $thumbnail_id  Post thumbnail ID or empty string.
	 * @return bool            $has_thumbnail true if the video post has an image attached.
	 */
	public function has_post_thumbnail( $has_thumbnail, $post, $thumbnail_id ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return $has_thumbnail;
		}

		if ( is_singular( 'aiovg_videos' ) ) {		
			$query = aiovg_get_global_query_object();
			
			if ( $query && $post->ID == $query->get_queried_object_id() ) {
				$featured_images_settings = get_option( 'aiovg_featured_images_settings' );

				if ( ! empty( $featured_images_settings['hide_on_single_video_pages'] ) ) {
					return false;
				}
			}
		}

		if ( ! empty( $thumbnail_id ) ) {
			return $has_thumbnail;		
		}

		if ( 'aiovg_videos' == get_post_type( $post->ID ) ) {
			$image_data = aiovg_get_image( $post->ID, 'large' );
			
			if ( ! empty( $image_data['src'] ) ) {
				$has_thumbnail = true;
			}
		}	

		return $has_thumbnail;		
	}

	/**
	 * Filters the video post thumbnail HTML.
	 *
	 * @since  2.4.0
	 * @param string       $html              The post thumbnail HTML.
	 * @param int          $post_id           The post ID.
	 * @param string       $post_thumbnail_id The post thumbnail ID.
	 * @param string|array $size              The post thumbnail size. Image size or array of width and height
	 *                                        values (in that order). Default 'post-thumbnail'.
	 * @param string       $attr              Query string of attributes.
	 * @return bool        $html              Filtered video post thumbnail HTML.
	 */
	public function post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		if ( is_singular( 'aiovg_videos' ) ) {		
			$query = aiovg_get_global_query_object();
			
			if ( $query && $post_id == $query->get_queried_object_id() ) {
				$featured_images_settings = get_option( 'aiovg_featured_images_settings' );

				if ( ! empty( $featured_images_settings['hide_on_single_video_pages'] ) ) {
					return '';
				}				
			}
		}

		if ( ! empty( $post_thumbnail_id ) ) {
			return $html;	
		}

		if ( 'aiovg_videos' == get_post_type( $post_id ) ) {
			$_html = '';

			$image_id = get_post_meta( $post_id, 'image_id', true );

			if ( ! empty( $image_id ) ) {
				$_html = wp_get_attachment_image( $image_id, $size, false, $attr );
			} 
			
			if ( empty( $_html ) ) {
				$image_url = get_post_meta( $post_id, 'image', true );
				
				if ( ! empty( $image_url ) ) {
					$alt  = get_post_field( 'post_title', $post_id );

					$attr = array( 'alt' => $alt );
					$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, NULL, $size );
					$attr = array_map( 'esc_attr', $attr );

					$_html = sprintf( '<img src="%s"', esc_url( $image_url ) );

					foreach ( $attr as $name => $value ) {
						$_html .= " $name=" . '"' . $value . '"';
					}

					$_html .= ' />';
				}
			}

			if ( ! empty( $_html ) ) {
				$html = $_html;
			}
		}

		return $html;		
	}

	/**
	 * Always use our custom page for AIOVG categories.
	 *
	 * @since  1.0.0
	 * @param  string $url      The term URL.
	 * @param  object $term     The term object.
	 * @param  string $taxonomy The taxonomy slug.
	 * @return string $url      Filtered term URL.
	 */
	public function term_link( $url, $term, $taxonomy ) {	
		if ( 'aiovg_categories' == $taxonomy ) {
			$url = aiovg_get_category_page_url( $term );
		}

		if ( 'aiovg_tags' == $taxonomy ) {
			$url = aiovg_get_tag_page_url( $term );
		}
		
		return $url;		
	}	
	
	/**
	 * Set cookie for accepting the privacy consent.
	 *
	 * @since 1.0.0
	 */
	public function set_gdpr_cookie() {	
		check_ajax_referer( 'aiovg_ajax_nonce', 'security' );	
		setcookie( 'aiovg_gdpr_consent', 1, time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN );		
		wp_send_json_success();			
	}

}
