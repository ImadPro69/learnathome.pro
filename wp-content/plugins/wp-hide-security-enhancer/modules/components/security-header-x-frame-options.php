<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_x_frame_options extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "X-Frame-Options (XFO)";
                }
                                    
            function get_module_settings()
                {
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'x_frame_options',
                                                                    'label'         =>  __('X-Frame-Options (XFO)',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('X-Frame-Options',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The X-Frame-Options HTTP response header can be used to indicate whether or not a browser should be allowed to render a page in a &#60;frame&#62;, &#60;iframe&#62;, &#60;embed&#62; or &#60;object&#62;. Sites can use this to avoid click-jacking attacks, by ensuring that their content is not embedded into other sites.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("The added security is provided only if the user accessing the document is using a browser that supports X-Frame-Options.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />"  . __("Options:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>DENY</b> - "  . __("The page cannot be displayed in a frame, regardless of the site attempting to do so.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>SAMEORIGIN</b> - "  . __("The page can only be displayed in a frame on the same origin as the page itself. The spec leaves it up to browser vendors to decide whether this option applies to the top level, the parent, or the whole chain, although it is argued that the option is not very useful unless all ancestors are also in the same origin.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />&nbsp;<br /><br />" . __("If you specify DENY, not only will the browser attempt to load the page in a frame fail when loaded from other sites, attempts to do so will fail when loaded from the same site. On the other hand, if you specify SAMEORIGIN, you can still use the page in a frame as long as the site including it in a frame is the same as the one serving the page.",    'wp-hide-security-enhancer')
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
                                            'value'             =>  'DENY'
                                            );
                    return $options;
                }
                
                
            function _init_x_frame_options( $saved_field_data )
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
                                    <input type="radio" class="radio" value="DENY" name="value" <?php if ( $module_settings['value'] == 'DENY' ) { ?>checked="checked"<?php } ?>> <span>DENY</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="SAMEORIGIN" name="value" <?php if ( $module_settings['value'] == 'SAMEORIGIN' ) { ?>checked="checked"<?php } ?>> <span>SAMEORIGIN</span>
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
                
                    
            function _callback_saved_x_frame_options( $saved_field_data )
                {
                    
                    if ( empty ( $saved_field_data ) ||  $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite                            =  '';
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    .=  "\n" . '        Header set X-Frame-Options "' . $saved_field_data['value'] .'"';
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