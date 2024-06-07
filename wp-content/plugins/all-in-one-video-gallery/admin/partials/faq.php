<?php

/**
 * Dashboard: FAQ.
 *
 * @link    https://plugins360.com
 * @since   1.6.5
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div id="aiovg-faq">
    <p class="about-description">
        1. <?php esc_html_e( 'Does the plugin support third-party page builders like "Elementor", "WPBakery", "Divi", etc.?', 'all-in-one-video-gallery' ); ?>
    </p>

    <?php 
    printf(
        __( 'Sure, this is the main reason we developed the shortcode builder. Simply generate your shortcode using the <a href="%s">Shortcode Builder</a> and use it in your page builder.', 'all-in-one-video-gallery' ),
        esc_url( admin_url( 'admin.php?page=all-in-one-video-gallery' ) )
    );
    ?>

    <p class="about-description">
        2. <?php esc_html_e( 'The plugin is not working for me. What should I do now?', 'all-in-one-video-gallery' ); ?>
    </p>

    <?php 
    printf(
        __( 'No Worries. We are just an email away from you. Please contact us <a href="%s">here</a>.', 'all-in-one-video-gallery' ),
        esc_url( admin_url( 'admin.php?page=all-in-one-video-gallery-contact' ) )
    );
    ?>
</div>
