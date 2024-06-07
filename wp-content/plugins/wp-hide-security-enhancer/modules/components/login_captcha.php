<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_login_captcha extends WPH_module_component
        {            
            var $captcha_type;
            
            function __construct()
                {
                    parent::__construct();
                    
                    include_once ( WPH_PATH . 'modules/components/login_captcha-google-v2.php');
                    $this->captcha_type['google_v2']   =   new WPH_module_login_captcha_google_v2();
                    
                    include_once ( WPH_PATH . 'modules/components/login_captcha-google-v3.php');
                    $this->captcha_type['google_v3']   =   new WPH_module_login_captcha_google_v3();

                }
            
            function get_component_title()
                {
                    return "Captcha";
                }
                
            function get_module_description()
                {
                    $html   =   '';
                    
                    $values =   (array)$this->wph->functions->get_module_item_setting( 'captcha_type' );
                    if ( ! is_array ( $values ) ||  ! isset ( $values['captcha_type'] ) )   
                        {
                            $values =   array();
                            $values['captcha_type'] =    'disabled';
                        }
                    
                    $captcha_options    =   $this->_get_captcha_options();
                        
                    foreach ( $captcha_options as   $captcha_type   =>  $group )
                        {
                            if ( $values['captcha_type']    ==  $captcha_type )
                                $html   =   $this->captcha_type[ $captcha_type ]->get_module_description( $values );     
                        }
      
      
                    $html   =   '<div class="postbox wph-postbox">
    <div class="wph_input widefat full_width option-captcha_type">
        <div class="row cell label ">
            <label for="">Captcha Integration Status</label>
        </div>

        <div class="row cell data entry"> 
            <div class="option_help">
                <div class="text">
                    ' . $html . '
                </div>
            </div>
        </div>
    </div>
</div>';
                    
      
                    return $html;                    
                }
                                                    
            function get_module_settings()
                {
                    
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'captcha_type',
                                                                    'label'         =>  __('Captcha Type',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  array(
                                                                                                __('Select the required CAPTCHA type to protect the Login, Registration page, Password Forget etc.',  'wp-hide-security-enhancer')
                                                                                                ),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Captcha Type',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __('CAPTCHA, short for "Completely Automated Public Turing test to tell Computers and Humans Apart," is a critical cybersecurity tool. It distinguishes human users from automated bots by presenting challenges like distorted characters or image recognition tasks. CAPTCHA safeguards websites, login pages, and online services from unwanted intrusion, spam, and fraud. Its evolution includes advanced variants and innovations like reCAPTCHA, continually enhancing digital security.',  'wp-hide-security-enhancer') . "<br />" .
                                                                                                                                        __('While it\'s a robust defense, adversaries are adapting, making CAPTCHA\'s ongoing development crucial in the fight against online threats.',  'wp-hide-security-enhancer') ,
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/general-emulate-cms/'
                                                                                                        ),
                                                                    
                                                                    'interface_help_split'  =>  FALSE,
                                                                    
                                                                    'input_type'    =>  'custom',
                                                                    
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                    
                                                                    );
                                                                    
                    return $this->module_settings;   
                }
                
                
                
            function _init_captcha_type ( $saved_field_data )
                {
                    if ( ! is_array ( $saved_field_data )   ||  $saved_field_data['captcha_type']  ==  'disabled' )
                        return;
                    
                    $captcha_options    =   $this->_get_captcha_options();
                        
                    foreach ( $captcha_options as   $captcha_type   =>  $group )
                        {
                            $this->captcha_type[ $captcha_type ]->init_captcha( $saved_field_data );     
                        }               
                }
            

            function _get_default_options()
                {
                    
                    $options    =   array ( 
                                            'captcha_type'           =>  'disabled',
                                            );
                    return $options;
                }
                
            function _get_captcha_options()
                {
                    
                    $captcha_options    =   array ( );
                    
                    $captcha_options    =   array_merge( $captcha_options, $this->captcha_type['google_v2']->get_captcha_options() );
                    $captcha_options    =   array_merge( $captcha_options, $this->captcha_type['google_v3']->get_captcha_options() );
                    
                    return $captcha_options;
                }  
                
            function _module_option_html( $module_settings )
                {
                            
                    $values =   $this->wph->functions->get_module_item_setting( $module_settings['id'] );
                    if ( ! is_array ( $values ) ||  ! isset ( $values['captcha_type'] ) )   
                        {
                            $values =   array();
                            $values['captcha_type'] =    'disabled';
                        }
                    
                    ?>
                        <div class="row spacer">
                            <fieldset>
                                <?php
                                
                                    $options    =   array ( 
                                            'disabled'              =>  __( 'Disabled',              'wp-hide-security-enhancer' ),
                                            'google_v2'             =>  __( 'Google Captcha V2',     'wp-hide-security-enhancer' ),
                                            'google_v3'             =>  __( 'Google Captcha V3',     'wp-hide-security-enhancer' ),
                                            'cloudflare_turnstile'  =>  __( 'CloudFlare Turnstile <span class="wph-pro">PRO</span>',  'wp-hide-security-enhancer' )
                                            );
                                    
                                    foreach ( $options   as $option_value =>  $option_title )
                                        {
                                            ?>
                                            <label>
                                                <input type="radio" <?php  if ( $option_value == 'cloudflare_turnstile' ) { echo 'disabled="disabled"'; }  ?> class="radio input-captcha-type" value="<?php echo $option_value ?>" name="captcha_type" <?php if ( $values['captcha_type'] == $option_value ) { ?>checked="checked"<?php } ?>> <span><?php echo $option_title ?></span>
                                            </label>    
                                            <?php
                                        }
                                
                                ?>                                                               
                            </fieldset>
                        </div>
                        
                        <script type="text/javascript">
                            jQuery('input.input-captcha-type').on('change', function() {
                                
                                if ( jQuery(this).val() ==  'disabled' )
                                    {
                                        jQuery('div.captcha-options').slideUp();
                                        jQuery('div.captcha-integration').slideUp();
                                    }
                                if ( jQuery(this).val() ==  'google_v2' )
                                    {
                                        jQuery('div.captcha-options').slideUp();
                                        jQuery('div.captcha-options.google_v2').slideDown();
                                        jQuery('div.captcha-integration').not('.google_v2').slideUp();
                                        jQuery('div.captcha-integration.google_v2').slideDown();
                                    }
                                if ( jQuery(this).val() ==  'google_v3' )
                                    {
                                        jQuery('div.captcha-options').slideUp();
                                        jQuery('div.captcha-options.google_v3').slideDown();
                                        jQuery('div.captcha-integration').not('.google_v3').slideUp();
                                        jQuery('div.captcha-integration.google_v3').slideDown();
                                    }
                                if ( jQuery(this).val() ==  'cloudflare_turnstile' )
                                    {
                                        jQuery('div.captcha-options').slideUp();
                                        jQuery('div.captcha-options.cloudflare_turnstile').slideDown();
                                        jQuery('div.captcha-integration').not('.cloudflare_turnstile').slideUp();
                                        jQuery('div.captcha-integration.cloudflare_turnstile').slideDown();
                                    }
                            })
                        </script>
 
                        <?php
                        
                            $captcha_options    =   $this->_get_captcha_options();
                        
                            foreach ( $captcha_options as   $captcha_type   =>  $group )
                                {
                                    ?>
                                    <div <?php if ( $captcha_type   !=  $values['captcha_type'] ) { echo 'style="display: none"';}  ?> class="captcha-options <?php echo $captcha_type ?> postbox wph-postbox">
                                    <div class="wph_input widefat">
                                    <?php
                                        
                                    foreach ( $group    as  $option_key =>  $option_args )
                                        {
                                            ?>
     
                                                    <div class="row spacer">
                                                        <p><?php echo $option_args['title'] ?></p>
                                                        <?php
                                                        
                                                        switch ( $option_args['type'] )
                                                            {
                                                                case 'input':   
                                                                                ?><input type="text" class="setting-value text" value="<?php 
                                                                                
                                                                                if ( isset ( $values[ $option_key ] ) )
                                                                                    echo esc_html ( $values[ $option_key ] );
                                                                                    else if ( isset ( $option_args['default'] ) )
                                                                                    echo esc_html ( $option_args['default'] );
                                                                                
                                                                                ?>" name="<?php echo $option_key ?>" /><?php
                                                                                break;
                                                                
                                                                case 'select':   
                                                                                ?><select class="setting-value text" name="<?php echo $option_key ?>">
                                                                                    <?php
                                                                                        
                                                                                        foreach ( $option_args['options'] as    $select_option_key  =>  $select_option_title )
                                                                                            {
                                                                                                ?><option <?php    
                                                                                                    if ( isset ( $values[ $option_key ] ) )
                                                                                                        selected ( $select_option_key, $values[ $option_key ] );
                                                                                                
                                                                                                ?> value="<?php echo $select_option_key ?>"><?php echo $select_option_title ?></option><?php    
                                                                                            }
                                                                                        
                                                                                    ?>    
                                                                                </select>
                                                                                <?php
                                                                                break;
                                                                
                                                            }
                                                        
                                                        ?>
                                                    </div>

                                            <?php
                                        }
                          
                                    ?>
                                    </div>
                                    <div class="wph_help option_help">
                                        <div class="text">
                                            <?php 
                                            $this->captcha_type[ $captcha_type ]->get_module_help(); ?>
                                        </div>
                                        
                                    </div>
                            
                            
                                    </div>
                                    <?php
                                }
              
                }
                
                
            function _module_option_processing( $module_data )
                {
                    $results            =   array();
                    
                    $module_settings    =   $this->wph->functions->get_module_item_setting( $module_data['id'] );
                    if ( ! is_array ( $module_settings ) ||  ! isset ( $module_settings['captcha_type'] ) )   
                        {
                            $module_settings =   array();
                            $module_settings['captcha_type'] =    'disabled';
                        }
                    
                    //check for API check
                    if ( isset ( $_POST['api_test'] )   &&  $_POST['api_test']  ==  'true' )
                        {                        
                            $captcha_options    =   $this->_get_captcha_options();
                        
                            foreach ( $captcha_options as   $captcha_type   =>  $group )
                                {
                                    if ( $module_settings['captcha_type']   ==  $captcha_type )
                                        {
                                            $module_settings    =   $this->captcha_type[ $captcha_type ]->api_test( $module_settings );
                                        }     
                                }
                
                            $results['value']   =   $module_settings;
                       
                            return $results;   
                        }
                    
                    
                    
                    $_settings =   array ( 'captcha_type'   =>  '' );
                    foreach ( $_settings   as  $setting_name  =>  $setting_value )
                        {
                            if ( ! isset ( $_POST[ $setting_name ] ) )
                                continue;
                                
                            $value  =   preg_replace( '/[^a-zA-Z0-9-_]/m' , '', $_POST[ $setting_name ] );
                            if ( empty ( $value ) )
                                continue;
                                
                            $module_settings[ $setting_name ]   =   $value;
                        }
                        
                    $_settings =   shortcode_atts ( $this->_get_captcha_options(), array() );
                    foreach ( $_settings   as  $captcha_type   =>  $group  )
                        {
                            foreach ( $group    as  $option_key =>  $option_args )
                                {
                                    if ( ! isset ( $_POST[ $option_key ] ) )
                                        continue;
                                        
                                    $value  =   preg_replace( '/[^a-zA-Z0-9-_\.]/m' , '', $_POST[ $option_key ] );
                                    if ( empty ( $value ) )
                                        continue;
                                    
                                    if ( isset ( $option_args['validation'] ) )
                                        $value  =   call_user_func( $option_args['validation'], array ( 'value' =>  $value, 'field_key' => $option_key  ) );
                                        
                                    $module_settings[ $option_key ]   =   $value;
                                }
                        }
                        
                    
                    if ( $module_settings['captcha_type']  ==  'google_v2'  &&  ! empty ( $module_settings['g2-site-key'] )  &&  ! empty ( $module_settings['g2-site-secret-key'] )   &&  isset ( $module_settings['g2_checked_for'] ) &&  ! empty ( $module_settings['g2_checked_for'] )   &&  md5 ( $module_settings['g2-site-key'] . $module_settings['g2-site-secret-key'] )  !=  $module_settings['g2_checked_for'] )
                        $module_settings['g2_checked_for']    =   '';
                    if ( $module_settings['captcha_type']  ==  'google_v3'  &&  ! empty ( $module_settings['g3-site-key'] )  &&  ! empty ( $module_settings['g3-site-secret-key'] )   &&  isset ( $module_settings['g3_checked_for'] ) &&  ! empty ( $module_settings['g3_checked_for'] )   &&  md5 ( $module_settings['g3-site-key'] . $module_settings['g3-site-secret-key'] )  !=  $module_settings['g3_checked_for'] )
                        $module_settings['g3_checked_for']    =   '';
                                        
                    $results['value']   =   $module_settings;
                       
                    return $results;
                    
                }
                
            
 

        }
?>