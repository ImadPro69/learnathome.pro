<?php

/**
 * Categories: Grid Layout.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div id="aiovg-<?php echo esc_attr( $attributes['uid'] ); ?>" class="aiovg aiovg-categories aiovg-categories-template-grid">
    <?php
    // Title
    if ( ! empty( $attributes['title'] ) ) : ?>
        <h2 class="aiovg-header">
            <?php echo esc_html( $attributes['title'] ); ?>
        </h2>
    <?php endif; ?>
    
    <div class="aiovg-section-categories aiovg-grid aiovg-row">
        <?php
        // The loop 
        $columns = (int) $attributes['columns'];

        foreach ( $terms as $key => $term ) :       
            $classes = array();
            $classes[] = 'aiovg-item-category';
            $classes[] = 'aiovg-item-category-' . $term->term_id;
            $classes[] = 'aiovg-col';
            $classes[] = 'aiovg-col-' . $columns;            
            if ( $columns > 3 ) $classes[] = 'aiovg-col-sm-3';
            if ( $columns > 2 ) $classes[] = 'aiovg-col-xs-2';
            ?>            
            <div class="<?php echo implode( ' ', $classes ); ?>" data-id="<?php echo esc_attr( $term->term_id ); ?>">		
                <?php the_aiovg_category_thumbnail( $term, $attributes ); ?>		
            </div> 
        <?php endforeach; ?>
    </div>

    <?php
    if ( ! empty( $attributes['show_pagination'] ) ) { // Pagination        
        the_aiovg_pagination( $attributes['max_num_pages'], '', $attributes['paged'], $attributes );
    } elseif ( ! empty( $attributes['show_more'] ) ) { // More button        
        the_aiovg_more_button( $attributes['max_num_pages'], $attributes );
    }
    ?>
</div>