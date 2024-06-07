<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_x_permitted_cross_domain_policies extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "X-Permitted-Cross-Domain-Policies";
                }
                                    
            function get_module_settings()
                {
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'x_permitted_cross_domain_policies',
                                                                    'label'         =>  __('X-Permitted-Cross-Domain-Policies',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('X-Permitted-Cross-Domain-Policies',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("A cross-domain policy file is an XML document that grants a web client, such as Adobe Flash Player or Adobe Acrobat (though not necessarily limited to these), permission to handle data across domains.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("When clients request content hosted on a particular source domain and that content makes requests directed towards a domain other than its own, the remote domain needs to host a cross-domain policy file that grants access to the source domain, allowing the client to continue the transaction.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("Normally a meta-policy is declared in the master policy file, but for those who can’t write to the root directory, they can also declare a meta-policy using the X-Permitted-Cross-Domain-Policies HTTP response header.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />"  . __("Options:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>none</b> - "  . __("No policy files are allowed anywhere on the target server, including this master policy file.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>master-only</b> - "  . __("Only this master policy file is allowed.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>by-content-type</b> - "  . __("[HTTP/HTTPS only] Only policy files served with Content-Type: text/x-cross-domain-policy are allowed.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>by-ftp-filename</b> - "  . __("[FTP only] Only policy files whose file names are crossdomain.xml (i.e. URLs ending in /crossdomain.xml) are allowed.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>all</b> - "  . __("All policy files on this target domain are allowed.",    'wp-hide-security-enhancer')
                                                                                                ),
                                                                                                   
                                                                    'input_type'    =>  'custom',
                                                                                                 
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                    
                                                                    ); 
                  
                                                                    
                    return $this->module_settings; 
                 
                    
                                                                    
                    return $this->module_settings;   
                }
            
            function _get_default_options()
                {
                    
                    $options    =   array ( 
                                            'enabled'           =>  'no',
                                            'value'             =>  'none'
                                            );
                    return $options;
                }
                
                
            function _init_x_permitted_cross_domain_policies( $saved_field_data )
                {
                    
                }
                
            
            function _module_option_html( $module_settings )
                {
                    
                    $values             =   $this->wph->functions->get_module_item_setting( $module_settings['id'] );
                    $module_settings    =   shortcode_atts ( $this->_get_default_options(), (array)$values )        
                    
                    ?>
                        <div class="row xspacer header">
                            <p><?php _e('Enable Header',    'wp-hide-security-enhancer') ?></p>
                            <fieldset>
                                <label>
                                    <input type="radio" class="setting-value default-value radio" value="no" name="enabled" <?php if ( $module_settings['enabled'] == 'no' ) { ?>checked="checked"<?php } ?>> <span>No</span>
                                </label>
                                <label>
                                    <input type="radio" class="setting-value radio" value="yes" name="enabled" <?php if ( $module_settings['enabled'] == 'yes' ) { ?>checked="checked"<?php } ?>> <span>Yes</span>
                                </label>                                                                
                            </fieldset>
                        </div>
                        
                        <p><?php _e('Header Options',    'wp-hide-security-enhancer') ?></p>
                        <div class="row spacer">
                            <fieldset>
                                <label>
                                    <input type="radio" class="radio" value="none" name="value" <?php if ( $module_settings['value'] == 'none' ) { ?>checked="checked"<?php } ?>> <span>none</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="master-only" name="value" <?php if ( $module_settings['value'] == 'master-only' ) { ?>checked="checked"<?php } ?>> <span>master-only</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="by-content-type" name="value" <?php if ( $module_settings['value'] == 'by-content-type' ) { ?>checked="checked"<?php } ?>> <span>by-content-type</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="by-ftp-filename" name="value" <?php if ( $module_settings['value'] == 'by-ftp-filename' ) { ?>checked="checked"<?php } ?>> <span>by-ftp-filename</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="all" name="value" <?php if ( $module_settings['value'] == 'all' ) { ?>checked="checked"<?php } ?>> <span>all</span>
                                </label>
                            </fieldset>
                        </div>
 
                        
                    
                    <?php
                }
                
                
            function _module_option_processing( $field_name )
                {
                    
                    $results            =   array();
                    
                    $module_settings =   shortcode_atts ( $this->_get_default_options(), array() );
                    foreach ( $module_settings   as  $setting_name  =>  $setting_value )
                        {
                            if ( ! isset ( $_POST[ $setting_name ] ) )
                                continue;
                                
                            $value  =   preg_replace( '/[^a-zA-Z0-9-_]/m' , '', $_POST[ $setting_name ] );
                            if ( empty ( $value ) )
                                continue;
                                
                            $module_settings[ $setting_name ]   =   $value;
                        }
                                        
                    $results['value']   =   $module_settings;
                       
                    return $results;
                    
                }
                
                
            function _callback_saved_x_permitted_cross_domain_policies($saved_field_data)
                {
                    
                    if ( empty ( $saved_field_data ) ||  $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite                            =  '';
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    .=  "\n" . '        Header set X-Permitted-Cross-Domain-Policies "' . $saved_field_data['value'] .'"';
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {  
                            
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                    $processing_response['type']    =   'header';
                                      
                    return  $processing_response;
                    
                } 
            

        }
?>