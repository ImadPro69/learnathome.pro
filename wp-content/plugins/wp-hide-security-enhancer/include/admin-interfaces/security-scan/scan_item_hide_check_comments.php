<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_hide_check_comments    extends WPH_security_scan_item
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
                    return 'hide_check_comments';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  __('New wp-comments-post.php',    'wp-hide-security-enhancer'),
                                        'icon'          =>  'dashicons-hidden',
                                        
                                        'help'          =>  __("To avoid boots to create smap comments, the default wp-comments-post.php should be changed to a custom one.
                                                            <br />This makes it easy to recognise as WordPress form. Boots always search for such file ( wp-comments-post.php ) and automatically submit spam messages.Though this option a new file slug can replace the default.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  10,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $found_issue        =   FALSE;
                    $option_value       =   $this->wph->functions->get_module_item_setting('new_wp_comments_post');
                    
                    if ( empty ( $option_value ) )
                        $found_issue    =   TRUE;
                    
                    $found_within_code  =   FALSE;
                    if ( ! $found_issue &&  $this->wph->security_scan->remote_html )
                        {
                            $seek_url   =   $option_value;
                            if ( stripos( $this->wph->security_scan->remote_html, $seek_url ) )
                                $found_within_code    =   TRUE;
                        }
                    
                    if ( $found_within_code )
                        $found_issue    =   TRUE;

                    if ( $found_issue   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            
                            if ( empty ( $option_value ) )
                                {
                                    $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The default wp-comments-post.php has not been customised.', 'wp-hide-security-enhancer' );
                                }
                                else
                                {
                                    $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The default wp-comments-post.php is still found within the source HTML.', 'wp-hide-security-enhancer' );
                                    if ( $found_within_code )
                                        $_JSON_response['description']  =   __( '<br />Ensure you cleared the site cache, then check again.', 'wp-hide-security-enhancer' );
                                }
                            
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary" href="'. get_dashboard_url( '', 'admin.php?page=wp-hide-rewrite&component=comments', 'admin' ) .'">Fix</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>The default wp-comments-post.php cannot be found anymore through the site source.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>