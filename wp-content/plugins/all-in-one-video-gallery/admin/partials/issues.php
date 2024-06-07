<?php

/**
 * Dashboard: Issues.
 *
 * @link    https://plugins360.com
 * @since   1.6.5
 *
 * @package All_In_One_Video_Gallery
 */

$sections = array(
    'found'   => __( 'Issues', 'all-in-one-video-gallery' ),
    'ignored' => __( 'Ignored', 'all-in-one-video-gallery' )
);

$active_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : 'found';
?>

<div id="aiovg-issues" class="aiovg-flex aiovg-flex-col aiovg-gap-4 aiovg-padding-top">
    <?php
    // Notices 
    if ( isset( $_GET['success'] ) && 1 == $_GET['success'] ) {
        printf( 
            '<div class="aiovg-notice aiovg-notice-success">%s</div>',
            ( 'found' == $active_section ? __( 'Congrats! Issues solved.', 'all-in-one-video-gallery' ) : __( 'Issues ignored.', 'all-in-one-video-gallery' ) )
        );
    }

    // Section Links
    $section_links = array();

    foreach ( $sections as $key => $title ) {
        $url = add_query_arg( 'section', $key, 'admin.php?page=all-in-one-video-gallery&tab=issues' );
        $url = admin_url( $url );

        $section_links[] = sprintf( 
            '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
            esc_url( $url ),
            ( $key == $active_section ? 'current' : '' ),
            esc_html( $title ),
            count( $issues[ $key ] )
        );
    }
    ?>
    <ul class="aiovg-no-margin subsubsub"><li><?php echo implode( ' | </li><li>', $section_links ); ?></li></ul>
    
    <!-- Issues List -->
    <form id="aiovg-issues-form" action="<?php echo esc_url( admin_url( 'admin.php?page=all-in-one-video-gallery&tab=issues&section=' . $active_section ) ); ?>" method="post">
        <table class="striped widefat">
            <thead>
                <tr>
                    <td><input type="checkbox" id="aiovg-issues-check-all" class="aiovg-no-margin" /></td>
                    <td><?php esc_html_e( 'Issue', 'all-in-one-video-gallery' ); ?></td>
                    <td><?php esc_html_e( 'Description', 'all-in-one-video-gallery' ); ?></td>
                </tr>
            </thead>
            <?php if ( count( $issues[ $active_section ] ) > 0 ) : ?>
                <tbody>
                    <?php foreach ( $issues[ $active_section ] as $key ) : 
                        $issue = $this->get_issue_details( $key );
                        ?>
                        <tr>
                            <td><input type="checkbox" name="issues[]" class="aiovg-issue aiovg-no-margin" value="<?php echo esc_attr( $key ); ?>" /></td>
                            <td><?php echo esc_html( $issue['title'] ); ?></td>
                            <td><?php echo wp_kses_post( $issue['description'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="aiovg-text-right" colspan="3">
                            <?php if ( 'found' == $active_section ) : ?>
                                <input type="submit" name="action" class="button" value="<?php esc_attr_e( 'Ignore', 'all-in-one-video-gallery' ); ?>" />
                            <?php endif; ?>

                            <input type="submit" name="action" class="button button-primary" value="<?php esc_attr_e( 'Apply Fix', 'all-in-one-video-gallery' ); ?>" />
                        </td>
                    </tr>
                </tfoot>
            <?php else : ?>
                <tr>
                    <td colspan="3">
                        <?php
                        if ( 'ignored' == $active_section ) {
                           esc_html_e( 'You have no ignored issues.', 'all-in-one-video-gallery' );
                        } else {
                            esc_html_e( 'You have no issues.', 'all-in-one-video-gallery' );
                        }
                        ?>
                    </td>
                </tr>  
            <?php endif; ?>
        </table> 

        <?php wp_nonce_field( 'aiovg_fix_ignore_issues', 'aiovg_issues_nonce' ); // Nonce ?>
    </form>   
</div>
