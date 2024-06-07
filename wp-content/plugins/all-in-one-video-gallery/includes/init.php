<?php

/**
 * The file that defines the core plugin class.
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
 * AIOVG_Init - The main plugin class.
 *
 * @since 1.0.0
 */
class AIOVG_Init {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    AIOVG_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Get things started.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->blocks_init();
		$this->widgets_init();
		$this->set_meta_caps();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once AIOVG_PLUGIN_DIR . 'includes/loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once AIOVG_PLUGIN_DIR . 'includes/i18n.php';
		
		/**
		 * The class responsibe for defining custom capabilities.
		 */
		 require_once AIOVG_PLUGIN_DIR . 'includes/roles.php';
		
		/**
		 * The class responsible for extending the 'wp_terms_checklist' function.
		 */
		require_once AIOVG_PLUGIN_DIR . 'includes/walker-terms-checklist.php';

		/**
		 * The class responsible for extending the 'wp_dropdown_categories' function.
		 */
		require_once AIOVG_PLUGIN_DIR . 'includes/walker-terms-dropdown.php';
		
		/**
		 * The class responsibe for the video player related functionalities.
		 */
		require_once AIOVG_PLUGIN_DIR . 'includes/player.php';
		
		/**
		 * The file that holds the general helper functions.
		 */
		require_once AIOVG_PLUGIN_DIR . 'includes/functions.php';

		/**
		 * The classes responsible for defining all actions that occur in the admin area.
		 */
		require_once AIOVG_PLUGIN_DIR . 'admin/admin.php';
		require_once AIOVG_PLUGIN_DIR . 'admin/videos.php';
		require_once AIOVG_PLUGIN_DIR . 'admin/categories.php';
		require_once AIOVG_PLUGIN_DIR . 'admin/tags.php';
		require_once AIOVG_PLUGIN_DIR . 'admin/settings.php';

		/**
		 * The classes responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once AIOVG_PLUGIN_DIR . 'public/public.php';		
		require_once AIOVG_PLUGIN_DIR . 'public/categories.php';
		require_once AIOVG_PLUGIN_DIR . 'public/videos.php';		
		require_once AIOVG_PLUGIN_DIR . 'public/video.php';
		require_once AIOVG_PLUGIN_DIR . 'public/search.php';
		require_once AIOVG_PLUGIN_DIR . 'public/likes.php';
		require_once AIOVG_PLUGIN_DIR . 'public/multilingual.php';
		require_once AIOVG_PLUGIN_DIR . 'public/conflict.php';		
		
		/**
		 * The class responsible for defining actions that occur in the gutenberg blocks.
		 */
		require_once AIOVG_PLUGIN_DIR. 'blocks/blocks.php';

		/**
		 * The classes responsible for defining all actions that occur in the widgets.
		 */
		require_once AIOVG_PLUGIN_DIR . 'widgets/categories.php';
		require_once AIOVG_PLUGIN_DIR . 'widgets/videos.php';				
		require_once AIOVG_PLUGIN_DIR . 'widgets/video.php';
		require_once AIOVG_PLUGIN_DIR . 'widgets/search.php';			

