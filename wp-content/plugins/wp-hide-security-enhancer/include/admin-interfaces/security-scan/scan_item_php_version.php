<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_php_version    extends WPH_security_scan_item
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
                    return 'php_version';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                    'title'         =>  __( 'PHP Version', 'wp-hide-security-enhancer' ),
                                    'icon'          =>  'dashicons-admin-generic',
                                    
                                    'help'          =>  __("Using the latest PHP version ensures the longevity of security updates. While older versions of PHP offer security updates for a time past “end of life,” the most secure option is the version that is actively maintained.",    'wp-hide-security-enhancer'),
                                    
                                    'score_points'  =>  5,
                                    
                                    'callback'      =>  'scan_item_php_version',
                                    );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();

                    $phpversion = phpversion();
                    
                    $_JSON_response['info']  =   __( 'Using Version: ', 'wp-hide-security-enhancer' ) . $phpversion;
                    
                                            
                    if ( version_compare ( $phpversion, '7.0', '>=' ) )
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span> You are using at least the minimum recommended PHP version.', 'wp-hide-security-enhancer' );
                        }
                        else
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span> You are using an older PHP version that the minimum recommended.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>