<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_firewall    extends WPH_security_scan_item
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
                    return 'firewall';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  __('Firewall',    'wp-hide-security-enhancer'),
                                        'icon'          =>  'dashicons-shield',
                                        
                                        'help'          =>  __("A Firewall is a security piece of software that adds a layer of protection to your site. A firewall works as a rules-based filter for all incoming traffic to a website, it ensures only the secure traffic is reaching the server, all malicious attempts will be blocked and logged.
                                                                <br />A Firewall works as Proactive ratter reactive security solution, so it helps to protect a website before the malicious and malware actually reach it. This is a huge improvement for security, as preventing any harm and damages to a site, spare the administrators of incalculable losses which the malware can do.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  20,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $found_errors       =   array();
                    
                    $firewall_check     =   array ( 
                                                    'header'        =>  array ( 
                                                                                'url'       =>  'query=header:',
                                                                                'message'   =>  __('Failed to block requests using malicious header calls.',    'wp-hide-security-enhancer')
                                                                                ),
                                                    'set_cookie'    =>  array(
                                                                                'url'       =>  'query=set-cookie:=',
                                                                                'message'   =>  __('Failed to block requests using malicious set-cookie calls.',    'wp-hide-security-enhancer')    
                                                                                    ),
                                                    'union'    =>  array(
                                                                                'url'       =>  'query=union(select(',
                                                                                'message'   =>  __('Failed to block requests using malicious MySQL code.',    'wp-hide-security-enhancer')    
                                                                                    ),
                                                    'globals'    =>  array(
                                                                                'url'       =>  'query=globals=',
                                                                                'message'   =>  __('Failed to block requests using malicious globals calls.',    'wp-hide-security-enhancer')    
                                                                                    ),
                                                    'request'    =>  array(
                                                                                'url'       =>  'query=request=',
                                                                                'message'   =>  __('Failed to block requests using malicious request calls.',    'wp-hide-security-enhancer')    
                                                                                    )                                                    
                                                );
                    
                    $args    =   array( 
                                        'sslverify'     => false, 
                                        'timeout'       => 15,
                                        'redirection'   => 0  
                                        );
                    
                    //using wordfence?
                    if ( ! defined ( 'WFWAF_RUN_COMPLETE' ) )
                        {
                            foreach (  $firewall_check  as  $item_id    =>  $firewall_item )
                                {
                                    $url   =   home_url() . '?' . $firewall_item['url'] ;
                                    $response   =   wp_remote_get( $url, $args  );
                                    
                                    if ( is_a( $response, 'WP_Error' ))
                                        {
                                            $found_errors[$item_id][]   =   $response->get_error_message();
                                            $found_errors[$item_id][]   =   $firewall_item['message'];
                                            continue;
                                        }
                                    
                                    if ( is_array( $response ) ) 
                                        {
                                            
                                            if  ( ! isset( $response['response']['code'] ) )
                                                {
                                                    $found_errors[$item_id][]  =   __('No valid respons for the call.',    'wp-hide-security-enhancer');
                                                    continue;
                                                }
                                            
                                            if  ( $response['response']['code'] !=  403 )
                                                {
                                                    $found_errors[$item_id][]  =   $firewall_item['message'];
                                                    continue;      
                                                }                                        
                                        }
                                }
                        }

                    if ( count ( $found_errors ) > 0 )
                        {
                            $_JSON_response['status']       =   FALSE;
                            
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>Your site does not appear to use a Firewall or fails to block specific hacks.', 'wp-hide-security-enhancer' );
                            $_JSON_response['description']  .=   '<br /><br />';
                            
                            foreach ( $found_errors   as  $found_error_messages )
                                {
                                    
                                    $_JSON_response['description']  .=  '<p class="important">';              
                                    $_JSON_response['description']  .=   '<b> <span class="dashicons dashicons-search"></span> ' . __( 'Found', 'wp-hide-security-enhancer' ) .' - ' . implode ( '<br />' , $found_error_messages ) .'</b>';
                                    $_JSON_response['description']  .=  '</p>';
                                    
                                }
                            
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary wph-pro" target="_blank" href="https://wp-hide.com/pricing/">PRO</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>Your site appears to use a Firewall.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>