<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_hide_replacements    extends WPH_security_scan_item
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
                    return 'hide_replacements';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                        'title'         =>  __('Replacements',    'wp-hide-security-enhancer'),
                                        'icon'          =>  'dashicons-hidden',
                                        
                                        'help'          =>  __("The module implements a post-processing engine, which allows arbitrary words to be replaced with custom ones. This works for all site data as HTML, Css, JavaScript assets.
                                                                This is the perfect tool to white-label any plugins or active code on a site, by replacing the specific words (classes, tags, JavaScript variables etc).
                                                                Examples can be found at <a href='https://wp-hide.com/how-to-easily-hide-elementor-page-builder/' target='_blank'>How to white label Elementor</a> also <a href='https://wp-hide.com/hide-your-avada-theme-avada-builder-and-fusion-core/' target='_blank'>HowHide your Avada Theme, Avada Builder and Fusion core</a> this makes the plugins totally unrecognizable for anonymous users.
                                                                <p>&nbsp;</p>
                                                                <p>This feature integrates perfectly with any site environment, regardles of the used plugins and themes. Changing any fingerprint does not break the site layout or disable any existing functionality.</p>",    'wp-hide-security-enhancer'),
                                        
                                        'score_points'  =>  50,
                                        );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $found_issue        =   FALSE;
                    $found_traces       =   array();
                    
                    $fingerprints   =   array ( 
                                                'Common WordPress fingerprints' =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]wp-', '-wp[\s\'\"]' ),
                                                                                            'replacements'  =>  array(  'wp-'    )
                                                                                    ),
                                                'Astra'                             =>  array (
                                                                                            'search'        =>  array ('[\s\'\"]astra-', '-astra[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'astra'    )
                                                                                    ),
                                                'Avada'                             =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]avada-', '-avada[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'avada'    )
                                                                                    ),               
                                                'Divi'                              =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]divi-', '-divi[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'divi'    )
                                                                                    ),
                                                'Elementor'                         =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]elementor-', '-elementor[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'elementor'    )
                                                                                    ),
                                                'Fusion Builder'                    =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]fusion-', '-fusion[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'fusion'    )
                                                                                    ),
                                                'Flatsome'                          =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]flatsome-'),
                                                                                            'replacements'  =>  array(  'flatsome'    )
                                                                                    ),
                                                'Porto'                             =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]porto-', '-porto[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'porto'    )
                                                                                    ),
                                                'Themify'                           =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]themify-', '-themify[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'themify'    )
                                                                                    ),
                                                'Uncode'                           =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]uncode-', '-uncode[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'uncode'    )
                                                                                    ),   
                                                'Yoast SEO'                         =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]yoast-'),
                                                                                            'replacements'  =>  array(  'yoast'    )
                                                                                    ),
                                                'WoodMart'                          =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]woodmart-', '-woodmart[\s\'\"]'),
                                                                                            'replacements'  =>  array(  'woodmart'    )
                                                                                    ),
                                                'WooCommerce'                       =>  array (
                                                                                            'search'        =>  array ( '[\s\'\"]woocommerce-', '-woocommerce[\s\'\"]', '[\s\'\"]wc_'),
                                                                                            'replacements'  =>  array(  'woocommerce'    )
                                                                                    ),
                                                'WP Bakery'                       =>  array (
                                                                                            'search'        =>  array ( 'js-composer', '[\s\'\"]vc_', '[\s\'\"]wpb_'),
                                                                                            'replacements'  =>  array(  'js-composer', 'vc_'    )
                                                                                    ),
                                                );
                    
                    if ( $this->wph->security_scan->remote_html )
                        {
                            foreach ( $fingerprints as $code_name   =>  $fingerprints_group )
                                {
                                    foreach ( $fingerprints_group['search']   as  $fingerprints_item )
                                        {
                                            if ( preg_match( '/' . $fingerprints_item . '/i', $this->wph->security_scan->remote_html ) )
                                                {
                                                    $found_issue    =   TRUE;
                                                    $found_traces[ $code_name ] =   TRUE;
                                                }
                                        }
                                }
                        }
                        else
                        $found_issue    =   TRUE;

                    if ( $found_issue   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>Your site assets still contain traceable data within HTML / CSS / JavaScript. Those can be removed using the Replacements functionality.', 'wp-hide-security-enhancer' );
                            $_JSON_response['description']  .=   '<br /><br />';
                            
                            foreach ( $found_traces   as  $code_name    =>  $found_status )
                                {
                                    
                                    $_JSON_response['description']  .=  '<p class="important">';              
                                    $_JSON_response['description']  .=   '<b> <span class="dashicons dashicons-search"></span> ' . __( 'Found', 'wp-hide-security-enhancer' ) .' - ' . $code_name .'</b>. ' . __( 'Add replacements for <code>', 'wp-hide-security-enhancer' ) . implode ( "</code>, <code>", $fingerprints[$code_name]['replacements'] ) . '</code>';
                                    $_JSON_response['description']  .=  '</p>';
                                    
                                }
                            
                            if ( $this->wph->security_scan->remote_errors   !== FALSE )
                                $_JSON_response['description']  .=   "<br /><br /><span class='error'>" . __('Unable to complete this security task as an error occoured', 'wp-hide-security-enhancer' ) . ': <b>' .$this->wph->security_scan->remote_errors . '</b></span>';
                            
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary wph-pro" target="_blank" href="https://wp-hide.com/pricing/">PRO</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>There are no obvious fingerprints.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>