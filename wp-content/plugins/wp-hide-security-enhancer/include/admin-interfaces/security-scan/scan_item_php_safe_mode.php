<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_php_safe_mode    extends WPH_security_scan_item
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
                    return 'php_safe_mode';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'PHP safe_mode',
                                        'icon'          =>  'dashicons-admin-generic',
                                        
                                        'help'          =>  __("The PHP safe mode is an attempt to solve the shared-server security problem. It is architecturally incorrect to try to solve this problem at the PHP level, but since the alternatives at the web server and OS levels aren't very realistic, many people, especially ISP's, use safe mode for now.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  5,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $safe_mode = (bool)ini_get( 'safe_mode' );

                    if ( $safe_mode    === TRUE   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The register_globals is ON. 
                                                                        To fix this security issue, change the php.ini:

                                                                        <br /><code>safe_mode = "off"</code>

                                                                        <br />or within .htaccess:

                                                                        <br /><code>php_flag safe_mode off</code>.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The safe_mode is Off.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>