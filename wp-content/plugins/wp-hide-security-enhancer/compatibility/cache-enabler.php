<?php

    /**
    * Compatibility: Cache Enabler
    * Introduced at: 1.3.4
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_cache_enabler
        {
            
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'cache_enabler_before_store',                    array( $this, 'cache_enabler_before_store'), 999 );
                    
                }             
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'cache-enabler/cache-enabler.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            function cache_enabler_before_store( $buffer )
                {
                                            
                    global $wph;
                    
                    $buffer    =   $wph->functions->content_urls_replacement( $buffer,  $wph->functions->get_replacement_list() );
                       
                    return $buffer; 
                    
                }
           
        }
        
        
    new WPH_conflict_handle_cache_enabler();


?>