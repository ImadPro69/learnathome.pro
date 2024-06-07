<?php
/**
 * Class BP_Verified_Member_Meta_Box
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package bp-verified-member/admin/meta-box
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'BP_Verified_Member_Meta_Box' ) ) :
	/**
	 * Class BP_Verified_Member_Meta_Box.
	 *
	 * @author themosaurus
	 * @package bp-verified-member/admin/meta-box
	 */
	class BP_Verified_Member_Meta_Box {
		/**
		 * BP_Verified_Member_Meta_Box constructor.
		 */
		public function __construct() {
			$this->name         = 'bp_verified_member_meta_box';
			$this->nonce_name   = "{$this->name}_nonce";
			$this->nonce_action = plugin_basename( __FILE__ );

			$this->meta_keys = array(
				'verified' => 'bp_verified_member',
			);

			add_action( 'bp_members_admin_user_metaboxes', array( $this, 'add_meta_box'  ), 10 );
			add_action( 'bp_members_admin_update_user',    array( $this, 'save_meta_box' ), 10 );
		}

		/**
		 * Add meta box.
		 *
		 * @since 1.0.0
		 */
		public function add_meta_box() {
			if ( current_user_can( 'manage_options' ) ) {
				add_meta_box(
					$this->name,
					esc_html__( 'Verify Member', 'bp-verified-member' ),
					array( $this, 'render_meta_box' ), // callback
					get_current_screen()->id
				);
			}
		}


		/**
		 * Render meta box.
		 *
		 * @since 1.0.0
		 */
		public function render_meta_box() {
			if ( ! empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ) {
				$user_id = intval( $_GET['user_id'] );
			} else {
				$user_id = get_current_user_id();
			}

			/** @var BP_Verified_Member $bp_verified_member */
			global $bp_verified_member;
			$is_verified_by_role        = $bp_verified_member->is_user_verified_by_role( $user_id );
			$is_verified_by_member_type = $bp_verified_member->is_user_verified_by_member_type( $user_id );
			$is_verified_by_meta        = $bp_verified_member->is_user_verified_by_meta( $user_id );
			?>
			<table class="form-table">
				<tbody>
				<tr class="<?php echo esc_attr( $this->meta_keys['verified'] ); ?>-wrap">
					<th scope="row"><?php esc_html_e( 'Verified Member', 'bp-verified-member' ); ?></th>
					<td>
						<?php if ( $is_verified_by_role ) : ?>
							<p><em><?php esc_html_e( 'This user belongs to a verified role. If you wish to unverify this user, please assign another role to them.', 'bp-verified-member' ); ?></em></p>
						<?php elseif ( $is_verified_by_member_type ) : ?>
							<p><em><?php esc_html_e( 'This user belongs to a verified member type. If you wish to unverify this user, please remove their member type or assign another member type to them.', 'bp-verified-member' ); ?></em></p>
						<?php else : ?>
							<label for="<?php echo esc_attr( $this->meta_keys['verified'] ); ?>">
								<input name="<?php echo esc_attr( $this->meta_keys['verified'] ); ?>" id="<?php echo esc_attr( $this->meta_keys['verified'] ); ?>" type="checkbox" <?php checked( $is_verified_by_meta, true ); ?>>
								<?php esc_html_e( 'Mark this member as verified', 'bp-verified-member' ); ?>
							</label>
						<?php endif; ?>
					</td>
				</tr>
				</tbody>
			</table>
			<?php wp_nonce_field( $this->nonce_action, $this->nonce_name );
		}

		/**
		 * Save meta data.
		 *
		 * @since 1.0.0
		 */
		public function save_meta_box() {
			if ( $this->can_save() ) {
				if ( ! empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ) {
					$user_id = intval( $_GET['user_id'] );
				} else {
					$user_id = get_current_user_id();
				}

				update_user_meta( $user_id, $this->meta_keys['verified'], ! empty( $_POST[ $this->meta_keys['verified'] ] ) );
			}
		}

		/**
		 * Check if meta box can be saved
		 *
		 * @return bool
		 */
		private function can_save() {
			return (
				isset( $_POST['save'] ) &&
				isset( $_POST[ $this->nonce_name ] ) &&
				wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action )
			);
		}
	}

endif;

return new BP_Verified_Member_Meta_Box();
