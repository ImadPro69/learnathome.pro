<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_hide_license_txt    extends WPH_security_scan_item
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
                    return 'hide_license_txt';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  __('Block license.txt',    'wp-hide-security-enhancer'),
                                        'icon'          =>  'dashicons-hidden',
                                        
                                        'help'          =>  __("This is a text file which contain the licensing terms for WordPress framework. Obviously you don't want that visible as every site containing such file must be a WordPress.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  10,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $found_issue        =   FALSE;
                    
                    $option       =   $this->wph->functions->get_module_item_setting('block_license_txt');
                    
                    if (    empty ( $option )   ||  $option ==  'no' )
                        {
                            if ( file_exists ( ABSPATH  .   'license.txt' ) )
                                {
                                    $found_issue    =   TRUE;
                                }
                        }

                    if ( $found_issue   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The license.txt file is still accessible.', 'wp-hide-security-enhancer' );
                            
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary" href="'. get_dashboard_url( '', 'admin.php?page=wp-hide-rewrite&component=root-files', 'admin' ) .'">Fix</a>',
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