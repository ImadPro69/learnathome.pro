<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_referrer_policy extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "Referrer-Policy";
                }
                                    
            function get_module_settings()
                {
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'referrer_policy',
                                                                    'label'         =>  __('Referrer-Policy',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Referrer-Policy',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The Referrer-Policy HTTP header determines the amount of referral information (sent through the Referer header) that should accompany requests. In addition to the HTTP header, this policy can also be set in HTML",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />"  . __("Options:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>no-referrer</b> - "  . __("The Referer header will be omitted: sent requests do not include any referrer information.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>no-referrer-when-downgrade</b> - "  . __("Send the origin, path, and querystring in Referer when the protocol security level stays the same or improves (HTTP→HTTP, HTTP→HTTPS, HTTPS→HTTPS). Don't send the Referer header for requests to less secure destinations (HTTPS→HTTP, HTTPS→file).",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>origin</b> - "  . __("Send only the origin in the Referer header. For example, a document at https://example.com/page.html will send the referrer https://example.com/.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>origin-when-cross-origin</b> - "  . __("When performing a same-origin request to the same protocol level (HTTP→HTTP, HTTPS→HTTPS), send the origin, path, and query string. Send only the origin for cross origin requests and requests to less secure destinations (HTTPS→HTTP).",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>same-origin</b> - "  . __("Send the origin, path, and query string for same-origin requests. Don't send the Referer header for cross-origin requests.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>strict-origin</b> - "  . __("Send only the origin when the protocol security level stays the same (HTTPS→HTTPS). Don't send the Referer header to less secure destinations (HTTPS→HTTP).",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>strict-origin-when-cross-origin (default)</b> - "  . __("Send the origin, path, and querystring when performing a same-origin request. For cross-origin requests send the origin (only) when the protocol security level stays same (HTTPS→HTTPS). Don't send the Referer header to less secure destinations (HTTPS→HTTP).",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>unsafe-url</b> - "  . __("Send the origin, path, and query string when performing any request, regardless of security.",    'wp-hide-security-enhancer') ,
                                                                                                'option_documentation_url'  =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy'
                                                                                                ),
                                        
                                                                    'input_type'    =>  'custom',
                                                                                                 
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                    
                                                                    ); 
                  
                                                                    
                    return $this->module_settings; 
    
                }
                
            function _get_default_options()
                {
                    
                    $options    =   array ( 
                                            'enabled'           =>  'no',
                                            'value'             =>  'strict-origin-when-cross-origin'
                                            );
                    return $options;
                }    
            
            
            function _init_referrer_policy( $saved_field_data )
                {
                    
                }
            
            function _module_option_html( $module_settings )
                {
                    
                    $values =   $this->wph->functions->get_module_item_setting( $module_settings['id'] );
                    $module_settings =   shortcode_atts ( $this->_get_default_options(), (array)$values )        
                    
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
                        
                        <p><b><?php _e('Header Options',    'wp-hide-security-enhancer') ?></b></p>
                        <div class="row spacer">
                            <fieldset>
                                <?php
                                
                                    $header_options =   array (
                                                                'no-referrer'                   =>  'no-referrer',
                                                                'no-referrer-when-downgrade'    =>  'no-referrer-when-downgrade',
                                                                'origin'                        =>  'origin',
                                                                'origin-when-cross-origin'      =>  'origin-when-cross-origin',
                                                                'same-origin'                   =>  'same-origin',
                                                                'strict-origin'                 =>  'strict-origin',
                                                                'strict-origin-when-cross-origin'    =>  'strict-origin-when-cross-origin',
                                                                'unsafe-url'                    =>  'unsafe-url',
                                                                );
                                    
                                    foreach ( $header_options   as $option_value =>  $option_title )
                                        {
                                            ?>
                                            <label>
                                                <input type="radio" class="radio" value="<?php echo $option_value ?>" name="value" <?php if ( $module_settings['value'] == $option_value ) { ?>checked="checked"<?php } ?>> <span><?php echo $option_title ?></span>
                                            </label>    
                                            <?php
                                        }
                                
                                ?>                                                               
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
                
                
            function _callback_saved_referrer_policy($saved_field_data)
                {
                    
                    if ( empty ( $saved_field_data ) ||  $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite                            =  '';
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    .=  "\n" . '        Header set Referrer-Policy "' . $saved_field_data['value'] .'"';
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