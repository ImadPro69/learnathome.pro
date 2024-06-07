<?php


    /**
    * Compatibility     : SG_CachePress
    * Introduced at     : 7.3.1
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_sg_cachepress
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_filter( 'init', array ( $this, 'init' ), 11 );
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'sg-cachepress/sg-cachepress.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            
            function init()
                {
                    ob_start( array( $this, 'process_buffer' ) );
                }
                    
                
            function process_buffer( $buffer )
                {
                    
                    $buffer    =   $this->wph->functions->content_urls_replacement( $buffer,  $this->wph->functions->get_replacement_list() );
                       
                    return $buffer;   
                    
                }
        }


    new WPH_conflict_sg_cachepress();
    
?>