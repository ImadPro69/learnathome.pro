<?php

/**
 * Walker Terms Checklist.
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

// This is required to be sure Walker_Category_Checklist class is available
require_once ABSPATH . 'wp-admin/includes/template.php';

/**
 * AIOVG_Walker_Terms_Checklist class.
 *
 * @since 1.0.0
 */
class AIOVG_Walker_Terms_Checklist extends Walker_Category_Checklist {
	
	/**
	 * Field name.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $name;
	
	/**
	 * Get things started.
	 *
	 * @since 1.0.0
	 * @param string $name Field name.
	 */
	public function __construct( $name ) {
		$this->name = $name;
	}
	
	/**
	 * Start the element output.
	 *
	 * @since 1.0.0
	 * @param string $output   Used to append additional content (passed by reference).
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {	
		$taxonomy = $args['taxonomy'];
		$name = $this->name;

		$args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
		$class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

		$args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

		/** This filter is documented in wp-includes/category-template.php */
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
			'<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
			checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
			disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
			esc_html( apply_filters( 'the_category', $category->name, '', '' ) ) . '</label>';		
	}

}
