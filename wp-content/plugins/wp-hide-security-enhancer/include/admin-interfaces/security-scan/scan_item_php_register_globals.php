<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_php_register_globals    extends WPH_security_scan_item
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
                    return 'php_register_globals';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'PHP register_globals',
                                        'icon'          =>  'dashicons-admin-generic',
                                        
                                        'help'          =>  __("When register_globals is enabled, PHP will automatically create variables in the global scope for any value passed in GET, POST or COOKIE. This, combined with the use of variables without initialization, has led to numerous security vulnerabilities.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  20,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $register_globals = (bool)ini_get( 'register_globals' );
                    
                    if ( $register_globals    === TRUE   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The register_globals is ON. 
                                                                        To fix this security issue, change the php.ini:

                                                                        <br /><code>register_globals = "off"</code>

                                                                        <br />or within .htaccess:

                                                                        <br /><code>php_flag register_globals off</code>.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The register_globals is Off.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>