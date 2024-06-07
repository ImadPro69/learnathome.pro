<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_cross_origin_opener_policy extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "Cross-Origin-Opener-Policy (COOP)";
                }
                                    
            function get_module_settings()
                {
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'cross_origin_opener_policy',
                                                                    'label'         =>  __('Cross-Origin-Opener-Policy (COOP)',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Cross-Origin-Opener-Policy',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The HTTP Cross-Origin-Opener-Policy (COOP) response header allows you to ensure a top-level document does not share a browsing context group with cross-origin documents.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("COOP will process-isolate your document and potential attackers can't access your global object if they were to open it in a popup, preventing a set of cross-origin attacks dubbed XS-Leaks.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("If a cross-origin document with COOP is opened in a new window, the opening document will not have a reference to it, and the window.opener property of the new window will be null. This allows you to have more control over references to a window than rel=noopener, which only affects outgoing navigations.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />"  . __("Options:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>unsafe-none</b> - "  . __("This is the default value. Allows the document to be added to its opener's browsing context group unless the opener itself has a COOP of same-origin or same-origin-allow-popups.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>same-origin-allow-popups</b> - "  . __("Retains references to newly opened windows or tabs that either don't set COOP or that opt out of isolation by setting a COOP of unsafe-none.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>same-origin</b> - "  . __("Isolates the browsing context exclusively to same-origin documents. Cross-origin documents are not loaded in the same browsing context.",    'wp-hide-security-enhancer'),
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
                                            'value'             =>  'unsafe-none'
                                            );
                    return $options;
                }
            
            
            function _init_cross_origin_embedder_policy( $saved_field_data )
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
                                    <input type="radio" class="radio" value="unsafe-none" name="value" <?php if ( $module_settings['value'] == 'unsafe-none' ) { ?>checked="checked"<?php } ?>> <span>unsafe-none</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="same-origin-allow-popups" name="value" <?php if ( $module_settings['value'] == 'same-origin-allow-popups' ) { ?>checked="checked"<?php } ?>> <span>same-origin-allow-popups</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="same-origin" name="value" <?php if ( $module_settings['value'] == 'same-origin' ) { ?>checked="checked"<?php } ?>> <span>same-origin</span>
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
                
                
            function _callback_saved_cross_origin_opener_policy($saved_field_data)
                {
                    
                    if ( empty ( $saved_field_data ) ||  $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite                            =  '';
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    .=  "\n" . '        Header set Cross-Origin-Opener-Policy "' . $saved_field_data['value'] .'"';
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