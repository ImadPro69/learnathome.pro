<?php

    /**
    * Compatibility     : Redirection
    * Introduced at     : 5.3.10
    * Last Checked          : 5.3.10
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    
    class WPH_conflict_handle_redirection
        {
                        
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    add_filter( 'wp-hide/ignore_ob_start_callback', array ( $this, 'ignore_ob_start_callback' ) );
                          
                }                        
            
            public function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'redirection/redirection.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
            public function ignore_ob_start_callback( $ignore )
                {
                    if ( wp_is_json_request() && strpos( $_SERVER['REQUEST_URI'], '/redirection/' ) !== FALSE )   
                        $ignore = TRUE;
                    
                    return $ignore;   
                }
                            
        }
    
    new WPH_conflict_handle_redirection();
    
?>