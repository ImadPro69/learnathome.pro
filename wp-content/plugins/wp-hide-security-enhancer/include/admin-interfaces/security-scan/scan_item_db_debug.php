<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_db_debug    extends WPH_security_scan_item
        {
            var $wph;
                     
            function __construct()
                {
                    $this->id       =   $this->get_id();
                   
                    global $wph;
                    
                    $this->wph  =   $wph;
                }   
            
            public function get_id()
                {
                    return 'db_debug';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'Database Debug',
                                        'icon'          =>  'dashicons-code-standards',
                                        
                                        'help'          =>  __("Debugging PHP code is part of any project, but WordPress comes with specific debug systems designed to simplify the process as well as standardize code across the core, plugins and themes.
                                                                On production sites, the debug should be disabled to avoid exposing paths and other pieces of information related to the site. ",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  5,
                                        
                                        'callback'      =>  'scan_item_db_debug',
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();

                    global $wpdb;
                    
                    $_JSON_response['info']  =   __( 'Current value: ', 'wp-hide-security-enhancer' ) . ( $wpdb->show_errors  === TRUE ? 'TRUE' : 'FALSE' );

                    if ( $wpdb->show_errors    === TRUE   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The database debug is active. Check your site wp-config.php and comment the WP_DEBUG and  WP_DEBUG_DISPLAY ( if exists ) constants declaration.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The database debug is disabled.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>