<?php
/**
 * Class BP_Verified_Member
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package bp-verified-member/inc
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Verified_Member
 */
class BP_Verified_Member {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'bp-verified-member', false, 'bp-verified-member/languages' );

		ob_start( array( $this, 'filter_html_output' ) );

		add_filter( 'plugin_action_links_' . BP_VERIFIED_MEMBER_PLUGIN_BASENAME, array( $this, 'add_plugin_page_settings_link' ), 10, 1 );

		add_filter( 'wp_enqueue_scripts',                          array( $this, 'enqueue_scripts'                                 ), 1,  0 );

		/**
		 * Display badge in profile
		 */
		add_filter( 'bp_get_displayed_user_mentionname',           array( $this, 'profile_display_username_verified_badge'         ), 10, 1 );
		add_filter( 'bp_get_displayed_user_username',              array( $this, 'profile_display_username_verified_badge'         ), 10, 1 );
		add_filter( 'bp_displayed_user_fullname',                  array( $this, 'profile_display_fullname_verified_badge'         ), 10, 1 );
		add_filter( 'bp_core_get_user_displayname',                array( $this, 'bb_profile_display_fullname_verified_badge'      ), 20, 2 );

		/**
		 * Display badge in activities
		 */
		add_filter( 'bp_get_activity_action',                      array( $this, 'activity_display_verified_badge'                 ), 10, 2 );
		add_filter( 'bp_activity_comment_name',                    array( $this, 'activity_comment_display_verified_badge'         ), 10, 1 );
		add_filter( 'bp_nouveau_get_activity_comment_action',      array( $this, 'nouveau_activity_comment_display_verified_badge' ), 10, 1 );

		/**
		 * Display badge in members lists
		 */
		add_filter( 'bp_get_group_member_link',                    array( $this, 'members_lists_display_verified_badge'            ), 10, 1 );
		add_filter( 'bp_get_group_invite_user_link',               array( $this, 'members_lists_display_verified_badge'            ), 10, 1 );
		add_action( 'bp_before_member_friend_requests_content',    array( $this, 'add_friend_requests_member_name_filter'          ), 10, 0 );
		add_action( 'bp_after_member_friend_requests_content',     array( $this, 'remove_friend_requests_member_name_filter'       ), 10, 0 );
		add_filter( 'the_content',                                 array( $this, 'add_global_search_name_filter'                   ), 1,  1 );
		add_filter( 'the_content',                                 array( $this, 'remove_global_search_name_filter'                ), 20, 1 );
		add_filter( 'bp_get_member_class',                         array( $this, 'member_directory_add_verified_class'             ), 10, 1 );
		add_filter( 'aa_user_name_template',                       array( $this, 'author_avatars_display_verified_badge'           ), 10, 3 );

		/**
		 * Display badge in BP widgets
		 */
		add_action( 'dynamic_sidebar_before',                      array( $this, 'add_bp_widgets_name_filter'                      ), 10, 2 );
		add_action( 'dynamic_sidebar_after',                       array( $this, 'remove_bp_widgets_name_filter'                   ), 10, 2 );
		add_action( 'wp_ajax_widget_members',                      array( $this, 'add_bp_widgets_name_filter'                      ), 1,  0 );
		add_action( 'wp_ajax_nopriv_widget_members',               array( $this, 'add_bp_widgets_name_filter'                      ), 1,  0 );
		add_action( 'wp_ajax_widget_members',                      array( $this, 'remove_bp_widgets_name_filter'                   ), 99, 0 );
		add_action( 'wp_ajax_nopriv_widget_members',               array( $this, 'remove_bp_widgets_name_filter'                   ), 99, 0 );

		/**
		 * Display badge in private messages
		 */
		add_filter( 'bp_core_get_userlink',                        array( $this, 'private_message_list_display_verified_badge'     ), 10, 2 );
		add_filter( 'bp_get_the_thread_message_sender_name',       array( $this, 'private_message_display_verified_badge'          ), 10, 1 );

		/**
		 * Display badge in WP comments and posts
		 */
		add_filter( 'get_comment_author',                          array( $this, 'comment_display_verified_badge'                  ), 10, 2 );
		add_filter( 'the_author',                                  array( $this, 'post_author_display_verified_badge'              ), 10, 1 );
		add_filter( 'get_the_author_display_name',                 array( $this, 'post_author_display_verified_badge'              ), 10, 2 );

		/**
		 * Display badge in forums
		 */
		add_filter( 'bbp_get_topic_author_links',                  array( $this, 'bbp_topic_display_verified_badge'                ), 20, 3 );
		add_filter( 'bbp_get_reply_author_links',                  array( $this, 'bbp_reply_display_verified_badge'                ), 20, 3 );
		add_filter( 'bbp_get_author_links',                        array( $this, 'bbp_reply_display_verified_badge'                ), 20, 3 );

		/**
		 * Display badge in rtMedia
		 */
		add_filter( 'bp_core_get_user_displayname',                array( $this, 'rtmedia_single_display_verified_badge'           ), 10, 2 );

		/**
		 * Remove badge in unnecessary locations
		 */
		add_filter( 'bp_get_send_public_message_link',             array( $this, 'remove_badge_from_link'                          ), 10, 1 );
		add_filter( 'widget_title',                                array( $this, 'remove_badge_from_widget_title'                  ), 10, 3 );
		add_action( 'wp',                                          array( $this, 'remove_badge_from_bp_nav_links'                  ), 99, 0 );
		add_filter( 'bp_notifications_get_notifications_for_user', array( $this, 'remove_badge_from_bp_notifications'              ), 10, 1 );
		add_filter( 'comment_reply_link',                          array( $this, 'remove_badge_from_comment_reply_link'            ), 10, 1 );

		/**
		 * Handle verification requests
		 */
		add_action( 'bp_profile_header_meta',                      array( $this, 'display_verification_request_button'             ), 10    );
		add_action( 'bp_before_member_in_header_meta',             array( $this, 'display_verification_request_button'             ), 10    );
		add_action( 'wp_ajax_bp_verified_member_request',          array( $this, 'request_verification'                            ), 10    );
		add_action( 'wp_ajax_nopriv_bp_verified_member_request',   array( $this, 'request_verification'                            ), 10    );
		add_action( 'bp_core_install_emails',                      array( $this, 'create_admin_verification_request_email'         ), 10    );

		/**
		 * Add verified filter type in BP Profile Search
		 */
		add_filter( 'bps_add_fields',                              array( $this, 'bps_add_verified_field'                          ), 99, 1 );

		/**
		 * BP Notification
		 */
		add_filter( 'bp_notifications_get_registered_components',  array( $this, 'register_bp_verified_member_component'           ), 10, 2 );
		add_action( 'bp_verified_member_verified_status_updated',  array( $this, 'create_verified_status_updated_notification'     ), 10, 2 );
		add_filter( 'bp_notifications_get_notifications_for_user', array( $this, 'format_verified_member_notification'             ), 99, 8 );
	}

	/**
	 * Post-process HTML output to replace any escaped badge's HTML with their unescaped version
	 * This effectively allows us to bypass any unwanted HTML-escaping of our badges in filters that we hook onto.
	 *
	 * @param string $html The HTML code of the page before it is served to the client
	 *
	 * @return string The processed HTML of the page
	 */
	public function filter_html_output( $html ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return $html;
		}

		// For AJAX requests that return serialized data
		if ( is_serialized( $html ) ) {
			return $html;
		}

		// Not enough output
		if ( strlen( $html ) < 100 ) {
			return $html;
		}

		// If this isn't textual content, don't do any filtering.
		$is_textual = false;
		foreach ( headers_list() as $header ) {
			if ( preg_match( "#^content-type:\\s*text/#i", $header ) || preg_match( "#^content-type:\\s*application/json#i", $header ) ) {
				$is_textual = true;
				break;
			}
		}
		if ( ! $is_textual )
			return $html;

		// If this isn't a GET or POST, don't do anything.
		$method = strtoupper( $_SERVER['REQUEST_METHOD'] );
		switch ( $method ) {
			case 'GET' :
			case 'POST' :
				break;
			default :
				return $html;
		}

		// Get the escaped and unescaped versions of the verified/unverified badges
		$verified_badge                = $this->get_verified_badge();
		$verified_badge_escaped        = esc_html( $verified_badge );
		$unverified_badge              = $this->get_unverified_badge();
		$unverified_badge_escaped      = esc_html( $unverified_badge );

		// Get the json encoded versions
		$verified_badge_json           = substr( substr( json_encode( $verified_badge ), 1 ), 0, - 1 );
		$verified_badge_escaped_json   = substr( substr( json_encode( $verified_badge_escaped ), 1 ), 0, - 1 );
		$unverified_badge_json         = substr( substr( json_encode( $unverified_badge ), 1 ), 0, - 1 );
		$unverified_badge_escaped_json = substr( substr( json_encode( $unverified_badge_escaped ), 1 ), 0, - 1 );

		if ( ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) && ! empty( $verified_badge ) && ! empty( $verified_badge_escaped ) && ! empty( $unverified_badge ) && ! empty( $unverified_badge_escaped ) ) {
			// Replace escaped badges with their unescaped versions
			$html = preg_replace( array(
				'/' . preg_quote( $verified_badge_escaped, '/' ) . '(?![^<]*>)/m',
				'/' . preg_quote( $unverified_badge_escaped, '/' ) . '(?![^<]*>)/m',
				'/' . preg_quote( $verified_badge_escaped_json, '/' ) . '(?![^<]*>)/m',
				'/' . preg_quote( $unverified_badge_escaped_json, '/' ) . '(?![^<]*>)/m',
			), array(
				$verified_badge,
				$unverified_badge,
				$verified_badge_json,
				$unverified_badge_json,
			), $html );
		}

		return $html;
	}

	/**
	 * Add plugin settings link in plugin page
	 *
	 * @param array $links The plugin page links for this plugin
	 *
	 * @return array The modified list of links for this plugin
	 */
	public function add_plugin_page_settings_link( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=bp-verified-member' ) ) . '">' . esc_html__( 'Settings', 'bp-verified-member' ) . '</a>';
		return $links;
	}

	/**
	 * Enqueue plugin scripts and styles.
	 */
	public function enqueue_scripts() {
		global $bp_verified_member_admin;

		wp_enqueue_style( 'dashicons' );

		wp_enqueue_script( 'popper2', BP_VERIFIED_MEMBER_PLUGIN_DIR_URL . 'assets/js/vendor/popper.min.js', array(), '2.11.0' );
		wp_enqueue_script( 'bp-verified-member', BP_VERIFIED_MEMBER_PLUGIN_DIR_URL . 'assets/js/main.js', array( 'jquery', 'popper2' ), BP_VERIFIED_MEMBER_VERSION );

		$badge   = $this->get_verified_badge();
		$tooltip = esc_html( $bp_verified_member_admin->settings->get_option( 'tooltip_content' ) );

		$unverified_badge         = $this->get_unverified_badge();
		$unverified_tooltip       = esc_html( $bp_verified_member_admin->settings->get_option( 'unverified_tooltip_content' ) );

		$ajax_url = esc_js( admin_url( 'admin-ajax.php' ) );

		wp_localize_script( 'bp-verified-member', 'bpVerifiedMember', array(
			'verifiedBadgeHtml' => $badge,
			'verifiedTooltip' => $tooltip,
			'unverifiedBadgeHtml' => $unverified_badge,
			'unverifiedTooltip' => $unverified_tooltip,
			'ajaxUrl' => $ajax_url,
		) );

		wp_enqueue_style( 'bp-verified-member', BP_VERIFIED_MEMBER_PLUGIN_DIR_URL . 'assets/css/style.css', array(), BP_VERIFIED_MEMBER_VERSION );

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'bp-verified-member', 'rtl', 'replace' );

		global $bp_verified_member_admin;
		$verified_badge_shape   = BP_VERIFIED_MEMBER_PLUGIN_DIR_URL . 'assets/images/mask-' . $bp_verified_member_admin->settings->get_option( 'badge_shape' ) . '.svg';
		$unverified_badge_shape = BP_VERIFIED_MEMBER_PLUGIN_DIR_URL . 'assets/images/mask-' . $bp_verified_member_admin->settings->get_option( 'unverified_badge_shape' ) . '.svg';

		$verified_badge_color   = $bp_verified_member_admin->settings->get_option( 'badge_color' );
		$unverified_badge_color = $bp_verified_member_admin->settings->get_option( 'unverified_badge_color' );

		$badge_dynamic_css = "
			:root {
				--bp-verified-members-verified-badge-shape: url('$verified_badge_shape');
				--bp-verified-members-unverified-badge-shape: url('$unverified_badge_shape');
			}
		
			.bp-verified-badge,
			.bp-verified-member .member-name-item > a:after,
			.bp-verified-member .item-title > a:after,
			.bp-verified-member > .author > a:after,
			.bp-verified-member .member-name > a:after {
				background-color: $verified_badge_color !important;
			}
			
			.bp-unverified-badge,
			.bp-unverified-member .member-name-item > a:after,
			.bp-unverified-member .item-title > a:after,
			.bp-unverified-member > .author > a:after,
			.bp-unverified-member .member-name > a:after {
				background-color: $unverified_badge_color !important;
			}
		";

		wp_add_inline_style( 'bp-verified-member', $badge_dynamic_css );
	}

	/**
	 * Display the verified badge on user profile.
	 *
	 * @param string $username Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function profile_display_username_verified_badge( $username ) {
		$user_id = bp_displayed_user_id();

		global $bp_verified_member_admin;

		if ( ! bp_is_user() || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_profile_username' ) ) ) {
			return $username;
		}

		return $username . $this->get_user_badge( $user_id );
	}

	/**
	 * Display the verified badge on user profile.
	 *
	 * @param string $fullname Full name of the member.
	 *
	 * @return string Modified username.
	 */
	public function profile_display_fullname_verified_badge( $fullname ) {
		// Prevent badge from breaking <link> and <title> tags in page header
		if ( doing_action( 'bp_head' ) || doing_action( 'document_title_parts' ) ) {
			return $fullname;
		}

		$user_id = bp_displayed_user_id();

		global $bp_verified_member_admin;

		if ( ! bp_is_user() || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_profile_fullname' ) ) ) {
			return $fullname;
		}

		return $fullname . $this->get_user_badge( $user_id );
	}

	/**
	 * Display the verified badge on user profile.
	 *
	 * @param string $fullname Full name of the member.
	 *
	 * @return string Modified username.
	 */
	public function bb_profile_display_fullname_verified_badge( $fullname, $user_id ) {
		// This is a BuddyBoss compatibility function
		if ( empty( BuddyPress()->buddyboss ) ) {
			return $fullname;
		}

		// Prevent badge from breaking <link> and <title> tags in page header
		if ( doing_action( 'bp_head' ) || doing_action( 'document_title_parts' ) ) {
			return $fullname;
		}

		// Skip if this is not the displayed user name
		if ( $user_id != bp_displayed_user_id() ) {
			return $fullname;
		}

		global $bp_verified_member_admin;

		if ( ! bp_is_user() || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_profile_fullname' ) ) ) {
			return $fullname;
		}

		return $fullname . $this->get_user_badge( $user_id );
	}

	/**
	 * Display the verified badge in activities.
	 *
	 * @param string   $activity_action Activity action text.
	 * @param stdClass|bool $activity Activity object.
	 *
	 * @return string Modified activity action.
	 */
	public function activity_display_verified_badge( $activity_action, $activity = false ) {
		global $bp_verified_member_admin;

		if ( ! bp_is_active( 'activity' ) || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_activity_stream' ) ) || empty( $activity ) ) {
			return $activity_action;
		}

		$user_id = $activity->user_id;

		$userlink = bp_core_get_userlink( $user_id );

		$profile_link      = trailingslashit( bp_core_get_user_domain( $activity->user_id ) . bp_get_profile_slug() );
		$user_profile_link = '<a href="' . $profile_link . '">' . bp_core_get_user_displayname( $activity->user_id ) . '</a>';

		$badge = $this->get_user_badge( $user_id );

		if ( ! empty( $badge ) ) {
			$activity_action = str_replace( $userlink, $userlink . $badge, $activity_action );
			$activity_action = str_replace( $user_profile_link, $user_profile_link . $badge, $activity_action );
		}

		return $activity_action;
	}

	/**
	 * Display verified badge in activities comments.
	 *
	 * @param string $name Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function activity_comment_display_verified_badge( $name ) {
		global $activities_template, $bp_verified_member_admin;

		if ( ! bp_is_active( 'activity' ) || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_activity_stream' ) ) ) {
			return $name;
		}

		$user_id = isset( $activities_template->activity->current_comment ) ? $activities_template->activity->current_comment->user_id : $activities_template->activity->user_id;

		return $name . $this->get_user_badge( $user_id );
	}

	/**
	 * Display verified badge in activities with BP Nouveau template pack.
	 *
	 * @param string $action Activity action text.
	 *
	 * @return string Modified activity action.
	 */
	public function nouveau_activity_comment_display_verified_badge( $action ) {
		if ( ! bp_is_active( 'activity' ) ) {
			return $action;
		}

		$badge = $this->get_verified_badge();

		// Unescape the verified badge in bp_nouveau
		$action = str_replace( esc_html( $badge ), $badge, $action );

		return $action;
	}

	/**
	 * Display verified badge in member lists.
	 *
	 * @param string $name Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function members_lists_display_verified_badge( $name ) {
		global $members_template;

		if ( ! $members_template->member ) {
			return $name;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_members_lists' ) ) ) {
			return $name;
		}

		$user_id = isset( $members_template->members ) ? $members_template->member->id : false;

		return $name . $this->get_user_badge( $user_id );
	}

	/**
	 * Add a filter to handle verified badges in member name
	 */
	public function add_friend_requests_member_name_filter() {
		if ( 'nouveau' !== bp_get_theme_package_id() ) {
			add_filter( 'bp_member_name', array( $this, 'members_lists_display_verified_badge' ), 10, 1 );
		}
	}

	/**
	 * Remove the filter that handles verified badges in member name
	 */
	public function remove_friend_requests_member_name_filter() {
		if ( 'nouveau' !== bp_get_theme_package_id() ) {
			remove_filter( 'bp_member_name', array( $this, 'members_lists_display_verified_badge' ), 10 );
		}
	}

	/**
	 * Add a filter to handle verified badges in BP Global Search results
	 *
	 * @param string $the_content Content of the current page. Unused in this situation.
	 *
	 * @return string Content of the current page.
	 */
	public function add_global_search_name_filter( $the_content ) {
		if ( ! is_admin() && is_search() ) {
			add_filter( 'bp_member_name', array( $this, 'members_lists_display_verified_badge' ), 10, 1 );
		}

		return $the_content;
	}

	/**
	 * Remove the filter that handles verified badges in BP Global Search results
	 *
	 * @param string $the_content Content of the current page. Unused in this situation.
	 *
	 * @return string Content of the current page.
	 */
	public function remove_global_search_name_filter( $the_content ) {
		if ( ! is_admin() && is_search() ) {
			remove_filter( 'bp_member_name', array( $this, 'members_lists_display_verified_badge' ), 10 );
		}

		return $the_content;
	}

	/**
	 * Add verified class for each verified member in the directory.
	 *
	 * @param array $classes Classes that will be output in the member container.
	 *
	 * @return array Modified classes array.
	 */
	public function member_directory_add_verified_class( $classes ) {
		global $bp_verified_member_admin, $members_template;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_members_lists' ) ) ) {
			return $classes;
		}

		$user_id = $members_template->member->id;

		$display_unverified_badge = ! empty( $bp_verified_member_admin->settings->get_option( 'display_unverified_badge' ) );
		$is_verified              = $this->is_user_verified( $user_id );

		if ( ! $is_verified && ! $display_unverified_badge ) {
			return $classes;
		}

		$classes[] = $is_verified ? 'bp-verified-member' : 'bp-unverified-member';

		return $classes;
	}

	/**
	 * Display verified badge in member lists.
	 *
	 * @param string $name_html Username of the member wrapped in html.
	 * @param string $name Username of the member.
	 * @param stdClass|bool $user User object
	 *
	 * @return string Modified username.
	 */
	public function author_avatars_display_verified_badge( $name_html, $name = '', $user = false ) {
		if ( empty( $user ) ) {
			return $name_html;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_members_lists' ) ) ) {
			return $name_html;
		}

		$user_id = $user->user_id;

		return sprintf( $name_html, '%s' . $this->get_user_badge( $user_id ) );
	}

	/**
	 * Display badge on member avatar
	 *
	 * @param string $avatar Member avatar HTML
	 * @param array $args Avatar args
	 *
	 * @return mixed
	 */
	public function member_avatar_display_verified_badge( $avatar, $args ) {
		global $members_template;

		if ( ! $members_template->member ) {
			return $avatar;
		}
		
		$user_id = isset( $members_template->members ) ? $members_template->member->id : false;

		return $avatar . $this->get_user_badge( $user_id );
	}

	/**
	 * Add a filter to handle verified badges in BP widgets
	 *
	 * @param int|string $index Index, name, or ID of the dynamic sidebar.
	 * @param bool       $has_widgets Whether the sidebar is populated with widgets.
	 */
	public function add_bp_widgets_name_filter( $index = '', $has_widgets = true ) {
		global $bp_verified_member_admin;

		if ( $has_widgets && ! empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_bp_widgets' ) ) ) {
			add_filter( 'bp_member_name',   array( $this, 'bp_widgets_display_verified_badge'    ), 10, 1 );
			add_filter( 'bp_member_avatar', array( $this, 'member_avatar_display_verified_badge' ), 10, 2 );
		}
	}

	/**
	 * Remove the filter that handles verified badges in BP widgets
	 *
	 * @param int|string $index Index, name, or ID of the dynamic sidebar.
	 * @param bool       $has_widgets Whether the sidebar is populated with widgets.
	 */
	public function remove_bp_widgets_name_filter( $index = '', $has_widgets = true ) {
		global $bp_verified_member_admin;

		if ( $has_widgets && ! empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_bp_widgets' ) ) ) {
			remove_filter( 'bp_member_name',   array( $this, 'bp_widgets_display_verified_badge'    ), 10 );
			remove_filter( 'bp_member_avatar', array( $this, 'member_avatar_display_verified_badge' ), 10 );
		}
	}

	/**
	 * Display verified badge in BP widgets
	 *
	 * @param string $name Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function bp_widgets_display_verified_badge( $name ) {
		global $members_template;

		if ( ! $members_template->member ) {
			return $name;
		}

		$user_id = isset( $members_template->members ) ? $members_template->member->id : false;

		return esc_attr( $name . $this->get_user_badge( $user_id ) );
	}

	/**
	 * Display verified badge in private messages list
	 *
	 * @param string $userlink The link to the user
	 * @param int $user_id The user id
	 *
	 * @return string The modified link to the user
	 */
	public function private_message_list_display_verified_badge( $userlink, $user_id ) {
		if ( ! bp_is_messages_component() ) {
			return $userlink;
		}

		global $messages_template;

		if ( ! $messages_template || ! $messages_template->thread || ( ! $messages_template->thread->last_sender_id && ! $messages_template->thread->recipients ) ) {
			return $userlink;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_messages' ) ) ) {
			return $userlink;
		}

		return $userlink . $this->get_user_badge( $user_id );
	}

	/**
	 * Display verified badge in private message.
	 *
	 * @param string $name Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function private_message_display_verified_badge( $name ) {
		global $thread_template;

		if ( ! $thread_template->message ) {
			return $name;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_messages' ) ) ) {
			return $name;
		}

		$user_id = isset( $thread_template->message->sender_id ) ? $thread_template->message->sender_id : false;

		return $name . $this->get_user_badge( $user_id );
	}

	/**
	 * Display the verified badge in wp comments
	 *
	 * @param string $comment_author The comment author name to display
	 * @param int $comment_id The ID of the comment
	 *
	 * @return string The comment author name
	 */
	public function comment_display_verified_badge( $comment_author, $comment_id ) {
		if ( is_admin() ) {
			return $comment_author;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_wp_comments' ) ) ) {
			return $comment_author;
		}

		$comment = get_comment( $comment_id );

		return $comment_author . $this->get_user_badge( $comment->user_id );
	}

	/**
	 * Display the verified badge in wp posts
	 *
	 * @param string $post_author The post author name to display
	 * @param int|null $user_id Id of the author
	 *
	 * @return string The post author name
	 */
	public function post_author_display_verified_badge( $post_author, $user_id = null ) {
		if ( is_admin() || doing_action( 'wp_head' ) ) {
			return $post_author;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_wp_posts' ) ) ) {
			return $post_author;
		}

		if ( empty( $user_id ) ) {
			$user_id = get_the_author_meta( 'ID' );
		}

		return $post_author . $this->get_user_badge( $user_id );
	}

	/**
	 * Display the verified badge in bbpress topics
	 *
	 * @param array $author_links The author links
	 * @param array $parsed_args Array of parsed args
	 * @param array $args Array of args
	 *
	 * @return array The author links to display
	 */
	public function bbp_topic_display_verified_badge( $author_links, $parsed_args, $args ) {
		if ( $parsed_args['type'] === 'avatar' ) {
			return $author_links;
		}

		$topic_id = is_numeric( $args )
			? bbp_get_reply_id( $args )
			: bbp_get_reply_id( $parsed_args['post_id'] );

		if ( bbp_is_topic_anonymous( $topic_id ) ) {
			return $author_links;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_bbp_topics' ) ) ) {
			return $author_links;
		}

		$user_id    = bbp_get_topic_author_id( $topic_id );
		$user_badge = $this->get_user_badge( $user_id );

		if ( ! empty( $user_badge ) ) {
			// Remove any badge that could have been added by another hook to avoid duplicates
			foreach ( $author_links as $key => $author_link ) {
				$author_link          = str_replace( array( $user_badge, esc_html( $user_badge ) ), '', $author_link );
				$author_links[ $key ] = $author_link;
			}

			$author_links[] .= $user_badge;
		}

		return $author_links;
	}

	/**
	 * Display the verified badge in bbpress replies
	 *
	 * @param array $author_links The author links
	 * @param array $parsed_args Array of parsed args
	 * @param array $args Array of args
	 *
	 * @return array The author links to display
	 */
	public function bbp_reply_display_verified_badge( $author_links, $parsed_args, $args ) {
		if ( $parsed_args['type'] === 'avatar' ) {
			return $author_links;
		}

		$reply_id = is_numeric( $args )
			? bbp_get_reply_id( $args )
			: bbp_get_reply_id( $parsed_args['post_id'] );

		if ( bbp_is_reply_anonymous( $reply_id ) ) {
			return $author_links;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_bbp_replies' ) ) ) {
			return $author_links;
		}

		$user_id    = bbp_get_reply_author_id( $reply_id );
		$user_badge = $this->get_user_badge( $user_id );

		if ( ! empty( $user_badge ) ) {
			// Remove any badge that could have been added by another hook to avoid duplicates
			foreach ( $author_links as $key => $author_link ) {
				$author_link          = str_replace( array( $user_badge, esc_html( $user_badge ) ), '', $author_link );
				$author_links[ $key ] = $author_link;
			}

			$author_links[] .= $user_badge;
		}

		return $author_links;
	}

	/**
	 * Display verified badge in rtMedia single
	 *
	 * @param string $display_name The user display name
	 * @param int $user_id The user ID
	 *
	 * @return string The user display name
	 */
	public function rtmedia_single_display_verified_badge( $display_name, $user_id ) {
		global $rt_ajax_request;
		if ( function_exists( 'is_rtmedia_single' ) && is_rtmedia_single() && $rt_ajax_request ) {
			global $bp_verified_member_admin;

			if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_rtmedia' ) ) ) {
				return $display_name;
			}

			return $display_name . $this->get_user_badge( $user_id );
		}

		return $display_name;
	}

	/**
	 * Remove the badge HTML from link
	 *
	 * @param string $link The link that needs badge removal
	 *
	 * @return string The modified link
	 */
	public function remove_badge_from_link( $link ) {
		$verified_badge   = $this->get_verified_badge();
		$unverified_badge = $this->get_unverified_badge();

		return str_replace( array( urlencode( $verified_badge ), urlencode( $unverified_badge ) ), '', $link );
	}

	/**
	 * Remove the badge HTML from the widget title
	 *
	 * @param string $title The widget title that needs badge removal
	 * @param string|bool $instance The widget instance
	 * @param string|bool $id_base The widget base id
	 *
	 * @return string The modified widget title
	 */
	public function remove_badge_from_widget_title( $title, $instance = false, $id_base = false ) {

		if ( $id_base !== 'bp_core_friends_widget' || empty( $instance ) ) {
			return $title;
		}

		$verified_badge   = $this->get_verified_badge();
		$unverified_badge = $this->get_unverified_badge();

		return str_replace( array( esc_html( $verified_badge ), esc_html( $unverified_badge ) ), '', $title );
	}

	/**
	 * Remove the badge HTML from BP nav links
	 */
	public function remove_badge_from_bp_nav_links() {
		/** @var BP_Core_Nav_Item[] $members_navs */
		$members_navs     = buddypress()->members->nav->get();
		$verified_badge   = $this->get_verified_badge();
		$unverified_badge = $this->get_unverified_badge();

		foreach ( $members_navs as $nav ) {
			$nav_args         = $nav->getArrayCopy();
			$nav_args['link'] = str_replace( array( $verified_badge, $unverified_badge ), '', $nav_args['link'] );
			$nav_parent_slug  = ! empty( $nav_args['parent_slug'] ) ? $nav_args['parent_slug'] : '';

			buddypress()->members->nav->edit_nav( $nav_args, $nav_args['slug'], $nav_parent_slug );
		}
	}

	/**
	 * Remove the badge HTML from BP notifications
	 *
	 * @param string|object $notification_content The notification that might need badge removal
	 *
	 * @return string|object The modified notification
	 */
	public function remove_badge_from_bp_notifications( $notification_content ) {
		$verified_badge   = $this->get_verified_badge();
		$unverified_badge = $this->get_unverified_badge();

		if ( is_string( $notification_content ) ) {
			$notification_content = str_replace( array( esc_html( $verified_badge ), esc_html( $unverified_badge ) ), '', $notification_content );
		}
		elseif ( ! empty( $notification_content->text ) ) {
			$notification_content->text = str_replace( array( esc_html( $verified_badge ), esc_html( $unverified_badge ) ), '', $notification_content->text );
		}
		elseif ( ! empty( $notification_content['text'] ) ) {
			$notification_content['text'] = str_replace( array( esc_html( $verified_badge ), esc_html( $unverified_badge ) ), '', $notification_content['text'] );
		}

		return $notification_content;
	}

	/**
	 * Remove the badge HTML from comment reply links
	 *
	 * @param string $comment_reply_link HTML for the comment reply link
	 *
	 * @return string
	 */
	public function remove_badge_from_comment_reply_link( $comment_reply_link ) {
		$verified_badge   = $this->get_verified_badge();
		$unverified_badge = $this->get_unverified_badge();

		return str_replace( array( esc_html( $verified_badge ), esc_html( $unverified_badge ) ), '', $comment_reply_link );
	}

	/**
	 * Get the verified badge HTML.
	 *
	 * @return string The badge HTML.
	 */
	public function get_verified_badge() {
		return apply_filters( 'bp_verified_member_verified_badge', '<span class="bp-verified-badge"></span>' );
	}

	/**
	 * Get the verified badge HTML.
	 *
	 * @return string The badge HTML.
	 */
	public function get_unverified_badge() {
		return apply_filters( 'bp_verified_member_unverified_badge', '<span class="bp-unverified-badge"></span>' );
	}

	/**
	 * Get the badge for the specified user. Returns empty string if user has no badge.
	 *
	 * @param int $user_id ID of the user to get the badge for
	 *
	 * @return string Badge HTML, or empty string if user has no badge
	 */
	public function get_user_badge( $user_id ) {
		global $bp_verified_member_admin;
		$display_unverified_badge = ! empty( $bp_verified_member_admin->settings->get_option( 'display_unverified_badge' ) );
		return $this->is_user_verified( $user_id ) ? $this->get_verified_badge() : ( $display_unverified_badge ? $this->get_unverified_badge() : '' );
	}

	/**
	 * Get whether a specified user is verified or not
	 *
	 * @param int $user_id ID of the user to check
	 *
	 * @return bool True if verified, false otherwise
	 */
	public function is_user_verified( $user_id ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		return $this->is_user_verified_by_role( $user_id ) || $this->is_user_verified_by_member_type( $user_id ) || $this->is_user_verified_by_meta( $user_id );
	}

	/**
	 * Get whether a specified user belongs to a verified role
	 *
	 * @param int $user_id ID of the user to check
	 *
	 * @return bool True if user belongs to a verified role, false otherwise
	 */
	public function is_user_verified_by_role( $user_id ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		global $bp_verified_member_admin;
		$verified_roles = $bp_verified_member_admin->settings->get_option( 'verified_roles' );
		$user           = get_userdata( $user_id );

		return ! empty( $verified_roles ) && ! empty( $user ) && ! empty( $user->roles ) && ! empty( array_intersect( $verified_roles, $user->roles ) );
	}

	/**
	 * Get whether a specified user belongs to a verified member type
	 *
	 * @param int $user_id ID of the user to check
	 *
	 * @return bool True if user belongs to a verified member type, false otherwise
	 */
	public function is_user_verified_by_member_type( $user_id ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		global $bp_verified_member_admin;
		$verified_member_types = $bp_verified_member_admin->settings->get_option( 'verified_member_types' );
		$user_member_types     = bp_get_member_type( $user_id, false );

		return ! empty( $verified_member_types ) && ! empty( $user_member_types ) && ! empty( array_intersect( $verified_member_types, $user_member_types ) );
	}

	/**
	 * Get whether a specified user has the verified meta
	 *
	 * @param int $user_id ID of the user to check
	 *
	 * @return bool True if user has the verified meta, false otherwise
	 */
	public function is_user_verified_by_meta( $user_id ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		global $bp_verified_member_admin;
		return ! empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) );
	}

	/**
	 * Display a button to request verification
	 */
	public function display_verification_request_button() {
		global $bp_verified_member_admin;
		if ( ! bp_is_my_profile() || ! $bp_verified_member_admin->settings->get_option( 'enable_verification_requests' ) ) {
			return;
		}

		$user_id = bp_displayed_user_id();
		if ( empty( $user_id ) || $this->is_user_verified( $user_id ) ) {
			return;
		}

		$verification_request_status = get_user_meta( $user_id, 'bp_verified_member_verification_request', true );

		if ( $verification_request_status === 'pending' ) : ?>

			<div>
				<button class="bp-verified-member-request-button bp-verified-member-verification-pending">
					<?php esc_html_e( 'Pending Verification...', 'bp-verified-member' ); ?>
				</button>
			</div>

		<?php else : ?>

			<div>
				<button class="bp-verified-member-request-button" data-bp-verified-member-request-nonce="<?php echo esc_attr( wp_create_nonce( 'bp-verified-member-request' ) ); ?>">
					<?php esc_html_e( 'Request Verification', 'bp-verified-member' ); ?> <?php echo $this->get_verified_badge(); ?>
				</button>
			</div>

		<?php endif;
	}

	/**
	 * Ajax action to send a verification request
	 */
	public function request_verification() {
		global $bp_verified_member_admin;
		if ( ! $bp_verified_member_admin->settings->get_option( 'enable_verification_requests' ) ) {
			wp_send_json_error( 'verification_requests_disabled' );
		}

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'bp-verified-member-request' ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}

		$user_id = get_current_user_id();

		if ( empty( $user_id ) ) {
			wp_send_json_error( 'not_logged_in' );
		}

		if ( $this->is_user_verified( $user_id ) ) {
			wp_send_json_error( 'already_verified' );
		}

		update_user_meta( $user_id, 'bp_verified_member_verification_request', 'pending' );

		$unseen_requests = get_transient( 'bp_verified_member_new_requests' );
		if ( empty( $unseen_requests ) ) {
			$unseen_requests = array();
		}
		$unseen_requests[] = $user_id;
		set_transient( 'bp_verified_member_new_requests', array_unique( $unseen_requests ) );

		// Send notification email
		bp_send_email( 'bp_verified_member_received_verification_request', get_bloginfo( 'admin_email' ), array(
			'tokens' => array(
				'site.name'      => get_bloginfo( 'name' ),
				'requester.name' => bp_core_get_user_displayname( $user_id ),
			),
		) );

		wp_send_json_success( esc_html__( 'Request Sent!', 'bp-verified-member' ) );
	}

	/**
	 * Create a BP email to notify admins when they receive a verification request
	 */
	public static function create_admin_verification_request_email() {
		if ( ! function_exists( 'bp_get_email_post_type' ) ) {
			return;
		}

		// Do not create if it already exists and is not in the trash
		$post_exists = post_exists( '[{{{site.name}}}] New verification request' );

		if ( $post_exists != 0 && get_post_status( $post_exists ) === 'publish' )
			return;

		ob_start(); ?>
		<p><?php esc_html_e( '{{requester.name}} has sent a verification request.', 'bp-verified-member' ); ?></p>
		<p><a href="<?php echo esc_url( admin_url( 'users.php?view=bp_verified_member_requests' ) ) ?>"><?php esc_html_e( 'View all verification requests', 'bp-verified-member' ) ?></a></p>
		<?php $email_content = ob_get_clean();

		// Create post object
		$email_post = array(
			'post_title'    => esc_html__( '[{{{site.name}}}] New verification request.', 'bp-verified-member' ),
			'post_content'  => $email_content,  // HTML email content.
			'post_excerpt'  => esc_html__( '{{requester.name}} has sent a verification request.', 'bp-verified-member' ),  // Plain text email content.
			'post_status'   => 'publish',
			'post_type'     => bp_get_email_post_type()
		);

		// Insert the email post into the database
		$post_id = wp_insert_post( $email_post );

		if ( $post_id ) {
			// Add our email to the taxonomy term 'bp_verified_member_received_verification_request'
			$tt_ids = wp_set_object_terms( $post_id, 'bp_verified_member_received_verification_request', bp_get_email_tax_type() );
			foreach ( $tt_ids as $tt_id ) {
				$term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
				wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
					'description' => esc_html__( 'A member has sent a verification request', 'bp-verified-member' ),
				) );
			}
		}
	}

	/**
	 * Add a "Verified / Unverified" filter to BP Profile Search
	 *
	 * @param array $fields The array of field choices
	 *
	 * @return array The new array of field choices
	 */
	public function bps_add_verified_field( $fields ) {
		$field              = new stdClass;
		$field->group       = __( 'Users data', 'bp-profile-search' );
		$field->code        = 'bp-verified-member';
		$field->name        = esc_html__( 'Verified / Unverified', 'bp-verified-member' );
		$field->description = '';
		$field->format      = 'text';
		$field->options     = array(
			'verified'   => esc_html__( 'Verified', 'bp-verified-member' ),
			'unverified' => esc_html__( 'Unverified', 'bp-verified-member' ),
		);
		$field->search      = array( $this, 'handle_bps_verified_filter' );

		$fields[] = $field;

		return $fields;
	}

	/**
	 * Handle the "Verified / Unverified" filter in BP Profile Search
	 *
	 * @param stdClass $field stdClass holding the current filter's value
	 *
	 * @return array Array of verified or unverified user ids depending on the requested filter
	 */
	public function handle_bps_verified_filter( $field ) {
		global $bp_verified_member_admin;

		$verified_roles        = $bp_verified_member_admin->settings->get_option( 'verified_roles' );
		$verified_member_types = $bp_verified_member_admin->settings->get_option( 'verified_member_types' );

		// Get verified members
		if ( 'verified' === $field->value ) {
			// Get users verified by meta
			$verified_user_ids = get_users( array(
				'meta_query' => array(
					array(
						'key'   => $bp_verified_member_admin->meta_box->meta_keys['verified'],
						'value' => true,
					),
				),
				'fields'     => 'ids',
			) );

			// Get users verified by role
			if ( ! empty( $verified_roles ) ) {
				$verified_roles_user_ids = get_users( array(
					'role__in' => $verified_roles,
					'fields'   => 'ids',
				) );
				$verified_user_ids       = array_merge( $verified_user_ids, $verified_roles_user_ids );
			}

			// Get users verified by member type
			if ( ! empty( $verified_member_types ) ) {
				$verified_member_type_users    = bp_core_get_users( array(
					'type'            => 'alphabetical',
					'member_type__in' => $verified_member_types,
				) );

				if ( ! empty( $verified_member_type_users['users'] ) ) {
					$verified_member_type_user_ids = array_map( function ( $user ) {
						return $user->ID;
					}, $verified_member_type_users['users'] );
					$verified_user_ids             = array_merge( $verified_user_ids, $verified_member_type_user_ids );
				}
			}

			return array_unique( $verified_user_ids );
		}
		// Get unverified members
		else {
			// Exclude verified meta
			$query_args = array(
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'     => $bp_verified_member_admin->meta_box->meta_keys['verified'],
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => $bp_verified_member_admin->meta_box->meta_keys['verified'],
						'value'   => true,
						'compare' => '!=',
					),
				),
				'fields'     => 'ids',
			);

			// Exclude verified roles
			if ( ! empty( $verified_roles ) ) {
				$query_args['role__not_in'] = $verified_roles;
			}

			// Exclude verified member types
			if ( ! empty( $verified_member_types ) ) {
				$users_verified_by_member_type = bp_core_get_users( array(
					'type'            => 'alphabetical',
					'member_type__in' => $verified_member_types
				) );

				if ( ! empty( $users_verified_by_member_type['users'] ) ) {
					if ( empty( $query_args['exclude'] ) ) {
						$query_args['exclude'] = array();
					}

					$query_args['exclude'] = array_map( function ( $user ) {
						return $user->ID;
					}, $users_verified_by_member_type['users'] );
				}
			}

			return get_users( $query_args );
		}
	}

	/**
	 * Register our custom bp_verified_member component in BuddyPress
	 *
	 * @param array $components Array of registered components
	 * @param array $active_components Array of active components
	 *
	 * @return array Array of registered components
	 */
	public function register_bp_verified_member_component( $components, $active_components ) {
		$components[] = 'bp_verified_member';
		return $components;
	}

	/**
	 * Send a BP notification when a user's verified status changes
	 *
	 * @param int $user_id ID of the user who's status changed
	 * @param string $new_status 'verified' or 'unverified'
	 */
	public function create_verified_status_updated_notification( $user_id, $new_status ) {
		global $bp_verified_member_admin;

		if ( 'verified' === $new_status && ! empty( $bp_verified_member_admin->settings->get_option( 'enable_verified_notification' ) ) ) {
			bp_notifications_add_notification( array(
				'user_id'          => $user_id,
				'component_name'   => 'bp_verified_member',
				'component_action' => 'bp_verified_member_verified',
				'date_notified'    => bp_core_current_time(),
				'is_new'           => 1,
				'allow_duplicate'  => false,
			) );
		}
		else if ( 'unverified' === $new_status && ! empty( $bp_verified_member_admin->settings->get_option( 'enable_unverified_notification' ) ) ) {
			bp_notifications_add_notification( array(
				'user_id'          => $user_id,
				'component_name'   => 'bp_verified_member',
				'component_action' => 'bp_verified_member_unverified',
				'date_notified'    => bp_core_current_time(),
				'is_new'           => 1,
				'allow_duplicate'  => false,
			) );
		}
	}

	/**
	 * Format the custom notifications contents
	 *
	 * @param string $content               Content of the notification
	 * @param int    $item_id               Notification item ID.
	 * @param int    $secondary_item_id     Notification secondary item ID.
	 * @param int    $action_item_count     Number of notifications with the same action.
	 * @param string $format                Format of return. Either 'string' or 'object'.
	 * @param string $component_action_name Canonical notification action.
	 * @param string $component_name        Notification component ID.
	 * @param int    $id                    Notification ID.
	 *
	 * @return string|array
	 */
	public function format_verified_member_notification( $content, $item_id, $secondary_item_id, $action_item_count, $format, $component_action_name, $component_name, $id ) {
		global $bp_verified_member_admin;

		if ( 'bp_verified_member_verified' === $component_action_name ) {
			$text = wp_kses_post( $bp_verified_member_admin->settings->get_option( 'verified_notification_content' ) );
		}
		else if ( 'bp_verified_member_unverified' === $component_action_name ) {
			$text = wp_kses_post( $bp_verified_member_admin->settings->get_option( 'unverified_notification_content' ) );
		}

		if ( ! empty( $text ) ) {
			$content = 'object' === $format ? array( 'text' => $text, 'link' => false ) : $text;
		}

		return $content;
	}
}
