<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_php_allow_url_include    extends WPH_security_scan_item
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
                    return 'php_allow_url_include';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'PHP allow_url_include',
                                        'icon'          =>  'dashicons-admin-generic',
                                        
                                        'help'          =>  __("The allow_url_include allows a developer to include a remote file using a URL rather than a local file path. This technique is used to reduce the load on the server. 
                                                                There are many servers with PHP configuration directive allow_url_include as enabled. When this setting is enabled, the server’s directory allows data retrieval from remote locations.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  10,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $allow_url_include = (bool)ini_get( 'allow_url_include' );

                    if ( $allow_url_include    === TRUE   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The allow_url_include is ON. 
                                                                        To fix this security issue, change the php.ini:

                                                                        <br /><code>allow_url_include = "off"</code>

                                                                        <br />or within .htaccess:

                                                                        <br /><code>php_flag allow_url_include off</code>
                                                                        <br />or within wp-config.php:
                                                                        <br /><code>ini_set("allow_url_include", "0");</code>.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The allow_url_include is Off.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>