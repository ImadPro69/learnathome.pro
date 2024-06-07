<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_admin_login_php extends WPH_module_component
        {
            
            function __construct()
                {
                    parent::__construct();
                    
                    add_action ( 'admin_enqueue_scripts', 'wp_enqueue_media' ); 
                    
                    $custom_logo_image_id =   $this->wph->functions->get_module_item_setting('custom_login_logo');
                    if ( ! empty ( $custom_logo_image_id ) )
                        add_action( 'login_footer', array ( $this, 'custom_login_logo' ) );
                }
            
            function get_component_title()
                {
                    return "Wp-login.php";
                }
                                    
            function get_module_settings()
                {
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'new_wp_login_php',
                                                                    'label'         =>  __('New wp-login.php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  array(
                                                                                                __('Map a new wp-login.php instead default.',  'wp-hide-security-enhancer')
                                                                                                ),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New wp-login.php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("There are a lot of security issues that come from having your login page open to the public. Most specifically, brute force attacks. Because of the ubiquity of WordPress, these kinds of attacks are becoming more and more common.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />" . __("Map a new wp-login.php instead default prevent hackers boot to attempt to brute force a site login. Being known only by the site owner, the url itself becomes private.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br /><span class='important'>" . __("Make sure you keep the new login url to a safe place, in case to forget.",    'wp-hide-security-enhancer') . "</span>",
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/admin-change-wp-login-php/'
                                                                                                        ),
                                                                    
                                                                    
                                                                    'options_pre'   =>  '<div class="icon">
                                                                                                <img src="' . WPH_URL . '/assets/images/warning.png" />
                                                                                            </div>
                                                                                            <div class="text">
                                                                                                <p>' . __('Make sure your log-in url is not already modified by another plugin or theme. In such case, you should disable other code and take advantage of these features.',  'wp-hide-security-enhancer') .'</p>
                                                                                            </div>' ,
                                         
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                    'processing_order'  =>  50
                                                                    
                                                                    );
                                                                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'block_default_wp_login_php',
                                                                    'label'         =>  __('Block default wp-login.php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default wp-login.php file from being accesible.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block default wp-login.php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("If set to Yes, the old login url will be blocked and a default theme 404 error page will be returned.",    'wp-hide-security-enhancer') .
                                                                                                                                         "<br /><br /><span class='important'>" . __('Ensure the New wp-login.php option works correctly on your server before activate this.',    'wp-hide-security-enhancer') . '</span>',
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/admin-change-wp-login-php/'
                                                                                                        ),
                                                                    
                                                                    'advanced_option'   =>  array(
                                                                        
                                                                                                        'description'               =>  '<b>' . __('This is an advanced option !',    'wp-hide-security-enhancer') . '</b><br />' . __('This can break the login page if server not supporting the feature. Ensure `New wp-login.php` option works fine before activate this.<br />If not working, use the recovery link to revert.',    'wp-hide-security-enhancer')
                                                                                                
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
                                                                    'id'            =>  'custom_login_logo',
                                                                    'label'         =>  __('Custom Login page Logo',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  array(
                                                                                                __('Change the default WordPress login page Logo.',  'wp-hide-security-enhancer')
                                                                                                ),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Custom Login Logo',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("The feature in the WP Hide plugin allows you to replace the standard WordPress login page logo with a custom image of your choice. This customization enhances your site's branding by displaying your logo on the login page, admin dashboard, and other areas where the default logo appears. To use this feature, simply upload your desired logo in the WP Hide settings and save the changes. Your new logo will be displayed immediately, providing a more personalized and professional appearance for your WordPress site.",    'wp-hide-security-enhancer') . "</span>" .
                                                                                                                                        "<br />" . __("Recommended width size is 320px.",    'wp-hide-security-enhancer') . "</span>",
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/admin-change-wp-login-php/'
                                                                                                        ),
                                                                                     
                                                                    'input_type'    =>  'custom',
                                                                    
                                                                    'module_option_html_render' =>  array( $this, '_custom_login_logo_module_option_html' ),
                                                                    'module_option_processing'  =>  array( $this, '_custom_login_logo_module_option_processing' ),
                                                                    
                                                                    
                                                                    'processing_order'  =>  50
                                                                    
                                                                    );
                    
                                                                    
                    return $this->module_settings;   
                }
                
                
                
            function _init_new_wp_login_php($saved_field_data)
                {
                    //check if the value has changed, e-mail the new url to site administrator
                    $saved_field_data   =   (string)$saved_field_data;
                    
                    //check if the value has changed, e-mail the new url to site administrator                    
                    $previous_url   =   get_option('wph-previous-login-url');
                    if( $saved_field_data    !=  $previous_url )
                        {
                            update_option( 'wph-login-changed-send-email', time() + 5 );                           
                            wp_cache_flush();
                            update_option('wph-previous-login-url', $saved_field_data );  
                        }
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                   
                    add_filter('login_url',             array($this,'login_url'), 999, 3 );
                    
                    add_filter('site_url',              array(  $this,'site_url'), 999, 3 ); 
  
                    //add replacement
                    $this->wph->functions->add_replacement( trailingslashit(    site_url()  ) .  'wp-login.php',  trailingslashit(    home_url()  ) .  $saved_field_data );
                     
                }
                
            
            static public function check_new_url_email_notice()
                {
                    $wph_login_changed_send_email   =   get_option ( 'wph-login-changed-send-email' );
                    if ( empty ( $wph_login_changed_send_email ) )
                        return;
                    
                    $wph_login_changed_send_email   =   (int) $wph_login_changed_send_email ;
                    if ( empty ( $wph_login_changed_send_email ) )
                        return;
                                            
                    if ( $wph_login_changed_send_email < time ( ) )
                        {
                            delete_option ( 'wph-login-changed-send-email' );
                            wp_cache_flush();
                            self::new_url_email_notice();
                        }
                    
                }
            
            static public function new_url_email_notice()
                {
                    global $wph;
                                        
                    $to         =   get_option('admin_email');
                    $subject    =   'New Login Url for your WordPress - ' .get_option('blogname');
                    $message    =   __('Hello',  'wp-hide-security-enhancer') . ", \n\n" 
                                    . __('This is an automated message to inform that your login url has been changed at',  'wp-hide-security-enhancer') . " " .  trailingslashit( home_url() ) . "\n"
                                    . __('The new login url is',  'wp-hide-security-enhancer') .  ": " . wp_login_url() . "\n\n"
                                    . __('Additionally, you can use the following link to recover the default login/admin access: ',  'wp-hide-security-enhancer') .  ": " . trailingslashit ( home_url() ) . '?wph-recovery='.  $wph->functions->get_recovery_code() . "\n\n"
                                    . __('Please ensure the safety of this URL for potential recovery in case of forgetfulness.',  'wp-hide-security-enhancer') . ".";
                    $headers = 'From: '.  get_option('blogname') .' <'.  get_option('admin_email')  .'>' . "\r\n"; 
                    
                    if ( ! function_exists( 'wp_mail' ) ) 
                        require_once ABSPATH . WPINC . '/pluggable.php';
                        
                    wp_mail( $to, $subject, $message, $headers ); 
                }
            
            function login_url( $login_url, $redirect, $force_reauth )
                {
                    //ensure there is no loop with another plugin
                    $backtrace  =   debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                    foreach ( $backtrace   as $key  =>  $backtrace )
                        {
                            if ( $key < 1 )
                                continue;
                                
                            if ( isset ( $backtrace['file'] )   && strpos( wp_normalize_path( $backtrace['file'] ), 'modules/components/admin-login_php.php' ) )
                                return $login_url;
                        }
                    
                    //check for redirect fro admin to login url and the disable redirect option
                    $new_admin_url          =   $this->wph->functions->get_module_item_setting('admin_url');
                    $disable_redirect_url   =   $this->wph->functions->get_module_item_setting('disable_admin_redirect_to_login');
                    if ( ! empty ( $new_admin_url ) &&  ! empty ( $disable_redirect_url )   &&  $disable_redirect_url   ==  'yes'  )
                        {
                            $continue = TRUE ;
                            if (  function_exists('is_user_logged_in') &&  is_user_logged_in() !== FALSE )
                                $continue = FALSE;
                                
                            if ( $continue )
                                {
                                    $new_admin_uri      =   trailingslashit(    site_url()  )   . trim($new_admin_url,  "/");
                                    $new_admin_uri      =   str_replace ( array ( 'https://', 'http://' ), '', $new_admin_uri );
                                    
                                    if ( stripos( str_replace ( array ( 'https://', 'http://' ), '', $redirect ), $new_admin_uri )   !== FALSE   ||  stripos( str_replace ( array ( 'https://', 'http://' ), '', $redirect ), str_replace ( array ( 'https://', 'http://' ), '', trailingslashit(    site_url()  ) . 'wp-admin' ) )   !== FALSE )
                                        return home_url();
                                }
                        }
                    
                    $parse_login_url        =   parse_url ( $login_url );
                    $new_wp_login_php       =   $this->wph->functions->get_module_item_setting('new_wp_login_php');
                    
                    //avoid looping
                    static $wph_home_url;
                    if ( is_null ( $wph_home_url ) )
                        $wph_home_url   =   home_url( $new_wp_login_php, 'login' );
                    
                    $login_url          =   $wph_home_url;
                    
                    if ( isset ( $parse_login_url['query'] )    &&   ! empty ( $parse_login_url['query'] ) )
                        $login_url .=   '?' .   $parse_login_url['query'];
                    
                    return $login_url;   
                }
                
            function site_url( $url, $path, $scheme )
                {
                    if ( ! in_array ( $scheme, array ( 'login', 'login_post' ) ) )
                        return $url;
                    
                    $new_wp_login_php       =   $this->wph->functions->get_module_item_setting('new_wp_login_php');
                    
                    if ( ! empty ( $new_wp_login_php ) )
                        $url    =   str_replace ( 'wp-login.php', $new_wp_login_php, $url );
                        
                    return $url;
                }
                
            function _callback_saved_new_wp_login_php($saved_field_data)
                {
                    $processing_response    =   array();
                    
                    if(empty($saved_field_data))
                        return  $processing_response; 
          
                    $new_wp_login_php =   untrailingslashit ( $this->wph->functions->get_url_path( trailingslashit(    site_url()  ) .  'wp-login.php'   ) );
                                
                    $rewrite_base   =   $saved_field_data;
                    $rewrite_to     =   $this->wph->functions->get_rewrite_to_base( $new_wp_login_php, TRUE, FALSE );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        $processing_response['rewrite'] = "\nRewriteRule ^"    .   $rewrite_base     .   '(.*) '. $rewrite_to .'$1 [L,QSA]';
                    
                    if($this->wph->server_web_config   === TRUE)
                        $processing_response['rewrite'] = '
                            <rule name="wph-new_wp_login_php" stopProcessing="true">
                                <match url="^'.  $rewrite_base   .'(.*)"  />
                                <action type="Rewrite" url="'.  $rewrite_to .'{R:1}"  appendQueryString="true" />
                            </rule>
                                                            ';
                                
                    return  $processing_response;   
                }
                
                
            function _init_block_default_wp_login_php($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
  
                }
                
            function _callback_saved_block_default_wp_login_php($saved_field_data)
                {
                    $processing_response    =   array();
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return  $processing_response;
                        
                    //prevent from blocking if the new_wp_login_php is empty
                    $new_wp_login_php     =   $this->wph->functions->get_module_item_setting('new_wp_login_php');
                    if (empty(  $new_wp_login_php ))
                        return FALSE;  
                                        
                    
                    $rewrite_base   =   $this->wph->functions->get_rewrite_base( 'wp-login.php', FALSE, FALSE );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_to_base( 'index.php', TRUE, FALSE );
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {           
                            $text   =       "RewriteCond %{ENV:REDIRECT_STATUS} ^$\n";
                            $text   .=      "RewriteRule ^" . $rewrite_base ." ".  $rewrite_to ."?wph-throw-404 [L]";
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                            $text   = '
                                        <rule name="wph-block_default_wp_login_php" stopProcessing="true">
                                            <match url="^'.  $rewrite_base   .'"  />
                                            <action type="Rewrite" url="'.  $rewrite_to .'?wph-throw-404" />  
                                        </rule>
                                                            ';
                               
                    $processing_response['rewrite'] = $text;    
                                                    
                    return  $processing_response;   
                }
                
            
            function _custom_login_logo_module_option_html()
                {
                
                    $custom_logo_image_id =   $this->wph->functions->get_module_item_setting('custom_login_logo');
                    
                    ?>
                    <div id="image_preview" style="margin-top: 10px;">
                        <img src="<?php
                        
                        if ( ! empty ( $custom_logo_image_id ) )
                            {
                                $image_url = wp_get_attachment_url( $custom_logo_image_id );
                                if ( $image_url )
                                    echo esc_url($image_url);
                            }
                        
                        ?>" id="image_thumbnail" style="max-width: 320px;<?php  if ( empty ( $custom_logo_image_id ) ) { echo 'display: none'; }  ?>">
                    </div>

                    <p>
                        <input type="hidden" id="custom_logo_image_id" name="custom_logo_image_id" value="<?php echo $custom_logo_image_id ?>" >
                        <button type="button" class="set_custom_images button-primary">Set Logo</button>  &nbsp; <button type="button" id="remove_custom_image" class="button" style="<?php  if ( empty ( $custom_logo_image_id ) ) { echo 'display: none'; }  ?>">Remove Logo</button>
                    </p>


                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            
                            jQuery('#remove_custom_image').on( 'click', function() {
                                jQuery( '#image_thumbnail' ).css ( 'display', 'none');
                                jQuery( '#image_thumbnail' ).attr ( 'src', '');
                                jQuery( '#custom_logo_image_id' ).val( '' );
                                jQuery( this ).css ( 'display', 'none');
                            })
                            
                            var mediaUploader;
                            jQuery('.set_custom_images').on('click', function(e) {
                                e.preventDefault();
                                var button = jQuery(this);
                                var inputField = button.prev();
                                // If the media uploader already exists, reopen it
                                if (mediaUploader) {
                                    mediaUploader.open();
                                    return;
                                }
                                // Create the media uploader
                                mediaUploader = wp.media({
                                    title: 'Choose Image',
                                    button: {
                                        text: 'Set Custom Logo'
                                    },
                                    multiple: false
                                });
                                // When an image is selected, run a callback
                                mediaUploader.on('select', function() {
                                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                                    inputField.val(attachment.id);
                                    var thumbnailUrl = attachment.sizes && attachment.sizes.full ? attachment.sizes.full.url : attachment.url;
                                    jQuery('#image_thumbnail').attr('src', thumbnailUrl).show();
                                    jQuery('#remove_custom_image').css ( 'display', 'inline-block');
                                });
                                // Open the uploader dialog
                                mediaUploader.open();
                            });
                        });
                    </script>
                    <?php
   
                }
                
            function _custom_login_logo_module_option_processing()
                {
                    $results            =   array();
                    
                    $custom_logo_image_id  =   preg_replace("/[^0-9]/", '', $_POST['custom_logo_image_id'] );
                    
                    $results['value']   =   $custom_logo_image_id;
                    
                    return $results;
                }
                
            
            function custom_login_logo()
                {
                    $custom_logo_image_id =   $this->wph->functions->get_module_item_setting('custom_login_logo');
                    
                    if ( empty ( $custom_logo_image_id ) )
                        return;
                        
                    $image_url = wp_get_attachment_url( $custom_logo_image_id );
                    if ( empty ( $image_url ) )
                        return;
                        
                    ?>
                    <style>
                        #login h1 img, .login h1 img {display: block; max-width: 100%; max-height: 100%}
                    </style>
                    <script type="text/javascript">

                        var elementToUpdate = document.querySelector('#login h1');
                        elementToUpdate.innerHTML = '';
                        var newElement = document.createElement('img');
                        newElement.setAttribute('src', '<?php echo esc_url($image_url); ?>');
                        elementToUpdate.appendChild(newElement);
                    
                    </script>
      
                    <?php   
                }
                            

        }
        
        

?>