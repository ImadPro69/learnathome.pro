<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_headers    extends WPH_security_scan_item
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
                    return 'headers';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  __('HTTP Response Security Headers',    'wp-hide-security-enhancer'),
                                        'icon'          =>  'dashicons-hidden',
                                        
                                        'help'          =>  __("HTTP Response Headers are a powerful tool to Harden Your Website.
                                                                The Hypertext Transfer Protocol (HTTP) is based on a client-server architecture, in which the client ( typically a web browser application ) establishes a connection with the server through a destination URL and waits for a response.
                                                                The HTTP Headers allow the client and the server send additional pieces of information with the HTTP request or response.
                                                                The HTTP Headers are categorised by their purpose: Authentication, Caching, Client hints, Conditionals, Connection management, Content negotiation, Controls, Cookies, CORS, Downloads, Message body information, Proxies, Redirects, Request context, Response context, Range requests, Security, Server-sent events, Transfer coding, WebSockets, Other
                                                                This area provides support for the <a href='https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security' target='_blank'>Security Headers</a> type. Those are the ones responsible for the security implementation for any page.",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  20,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $found_issue        =   FALSE;
                    $found_headers      =   array();
                    $not_found_headers  =   array();
                                      
                    if ( $this->wph->security_scan->remote_headers )
                        {
                            $WPH_module_general_security_check_headers  =   new WPH_module_general_security_check_headers();
                            
                            $headers    =   $this->wph->security_scan->remote_headers;
                            
                            $found_headers  =   array ( );
                            
                            foreach ( $headers->getAll() as $header_key =>  $header_value )
                                {
                                    $header_key =   strtolower ( $header_key ) ;
                                    $header_key =   trim ( $header_key );
                                                                        
                                    if ( isset( $WPH_module_general_security_check_headers->headers[ $header_key ] ) )
                                        $found_headers[]    =   $header_key;
                                }
                            
                            foreach ( $WPH_module_general_security_check_headers->headers    as $header_key  =>  $header_data )
                                {
                                    if ( in_array ( $header_key, $found_headers ) )
                                        continue;
                                        
                                    $not_found_headers[]    =   $header_key;   
                                }
                                
                            if ( count ( $not_found_headers ) > 0    )
                                $found_issue    =   TRUE;
                        }
                        else
                        $found_issue    =   TRUE;

                    if ( $found_issue   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>Your site is missing some security headers.', 'wp-hide-security-enhancer' );
                            
                            foreach ( $not_found_headers   as  $not_found_header )
                                {
                                    
                                    $_JSON_response['description']  .=  '<p class="important">';              
                                    $_JSON_response['description']  .=   '<b> <span class="dashicons dashicons-search"></span> Not Found - ' . ucfirst ( $not_found_header ) .'</b>';
                                    $_JSON_response['description']  .=  '</p>';
                                    
                                }
                                
                            if ( $this->wph->security_scan->remote_started  &&  $this->wph->security_scan->remote_errors   !== FALSE )
                                $_JSON_response['description']  .=   "<br /><br /><span class='error'>" . __('Unable to complete this security task as an error occoured', 'wp-hide-security-enhancer' ) . ': <b>' .$this->wph->security_scan->remote_errors . '</b></span>';
                            
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary" href="'. get_dashboard_url( '', 'admin.php?page=wp-hide-security-headers', 'admin' ) .'">Fix</a>',
                                                                        'fix2'       =>  '<a class="button-primary wph-pro" target="_blank" href="https://wp-hide.com/pricing/">PRO</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>There are no headers containing valuable pieces of information regarding your environment.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>