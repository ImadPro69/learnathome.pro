<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_x_content_type_options extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "X-Content-Type-Options";
                }
                                    
            function get_module_settings()
                {
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'x_content_type_options',
                                                                    'label'         =>  __('X-Content-Type-Options',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('X-Content-Type-Options',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The X-Content-Type-Options response HTTP header is a marker used by the server to indicate that the MIME types advertised in the Content-Type headers should be followed and not be changed. The header allows you to avoid MIME type sniffing by saying that the MIME types are deliberately configured.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("This header was introduced by Microsoft in IE 8 as a way for webmasters to block content sniffing that was happening and could transform non-executable MIME types into executable MIME types. Since then, other browsers have introduced it, even if their MIME sniffing algorithms were less aggressive.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("Starting with Firefox 72, top-level documents also avoid MIME sniffing (if Content-type is provided). This can cause HTML web pages to be downloaded instead of being rendered when they are served with a MIME type other than text/html. Make sure to set both headers correctly.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />"  . __("Options:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>nosniff</b> - "  . __("Blocks a request if the request destination is of type style and the MIME type is not text/css, or of type script and the MIME type is not a JavaScript MIME type.",    'wp-hide-security-enhancer')
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
                                            'value'             =>  'nosniff'
                                            );
                    return $options;
                } 
                
            function _init_x_content_type_options( $saved_field_data )
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
                                    <input type="radio" class="radio" value="nosniff" name="value" <?php if ( $module_settings['value'] == 'nosniff' ) { ?>checked="checked"<?php } ?>> <span>nosniff</span>
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
                
                
            function _callback_saved_x_content_type_options($saved_field_data)
                {
                    
                    if ( empty ( $saved_field_data ) ||  $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite                            =  '';
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    .=  "\n" . '        Header set X-Content-Type-Options "' . $saved_field_data['value'] .'"';
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