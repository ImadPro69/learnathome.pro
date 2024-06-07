<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_wp_version_stability    extends WPH_security_scan_item
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
                    return 'wp_version_stability';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                    'title'         =>  'WordPress Version Stability',
                                    'icon'          =>  'dashicons-wordpress-alt',
                                    
                                    'help'          =>  __("Over time, security breaches are found within the WordPress core. This option checks whenever the WordPress version deployed on your site is succeptible to a known vulenrability. ",    'wp-hide-security-enhancer'),
                                    
                                    'score_points'  =>  5,
                                    
                                    'callback'      =>  'scan_item_wp_version_stability',
                                    'use_transient' =>  TRUE
                                    );
                }
                
            
            function scan()
                {
                    global $wp_version;
                    
                    $_JSON_response         =   array();
                    $wp_stability           =   FALSE;
                    
                    $_JSON_response['info']  =   __( 'Using Version: ', 'wp-hide-security-enhancer' ) . $wp_version;
                    
                    $response       =   wp_remote_get( 'http://api.wordpress.org/core/stable-check/1.0/', array( 'sslverify' => false, 'timeout' => 10 )  );
                    
                    $http_response  =   FALSE;
                    if ( ! is_wp_error( $response ) )
                        $http_response  =   $response['http_response'];
                    
                    if ( ! is_array( $response )    ||  ! is_object( $http_response )   ||  $http_response->get_status() !=  200 )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span> Unable to connect with WordPress API. Try again later.', 'wp-hide-security-enhancer' );
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary" target="_blank" href="'. get_dashboard_url( '', 'update-core.php', 'admin' ) .'">Fix</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                            
                            return $this->return_json_response( $_JSON_response );
                        }
                    
                    $response_body  =   json_decode ( $response['body'] );
                    if ( $response_body->{$wp_version} )
                        {
                            $wp_stability  =   $response_body->{$wp_version};
                        }
                        
                    if ( $wp_stability )
                        {
                            if ( $wp_stability  ==  'latest' )
                                {
                                    $_JSON_response['status']       =   TRUE;
                                    $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span> The current Wordpress version stability tag is ', 'wp-hide-security-enhancer' ) . '<b> ' . strtoupper ( $wp_stability ) .'</b> ' ;
                                }
                                else
                                {
                                    $_JSON_response['status']       =   FALSE;
                                    
                                    if ( $wp_stability  ==  'outdated' )
                                        $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>  The current Wordpress version stability tag is ', 'wp-hide-security-enhancer' ) . '<b> ' . strtoupper ( $wp_stability ) .'</b> ';
                                        else
                                        {
                                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>  The current Wordpress version stability tag is ', 'wp-hide-security-enhancer' ) . '<b> ' . strtoupper ( $wp_stability ) .'</b> ' . __('. This is critical and require urgent WordPress update.', 'wp-hide-security-enhancer' );
                                            $_JSON_response['score_points'] =   20;
                                        }
                                        
                                    $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary" target="_blank" href="'. get_dashboard_url( '', 'update-core.php', 'admin' ) .'">Fix</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                                }    
                        }
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>