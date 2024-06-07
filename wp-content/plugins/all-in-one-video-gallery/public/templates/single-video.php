<?php

/**
 * Single Video Page.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div class="aiovg aiovg-single-video">
    <!-- Player -->
    <?php the_aiovg_player( $post->ID ); ?>

    <!-- After Player -->
    <?php the_aiovg_content_after_player( $post->ID, $attributes ); ?>

    <!-- Description -->
    <div class="aiovg-description aiovg-margin-top aiovg-hide-if-empty"><?php echo $content; ?></div>

    <!-- Meta informations -->
    <div class="aiovg-meta aiovg-flex aiovg-flex-col aiovg-gap-1 aiovg-margin-top">
        <?php         
        // Categories
        if ( $attributes['show_category'] && ! empty( $attributes['categories'] ) ) {
            $meta = array();

            foreach ( $attributes['categories'] as $category ) {
                $category_url = aiovg_get_category_page_url( $category );

                $meta[] = sprintf( 
                    '<a class="aiovg-link-category" href="%s">%s</a>', 
                    esc_url( $category_url ), 
                    esc_html( $category->name ) 
                );
            }

            echo '<div class="aiovg-category aiovg-flex aiovg-flex-wrap aiovg-gap-1 aiovg-items-center aiovg-text-small">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
            </svg>';
            echo '<div class="aiovg-item-category">' . implode( '<span class="aiovg-separator">,</span></div><div class="aiovg-item-category">', $meta ) . '</div>';
            echo '</div>';
        }
        ?>

        <?php
        // Tags
        if ( $attributes['show_tag'] && ! empty( $attributes['tags'] ) ) {
            $meta = array();

            foreach ( $attributes['tags'] as $tag ) {
                $tag_url = aiovg_get_tag_page_url( $tag );

                $meta[] = sprintf( 
                    '<a class="aiovg-link-tag" href="%s">%s</a>', 
                    esc_url( $tag_url ), 
                    esc_html( $tag->name ) 
                );
            }

            echo '<div class="aiovg-tag aiovg-flex aiovg-flex-wrap aiovg-gap-1 aiovg-items-center aiovg-text-small">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />
            </svg>';
            echo '<div class="aiovg-item-tag">' . implode( '<span class="aiovg-separator">,</span></div><div class="aiovg-item-tag">', $meta ) . '</div>';
            echo '</div>';
        }
        ?>

        <?php
        $meta = array();					

        // Date
        if ( $attributes['show_date'] ) {
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
            </svg>';

            $meta[] = sprintf(
                '<div class="aiovg-date aiovg-flex aiovg-gap-1 aiovg-items-center">%s<time>%s</time></div>',
                $icon,
                esc_html( aiovg_get_the_date() )
            );
        }
                
        // Author
        if ( $attributes['show_user'] ) {
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>';

            $author_url = aiovg_get_user_videos_page_url( $post->post_author );

            $meta[] = sprintf( 
                '<div class="aiovg-user aiovg-flex aiovg-gap-1 aiovg-items-center">%s<a href="%s" class="aiovg-link-author">%s</a></div>', 
                $icon,
                esc_url( $author_url ), 
                esc_html( get_the_author() ) 
            );			
        }

        // Views
        if ( $attributes['show_views'] ) {
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>';

            $views_count = get_post_meta( get_the_ID(), 'views', true );

            $meta[] = sprintf(
                '<div class="aiovg-views aiovg-flex aiovg-gap-1 aiovg-items-center">%s<span class="aiovg-views-count">%s</span></div>',
                $icon,
                sprintf( esc_html__( '%s views', 'all-in-one-video-gallery' ), number_format_i18n( $views_count ) )
            );
        }

        // ...
        if ( count( $meta ) ) {
            echo '<div class="aiovg-meta aiovg-flex aiovg-flex-wrap aiovg-gap-1 aiovg-items-center aiovg-text-small">';
            echo implode( '<span class="aiovg-text-separator">/</span>', $meta );
            echo '</div>';
        }
        ?>          
    </div> 
    
    <!-- Share buttons -->
    <?php if ( $attributes['share'] ) the_aiovg_socialshare_buttons(); ?>
</div>

<?php
// Related videos
if ( $attributes['related'] ) {
    $shortcode = sprintf(
        '[aiovg_related_videos title="%s"]',
        esc_html__( 'You may also like', 'all-in-one-video-gallery' )
    );

	$related_videos = do_shortcode( $shortcode );
		
	if ( strip_tags( $related_videos ) != aiovg_get_message( 'videos_empty' ) ) {
        echo '<br />';
		echo $related_videos;
	} 
}