<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_wp_debug    extends WPH_security_scan_item
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
                    return 'wp_debug';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'WP Debug',
                                        'icon'          =>  'dashicons-code-standards',
                                        
                                        'help'          =>  __("Debugging PHP code is part of any project, but WordPress comes with specific debug systems designed to simplify the process as well as standardize code across the core, plugins and themes.
                                                                On production sites, the debug should be disabled to avoid exposing paths and other pieces of information related to the site. ",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  5,
                                        
                                        'callback'      =>  'scan_item_wp_debug',
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $_JSON_response['info']  =   __( 'Current value: ', 'wp-hide-security-enhancer' ) . ( WP_DEBUG  === TRUE ? 'TRUE' : 'FALSE' );

                    if ( defined ( 'WP_DEBUG' ) &&  WP_DEBUG    === TRUE   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The WP_DEBUG is active. Check your site wp-config.php and comment the constant declaration.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The WP_DEBUG is disabled.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>