		$this->loader = new AIOVG_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function set_locale() {
		$i18n = new AIOVG_i18n();
		$this->loader->add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_admin_hooks() {
		// Hooks common to all admin pages
		$admin = new AIOVG_Admin();
				
		$this->loader->add_action( 'admin_init', $admin, 'insert_missing_options', 1 );		
		$this->loader->add_action( 'admin_init', $admin, 'handle_form_actions' );
		$this->loader->add_action( 'admin_menu', $admin, 'admin_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );		
		
		$this->loader->add_filter( 'display_post_states', $admin, 'add_display_post_states', 10, 2 );
		$this->loader->add_filter( 'plugin_action_links_' . AIOVG_PLUGIN_FILE_NAME, $admin, 'plugin_action_links' );
		$this->loader->add_filter( 'wp_check_filetype_and_ext', $admin, 'add_filetype_and_ext', 10, 4 );	
		
		// Hooks specific to the videos page
		$videos = new AIOVG_Admin_Videos();
		
		$this->loader->add_action( 'admin_menu', $videos, 'admin_menu' );
		$this->loader->add_action( 'init', $videos, 'register_post_type' );
		$this->loader->add_action( 'before_delete_post', $videos, 'before_delete_post' );
		
		if ( is_admin() ) {		
			$this->loader->add_action( 'post_submitbox_misc_actions', $videos, 'post_submitbox_misc_actions' );
			$this->loader->add_action( 'add_meta_boxes', $videos, 'add_meta_boxes' );
			$this->loader->add_action( 'save_post', $videos, 'save_meta_data', 10, 2 );
			$this->loader->add_action( 'restrict_manage_posts', $videos, 'restrict_manage_posts' );
			$this->loader->add_action( 'manage_aiovg_videos_posts_custom_column', $videos, 'custom_column_content', 10, 2 );
			
			$this->loader->add_filter( 'parent_file', $videos, 'parent_file' );
			$this->loader->add_filter( 'parse_query', $videos, 'parse_query' );
			$this->loader->add_filter( 'post_row_actions', $videos, 'row_actions', 10, 2 );
			$this->loader->add_filter( 'manage_edit-aiovg_videos_columns', $videos, 'get_columns' );
			$this->loader->add_filter( 'manage_edit-aiovg_videos_sortable_columns', $videos, 'sortable_columns' );
			$this->loader->add_filter( 'use_block_editor_for_post_type', $videos, 'disable_gutenberg', 10, 2 );
			$this->loader->add_filter( 'gutenberg_can_edit_post_type', $videos, 'disable_gutenberg', 10, 2 );			
		}
		
		// Hooks specific to the categories page
		$categories = new AIOVG_Admin_Categories();
		
		$this->loader->add_action( 'admin_menu', $categories, 'admin_menu' );
		$this->loader->add_action( 'init', $categories, 'register_taxonomy' );
		$this->loader->add_action( 'aiovg_categories_add_form_fields', $categories, 'add_form_fields' );		
		$this->loader->add_action( 'aiovg_categories_edit_form_fields', $categories, 'edit_form_fields' );
		$this->loader->add_action( 'created_aiovg_categories', $categories, 'save_form_fields' );
		$this->loader->add_action( 'edited_aiovg_categories', $categories, 'save_form_fields' );		
		$this->loader->add_action( 'pre_delete_term', $categories, 'pre_delete_term', 10, 2 );	
		
		$this->loader->add_filter( 'parent_file', $categories, 'parent_file' );
		$this->loader->add_filter( "manage_edit-aiovg_categories_columns", $categories, 'get_columns' );
		$this->loader->add_filter( "manage_edit-aiovg_categories_sortable_columns", $categories, 'get_columns' );
		$this->loader->add_filter( "manage_aiovg_categories_custom_column", $categories, 'custom_column_content', 10, 3 );		
		
		// Hooks specific to the tags page
		$tags = new AIOVG_Admin_Tags();
		
		$this->loader->add_action( 'admin_menu', $tags, 'admin_menu' );
		$this->loader->add_action( 'init', $tags, 'register_taxonomy' );
		
		$this->loader->add_filter( 'parent_file', $tags, 'parent_file' );
		$this->loader->add_filter( "manage_edit-aiovg_tags_columns", $tags, 'get_columns' );
		$this->loader->add_filter( "manage_edit-aiovg_tags_sortable_columns", $tags, 'get_columns' );
		$this->loader->add_filter( "manage_aiovg_tags_custom_column", $tags, 'custom_column_content', 10, 3 );
		
		// Hooks specific to the settings page
		$settings = new AIOVG_Admin_Settings();
		
		$this->loader->add_action( 'admin_menu', $settings, 'admin_menu' );
		$this->loader->add_action( 'admin_init', $settings, 'admin_init' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_public_hooks() {
		// Hooks common to all public pages
		$public = new AIOVG_Public();

		$this->loader->add_action( 'template_redirect', $public, 'template_redirect', 0 );
		$this->loader->add_action( 'init', $public, 'init' );
		$this->loader->add_action( 'init', $public, 'register_styles' );
		$this->loader->add_action( 'init', $public, 'register_scripts' );
		$this->loader->add_action( 'aiovg_enqueue_block_editor_assets', $public, 'enqueue_block_editor_assets' );	
		$this->loader->add_action( 'wp_loaded', $public, 'maybe_flush_rules', 11 );				
		$this->loader->add_action( 'wp_head', $public, 'wp_head' );
		$this->loader->add_action( 'wp_ajax_aiovg_set_cookie', $public, 'set_gdpr_cookie' );
		$this->loader->add_action( 'wp_ajax_nopriv_aiovg_set_cookie', $public, 'set_gdpr_cookie' );
		
		if ( aiovg_can_use_yoast() ) {
			$this->loader->add_filter( 'wpseo_title', $public, 'wpseo_title' );
			$this->loader->add_filter( 'wpseo_opengraph_title', $public, 'wpseo_title' );
			$this->loader->add_filter( 'wpseo_metadesc', $public, 'wpseo_metadesc' );
			$this->loader->add_filter( 'wpseo_opengraph_desc', $public, 'wpseo_metadesc' );
			$this->loader->add_filter( 'wpseo_canonical', $public, 'wpseo_canonical' );
			$this->loader->add_filter( 'wpseo_opengraph_url', $public, 'wpseo_canonical' );
			$this->loader->add_filter( 'wpseo_opengraph_image', $public, 'wpseo_opengraph_image' );
			$this->loader->add_filter( 'wpseo_twitter_image', $public, 'wpseo_twitter_image' );
			$this->loader->add_filter( 'wpseo_breadcrumb_links', $public, 'wpseo_breadcrumb_links' );
			$this->loader->add_filter( 'wpseo_video_custom_field_details', $public, 'wpseo_video_sitemap_entry' );
			$this->loader->add_filter( 'wpseo_video_youtube_details', $public, 'wpseo_video_sitemap_entry' );
			$this->loader->add_filter( 'wpseo_video_vimeo_details', $public, 'wpseo_video_sitemap_entry' );
		} else {
			$this->loader->add_filter( 'wp_title', $public, 'wp_title', 99, 3 );
			$this->loader->add_filter( 'document_title_parts', $public, 'document_title_parts' );
		}				

		$this->loader->add_filter( 'the_title', $public, 'the_title', 99, 2 );
		$this->loader->add_filter( 'single_post_title', $public, 'the_title', 99 );
		$this->loader->add_filter( 'has_post_thumbnail', $public, 'has_post_thumbnail', 10, 3 );
		$this->loader->add_filter( 'post_thumbnail_html', $public, 'post_thumbnail_html', 10, 5 );
		$this->loader->add_filter( 'term_link', $public, 'term_link', 10, 3 );
		
		// Hooks specific to the categories page
		$categories = new AIOVG_Public_Categories();
		
		$this->loader->add_action( 'wp_ajax_aiovg_load_more_categories', $categories, 'ajax_callback_load_more_categories' );
		$this->loader->add_action( 'wp_ajax_nopriv_aiovg_load_more_categories', $categories, 'ajax_callback_load_more_categories' );
		
		// Hooks specific to the videos page
		$videos = new AIOVG_Public_Videos();

		$this->loader->add_action( 'wp_ajax_aiovg_load_more_videos', $videos, 'ajax_callback_load_more_videos' );
		$this->loader->add_action( 'wp_ajax_nopriv_aiovg_load_more_videos', $videos, 'ajax_callback_load_more_videos' );
		
		// Hooks specific to the single video page
		$video = new AIOVG_Public_Video();
		
		$this->loader->add_action( 'template_include', $video, 'template_include', 999 );
		$this->loader->add_action( 'init', $video, 'download_video', 1 );		
		$this->loader->add_action( 'wp_ajax_aiovg_update_views_count', $video, 'ajax_callback_update_views_count' );
		$this->loader->add_action( 'wp_ajax_nopriv_aiovg_update_views_count', $video, 'ajax_callback_update_views_count' );	

		$this->loader->add_filter( 'upload_mimes', $video, 'add_mime_types' );
		$this->loader->add_filter( 'aiovg_videojs_player_sources', $video, 'player_sources', 10, 2 );
		$this->loader->add_filter( 'aiovg_vidstack_player_sources', $video, 'player_sources', 10, 2 );
		$this->loader->add_filter( 'aiovg_iframe_vidstack_player_sources', $video, 'player_sources' );
		$this->loader->add_filter( 'aiovg_iframe_videojs_player_sources', $video, 'player_sources' );
		$this->loader->add_filter( 'the_content', $video, 'the_content', 20 );
		$this->loader->add_filter( 'comments_open', $video, 'comments_open', 10, 2 );		
		
		// Hooks specific to the search form
		$search = new AIOVG_Public_Search();

		// Hooks specific to the like / dislike button
		$likes_settings = get_option( 'aiovg_likes_settings' );

		if ( ! empty( $likes_settings['like_button'] ) || ! empty( $likes_settings['dislike_button'] ) ) {
			$likes = new AIOVG_Public_Likes();
			
			$this->loader->add_action( 'wp_ajax_aiovg_get_likes_dislikes_info', $likes, 'ajax_callback_get_likes_dislikes_info' );
			$this->loader->add_action( 'wp_ajax_nopriv_aiovg_get_likes_dislikes_info', $likes, 'ajax_callback_get_likes_dislikes_info' );
			$this->loader->add_action( 'wp_ajax_aiovg_toggle_likes', $likes, 'ajax_callback_toggle_likes' );
			$this->loader->add_action( 'wp_ajax_nopriv_aiovg_toggle_likes', $likes, 'ajax_callback_toggle_likes' );
			$this->loader->add_action( 'wp_ajax_aiovg_toggle_dislikes', $likes, 'ajax_callback_toggle_dislikes' );
			$this->loader->add_action( 'wp_ajax_nopriv_aiovg_toggle_dislikes', $likes, 'ajax_callback_toggle_dislikes' );
		}		

		// Hooks that make the plugin multilingual compatible
		if ( ! is_admin() ) {
			$public_multilingual = new AIOVG_Public_Multilingual();

			$this->loader->add_filter( 'option_aiovg_page_settings', $public_multilingual, 'filter_page_settings_for_polylang' );
		}

		// Fixes for third-party plugin/theme conflict
		$conflict = new AIOVG_Public_Conflict();

		$this->loader->add_filter( 'autoptimize_filter_noptimize', $conflict, 'noptimize' );
		$this->loader->add_filter( 'smush_skip_iframe_from_lazy_load', $conflict, 'smush', 999, 2 );
		$this->loader->add_filter( 'rank_math/snippet/rich_snippet_videoobject_entity', $conflict, 'rank_math' );
	}

	/**
	 * Initialize Blocks.
	 *
	 * @since  1.5.6
	 * @access private
	 */
	private function blocks_init() {
		if ( is_admin() ) {
			global $pagenow;
			if ( 'widgets.php' === $pagenow ) return;
		}

		global $wp_version;

		$blocks = new AIOVG_Blocks();

		$this->loader->add_action( 'init', $blocks, 'register_block_types' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $blocks, 'enqueue_block_editor_assets' );

		if ( version_compare( $wp_version, '5.8', '>=' ) ) {
			$this->loader->add_filter( 'block_categories_all', $blocks, 'block_categories' );
		} else {
			$this->loader->add_filter( 'block_categories', $blocks, 'block_categories' );
		}
	}
	
	/**
	 * Add hook to register widgets.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function widgets_init() {
		$this->loader->add_action( 'widgets_init', $this, 'register_widgets' );
	}
	
	/**
	 * Register widgets.
	 *
	 * @since 1.0.0
	 */
	public function register_widgets() {		
		register_widget( "AIOVG_Widget_Categories" );
		register_widget( "AIOVG_Widget_Videos" );		
		register_widget( "AIOVG_Widget_Video" );
		register_widget( "AIOVG_Widget_Search" );		
	}
	
	/**
	 * Map meta caps to primitive caps.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function set_meta_caps() {
		$roles = new AIOVG_Roles();
		$this->loader->add_filter( 'map_meta_cap', $roles, 'meta_caps', 10, 4 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

}
