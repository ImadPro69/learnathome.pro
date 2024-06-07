<?php

/**
 * The admin-specific functionality of the plugin.
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
 * AIOVG_Admin class.
 *
 * @since 1.0.0
 */
class AIOVG_Admin {
	
	/**
	 * Insert missing plugin options.
	 *
	 * @since 1.5.2
	 */
	public function insert_missing_options() {		
		if ( AIOVG_PLUGIN_VERSION !== get_option( 'aiovg_version' ) ) {	
			$defaults = aiovg_get_default_settings();
			
			// Update the plugin version		
			update_option( 'aiovg_version', AIOVG_PLUGIN_VERSION );			

			// Insert the missing player settings
			$player_settings = get_option( 'aiovg_player_settings' );

			$new_player_settings = array();
			
			if ( ! array_key_exists( 'muted', $player_settings ) ) {
				$new_player_settings['muted'] = $defaults['aiovg_player_settings']['muted'];				
			}
			
			if ( ! array_key_exists( 'cc_load_policy', $player_settings ) ) {
				$new_player_settings['cc_load_policy'] = 0;				
			}
			
			if ( ! array_key_exists( 'quality_levels', $player_settings ) ) {
				$new_player_settings['quality_levels'] = $defaults['aiovg_player_settings']['quality_levels'];				
			}
			
			if ( ! array_key_exists( 'use_native_controls', $player_settings ) ) {
				$new_player_settings['use_native_controls'] = $defaults['aiovg_player_settings']['use_native_controls'];				
			}

			if ( ! array_key_exists( 'force_js_initialization', $player_settings ) ) {
				$new_player_settings['player'] = 'videojs';
				$new_player_settings['force_js_initialization'] = ( isset( $player_settings['player'] ) && 'standard' == $player_settings['player'] ) ? 1 : 0;				
			}

			if ( ! array_key_exists( 'lazyloading', $player_settings ) ) {
				$new_player_settings['lazyloading'] = 0;				
			}

			if ( count( $new_player_settings ) ) {
				update_option( 'aiovg_player_settings', array_merge( $player_settings, $new_player_settings ) );
			}						

			// Insert the missing videos settings
			$videos_settings = get_option( 'aiovg_videos_settings' );
			$image_settings  = get_option( 'aiovg_image_settings', array() );

			$new_videos_settings = array();

			if ( ! array_key_exists( 'template', $videos_settings ) ) {
				$new_videos_settings['template'] = $defaults['aiovg_videos_settings']['template'];				
			}

			if ( ! empty( $image_settings ) ) {
				$new_videos_settings['display'] = $videos_settings['display'];
				$new_videos_settings['display']['title'] = 'title';
			}

			if ( ! array_key_exists( 'thumbnail_style', $videos_settings ) ) {
				$new_videos_settings['thumbnail_style'] = $defaults['aiovg_videos_settings']['thumbnail_style'];
			}			

			if ( count( $new_videos_settings ) ) {
				update_option( 'aiovg_videos_settings', array_merge( $videos_settings, $new_videos_settings ) );
			}

			// Insert the missing categories settings
			$categories_settings = get_option( 'aiovg_categories_settings' );

			$new_categories_settings = array();

			if ( ! array_key_exists( 'template', $categories_settings ) ) {
				$new_categories_settings['template'] = $defaults['aiovg_categories_settings']['template'];				
			}

			if ( ! array_key_exists( 'limit', $categories_settings ) ) {
				$new_categories_settings['limit'] = $defaults['aiovg_categories_settings']['limit'];				
			}

			if ( ! array_key_exists( 'hierarchical', $categories_settings ) ) {
				$new_categories_settings['hierarchical'] = $defaults['aiovg_categories_settings']['hierarchical'];				
			}

			if ( ! array_key_exists( 'back_button', $categories_settings ) ) {
				$new_categories_settings['back_button'] = 1;				
			}

			if ( count( $new_categories_settings ) ) {
				update_option( 'aiovg_categories_settings', array_merge( $categories_settings, $new_categories_settings ) );
			}			

			// Insert the missing video settings
			$video_settings = get_option( 'aiovg_video_settings' );

			$new_video_settings = array();

			if ( ! empty( $image_settings ) ) {
				$new_video_settings['display'] = $video_settings['display'];
				$new_video_settings['display']['share'] = 'share';
			}

			if ( empty( $video_settings['has_comments'] ) ) {
				$new_video_settings['has_comments'] = -1;				
			}

			if ( count( $new_video_settings ) ) {
				update_option( 'aiovg_video_settings', array_merge( $video_settings, $new_video_settings ) );
			}

			// Insert the images settings
			if ( false == get_option( 'aiovg_images_settings' ) ) {
				$images_settings = array(
					'width' => $defaults['aiovg_images_settings']['width'],
					'ratio' => $defaults['aiovg_images_settings']['ratio'],
					'size'  => $defaults['aiovg_images_settings']['size']
				);

				if ( ! empty( $image_settings ) ) {
					$images_settings['ratio'] = $image_settings['ratio'];
				}

				if ( isset( $videos_settings['ratio'] ) ) {
					$images_settings['ratio'] = $videos_settings['ratio'];
				}

				add_option( 'aiovg_images_settings', $images_settings );
			}

			// Insert the featured images settings
			if ( false == get_option( 'aiovg_featured_images_settings' ) ) {
				add_option( 'aiovg_featured_images_settings', array(
					'enabled'                    => $defaults['aiovg_featured_images_settings']['enabled'],
					'download_external_images'   => $defaults['aiovg_featured_images_settings']['download_external_images'],
					'hide_on_single_video_pages' => $defaults['aiovg_featured_images_settings']['hide_on_single_video_pages']
				));
			}

			// Insert the likes / dislikes settings
			if ( false == get_option( 'aiovg_likes_settings' ) ) {
				add_option( 'aiovg_likes_settings', array(
					'like_button'            => $defaults['aiovg_likes_settings']['like_button'],
					'dislike_button'         => $defaults['aiovg_likes_settings']['dislike_button'],
					'login_required_to_vote' => $defaults['aiovg_likes_settings']['login_required_to_vote']
				));
			}

			// Insert the related videos settings
			if ( false == get_option( 'aiovg_related_videos_settings' ) ) {
				add_option( 'aiovg_related_videos_settings', array(
					'columns' => $videos_settings['columns'],
					'limit'   => $videos_settings['limit'],
					'orderby' => $videos_settings['orderby'],
					'order'   => $videos_settings['order'],
					'display' => array(
						'pagination' => 'pagination'
					)
				));
			}
			
			// Insert the missing socialshare settings
			$socialshare_settings = get_option( 'aiovg_socialshare_settings' );

			$new_socialshare_settings = array();

			if ( ! array_key_exists( 'open_graph_tags', $socialshare_settings ) ) {
				$new_socialshare_settings['open_graph_tags'] = $defaults['aiovg_socialshare_settings']['open_graph_tags'];				
			}

			if ( ! array_key_exists( 'twitter_username', $socialshare_settings ) ) {
				$new_socialshare_settings['twitter_username'] = $defaults['aiovg_socialshare_settings']['twitter_username'];				
			}

			if ( count( $new_socialshare_settings ) ) {
				update_option( 'aiovg_socialshare_settings', array_merge( $socialshare_settings, $new_socialshare_settings ) );
			}
			
			// Insert the missing general settings
			$general_settings = get_option( 'aiovg_general_settings' );

			$new_general_settings = array();

			if ( ! array_key_exists( 'maybe_flush_rewrite_rules', $general_settings ) ) {
				$new_general_settings['maybe_flush_rewrite_rules'] = $defaults['aiovg_general_settings']['maybe_flush_rewrite_rules'];
			}

			if ( ! array_key_exists( 'delete_media_files', $general_settings ) ) {
				$new_general_settings['delete_media_files'] = $defaults['aiovg_general_settings']['delete_media_files'];
			}

			if ( count( $new_general_settings ) ) {
				update_option( 'aiovg_general_settings', array_merge( $general_settings, $new_general_settings ) );
			}

			// Insert the api settings
			if ( false == get_option( 'aiovg_api_settings' ) ) {
				$automations_settings = get_option( 'aiovg_automations_settings', array() );

				$defaults = array(
					'youtube_api_key'    => isset( $automations_settings['youtube_api_key'] ) ? $automations_settings['youtube_api_key'] : '',
					'vimeo_access_token' => isset( $general_settings['vimeo_access_token'] ) ? $general_settings['vimeo_access_token'] : ''
				);
					
				add_option( 'aiovg_api_settings', $defaults );			
			}
			
			// Insert the missing page settings
			$page_settings = get_option( 'aiovg_page_settings' );

			if ( ! array_key_exists( 'tag', $page_settings ) ) {
				aiovg_insert_missing_pages();			
			}

			// Insert the privacy settings			
			if ( false == get_option( 'aiovg_privacy_settings' ) ) {
				add_option( 'aiovg_privacy_settings', $defaults['aiovg_privacy_settings'] );
			}	
			
			// Delete the unwanted plugin options
			delete_option( 'aiovg_image_settings' );
		}
	}		

