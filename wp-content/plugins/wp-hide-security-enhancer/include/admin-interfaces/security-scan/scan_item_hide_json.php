<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_hide_json    extends WPH_security_scan_item
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
                    return 'hide_json';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  __('JSON REST',    'wp-hide-security-enhancer'),
                                        'icon'          =>  'dashicons-hidden',
                                        
                                        'help'          =>  __("The WordPress REST API is an easy-to-use set of HTTP endpoints which allows access a site data in simple JSON format. That including users, posts, taxonomies and more. Retrieving or updating is as simple as sending a HTTP request.
                                                                <br />A REST API can be consumed everywhere. On mobile applications, on front-end (web apps) or any other devices that have access on the net, practically everything can connect from anywhere to your site and interact though JSON REST API service.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  10,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $found_issue        =   FALSE;
                    
                    $disable_json_rest_v1       =   $this->wph->functions->get_module_item_setting('disable_json_rest_v1');
                    $disable_json_rest_v2       =   $this->wph->functions->get_module_item_setting('disable_json_rest_v2');
                    
                    if ( ( empty ( $disable_json_rest_v1 )   ||  $disable_json_rest_v1   ==  'no' ) &&  ( empty ( $disable_json_rest_v2 )   ||  $disable_json_rest_v2   ==  'no' ) )
                        $found_issue    =   TRUE;

                    if ( $found_issue   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The JSON endpoint should be customised. If not used, should be disabled.', 'wp-hide-security-enhancer' );
                            
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary wph-pro" target="_blank" href="https://wp-hide.com/pricing/">PRO</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The option appears properly configured.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>