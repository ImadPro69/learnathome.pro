<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_use_admin_user    extends WPH_security_scan_item
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
                    return 'use_admin_user';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  'User using Admin or Administrator',
                                        'icon'          =>  'dashicons-admin-users',
                                        
                                        'help'          =>  __("When setting up a new WordPress site, many users create the default administrator account using the username `admin`.
                                                                Considering entering the dashboard requires a username and a password, using the login `admin` makes the hackers have an easier time trying to brute force in. ",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  5,
                                        
                                        'callback'      =>  'scan_item_use_admin_user',
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();

                    $args   =   array ( 
                                        'login__in'         => array ( 'admin', 'administrator' )
                                        );
                    $user_query         =   new WP_User_Query( $args );
                    $found_users        =   $user_query->get_results();
                    $_JSON_response['info']  =   __( 'Found users: ', 'wp-hide-security-enhancer' ) . count ( $found_users );

                    if ( count ( $found_users ) > 0   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The admin or administrator usernames were found on your system!', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'read_more'         =>  '<a class="button" target="_blank" href="https://www.wpbeginner.com/wp-tutorials/how-to-change-your-wordpress-username/">Read More</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>There are no admin or administrator usernames.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>