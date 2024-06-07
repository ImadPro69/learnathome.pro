<?php


    /**
    * Compatibility     : A2 Optimized WP
    * Introduced at     : 2.1.4.6.2.2
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_a2_optimized_wp
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'a2opt_cache_page_contents_before_store',                    array( $this, 'proces_html_buffer'), 999 );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'a2-optimized-wp/a2-optimized.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            function proces_html_buffer( $buffer )
                {
                                            
                    $buffer =   $this->wph->ob_start_callback( $buffer );
                       
                    return $buffer;
                    
                }
           
        }
        
        
    new WPH_conflict_handle_a2_optimized_wp();


?>