<?php


    /**
    * Compatibility     : JCH Optimize
    * Introduced at     : 3.1
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_jch_optimize
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'jch_optimize_save_content',                    array( $this, 'proces_html_buffer'), 999 );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'jch-optimize/jch-optimize.php' ))
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
        
        
    new WPH_conflict_handle_jch_optimize();


?>