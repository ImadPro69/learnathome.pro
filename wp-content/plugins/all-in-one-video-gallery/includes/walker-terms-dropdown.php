<?php

/**
 * Walker Terms Dropdown.
 *
 * @link    https://plugins360.com
 * @since   3.0.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This is required to be sure Walker_CategoryDropdown class is available
require_once ABSPATH . 'wp-includes/class-walker-category-dropdown.php';

/**
 * AIOVG_Walker_Terms_Dropdown class.
 *
 * @since 1.0.0
 */
class AIOVG_Walker_Terms_Dropdown extends Walker_CategoryDropdown {
	
	/**
	 * Start the element output.
	 *
	 * @since 3.0.0
	 * @param string $output   Used to append additional content (passed by reference).
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_dropdown_categories()
	 * @param int    $id       ID of the current term.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {	
		$taxonomy = $args['taxonomy'];
		$pad = str_repeat( '&nbsp;', $depth * 3 );
 
		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters( 'list_cats', $category->name, $category );
 
		if ( isset( $args['value_field'] ) && isset( $category->{$args['value_field']} ) ) {
			$value_field = $args['value_field'];
		} else {
			$value_field = 'term_id';
		}

		// Term link	
		if ( 'aiovg_categories' == $taxonomy ) {
			$term_link = aiovg_get_category_page_url( $category );
		} else {
			$term_link = get_term_link( $category );
		}
 
		$output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->{$value_field} ) . "\"";
 
		// Type-juggling causes false matches, so we force everything to a string.
		if ( (string) $category->{$value_field} === (string) $args['selected'] )
			$output .= ' selected="selected"';
		
		$output .= ' data-uri="' . esc_url( $term_link ) . '" '; /* Custom */
		
		$output .= '>';
		$output .= $pad . $cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;(' . number_format_i18n( $category->count ) . ')';
		$output .= "</option>\n";	
	}

}
