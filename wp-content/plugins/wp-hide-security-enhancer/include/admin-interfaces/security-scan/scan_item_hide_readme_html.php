<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_hide_readme_html    extends WPH_security_scan_item
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
                    return 'hide_readme_html';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  __('Block readme.html',    'wp-hide-security-enhancer'),
                                        'icon'          =>  'dashicons-hidden',
                                        
                                        'help'          =>  __("A Hypertext Markup Language file with general information about installed WordPress, version, instalation steps, updating, requirements, resources etc.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  10,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $found_issue        =   FALSE;
                    
                    $option       =   $this->wph->functions->get_module_item_setting('block_readme_html');
                    
                    if (    empty ( $option )   ||  $option ==  'no' )
                        {
                            if ( file_exists ( ABSPATH  .   'readme.html' ) )
                                {
                                    $found_issue    =   TRUE;
                                }
                        }

                    if ( $found_issue   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The readme.html file is still accessible.', 'wp-hide-security-enhancer' );
                            
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