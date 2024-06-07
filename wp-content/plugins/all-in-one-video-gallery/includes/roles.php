<?php

/**
 * Roles and Capabilities.
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
 * AIOVG_Roles class.
 *
 * @since 1.0.0
 */
class AIOVG_Roles {

	/**
	 * Add new capabilities.
	 *
	 * @since 1.0.0
	 */
	public function add_caps() {	
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			// Add the "administrator" capabilities
			$capabilities = $this->get_core_caps();
			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
			$wp_roles->add_cap( 'administrator', 'manage_aiovg_options' );
			
			// Add the "editor" capabilities
			$wp_roles->add_cap( 'editor', 'edit_aiovg_videos' );			
			$wp_roles->add_cap( 'editor', 'edit_others_aiovg_videos' );			
			$wp_roles->add_cap( 'editor', 'publish_aiovg_videos' );			
			$wp_roles->add_cap( 'editor', 'read_private_aiovg_videos' );	
			$wp_roles->add_cap( 'editor', 'delete_aiovg_videos' );			
			$wp_roles->add_cap( 'editor', 'delete_private_aiovg_videos' );
			$wp_roles->add_cap( 'editor', 'delete_published_aiovg_videos' );
			$wp_roles->add_cap( 'editor', 'delete_others_aiovg_videos' );
			$wp_roles->add_cap( 'editor', 'edit_private_aiovg_videos' );
			$wp_roles->add_cap( 'editor', 'edit_published_aiovg_videos' );
			
			// Add the "author" capabilities
			$wp_roles->add_cap( 'author', 'edit_aiovg_videos' );						
			$wp_roles->add_cap( 'author', 'publish_aiovg_videos' );
			$wp_roles->add_cap( 'author', 'delete_aiovg_videos' );
			$wp_roles->add_cap( 'author', 'delete_published_aiovg_videos' );
			$wp_roles->add_cap( 'author', 'edit_published_aiovg_videos' );
			
			// Add the "contributor" capabilities
			$wp_roles->add_cap( 'contributor', 'edit_aiovg_videos' );						
			$wp_roles->add_cap( 'contributor', 'publish_aiovg_videos' );
			$wp_roles->add_cap( 'contributor', 'delete_aiovg_videos' );
			$wp_roles->add_cap( 'contributor', 'delete_published_aiovg_videos' );
			$wp_roles->add_cap( 'contributor', 'edit_published_aiovg_videos' );
			
			// Add the "subscriber" capabilities
			$wp_roles->add_cap( 'subscriber', 'edit_aiovg_videos' );						
			$wp_roles->add_cap( 'subscriber', 'publish_aiovg_videos' );
			$wp_roles->add_cap( 'subscriber', 'delete_aiovg_videos' );
			$wp_roles->add_cap( 'subscriber', 'delete_published_aiovg_videos' );
			$wp_roles->add_cap( 'subscriber', 'edit_published_aiovg_videos' );			
		}		
	}

	/**
	 * Gets the core post type capabilities.
	 *
	 * @since  1.0.0
	 * @return array $capabilities Core post type capabilities.
	 */
	public function get_core_caps() {	
		$capabilities = array();

		$capability_types = array( 'aiovg_video' );

		foreach ( $capability_types as $capability_type ) {		
			$capabilities[ $capability_type ] = array(
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",
			);
		}

		return $capabilities;		
	}
	
	/**
	 * Filter a user's capabilities depending on specific context and/or privilege.
	 *
	 * @since  1.0.0
	 * @param  array  $caps    Returns the user's actual capabilities.
	 * @param  string $cap     Capability name.
	 * @param  int    $user_id The user ID.
	 * @param  array  $args    Adds the context to the cap. Typically the object ID.
	 * @return array           Actual capabilities for meta capability.
	 */
	public function meta_caps( $caps, $cap, $user_id, $args ) {
		if ( empty( $args ) ) {
			return $caps;
		}
				
		// If editing, deleting, or reading a listing, get the post and post type object
		if ( 'edit_aiovg_video' == $cap || 'delete_aiovg_video' == $cap || 'read_aiovg_video' == $cap ) {
			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );

			// Set an empty array for the caps.
			$caps = array();
		}

		// If editing a listing, assign the required capability
		if ( 'edit_aiovg_video' == $cap ) {
			if ( $user_id == $post->post_author ) {
				$caps[] = $post_type->cap->edit_aiovg_videos;
			} else {
				$caps[] = $post_type->cap->edit_others_aiovg_videos;
			}
		}

		// If deleting a listing, assign the required capability
		elseif ( 'delete_aiovg_video' == $cap ) {
			if ( $user_id == $post->post_author ) {
				$caps[] = $post_type->cap->delete_aiovg_videos;
			} else {
				$caps[] = $post_type->cap->delete_others_aiovg_videos;
			}
		}

		// If reading a private listing, assign the required capability
		elseif ( 'read_aiovg_video' == $cap ) {
			if ( 'private' != $post->post_status ) {
				$caps[] = 'read';
			} elseif ( $user_id == $post->post_author ) {
				$caps[] = 'read';
			} else {
				$caps[] = $post_type->cap->read_private_aiovg_videos;
			}
		}

		// Return the capabilities required by the user
		return $caps;
	}
	
	/**
	 * Remove core post type capabilities (called on uninstall).
	 *
	 * @since 1.0.0
	 */
	public function remove_caps() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {		
			// Remove the "administrator" capabilities
			$capabilities = $this->get_core_caps();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->remove_cap( 'administrator', $cap );
				}
			}
			$wp_roles->remove_cap( 'administrator', 'manage_aiovg_options' );
			
			// Remove the "editor" capabilities
			$wp_roles->remove_cap( 'editor', 'edit_aiovg_videos' );			
			$wp_roles->remove_cap( 'editor', 'edit_others_aiovg_videos' );			
			$wp_roles->remove_cap( 'editor', 'publish_aiovg_videos' );			
			$wp_roles->remove_cap( 'editor', 'read_private_aiovg_videos' );
			$wp_roles->remove_cap( 'editor', 'delete_aiovg_videos' );			
			$wp_roles->remove_cap( 'editor', 'delete_private_aiovg_videos' );
			$wp_roles->remove_cap( 'editor', 'delete_published_aiovg_videos' );
			$wp_roles->remove_cap( 'editor', 'delete_others_aiovg_videos' );
			$wp_roles->remove_cap( 'editor', 'edit_private_aiovg_videos' );
			$wp_roles->remove_cap( 'editor', 'edit_published_aiovg_videos' );
			
			// Remove the "author" capabilities
			$wp_roles->remove_cap( 'author', 'edit_aiovg_videos' );						
			$wp_roles->remove_cap( 'author', 'publish_aiovg_videos' );
			$wp_roles->remove_cap( 'author', 'delete_aiovg_videos' );
			$wp_roles->remove_cap( 'author', 'delete_published_aiovg_videos' );
			$wp_roles->remove_cap( 'author', 'edit_published_aiovg_videos' );
			
			// Remove the "contributor" capabilities
			$wp_roles->remove_cap( 'contributor', 'edit_aiovg_videos' );						
			$wp_roles->remove_cap( 'contributor', 'publish_aiovg_videos' );
			$wp_roles->remove_cap( 'contributor', 'delete_aiovg_videos' );
			$wp_roles->remove_cap( 'contributor', 'delete_published_aiovg_videos' );
			$wp_roles->remove_cap( 'contributor', 'edit_published_aiovg_videos' );
			
			// Remove the "subscriber" capabilities
			$wp_roles->remove_cap( 'subscriber', 'edit_aiovg_videos' );						
			$wp_roles->remove_cap( 'subscriber', 'publish_aiovg_videos' );
			$wp_roles->remove_cap( 'subscriber', 'delete_aiovg_videos' );
			$wp_roles->remove_cap( 'subscriber', 'delete_published_aiovg_videos' );
			$wp_roles->remove_cap( 'subscriber', 'edit_published_aiovg_videos' );
		}		
	}
	
}
