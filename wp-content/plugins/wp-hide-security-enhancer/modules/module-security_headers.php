<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_security_headers extends WPH_module
        {
      
            function load_components()
                {
                    
                    //add components
                    include( WPH_PATH . "modules/components/security-check_headers.php");
                    $this->components[]  =   new WPH_module_general_security_check_headers();
                    
                    include( WPH_PATH . "modules/components/security-header-cross-origin-embedder-policy.php");
                    $this->components[]  =   new WPH_module_general_security_header_cross_origin_embedder_policy();
                    
                    include( WPH_PATH . "modules/components/security-header-cross-origin-opener-policy.php");
                    $this->components[]  =   new WPH_module_general_security_header_cross_origin_opener_policy();
                    
                    include( WPH_PATH . "modules/components/security-header-cross-origin-resource-policy.php");
                    $this->components[]  =   new WPH_module_general_security_header_cross_origin_resource_policy();
                    
                    include( WPH_PATH . "modules/components/security-header-referrer-policy.php");
                    $this->components[]  =   new WPH_module_general_security_header_referrer_policy();
                    
                    include( WPH_PATH . "modules/components/security-header-x-content-type-options.php");
                    $this->components[]  =   new WPH_module_general_security_header_x_content_type_options();
                    
                    include( WPH_PATH . "modules/components/security-header-x-download-options.php");
                    $this->components[]  =   new WPH_module_general_security_header_x_download_options();
                    
                    include( WPH_PATH . "modules/components/security-header-x-frame-options.php");
                    $this->components[]  =   new WPH_module_general_security_header_x_frame_options();
                    
                    include( WPH_PATH . "modules/components/security-header-x-permitted-cross-domain-policies.php");
                    $this->components[]  =   new WPH_module_general_security_header_x_permitted_cross_domain_policies();
                    
                    include( WPH_PATH . "modules/components/security-header-x-xss-protection.php");
                    $this->components[]  =   new WPH_module_general_security_header_x_xss_protection(); 
                            
                    //action available for mu-plugins
                    do_action('wp-hide/module_load_components', $this);
                    
                }
            
            function use_tabs()
                {
                    
                    return TRUE;
                }
            
            function get_module_id()
                {
                    
                    return 'security';
                }
                
            function get_module_slug()
                {
                    
                    return 'wp-hide-security-headers';   
                }
    
            function get_interface_menu_data()
                {
                    $interface_data                     =   array();
                    
                    $interface_data['menu_title']       =   __('<span class="wph-info">Security&rarr;</span> Headers',    'wp-hide-security-enhancer');
                    $interface_data['menu_slug']        =   self::get_module_slug();
                    $interface_data['menu_position']    =   70;
                    
                    return $interface_data;
                }
    
            function get_interface_data()
                {
      
                    $interface_data                     =   array();
                    
                    $interface_data['title']              =   __('WP Hide & Security Enhancer - Security Headers',    'wp-hide-security-enhancer');
                    $interface_data['description']        =   '';
                    $interface_data['handle_title']       =   '';
                    
                    return $interface_data;
                    
                }
            
            
            
                
                
        }
    
 
?>