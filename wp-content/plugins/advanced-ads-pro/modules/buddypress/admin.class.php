<?php
/**
 * Class Advanced_Ads_Pro_Module_BuddyPress_Admin
 * Manage backend-facing logic for BuddyPress/BuddyBoss integration
 */
class Advanced_Ads_Pro_Module_BuddyPress_Admin {

	/**
	 * Advanced_Ads_Pro_Module_BuddyPress_Admin constructor.
	 */
	public function __construct() {
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return;
		}

		// stop if BuddyPress isn't activated
		if ( ! class_exists( 'BuddyPress', false ) ) {
			return;
		}

		// add sticky placement
		add_action( 'advanced-ads-placement-types', [ $this, 'add_placement' ] );
		// content of sticky placement
		add_action( 'advanced-ads-placement-options-after', [ $this, 'placement_options' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		add_action( 'wp_ajax_advads-pro-buddyboss-render-xprofile-field', [ $this, 'render_xprofile_field_ajax' ] );
	}

	/**
	 * Register the BuddyPress/BuddyBoss placement
	 *
	 * @param array $types registered placement types.
	 *
	 * @return array
	 */
	public function add_placement( $types ) {
		// ad injection on a BuddyPress/BuddyBoss activity-stream
		if ( Advanced_Ads_Pro_Module_BuddyPress::is_buddyboss() ) {
			$types['buddypress'] = [
				'title'       => __( 'BuddyBoss Content', 'advanced-ads-pro' ),
				'description' => __( 'Display ads on BuddyBoss related pages.', 'advanced-ads-pro' ),
				'image'       => AAP_BASE_URL . 'modules/buddypress/assets/img/buddyboss.png',
				'order'       => 31,
				'options'     => [
					'placement-display-conditions' => [ 'request_uri', 'buddypress_group' ],
				],
			];
		} else {
			$types['buddypress'] = [
				'title'       => __( 'BuddyPress Content', 'advanced-ads-pro' ),
				'description' => __( 'Display ads on BuddyPress related pages.', 'advanced-ads-pro' ),
				'image'       => AAP_BASE_URL . 'modules/buddypress/assets/img/buddypress-icon.png',
				'order'       => 31,
				'options'     => [
					'placement-display-conditions' => [ 'request_uri', 'buddypress_group' ],
				],
			];
		}
		return $types;
	}

	/**
	 * Register options for the BuddyPress placement
	 *
	 * @param string $placement_slug slug of the placement.
	 * @param array  $placement options of the placement.
	 */
	public function placement_options( $placement_slug = '', $placement = [] ) {
		if ( 'buddypress' === $placement['type'] ) {
			$buddypress_positions = $this->get_buddypress_hooks();
			$current              = Advanced_Ads_Pro_Module_BuddyPress::get_hook_from_placement_options( $placement );
			$activity_type        = isset( $placement['options']['activity_type'] ) ? $placement['options']['activity_type'] : 'any';
			$hook_repeat          = ! empty( $placement['options']['hook_repeat'] );
			$index                = ( isset( $placement['options']['pro_buddypress_pages_index'] ) ) ? Advanced_Ads_Pro_Utils::absint( $placement['options']['pro_buddypress_pages_index'], 1 ) : 1;
			require AAP_BASE_PATH . 'modules/buddypress/views/position-option.php';
		}
	}

	/**
	 * Load the hooks relevant for BuddyPress/BuddyBoss
	 *
	 * @return array list of hooks for BuddyPress depending on the BP theme
	 */
	public function get_buddypress_hooks() {
		if ( ! Advanced_Ads_Pro_Module_BuddyPress::is_legacy_theme() ) {
			return [
				__( 'Activity Entry', 'advanced-ads-pro' ) => [
					'bp_after_activity_entry' => 'after activity entry',
				],
			];
		}

		// Return legacy hooks.
		return [
			__( 'Activity Entry', 'advanced-ads-pro' ) => [
				'bp_before_activity_entry'          => 'before activity entry',
				'bp_activity_entry_content'         => 'activity entry content',
				'bp_after_activity_entry'           => 'after activity entry',
				'bp_before_activity_entry_comments' => 'before activity entry comments',
				'bp_activity_entry_comments'        => 'activity entry comments',
				'bp_after_activity_entry_comments'  => 'after activity entry comments',
			],
			__( 'Group List', 'advanced-ads-pro' )     => [
				'bp_directory_groups_item' => 'directory groups item',
			],
			__( 'Member List', 'advanced-ads-pro' )    => [
				'bp_directory_members_item' => 'directory members item',
			],
		];
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_admin_scripts() {
		if ( ! Advanced_Ads_Admin::screen_belongs_to_advanced_ads() ) {
			return;
		}

		wp_enqueue_script( 'advanced-ads-pro/buddyboss-admin', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', [ 'jquery' ], AAP_VERSION, true );
	}

	/**
	 * Renders a html field corresponding to the currently selected field type.
	 */
	public function render_xprofile_field_ajax() {
		check_ajax_referer( 'advanced-ads-admin-ajax-nonce', 'nonce' );

		if (
			! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads' ) )
			|| ! isset( $_POST['field_name'] )
			|| ! isset( $_POST['field_type'] )
		) {
			die;
		}

		self::render_xprofile_field(
			preg_replace( '/[^a-z0-9\[\]]/', '', $_POST['field_name'] ),
			preg_replace( '/[^a-z0-9]/', '', $_POST['field_type'] ),
			''
		);
		die;
	}

	/**
	 * Renders a html field corresponding to the currently selected field type.
	 *
	 * @param string $name Field name.
	 * @param string $field_type type Field type.
	 * @param string $value Field value.
	 */
	public static function render_xprofile_field( $name, $field_type, $value = '' ) {
		if ( function_exists( 'bp_get_active_member_types' ) && $field_type === Advanced_Ads_Pro_Module_BuddyPress::FIELD_MEMBERTYPES ) {
			$bp_active_member_types = bp_get_active_member_types();
			if ( ! empty( $bp_active_member_types ) ) {
				printf(
					'<select name="%s[value]" class="advanced-ads-buddyboss-xprofile-dynamic-field">',
					esc_attr( $name )
				);
				foreach ( $bp_active_member_types as $bp_active_member_type ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $bp_active_member_type ),
						selected( $bp_active_member_type, (int) $value, false ),
						esc_attr( get_post_meta( $bp_active_member_type, '_bp_member_type_label_singular_name', true ) )
					);
				}
				echo '</select>';
			}
		} else {
			printf(
				'<input type="text" name="%s[value]" value="%s" class="advanced-ads-buddyboss-xprofile-dynamic-field">',
				esc_attr( $name ),
				esc_attr( $value )
			);
		}
	}
}

