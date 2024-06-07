<?php

/**
 * Tags
 *
 * @link    https://plugins360.com
 * @since   2.4.3
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Admin_Tags class.
 *
 * @since 2.4.3
 */
class AIOVG_Admin_Tags {

	/**
	 * Add "Tags" menu.
	 *
	 * @since 2.4.3
	 */
	public function admin_menu() {	
		add_submenu_page(
			'all-in-one-video-gallery',
			__( 'All-in-One Video Gallery - Tags', 'all-in-one-video-gallery' ),
			__( 'Video Tags', 'all-in-one-video-gallery' ),
			'manage_aiovg_options',
			'edit-tags.php?taxonomy=aiovg_tags&post_type=aiovg_videos'
		);	
	}

	/**
	 * Move "Tags" submenu under our plugin's main menu.
	 *
	 * @since  2.4.3
	 * @param  string $parent_file The parent file.
	 * @return string $parent_file The parent file.
	 */
	public function parent_file( $parent_file ) {	
		global $submenu_file, $current_screen;

		if ( 'aiovg_tags' == $current_screen->taxonomy ) {
			$submenu_file = 'edit-tags.php?taxonomy=aiovg_tags&post_type=aiovg_videos';
			$parent_file  = 'all-in-one-video-gallery';
		}

		return $parent_file;
	}
	
	/**
	 * Register the custom taxonomy "aiovg_tags".
	 *
	 * @since 2.4.3
	 */
	public function register_taxonomy() {	
		$labels = array(
			'name'                       => _x( 'Video Tags', 'Taxonomy General Name', 'all-in-one-video-gallery' ),
			'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'all-in-one-video-gallery' ),
			'menu_name'                  => __( 'Video Tags', 'all-in-one-video-gallery' ),
			'all_items'                  => __( 'All Tags', 'all-in-one-video-gallery' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'new_item_name'              => __( 'New Tag Name', 'all-in-one-video-gallery' ),
			'add_new_item'               => __( 'Add New Tag', 'all-in-one-video-gallery' ),
			'edit_item'                  => __( 'Edit Tag', 'all-in-one-video-gallery' ),
			'update_item'                => __( 'Update Tag', 'all-in-one-video-gallery' ),
			'view_item'                  => __( 'View Tag', 'all-in-one-video-gallery' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'all-in-one-video-gallery' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'all-in-one-video-gallery' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'all-in-one-video-gallery' ),
			'popular_items'              => __( 'Popular Tags', 'all-in-one-video-gallery' ),
			'search_items'               => __( 'Search Tags', 'all-in-one-video-gallery' ),
			'not_found'                  => __( 'No tags found', 'all-in-one-video-gallery' ),
			'no_terms'                   => __( 'No tags', 'all-in-one-video-gallery' ),
			'items_list'                 => __( 'Tags list', 'all-in-one-video-gallery' ),
			'items_list_navigation'      => __( 'Tags list navigation', 'all-in-one-video-gallery' ),
		);
		
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_in_rest'               => true,
			'show_tagcloud'              => true,
			'capabilities'               => array(
				'manage_terms' => 'manage_aiovg_options',
				'edit_terms'   => 'manage_aiovg_options',				
				'delete_terms' => 'manage_aiovg_options',
				'assign_terms' => 'edit_aiovg_videos'
			)
		);
		
		register_taxonomy( 'aiovg_tags', array( 'aiovg_videos' ), $args );	
	}
	
	/**
	 * Retrieve the table columns.
	 *
	 * @since  2.4.3
	 * @param  array $columns Array of default table columns.
	 * @return array $columns Updated list of table columns.
	 */
	public function get_columns( $columns ) {	
		$columns['tax_id'] = __( 'ID', 'all-in-one-video-gallery' );
    	return $columns;		
	}
	
	/**
	 * This function renders the custom columns in the list table.
	 *
	 * @since 2.4.3
	 * @param string $content Content of the column.
	 * @param string $column  Name of the column.
	 * @param string $term_id Term ID.
	 */
	public function custom_column_content( $content, $column, $term_id ) {		
		if ( 'tax_id' == $column ) {
        	$content = $term_id;
    	}
		
		return $content;	
	}

}
