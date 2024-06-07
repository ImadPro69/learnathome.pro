<?php

/**
 * Search Form: Horizontal Layout.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

$is_form_submitted = false;
if ( isset( $_GET['vi'] ) || isset( $_GET['ca'] ) || isset( $_GET['ta'] ) ) {
	$is_form_submitted = true;
}
?>

<div class="aiovg aiovg-search-form aiovg-search-form-template-horizontal">
	<form method="get" action="<?php echo esc_url( aiovg_get_search_page_url() ); ?>">
    	<?php if ( ! get_option('permalink_structure') ) : ?>
       		<input type="hidden" name="page_id" value="<?php echo esc_attr( $attributes['search_page_id'] ); ?>" />
    	<?php endif; ?>

		<?php if ( $attributes['has_keyword'] ) : ?>
			<div class="aiovg-form-group aiovg-field-keyword">
				<input type="text" name="vi" class="aiovg-form-control" placeholder="<?php esc_attr_e( 'Enter your Keyword', 'all-in-one-video-gallery' ); ?>" value="<?php echo isset( $_GET['vi'] ) ? esc_attr( stripslashes( $_GET['vi'] ) ) : ''; ?>" />
			</div>
		<?php endif; ?>
		
		<!-- Hook for developers to add new fields -->
        <?php do_action( 'aiovg_search_form_fields', $attributes ); ?>
		
		<?php if ( $attributes['has_category'] ) : ?>  
			<div class="aiovg-form-group aiovg-field-category">
				<?php
				$categories_args = array(
					'show_option_none'  => '— ' . esc_html__( 'Select a Category', 'all-in-one-video-gallery' ) . ' —',
					'option_none_value' => '',
					'taxonomy'          => 'aiovg_categories',
					'name' 			    => 'ca',
					'class'             => 'aiovg-form-control',
					'orderby'           => 'name',
					'selected'          => isset( $_GET['ca'] ) ? (int) $_GET['ca'] : '',
					'hierarchical'      => true,
					'depth'             => 10,
					'show_count'        => false,
					'hide_empty'        => false,
				);

				$categories_excluded = get_terms(array(
					'taxonomy'   => 'aiovg_categories',
					'hide_empty' => false,
					'fields'     => 'ids',
					'meta_key'   => 'exclude_search_form',
    				'meta_value' => 1
				));

				if ( ! empty( $categories_excluded ) && ! is_wp_error( $categories_excluded ) ) {
					$categories_args['exclude']	= array_map( 'intval', $categories_excluded );
				}

				$categories_args = apply_filters( 'aiovg_search_form_categories_args', $categories_args );
				wp_dropdown_categories( $categories_args );
				?>
			</div>
		<?php endif; ?>

		<?php if ( $attributes['has_tag'] ) : $uid = aiovg_get_uniqid(); ?>
			<div class="aiovg-form-group aiovg-field-tag">
				<div class="aiovg-autocomplete" data-uid="<?php echo esc_attr( $uid ); ?>">
					<input type="text" id="aiovg-autocomplete-input-<?php echo esc_attr( $uid ); ?>" class="aiovg-form-control aiovg-autocomplete-input" placeholder="<?php esc_attr_e( 'Select Tags', 'all-in-one-video-gallery' ); ?>" autocomplete="off" />
					
					<?php
					$tags_args = array(
						'taxonomy'   => 'aiovg_tags',
						'orderby'    => 'name', 
						'order'      => 'asc',
						'hide_empty' => false
					);
	
					$terms = get_terms( $tags_args );
	
					// Source
					echo '<select id="aiovg-autocomplete-select-' . esc_attr( $uid ) . '" class="aiovg-autocomplete-select" style="display: none;">';
	
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							printf(
								'<option value="%d">%s</option>',
								$term->term_id,
								esc_html( $term->name )
							);
						}
					}
	
					echo '</select>';
					?>
				</div>
			</div>
		<?php endif; ?>	

		<div class="aiovg-form-group aiovg-field-submit aiovg-flex aiovg-gap-2 aiovg-items-center aiovg-hidden-mobile">
			<input type="submit" class="aiovg-button" value="<?php esc_attr_e( 'Search Videos', 'all-in-one-video-gallery' ); ?>" /> 
			<?php if ( $is_form_submitted ) : ?>
				<a href="<?php echo esc_url( aiovg_get_search_page_url() ); ?>" class="aiovg-flex aiovg-gap-1 aiovg-items-center aiovg-text-small">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
						<path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
					</svg>
					<?php esc_html_e( 'Reset', 'all-in-one-video-gallery' ); ?>
				</a>
			<?php endif; ?>
		</div>   
		
		<?php if ( $attributes['has_tag'] ) : ?>
			<div id="aiovg-autocomplete-tags-<?php echo esc_attr( $uid ); ?>" class="aiovg-autocomplete-tags aiovg-hide-if-empty"><?php 
				if ( isset( $_GET['ta'] ) ) {
					$selected_tags = array_map( 'intval', $_GET['ta'] );
					$selected_tags = array_filter( $selected_tags );
	
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							if ( in_array( $term->term_id, $selected_tags ) ) {
								$html  = '<span class="aiovg-tag-item aiovg-tag-item-' . $term->term_id . '">';									
								$html .= '<a href="javascript:void(0);">';
								$html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
									<path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
								</svg>';
								$html .= esc_html( $term->name );
								$html .= '</a>';
								$html .= '<input type="hidden" name="ta[]" value="' . $term->term_id . '" />';
								$html .= '</span>';
	
								echo $html;
							}
						}
					}
				}
			?></div>
		<?php endif; ?>

		<div class="aiovg-form-group aiovg-field-submit aiovg-flex aiovg-gap-2 aiovg-items-center aiovg-hidden-desktop">
			<input type="submit" class="aiovg-button" value="<?php esc_attr_e( 'Search Videos', 'all-in-one-video-gallery' ); ?>" /> 
			<?php if ( $is_form_submitted ) : ?>
				<a href="<?php echo esc_url( aiovg_get_search_page_url() ); ?>" class="aiovg-flex aiovg-gap-1 aiovg-items-center aiovg-text-small">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
						<path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
					</svg>
					<?php esc_html_e( 'Reset', 'all-in-one-video-gallery' ); ?>
				</a>
			<?php endif; ?>
		</div> 
	</form> 
</div>
