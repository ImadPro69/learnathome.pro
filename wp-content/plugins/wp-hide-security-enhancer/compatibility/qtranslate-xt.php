<?php

    /**
    * Compatibility     : qTranslate-XT
    * Introduced at     : 3.15.1
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_qtranslatext
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter ( 'wp-hide/modules_components_run/completed',    array ( $this, 'modules_components_run__completed' ) );
                    add_filter ( 'qtranslate_language_detect_redirect',         array ( $this, 'qtranslate_language_detect_redirect' ), 10, 3 );
                          
                }                        
            
            public function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'qtranslate-xt/qtranslate.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
            
            public function modules_components_run__completed()
                {
                    $WPH_module    =   $this->wph->functions->return_component_instance( 'WPH_module_admin_new_wp_login_php' );
                    remove_filter('login_url', array( $WPH_module, 'login_url'), 999 );                        
                }
                
            public function qtranslate_language_detect_redirect( $url_lang, $url_orig, $url_info  )
                {
                    $new_path       =   $this->wph->functions->get_module_item_setting('new_wp_login_php' );
                    if ( ! empty(  $new_path ) )
                        {
                            if ( strpos ( untrailingslashit ( $url_lang ), $new_path ) !==  FALSE )
                                return $url_orig; 
                        }    
                    
                    
                    return $url_lang;
                }
        }
        
    new WPH_conflict_handle_qtranslatext();


?>