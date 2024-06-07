<?php

/**
 * Categories: Dropdown Template.
 *
 * @link    https://plugins360.com
 * @since   3.0.0
 *
 * @package All_In_One_Video_Gallery
 */

$page_settings = get_option( 'aiovg_page_settings' );

$category_url = '/';
if ( ! empty( $page_settings['category'] ) ) {
	$category_url = get_permalink( (int) $page_settings['category'] );
}
?>

<div class="aiovg aiovg-categories aiovg-categories-template-dropdown" data-uri="<?php echo esc_url( $category_url ); ?>">
	<?php
	$query = array(
		'taxonomy'          => 'aiovg_categories',
		'name'              => 'aiovg_categories',
		'show_option_none'  => '— ' . esc_html__( 'Select a Category', 'all-in-one-video-gallery' ) . ' —',
		'option_none_value' => (int) $attributes['id'],
		'orderby'           => sanitize_text_field( $attributes['orderby'] ),
		'order'             => sanitize_text_field( $attributes['order'] ),
		'hide_empty'        => (int) $attributes['hide_empty'], 
		'hierarchical'      => (int) $attributes['hierarchical'],                
		'show_count'        => (int) $attributes['show_count'], 		
		'walker'            => new AIOVG_Walker_Terms_Dropdown(),   
		'echo'              => 0
	);

	if ( $query['hierarchical'] ) {
		$query['child_of'] = (int) $attributes['id'];
	} else {
		$query['parent'] = (int) $attributes['id'];
	}

	if ( $term_slug = get_query_var( 'aiovg_category' ) ) {
		$term = get_term_by( 'slug', sanitize_title( $term_slug ), 'aiovg_categories' );
		$query['selected'] = $term->term_id;
	}

	if ( isset( $_GET['ca'] ) ) {
		$query['selected'] = (int) $_GET['ca'];
	}
	
	$query = apply_filters( 'aiovg_categories_args', $query, $attributes );
	$dropdown_html = wp_dropdown_categories( $query );

	echo $dropdown_html;
	?>
</div>