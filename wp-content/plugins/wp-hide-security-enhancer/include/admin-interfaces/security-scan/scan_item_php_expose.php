<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_php_expose    extends WPH_security_scan_item
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
                    return 'php_expose';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'PHP expose',
                                        'icon'          =>  'dashicons-admin-generic',
                                        
                                        'help'          =>  __("When the expose_php directive is enabled, PHP includes critical pieces of information within the HTTP response X-Powered-By header when a page is requested.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  10,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $expose_php = (bool)ini_get( 'expose_php' );
                    
                    if ( $expose_php    === TRUE   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The expose_php is ON. 
                                                                        To fix this security issue, change the php.ini:

                                                                        <br /><code>expose_php = "off"</code>

                                                                        <br />or within .htaccess:

                                                                        <br /><code>php_flag expose_php off</code>.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The expose_php is Off.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>