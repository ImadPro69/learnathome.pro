<?php

    /**
    * Compatibility     : WPForms Lite
    * Introduced at     : 1.8.7.2
    * Last checked on   : 1.8.7.2
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_wpforms
        {
                        
            static function init()
                {
                    if( !   self::is_plugin_active() )
                        return FALSE;
                    
                    if ( isset ( $_POST['action'] ) &&  strpos( $_POST['action'], 'wpforms' )   !== FALSE  )
                        add_filter ( 'wph/components/rewrite-default/superglobal_variables_replacements' , array ( 'WPH_conflict_handle_wpforms', 'do_superglobal_variables_replacements' ), 10, 3 );
                    
                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if ( is_plugin_active( 'wpforms-lite/wpforms.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
            /**
            * control certain data to be preserved as is
            *     
            * @param mixed $do_replace
            * @param mixed $key
            * @param mixed $superglobal_type
            */
            static function do_superglobal_variables_replacements( $do_replace, $key, $superglobal_type ) 
                {
                    //Ignore the _wp_http_referer to avoid fails when conpare between the urls 
                    if ( $key == '_wp_http_referer' )
                        return FALSE;
                        
                    return $do_replace;      
                }

           
        }
        
        
    WPH_conflict_handle_wpforms::init();


?>