	/**
	 * Handle form actions.
	 *
	 * @since 1.6.5
	 */
	public function handle_form_actions() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['issues'] ) && isset( $_POST['aiovg_issues_nonce'] ) ) {
			// Verify that the nonce is valid
    		if ( wp_verify_nonce( $_POST['aiovg_issues_nonce'], 'aiovg_fix_ignore_issues' ) ) {
				$redirect_url = admin_url( 'admin.php?page=all-in-one-video-gallery&tab=issues' );

				// Fix Issues
				if ( __( 'Apply Fix', 'all-in-one-video-gallery' ) == $_POST['action'] ) {
					$this->fix_issues();

					$redirect_url = add_query_arg( 
						array( 
							'section' => 'found',
							'success' => 1
						), 
						$redirect_url 
					);
				}

				// Ignore Issues
				if ( __( 'Ignore', 'all-in-one-video-gallery' ) == $_POST['action'] ) {
					$this->ignore_issues();

					$redirect_url = add_query_arg( 
						array( 
							'section' => 'ignored',
							'success' => 1
						), 
						$redirect_url 
					);
				}

				// Redirect
				wp_redirect( $redirect_url );
        		exit;
			}
		}		
	}

	/**
	 * Add plugin's main menu and "Dashboard" menu.
	 *
	 * @since 1.6.5
	 */
	public function admin_menu() {	
		add_menu_page(
            __( 'All-in-One Video Gallery', 'all-in-one-video-gallery' ),
            __( 'Video Gallery', 'all-in-one-video-gallery' ),
            'manage_aiovg_options',
            'all-in-one-video-gallery',
            array( $this, 'display_dashboard_content' ),
            'dashicons-playlist-video',
            5
		);	
		
		add_submenu_page(
			'all-in-one-video-gallery',
			__( 'All-in-One Video Gallery - Dashboard', 'all-in-one-video-gallery' ),
			__( 'Dashboard', 'all-in-one-video-gallery' ),
			'manage_aiovg_options',
			'all-in-one-video-gallery',
			array( $this, 'display_dashboard_content' )
		);
	}

	/**
	 * Display dashboard page content.
	 *
	 * @since 1.6.5
	 */
	public function display_dashboard_content() {
		$tabs = array(			
			'shortcode-builder' => __( 'Shortcode Builder', 'all-in-one-video-gallery' ),
			'faq'               => __( 'FAQ', 'all-in-one-video-gallery' )
		);
		
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'shortcode-builder';

		// Issues
		$issues = $this->check_issues();

		if ( count( $issues['found'] ) || 'issues' == $active_tab ) {
			$tabs['issues'] = __( 'Issues Found', 'all-in-one-video-gallery' );
		}

		// Validate active tab
		if ( ! in_array( $active_tab, array_keys( $tabs ) ) ) {
			$active_tab = 'shortcode-builder';
		}

		require_once AIOVG_PLUGIN_DIR . 'admin/partials/dashboard.php';	
	}

	/**
	 * Check for plugin issues.
	 *
	 * @since  1.6.5
	 * @return array $issues Array of issues found.
	 */
	public function check_issues() {
		$issues = array(
			'found'   => array(),
			'ignored' => array()
		);

		$_issues = get_option( 'aiovg_issues', $issues );
		$ignored = $_issues['ignored'];		

		// Check: pages_misconfigured
		$page_settings = get_option( 'aiovg_page_settings' );
		$pages = aiovg_get_custom_pages_list();

		foreach ( $pages as $key => $page ) {
			$issue_found = 0;
			$post_id = $page_settings[ $key ];			

			if ( $post_id > 0 ) {
				$post = get_post( $post_id );

				if ( empty( $post ) || 'publish' != $post->post_status ) {
					$issue_found = 1;
				} elseif ( ! empty( $pages[ $key ]['content'] ) && false === strpos( $post->post_content, $pages[ $key ]['content'] ) ) {
					$issue_found = 1;				
				}
			} else {
				$issue_found = 1;
			}

			if ( $issue_found ) {
				if ( in_array( 'pages_misconfigured', $ignored ) ) {
					$issues['ignored'][] = 'pages_misconfigured';
				} else {
					$issues['found'][] = 'pages_misconfigured';
				}

				break;
			}			
		}		

		$issues = apply_filters( 'aiovg_check_issues', $issues );

		// Update		
		update_option( 'aiovg_issues', $issues );

		// Return
		return $issues;
	}	

	/**
	 * Apply fixes.
	 *
	 * @since 1.6.5
	 */
	public function fix_issues() {		
		$fixed = array();

		// Apply the fixes
		$_issues = aiovg_sanitize_array( $_POST['issues'] );

		foreach ( $_issues as $issue ) {
			switch ( $issue ) {
				case 'pages_misconfigured':	
					global $wpdb;

					$page_settings = get_option( 'aiovg_page_settings' );

					$pages = aiovg_get_custom_pages_list();					

					foreach ( $pages as $key => $page ) {
						$issue_found = 0;
						$post_id = $page_settings[ $key ];			
			
						if ( $post_id > 0 ) {
							$post = get_post( $post_id );
			
							if ( empty( $post ) || 'publish' != $post->post_status ) {
								$issue_found = 1;
							} elseif ( ! empty( $pages[ $key ]['content'] ) && false === strpos( $post->post_content, $pages[ $key ]['content'] ) ) {
								$issue_found = 1;		
							}
						} else {
							$issue_found = 1;
						}	
						
						if ( $issue_found ) {
							$insert_id = 0;

							if ( ! empty( $pages[ $key ]['content'] ) ) {
								$query = $wpdb->prepare(
									"SELECT ID FROM {$wpdb->posts} WHERE `post_content` LIKE %s",
									sanitize_text_field( $pages[ $key ]['content'] )
								);

								$ids = $wpdb->get_col( $query );
							} else {
								$ids = array();
							}

							if ( ! empty( $ids ) ) {
								$insert_id = $ids[0];

								// If the page is not published
								if ( 'publish' != get_post_status( $insert_id ) ) {
									wp_update_post(
										array(
											'ID'          => $insert_id,
											'post_status' => 'publish'
										)
									);
								}
							} else {
								$insert_id = wp_insert_post(
									array(
										'post_title'     => $pages[ $key ]['title'],
										'post_content'   => $pages[ $key ]['content'],
										'post_status'    => 'publish',
										'post_author'    => 1,
										'post_type'      => 'page',
										'comment_status' => 'closed'
									)
								);
							}

							$page_settings[ $key ] = $insert_id;
						}
					}

					update_option( 'aiovg_page_settings', $page_settings );

					$fixed[] = $issue;
					break;
			}
		}

		$fixed = apply_filters( 'aiovg_fix_issues', $fixed );

		// Update
		$issues = get_option( 'aiovg_issues', array(
			'found'   => array(),
			'ignored' => array()
		));

		foreach ( $issues['found'] as $index => $issue ) {
			if ( in_array( $issue, $fixed ) ) {
				unset( $issues['found'][ $index ] );
			}
		}

		foreach ( $issues['ignored'] as $index => $issue ) {
			if ( in_array( $issue, $fixed ) ) {
				unset( $issues['ignored'][ $index ] );
			}
		}

		update_option( 'aiovg_issues', $issues );
	}

	/**
	 * Ignore issues.
	 *
	 * @since 1.6.5
	 */
	public function ignore_issues() {
		$ignored = array();

		// Ignore the issues
		$_issues = aiovg_sanitize_array( $_POST['issues'] );		

		foreach ( $_issues as $issue ) {
			switch ( $issue ) {
				case 'pages_misconfigured':					
					$ignored[] = $issue;
					break;
			}
		}

		$ignored = apply_filters( 'aiovg_ignore_issues', $ignored );

		// Update
		$issues = get_option( 'aiovg_issues', array(
			'found'   => array(),
			'ignored' => array()
		));

		foreach ( $issues['found'] as $index => $issue ) {
			if ( in_array( $issue, $ignored ) ) {
				unset( $issues['found'][ $index ] );
			}
		}

		$issues['ignored'] = array_merge( $issues['ignored'], $ignored );

		update_option( 'aiovg_issues', $issues );
	}	

	/**
	 * Get details of the given issue.
	 *
	 * @since  1.6.5
	 * @param  string $issue Issue code.
	 * @return array         Issue details.
	 */
	public function get_issue_details( $issue ) {
		$issues_list = array(
			'pages_misconfigured' => array(
				'title'       => __( 'Pages Misconfigured', 'all-in-one-video-gallery' ),
				'description' => sprintf(
					__( 'During activation, our plugin adds few <a href="%s" target="_blank">pages</a> dynamically on your website that are required for the internal logic of the plugin. We found some of those pages are missing, misconfigured or having a wrong shortcode.', 'all-in-one-video-gallery' ),
					esc_url( admin_url( 'admin.php?page=aiovg_settings&tab=advanced&section=aiovg_page_settings' ) )
				)
			)
		);

		$issues_list = apply_filters( 'aiovg_get_issues_list', $issues_list );
	
		return isset( $issues_list[ $issue ] ) ? $issues_list[ $issue ] : '';
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-magnific-popup', 
			AIOVG_PLUGIN_URL . 'vendor/magnific-popup/magnific-popup.min.css', 
			array(), 
			'1.1.0', 
			'all' 
		);
		
		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-admin', 
			AIOVG_PLUGIN_URL . 'admin/assets/css/admin.min.css', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();
		
        wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-magnific-popup', 
			AIOVG_PLUGIN_URL . 'vendor/magnific-popup/magnific-popup.min.js', 
			array( 'jquery' ), 
			'1.1.0', 
			array( 'strategy' => 'defer' ) 
		);
		
		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-admin', 
			AIOVG_PLUGIN_URL . 'admin/assets/js/admin.min.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			array( 'strategy' => 'defer' ) 
		);

		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-admin', 
			'aiovg_admin', 
			array(
				'ajax_nonce' => wp_create_nonce( 'aiovg_ajax_nonce' ),
				'site_url'   => esc_url_raw( get_site_url() ),
				'i18n'       => array(
					'copied'             => __( 'Copied!', 'all-in-one-video-gallery' ),
					'no_issues_selected' => __( 'Please select at least one issue.', 'all-in-one-video-gallery' ),
					'no_video_selected'  => __( 'No video selected. The last added video will be displayed.', 'all-in-one-video-gallery' ),
					'quality_exists'     => __( 'Sorry, there is already a video with this quality level.', 'all-in-one-video-gallery' ),
					'remove'             => __( 'Remove', 'all-in-one-video-gallery' ),
				)				
			)
		);
	}

	/**
	 * Add a post display state for special AIOVG pages in the page list table.
	 *
	 * @since 2.5.8
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 */
	public function add_display_post_states( $post_states, $post ) {
		$page_settings = get_option( 'aiovg_page_settings', array() );
		
		if ( isset( $page_settings['category'] ) && $page_settings['category'] == $post->ID ) {
			$post_states['aiovg_page_for_category'] = __( 'Video Category Page', 'all-in-one-video-gallery' );
		}

		if ( isset( $page_settings['tag'] ) && $page_settings['tag'] == $post->ID ) {
			$post_states['aiovg_page_for_tag'] = __( 'Video Tag Page', 'all-in-one-video-gallery' );
		}
		
		if ( isset( $page_settings['search'] ) && $page_settings['search'] == $post->ID ) {
			$post_states['aiovg_page_for_search'] = __( 'Search Results Page', 'all-in-one-video-gallery' );
		}
		
		if ( isset( $page_settings['user_videos'] ) && $page_settings['user_videos'] == $post->ID ) {
			$post_states['aiovg_page_for_user_videos'] = __( 'User Videos Page', 'all-in-one-video-gallery' );
		}
		
		if ( isset( $page_settings['player'] ) && $page_settings['player'] == $post->ID ) {
			$post_states['aiovg_page_for_player'] = __( 'Player Page', 'all-in-one-video-gallery' );
		}
		
		if ( isset( $page_settings['user_dashboard'] ) && $page_settings['user_dashboard'] == $post->ID ) {
			$post_states['aiovg_page_for_user_dashboard'] = __( 'User Dashboard Page', 'all-in-one-video-gallery' );
		}
		
		if ( isset( $page_settings['video_form'] ) && $page_settings['video_form'] == $post->ID ) {
			$post_states['aiovg_page_for_video_form'] = __( 'Video Form Page', 'all-in-one-video-gallery' );
		}

		if ( isset( $page_settings['playlist'] ) && $page_settings['playlist'] == $post->ID ) {
			$post_states['aiovg_page_for_playlist'] = __( 'My Playlists Page', 'all-in-one-video-gallery' );
		}

		return $post_states;
	}

	/**
	 * Add a settings link on the plugin listing page.
	 *
	 * @since  1.0.0
	 * @param  array  $links An array of plugin action links.
	 * @return string $links Array of filtered plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( 
			'<a href="%s">%s</a>', 
			esc_url( admin_url( 'admin.php?page=aiovg_settings' ) ), 
			__( 'Settings', 'all-in-one-video-gallery' ) 
		);

        array_unshift( $links, $settings_link );
		
    	return $links;
	}

	/**
	 * Sets the extension and mime type for .vtt files.
	 *
	 * @since  1.5.7
	 * @param  array  $types    File data array containing 'ext', 'type', and 'proper_filename' keys.
     * @param  string $file     Full path to the file.
     * @param  string $filename The name of the file (may differ from $file due to $file being in a tmp directory).
     * @param  array  $mimes    Key is the file extension with value as the mime type.
	 * @return array  $types    Filtered file data array.
	 */
	public function add_filetype_and_ext( $types, $file, $filename, $mimes ) {
		if ( false !== strpos( $filename, '.vtt' ) ) {			
			$types['ext']  = 'vtt';
			$types['type'] = 'text/vtt';
		}
	
		return $types;
	}

}
