<?php

/**
 * Search Form: Compact Layout.
 *
 * @link    https://plugins360.com
 * @since   3.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div class="aiovg aiovg-search-form aiovg-search-form-template-compact">
	<form method="get" action="<?php echo esc_url( aiovg_get_search_page_url() ); ?>">
    	<?php if ( ! get_option('permalink_structure') ) : ?>
       		<input type="hidden" name="page_id" value="<?php echo esc_attr( $attributes['search_page_id'] ); ?>" />
    	<?php endif; ?>

		<div class="aiovg-form-group aiovg-field-keyword">
			<input type="text" name="vi" class="aiovg-form-control" placeholder="<?php esc_attr_e( 'Search Videos', 'all-in-one-video-gallery' ); ?>" value="<?php echo isset( $_GET['vi'] ) ? esc_attr( stripslashes( $_GET['vi'] ) ) : ''; ?>" />
		</div>
		
		<!-- Hook for developers to add new fields -->
        <?php do_action( 'aiovg_search_form_fields', $attributes ); ?>		

		<div class="aiovg-form-group aiovg-field-submit">
			<button type="submit" class="aiovg-button"> 
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
					<path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
				</svg>
			</button>
		</div>		
	</form> 
</div>
