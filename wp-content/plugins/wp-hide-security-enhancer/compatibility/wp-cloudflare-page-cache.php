<?php


    /**
    * Compatibility     : Super Page Cache for Cloudflare
    * Introduced at     : 4.6.1
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_wp_super_cache_for_cloudflare
        {
            var $wph;
                            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                    
                    $this->_init();
                    
                }
                
            private function _init()
                {
                    add_filter( 'swcfpc_normal_fallback_cache_html',                    array( $this, 'process_buffer'), 999 );
                    add_filter( 'swcfpc_curl_fallback_cache_html',                      array( $this, 'process_buffer'), 999 );
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'wp-cloudflare-page-cache/wp-cloudflare-super-page-cache.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                     
            function process_buffer( $buffer )
                {
                    
                    global $wph;
                    
                    $buffer =   $wph->ob_start_callback( $buffer );
                    
                    return $buffer;
                    
                }
                
          
                            
        }


        new WPH_conflict_handle_wp_super_cache_for_cloudflare();
        
?>