<?php

    /**
    * Compatibility: Oxygen
    * Introduced at:    3.1
    * Last checked:     4.1.1
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_oxygen
        {
            var $wph;
                                        
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'plugins_loaded',                        array( $this,   'plugins_loaded'), 999 );
                    
                    if ( isset ( $_GET['ct_builder'] ) )
                        {
                            add_filter( 'wph/components/components_run/ignore_component',    array( $this,    'ignore_field_id'), 999, 4 );
                        }
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'oxygen/functions.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            
            function plugins_loaded()
                {
                    
                    include ('oxygen-class.php');    
                    
                    global $oxygen_signature;
                    
                    $oxygen_signature = new WPH_OXYGEN_VSB_Signature();
                }
                
                
            public static function ignore_field_id( $ignore_field, $component_id, $saved_field_value, $_class_instance )
                {
                    
                    if  ( in_array( $component_id, array( 'remove_html_new_lines', 'styles_remove_id_attribute', 'scripts_remove_id_attribute' ) ) )
                        {
                            $ignore_field   =   TRUE;
                        }
                    
                    return $ignore_field;
                    
                }
                
                            
        }


        new WPH_conflict_handle_oxygen();
        
        
        

?>