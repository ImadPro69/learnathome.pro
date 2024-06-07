<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_item
        {
            var $wph;
            
            var $id;
                     
            function __construct()
                {
                    $this->id       =   $this->get_id();
                   
                    global $wph;
                    
                    $this->wph  =   $wph;
                }   
            
            public function get_id()
                {
                
                }
                
                
            public function get_settings()
                {
                
                
                }
                
            public function return_json_response( $response )
                {
                    $defaults   =   array ( 
                                            'info'          =>  '',
                                            'status'        =>  FALSE,
                                            'description'   =>  '',
                                            'actions'       =>  array()
                                            );
                    
                    $response   =   wp_parse_args ( $response, $defaults );    
                    
                    return json_encode( $response );
                }
            
        }
        
        
?>