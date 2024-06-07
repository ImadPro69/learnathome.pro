<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_keys_and_salts    extends WPH_security_scan_item
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
                    return 'keys_and_salts';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'Authentication Unique Keys and Salts',
                                        'icon'          =>  'dashicons-admin-generic',
                                        
                                        'help'          =>  __("WordPress security authentication or secret key or SALT keys, are the encrypted code that protects your login information. 
                                                                Salt keys are cryptographic elements used to 'hash' data in order to secure it. In fact, most serious platforms and systems use similar mechanisms to protect sensitive data.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  10,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $wrong_value    =   FALSE;
                    
                    $constants  =   array(
                                            'AUTH_KEY',
                                            'SECURE_AUTH_KEY',
                                            'LOGGED_IN_KEY',
                                            'NONCE_KEY',
                                            'AUTH_SALT',
                                            'SECURE_AUTH_SALT',
                                            'LOGGED_IN_SALT',
                                            'NONCE_SALT'
                                            );
                                            
                    foreach ( $constants as $constant )
                        {
                            if ( empty ( constant ( $constant ) )   ||  constant ( $constant )  ==  'put your unique phrase here'  )
                                {
                                    $wrong_value    =   TRUE;
                                    break;
                                }
                        }

                    if ( $wrong_value   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The Authentication unique keys and salts are empty or invalid.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'read_more'     =>  '<a class="button" target="_blank" href="https://www.malcare.com/blog/wordpress-salts/">Read More</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The Authentication unique keys and salts are correctly set-up.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>