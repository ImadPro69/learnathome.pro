<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_mysql_version    extends WPH_security_scan_item
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
                    return 'mysql_version';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'MySQL Version',
                                        'icon'          =>  'dashicons-database',
                                        
                                        'help'          =>  __("Using a higher MySQL version ensures better capability for your system. Older versions are often exploitable making the system unstable and predisposing to security breaches.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  5,
                                        
                                        'callback'      =>  'scan_item_mysql_version',
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();

                    global $wpdb;
                                       
                    $_JSON_response['info']  =   __( 'Using Version: ', 'wp-hide-security-enhancer' ) . $wpdb->db_version();

                    if ( version_compare ( $wpdb->db_version(), '5.0', '>=' ) )
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