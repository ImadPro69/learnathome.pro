<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_new_xml_rpc_path extends WPH_module_component
        {
            
            function get_component_title()
                {
                    return "XML-RPC";
                }
                                                
            function get_module_settings()
                {
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'new_xml_rpc_path',
                                                                    'label'         =>  __('New XML-RPC Path',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('The default XML-RPC path is set to xmlrpc.php.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New XML-RPC Path',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("XML-RPC is a remote procedure call (RPC) protocol which uses XML to encode its calls and HTTP as a transport mechanism. This service allow other applications to talk to your WordPress site.",    'wp-hide-security-enhancer') . "<br />  <br />" .
                                                                                                                                            __("As default the path to XML-RPC file is:",    'wp-hide-security-enhancer') .
                                                                                                                                            "<code>https://-domain-name-/xmlrpc.php</code>
                                                                                                                                            <br /><br />" . __("Through this option it can be changed to anything else. This ensure the protocol will not be called by anyone who don't know the actual path.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/rewrite-xml-rpc/',
                                                                                                        'input_value_extension'     =>  'php'
                                                                                                        ),
                                                                    
                                                                    'value_description' =>  __('e.g. my-xml-rpc.php',    'wp-hide-security-enhancer'),
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name'), array($this->wph->functions, 'php_extension_required')),
                                                                    'processing_order'  =>  50
                                                                    );
                                                                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'block_xml_rpc',
                                                                    'label'         =>  __('Block default xmlrpc.php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block the default xmlrpc.php XML-RPC service.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block default xmlrpc.php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This blocks the default XML-RPC service. The functionality apply if <b>New XML-RPC Path</b> option is filled in.",    'wp-hide-security-enhancer') . "<br/><br />" .
                                                                                                                                        __("Keep in mind that somthird-party services, like Jetpack, rely on XML-RPC to connect to WordPress sites for features such as monitoring, statistics, and site management.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/rewrite-xml-rpc/'
                                                                                                        ),
                                                                    
                                                                    'advanced_option'   =>  array(
                                                                        
                                                                                                        'description'               =>  '<b>' . __('This is an advanced option !',    'wp-hide-security-enhancer') . '</b><br />' . __('This can break specific functionality. Some plugins like Jetpack use this API. Once active test it thoroughly.<br />If not working, set to <b>No</b> to revert.',    'wp-hide-security-enhancer')
                                                                                                
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    
                                                                    );
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'disable_xml_rpc_auth',
                                                                    'label'         =>  __('Disable XML-RPC methods requiring authentication',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Filter XML-RPC methods requiring authentication, such as for publishing purposes, are enabled.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable XML-RPC methods requiring authentication',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("As default, certain methods require authentication for the protocol to be used along with a remote application (e.g. wp.restoreRevision, wp.getRevisions, wp.getPostTypes, wp.getPostType, wp.getPostFormats, wp.getMediaLibrary, wp.getMediaItem etc).",    'wp-hide-security-enhancer') .
                                                                                                                                        "<br />" . __("Activating the option, methods requiring authentication will be blocked through a call.",    'wp-hide-security-enhancer') .
                                                                                                                                        "<br />" . __("Brute force attacks often target the XML-RPC service. Therefore, it's advisable to enable this option unless you are using the service for specific purposes, such as with a remote mobile app.",    'wp-hide-security-enhancer') .
                                                                                                                                        "<br />" .__("Keep in mind that somthird-party services, like Jetpack, rely on XML-RPC to connect to WordPress sites for features such as monitoring, statistics, and site management.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/rewrite-xml-rpc/'
                                                                                                        ),
                                                                                                                                
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    
                                                                    );
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'disable_xml_rpc_service',
                                                                    'label'         =>  __('Disable XML-RPC service',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('When the XML-RPC service is deactivated, any calls to it result in the server returning a default 404 Not Found error page.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable XML-RPC service',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Before disabling the XML-RPC, ensure the service is not used for any of the followings:",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><ul><li>" . __("Mobile Apps: XML-RPC allows users to manage their WordPress sites via mobile apps. This feature makes it convenient for bloggers and administrators to create, edit, or delete posts from smartphones and tablets.",    'wp-hide-security-enhancer') .
                                                                                                                                            "</li><li>" . __("Third-Party Services: Many third-party services, like Jetpack, rely on XML-RPC to connect to WordPress sites for features such as monitoring, statistics, and site management.",    'wp-hide-security-enhancer') .
                                                                                                                                            "</li><li>" . __("Content Syndication: XML-RPC can be used to syndicate content between different WordPress sites, sharing posts and updates.",    'wp-hide-security-enhancer') .
                                                                                                                                            "</li></ul>" . __("The Benefits of Disabling XML-RPC:",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><ul><li>" . __("Improved Security: Disabling XML-RPC eliminates a potential entry point for attackers, protecting your site from brute force attacks and other malicious activities.",    'wp-hide-security-enhancer') .
                                                                                                                                            "</li><li>" . __("Reduced Server Load: By preventing DDoS attacks through XML-RPC, you can reduce the load on your server and improve site performance and availability.",    'wp-hide-security-enhancer') .
                                                                                                                                            "</li><li>" . __("Better Control: Disabling XML-RPC ensures that your site remains under your control, minimizing the risk of unauthorized access or content manipulation.",    'wp-hide-security-enhancer') .
                                                                                                                                            "</li></ul>" ,
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-xml-rpc/'
                                                                                                        ),
                                                                    'advanced_option'   =>  array(
                                                                        
                                                                                                        'description'               =>  '<b>' . __('This is an advanced option !',    'wp-hide-security-enhancer') . '</b><br />' . __('This can break specific functionality. Some plugins like Jetpack use this API. Once active test it thoroughly.<br />If not working, set to <b>No</b> to revert.',    'wp-hide-security-enhancer')
                                                                                                
                                                                                                ),                                    
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    
                                                                    );
                    
                                                                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'remove_xml_rpc_tag',
                                                                    'label'         =>  __('Remove pingback',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove pingback link tag from theme.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove pingback',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("A pingback is one of four types of link-back methods for Web authors to request notification when somebody links to one of their documents. This enables authors to keep track of who is linking to, or referring to their articles Using this option this functionality can be removed.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/rewrite-xml-rpc/'
                                                                                                        ),
                                                                                                                     
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  58
                                                                    
                                                                    );
                                                                    
                    return $this->module_settings;   
                }
                
                
                
            function _init_new_xml_rpc_path($saved_field_data)
                {
                    if(empty($saved_field_data))
                        return FALSE;
                    
                    //add default plugin path replacement
                    $old_url    =   trailingslashit(    site_url()  )   . 'xmlrpc.php';
                    $new_url    =   trailingslashit(    home_url()  )   . $saved_field_data;
                    $this->wph->functions->add_replacement( $old_url ,  $new_url );
                }
                
            function _callback_saved_new_xml_rpc_path($saved_field_data)
                {
                    $processing_response    =   array();
                    
                    //check if the field is noe empty
                    if(empty($saved_field_data))
                        return  $processing_response;
                        
                    //check if the service diable option isn't active
                    $disable_xml_rpc_service       =   $this->wph->functions->get_module_item_setting('disable_xml_rpc_service');
                    if ( $disable_xml_rpc_service == 'yes' )
                        return; 
                    
                    $file_path   =   $this->wph->functions->get_url_path( trailingslashit(site_url()) . 'xmlrpc.php'    );
                    
                    $rewrite_to     =   $this->wph->functions->get_rewrite_to_base( $file_path, TRUE, FALSE );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        $processing_response['rewrite'] = "\nRewriteRule ^"    .   $saved_field_data  .   ' '. $rewrite_to .' [L,QSA]';
                    
                    if($this->wph->server_web_config   === TRUE)
                        $processing_response['rewrite'] = '
                            <rule name="wph-new_xml_rpc_path" stopProcessing="true">
                                <match url="^'.  $saved_field_data   .'"  />
                                <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="true" />
                            </rule>
                                                            ';
                                
                    return  $processing_response;   
                }
                
   
            function _callback_saved_block_xml_rpc($saved_field_data)
                {
                    $processing_response    =   array();
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
                    //check if the service diable option isn't active
                    $disable_xml_rpc_service       =   $this->wph->functions->get_module_item_setting('disable_xml_rpc_service');
                    if ( $disable_xml_rpc_service == 'yes' )
                        return;
                    
                    $rewrite_to     =   $this->wph->functions->get_rewrite_to_base( 'index.php', TRUE, FALSE, 'site_path' );
                    
                    $text   =   '';
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                                        
                            $text   =   "RewriteCond %{ENV:REDIRECT_STATUS} ^$\n";
                            $text   .=  "RewriteCond %{HTTP_USER_AGENT}  !^WordPress\/[0-9\.\ ]+CFNetwork [NC]\n";
                            $text   .=  "RewriteRule ^xmlrpc.php ".  $rewrite_to ."?wph-throw-404 [L]";
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        $text   = '
                                    <rule name="wph-block_xml_rpc" stopProcessing="true">
                                        <match url="^xmlrpc.php"  />
                                        <action type="Rewrite" url="'.  $rewrite_to .'?wph-throw-404" />  
                                    </rule>
                                                        ';
                    
                               
                    $processing_response['rewrite'] = $text;            
                                
                    return  $processing_response;     
                    
                    
                }
                
            function _init_disable_xml_rpc_auth($saved_field_data)
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    
                    add_filter( 'xmlrpc_enabled', '__return_false' ); 
                    
                }
            
            
            
            function _callback_saved_disable_xml_rpc_service( $saved_field_data )
                {
                    
                    if ( empty ( $saved_field_data ) ||  $saved_field_data   ==  'no' )
                        return FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                        
                    $rewrite                            =  '';
                    
                    $rewrite_base       =   $this->wph->functions->get_rewrite_base( 'xmlrpc.php', FALSE, FALSE, 'wp_path' );
                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                              
                            $rewrite   .=      "\nRewriteRule ^" . $rewrite_base ." - [R=404]";
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite     =  "\n" . '<rule name="wph-block_default_disable_xml_rpc_service" stopProcessing="true">';
                            $rewrite    .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                            $rewrite    .=  "\n" .    '    <action type="Redirect" url="-"  appendQueryString="false" redirectType="404" />';
                            $rewrite    .=  "\n" . '</rule>';   
                        }
                        
                                          
                    $processing_response['rewrite'] = $rewrite;    
                                                    
                    return  $processing_response;
                    
                }
            
                
            function _init_remove_xml_rpc_tag($saved_field_data)
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    
                    add_filter('wp-hide/ob_start_callback', array($this, 'remove_xml_rpc_tag'));
                    
                }
                
                
            function remove_xml_rpc_tag( $buffer )
                {
                    
                    $result   = preg_match_all('/(<link([^>]+)rel=("|\')pingback("|\')([^>]+)?\/?>)/im', $buffer, $founds);
    
                    if(!isset($founds[0])   ||  count($founds[0])    <   1)
                        return $buffer;
    
                    if(count($founds[0]) > 0)
                        {
                            foreach ($founds[0]  as  $found)
                                {
                                    if(empty($found))
                                        continue;

                                    $buffer =   str_replace($found, "", $buffer);
                                    
                                }
                            
                            
                        }
                    
                    return $buffer;
     
                }


        }
?>