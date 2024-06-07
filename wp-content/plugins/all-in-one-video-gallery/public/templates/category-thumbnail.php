<?php

/**
 * Category Thumbnail.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

$images_settings = get_option( 'aiovg_images_settings' );

$permalink = aiovg_get_category_page_url( $term );   

$image_size = ! empty( $images_settings['size'] ) ? $images_settings['size'] : 'large';
$image_data = aiovg_get_image( $term->term_id, $image_size, 'term', true );
$image      = $image_data['src'];
$image_alt  = ! empty( $image_data['alt'] ) ? $image_data['alt'] : $term->name;
?>

<div class="aiovg-thumbnail">
    <a href="<?php echo esc_url( $permalink ); ?>" class="aiovg-responsive-container" style="padding-bottom: <?php echo esc_attr( $attributes['ratio'] ); ?>;">
        <img src="<?php echo esc_url( $image ); ?>" class="aiovg-responsive-element" alt="<?php echo esc_attr( $image_alt ); ?>" />
    </a>
    
    <div class="aiovg-caption">
        <div class="aiovg-title">
            <a href="<?php echo esc_url( $permalink ); ?>" class="aiovg-link-title">
                <?php echo esc_html( $term->name ); ?>
            </a>
        </div>        

        <?php if ( ! empty( $attributes['show_count'] ) ) : ?>
            <div class="aiovg-count aiovg-flex aiovg-gap-1 aiovg-items-center aiovg-text-muted aiovg-text-small">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112Z" />
                </svg>
                <?php printf( _n( '%s video', '%s videos', $term->count, 'all-in-one-video-gallery' ), number_format_i18n( $term->count ) ); ?>
            </div>
        <?php endif; ?> 
        
        <?php if ( ! empty( $attributes['show_description'] ) && $term->description ) : ?>
            <div class="aiovg-description">
                <?php echo wp_kses_post( nl2br( $term->description ) ); ?>
            </div>
        <?php endif; ?>
    </div>            			
</div>