<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_x_xss_protection extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "X-XSS-Protection";
                }
                                    
            function get_module_settings()
                {
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'x_xss_protection',
                                                                    'label'         =>  __('X-XSS-Protection',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('X-XSS-Protection',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The HTTP X-XSS-Protection response header is a feature of Internet Explorer, Chrome and Safari that stops pages from loading when they detect reflected cross-site scripting (XSS) attacks. These protections are largely unnecessary in modern browsers when sites implement a strong Content-Security-Policy that disables the use of inline JavaScript ('unsafe-inline').",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />"  . __("Options:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>0</b> - "  . __("Disables XSS filtering.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>1</b> - "  . __("Enables XSS filtering (usually default in browsers). If a cross-site scripting attack is detected, the browser will sanitize the page (remove the unsafe parts).",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>1; mode=block</b> - "  . __("Enables XSS filtering. Rather than sanitizing the page, the browser will prevent rendering of the page if an attack is detected.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>1; report=</b> - "  . __("Chromium only. Enables XSS filtering. If a cross-site scripting attack is detected, the browser will sanitize the page and report the violation. This uses the functionality of the CSP report-uri directive to send a report.",    'wp-hide-security-enhancer')
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
                                            'value'             =>  '0',
                                            'report_to'         =>  ''
                                            );
                    return $options;
                }
                
                
            function _init_x_xss_protection( $saved_field_data )
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
                        <div class="row spacer option-item">
                            <fieldset>
                                <label>
                                    <input type="radio" class="radio" value="0" name="value" <?php if ( $module_settings['value'] == '0' ) { ?>checked="checked"<?php } ?>> <span>0</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="1" name="value" <?php if ( $module_settings['value'] == '1' ) { ?>checked="checked"<?php } ?>> <span>1</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="1; mode=block" name="value" <?php if ( $module_settings['value'] == '1; mode=block' ) { ?>checked="checked"<?php } ?>> <span>1; mode=block</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="1; report=" name="value" <?php if ( $module_settings['value'] == '1; report=' ) { ?>checked="checked"<?php } ?>> <span>1; report=</span>
                                </label>
                                <label>
                                    <input style="<?php if ( $module_settings['value'] != '1; report=' ) { echo 'display: none';} ?>" type="text" placeholder="Report URI" value="<?php echo $module_settings['report_to']; ?>" name="report_to">
                                </label>
                            </fieldset>
                        </div>
 
                        <script type='text/javascript'>
                                
                            jQuery( '.option-item input[name="value"]' ).on('change', function() {
                                  var val   =   jQuery( this ).val();
                                  if ( val == '1; report=' )
                                    jQuery(this).closest('.option-item').find('input[name="report_to"]').show('fast');
                                    else
                                    jQuery(this).closest('.option-item').find('input[name="report_to"]').hide('fast');
                                });
                        </script>
                    
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
                                
                            $value  =   preg_replace( '/[^a-zA-Z0-9-_;:.=\/ ]/m' , '', $_POST[ $setting_name ] );
                            if ( empty ( $value ) )
                                continue;
                                
                            $module_settings[ $setting_name ]   =   $value;
                        }
                                        
                    $results['value']   =   $module_settings;
                       
                    return $results;
                    
                }
                
                
            function _callback_saved_x_xss_protection( $saved_field_data )
                {
                    
                    if ( empty ( $saved_field_data ) ||  $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite                            =  '';
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    .=  "\n" . '        Header set X-XSS-Protection "' . $saved_field_data['value'];
                            if ( $saved_field_data['value'] ==  '1; report=' )
                                $rewrite    .=  ';' .   $saved_field_data['report_to'];
                            $rewrite    .= '"';    
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