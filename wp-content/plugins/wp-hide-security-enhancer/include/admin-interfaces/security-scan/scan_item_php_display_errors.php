<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_php_display_errors    extends WPH_security_scan_item
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
                    return 'php_display_errors';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'PHP display_errors',
                                        'icon'          =>  'dashicons-admin-generic',
                                        
                                        'help'          =>  __("The display_error setting in PHP is used to determine whether errors should be printed to the screen or not.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  5,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $display_errors = (bool)ini_get( 'display_errors' );
                    
                    if ( $display_errors    === TRUE   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The display_errors is ON. 
                                                                        <br />To fix this security issue, change the php.ini:
                                                                        <br /><code>display_errors = "off"</code>
                                                                        <br />or within .htaccess:
                                                                        <br /><code>php_flag display_errors off</code>
                                                                        <br />or within wp-config.php:
                                                                        <br /><code>ini_set("display_errors", "0");</code>.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The display_errors is Off.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>