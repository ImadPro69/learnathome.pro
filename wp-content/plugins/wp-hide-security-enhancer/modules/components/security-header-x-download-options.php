<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_x_download_options extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "X-Download-Options";
                }
                                    
            function get_module_settings()
                {
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'x_download_options',
                                                                    'label'         =>  __('X-Download-Options',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('X-Download-Options',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The X-Download-Options is specific to IE 8, and is related to how IE 8 handles downloaded HTML files. Turns out if you download an HTML file from a web page and chooses to \"Open\" it in IE, it will execute in the context of the web site. That means that any scripts in that file will also execute with the origin of the web site.",    'wp-hide-security-enhancer') 
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
                                            'value'             =>  'noopen'
                                            );
                    return $options;
                } 
                
                
            function _init_x_download_options( $saved_field_data )
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
                                    <input type="radio" class="radio" value="noopen" name="value" <?php if ( $module_settings['value'] == 'noopen' ) { ?>checked="checked"<?php } ?>> <span>noopen</span>
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
                
                
            function _callback_saved_x_download_options($saved_field_data)
                {
                    
                    if ( empty ( $saved_field_data ) ||  $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite                            =  '';
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    .=  "\n" . '        Header set X-Download-Options "' . $saved_field_data['value'] .'"';
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