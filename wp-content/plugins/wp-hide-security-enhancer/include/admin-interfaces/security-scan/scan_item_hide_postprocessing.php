<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_hide_postprocessing    extends WPH_security_scan_item
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
                    return 'hide_postprocessing';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  __('Post-Processing',    'wp-hide-security-enhancer'),
                                        'icon'          =>  'dashicons-hidden',
                                        
                                        'help'          =>  __("The feature provides a post-processing engine for all site assets ( CSS /  JavaScript ). That encodes the CSS  and JavaScript, which makes it unable to read. Also, ensure perfect URLs disguise, as even if changing the plugin's name, most of the URLs still contain traces within. 
                                                                    This is also a great tool for making optimisation of the site assets by combining, minifying, comment removal etc. 
                                                                    Perfectly functional and integration in conjunction with other SEO/Optimisation plugins.
                                                                    <p>&nbsp;</p>
                                                                    <p>There are 4 types of processing options:
                                                                    <b>Combine</b>: Merge all code in (usually) 2 files, one in the header and another in the footer.
                                                                    <b>Combine & Encode Inline</b>: Merge all code in (usually) 2 files, one in the header and another in the footer. Additionally, the Inline code will be base64 encoded and placed in the same spot.
                                                                    <b>In Place</b>: All JavaScript code will be processed and the results will be placed in the same spot. Any InLine code will be processed and saved into a data-collection directory for later usage.
                                                                    <b>In Place & Encode Inline</b>: All code will be processed and the results will be placed in the same spot. Additionally, the Inline code will be base64 encoded.</p>",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  30,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    //The free code does not include such functionality
                    $found_issue    =   TRUE;

                    if ( $found_issue   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>Your site assets still contain traceable data within CSS / JavaScript', 'wp-hide-security-enhancer' );
                            
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary wph-pro" target="_blank" href="https://wp-hide.com/pricing/">PRO</a>',
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