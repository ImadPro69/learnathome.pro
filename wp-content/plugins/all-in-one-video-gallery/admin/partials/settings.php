<?php

/**
 * Settings Form.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

$active_tab     = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
$active_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';

$sections = array();
foreach ( $this->sections as $section ) {
	$tab = $section['tab'];
	
	if ( ! isset( $sections[ $tab ] ) ) {
		$sections[ $tab ] = array();
    }
    
    $sections[ $tab ][] = $section;
}
?>

<div id="aiovg-settings" class="aiovg aiovg-settings wrap">
    <h1><?php esc_html_e( 'Plugin Settings', 'all-in-one-video-gallery' ); ?></h1>

    <?php settings_errors(); ?>

    <h2 class="nav-tab-wrapper wp-clearfix">
		<?php		
        foreach ( $this->tabs as $tab => $title ) {
            $url = add_query_arg( 'tab', $tab, admin_url( 'admin.php?page=aiovg_settings' ) );            

			foreach ( $sections[ $tab ] as $section ) {
                $url = add_query_arg( 'section', $section['id'], $url );

				if ( $tab == $active_tab && empty( $active_section ) ) {
					$active_section = $section['id'];
                }
                
				break;
            }

            $classes = array( 'nav-tab' );
            if ( $tab == $active_tab ) $classes[] = 'nav-tab-active';
            
            printf( 
                '<a href="%s" class="%s">%s</a>', 
                esc_url( $url ), 
                implode( ' ', $classes ), 
                esc_html( $title )
            );
        }
        ?>
    </h2>
    
    <?php	
	$section_links = array();

	foreach ( $sections[ $active_tab ] as $section ) {
        $page = $section['page'];

        $url = add_query_arg( 
            array(
                'tab'     => $active_tab,
                'section' => $page
            ), 
            admin_url( 'admin.php?page=aiovg_settings' ) 
        );

        if ( ! isset(  $section_links[ $page ] ) ) {
            $section_links[ $page ] = sprintf( 
                '<a href="%s" class="%s">%s</a>',			
                esc_url( $url ),
                ( $section['id'] == $active_section ? 'current' : '' ),
                ( isset( $section['menu_title'] ) ? esc_html( $section['menu_title'] ) : esc_html( $section['title'] ) )
            );
        }
	}

	if ( count( $section_links ) > 1 ) : ?>
		<ul class="aiovg-margin-bottom subsubsub"><li><?php echo implode( ' | </li><li>', $section_links ); ?></li></ul>
		<div class="clear"></div>
	<?php endif; ?>
    
	<form method="post" action="options.php"> 
        <?php
        $page_hook = $active_section;
        
        settings_fields( $page_hook );
        do_settings_sections( $page_hook );
        
        submit_button();
        ?>
    </form>
</div>