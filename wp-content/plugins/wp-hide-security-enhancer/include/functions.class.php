<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_functions
        {
            var $wph;
                                  
            function __construct()
                {
                    global $wph;
                    $this->wph          =   &$wph;
                }
            
            function get_module_default_setting()
                {
                    $defaults   = array (
                                            'id'                =>  '',
                                            'visible'           =>  TRUE,
                                            'label'             =>  '',
                                            'description'       =>  '',
                                            'value_description' =>  '',
                                            'input_type'        =>  'text',
                                            'default_value'     =>  '',
                                            'sanitize_type'     =>  array('sanitize_title'),
                                            
                                            'help'              =>  FALSE,
                                            'advanced_option'   =>  FALSE,
                                            
                                            
                                            'options_pre'           =>  '',
                                            'options'               =>  array(),
                                            'options_post'          =>  '',
                                            
                                            'interface_help_split'      =>  TRUE,
                                            
                                            'require_save'              =>  TRUE,
                                            
                                            //callback function when components run. Default being set for _init_{$field_id}
                                            'callback'                  =>  '',
                                            //callback function to return the rewrite code, Default being set for _callback_saved_{$field_id}
                                            'callback_saved'            =>  '',
                                            //PassThrough any additional arguments                                            
                                            'callback_arguments'         =>  array(),
                                            
                                            //custom html render content for this module component option
                                            'module_option_html_render' =>  '',
                                            
                                            //custom processing (interface save) for this module component option
                                            'module_option_processing' =>  '',
                                            
                                            'processing_order'  =>  10,
                                        );   
                    
                    return $defaults;
                }
                
            function filter_settings( $module_settings, $strip_splits    =   FALSE )
                {
                    if( ! is_array( $module_settings )  || count( $module_settings ) < 1)
                        return $module_settings;
                    
                    $defaults   =   $this->get_module_default_setting();
                    
                    foreach($module_settings    as  $key    =>  $module_setting)
                        {
                            if(isset($module_setting['type'])   &&  $module_setting['type'] ==  'split')
                                {
                                    if($strip_splits    === TRUE)
                                        unset($module_settings[$key]);
                                        
                                    continue;
                                }
                            
                            $module_setting   =   wp_parse_args( $module_setting, $defaults );
                            
                            switch($module_setting['input_type'])
                                {
                                    case    'text' :
                                                        $defaults_type   = array (
                                                                                'placeholder'                =>  '',
                                                                            );
                                                        $module_setting   =   wp_parse_args( $module_setting, $defaults_type );
                                                        
                                                        break;   
                                    
                                    
                                }
       
                            $module_settings[$key]  =   $module_setting;
                        }
                    
                    $module_settings    =   array_values($module_settings);
                    
                    return $module_settings;
                    
                }
                               
            
            function do_reset_settings()
                {
                    
                    $nonce  =   $_POST['_wpnonce'];
                    if ( ! wp_verify_nonce( $nonce, 'wp-hide-reset-settings' ) )
                        return FALSE;
                        
                    //only for admins
                    If ( !  current_user_can ( 'manage_options' ) )
                        return FALSE;
                    
                    $settings   =   $this->get_settings();
                    
                    $settings['module_settings']   =   $this->reset_settings();

                    //eset the write string
                    $settings['write_check_string']   =   '';
                             
                    //update the settings
                    $this->update_settings( $settings );
                    
                    //udpate the cass settings as well
                    $this->wph->settings    =   $settings;   
                    
                    //trigger the settings changed action
                    do_action('wph/settings_changed', null, null);
                    
                    //redirect
                    $new_admin_url     =   $this->get_module_item_setting('admin_url'  ,   'admin');
                    if(!empty($new_admin_url)   &&  $this->is_permalink_enabled())
                        $new_location       =   trailingslashit(    home_url()  )   . $new_admin_url .  "/admin.php?page=wp-hide";
                        else
                        $new_location       =   trailingslashit(    site_url()  )   .  "wp-admin/admin.php?page=wp-hide";
                         
                    $new_location   .=  '&reset_settings=true';
                        
                    wp_redirect( $new_location );
                    die();
                    
                }
                
            function process_interface_save()
                {
                    $nonce  =   $_POST['wph-interface-nonce'];
                    if ( ! wp_verify_nonce( $nonce, 'wph/interface_fields' ) )
                        return FALSE;
                    
                    //only for admins
                    If ( !  current_user_can ( 'manage_options' ) )
                        return FALSE;
                        
                    $screen_slug  =   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['page'] );
                    if(empty($screen_slug))
                        return FALSE;
                        
                    $tab_slug     =   isset($_GET['component'])   ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['component'] )  :   FALSE;
                        
                    $module =   $this->get_module_by_slug($screen_slug);
                    if(!is_object($module))
                        return FALSE;
                    
                    //if no tag slug check if module use tabs and use the very first one
                    if(empty($tab_slug)   &&  $module->use_tabs  === TRUE)
                        {
                            //get the first component
                            foreach($module->components   as  $module_component)
                                {
                                    if( ! $module_component->title)
                                        continue;
                                    
                                    $tab_slug =   $module_component->id;
                                    break;
                                }  
                            
                        }
                        
                    //clean the environment ignore errors
                    delete_option( 'wph-environment-ignore-rewrite-test' );
                                            
                    $unique_require_updated_settings    =   array();
                    
                    //proces the fields
                    $module_settings    =   $this->filter_settings(   $module->get_module_components_settings($tab_slug)    );
                    
                    $processed_fields   =   array();
                                                            
                    foreach ( $module_settings as $module_setting )
                        {
                            if(isset($module_setting['type'])   &&  $module_setting['type'] ==  'split')
                                continue;
                            
                            $field_name =   $module_setting['id'];
                            
                            $processed_fields[] =   $field_name;
                            
                            if ( isset($module_setting['module_option_processing'])    &&  is_callable( $module_setting['module_option_processing']) )
                                {
                                    $results    =   call_user_func( $module_setting['module_option_processing'], $module_setting );
                                    
                                    $value  =   $results['value'];
                                }
                                else
                                {
                            
                                    //$value      =   isset($_POST[$field_name])  ?   sanitize_text_field($_POST[$field_name]) :   '';
                                    $value      =   isset($_POST[$field_name])  ?   preg_replace( '/[^a-zA-Z0-9-_\.\/]/m' , "", $_POST[$field_name] )   :   '';
                                                                        
                                    //if empty use the default
                                    if(empty($value))
                                        $value  =   $module_setting['default_value'];
                                             
                                    //sanitize value
                                    foreach($module_setting['sanitize_type']    as  $sanitize)
                                        {
                                            $value  =   call_user_func_array( $sanitize, array( $value ) );   
                                        }
                                }
                                
                            //held the value
                            if ( $module_setting['input_type']   ==  'text'  &&  ! empty( $value ))
                                {
                                    //if require unique, save for postprocessing
                                    $unique_require_updated_settings[ $field_name ]  =   array(
                                                                                                'module_name'   =>  $module_setting['label'],
                                                                                                'value'         =>  $value
                                                                                                );
                                }
                                else
                                $this->wph->settings['module_settings'][ $field_name ]  =   $value;
                        }
                    
                    //delete previous errors transient
                    delete_transient( 'wph-process_interface_save_errors' );
                    
                    $errors                         =   FALSE;
                    
                    global $process_interface_save_errors;
                    
                    if ( ! is_array ( $process_interface_save_errors ) )
                        $process_interface_save_errors  =   array();
                    
                    //put the new values into a temporary settings variable
                    $_settings_ =   $this->wph->settings['module_settings'];   
                    
                    //clean up all values if $tab_slug is theme, to prevent deleted themes to still held values which oterwise can't be used anymore
                    if  ( $tab_slug == 'theme' )
                        {
                            $reset_fileds   =   array(
                                                        'new_theme_path_',
                                                        'new_style_file_path_'
                                                        );
                            foreach($reset_fileds as $reset_filed )
                                {
                                    foreach  ( $_settings_ as   $key    =>  $setting ) 
                                        {
                                            if  ( strpos ( $key, $reset_filed ) !== FALSE )
                                                $_settings_[ $key ] =   '';
                                        }
                                    
                                }  
                            
                        }
                    
                    foreach($unique_require_updated_settings   as  $field_name =>  $data)
                        {
                            $_settings_[ $field_name ]    =   $data['value'];
                        }
                    
                    
                    //ensure the base slug is not being used by another option
                    // e.g.   skin     skin/module
                    $_settings_for_regex    =   array();
                    foreach ( $_settings_   as $field_name =>   $option_value )
                        {
                            if  (  ! is_string( $option_value ) )
                                continue;
                                             
                            $parts  =   explode("/", $option_value);
                            
                            $_settings_for_regex[ $field_name ] =   $parts[0];
                        } 

                    if ( $tab_slug != 'cdn' )
                        {    
                            $reserved_values          =   array(
                                                                                                                        'wp'                =>  __('is a system reserved.',     'wp-hide-security-enhancer'),
                                                                                                                        'admin'             =>  __('is a system reserved.',     'wp-hide-security-enhancer'),
                                                                                                                        'wp-admin'          =>  __('is a system reserved.',     'wp-hide-security-enhancer'),
                                                                                                                        'wp-content'        =>  __('is a system reserved.',     'wp-hide-security-enhancer'),
                                                                                                                        'wp-includes'       =>  __('is a system reserved.',     'wp-hide-security-enhancer'),
                                                                                                                        'admin-ajax.php'    =>  __('is a system reserved.',     'wp-hide-security-enhancer'),
                                                                                                                        'dashboard'         =>  __('is a system reserved.',     'wp-hide-security-enhancer'),
                                                                                                                        'cpanel'            =>  __('is a system reserved.',     'wp-hide-security-enhancer')
                                                                                                                        );
                        }
                        else
                        $reserved_values    =   array();
                    
                    $domain_parsed =   parse_url ( home_url() ) ;
                    $domain_parsed_host_parts   =   explode ( "." , $domain_parsed['host'] );
                    
                    if ( $tab_slug != 'cdn' )
                        $reserved_values[$domain_parsed_host_parts[0]]          =   __('is similar to domain name.',     'wp-hide-security-enhancer');
                    
                    //clean the just updated fields within main settings array
                    foreach($unique_require_updated_settings   as  $field_name =>  $data)
                        {
                            if( isset($_settings_[ $field_name ]) )
                                $_settings_[ $field_name ]    =   '';
                            
                            //check if the value already exists in other setting
                            if(array_search( $data['value'] , $_settings_)    !== FALSE)
                                {
                                    $errors =   TRUE;
                                    $process_interface_save_errors[]    =   array(  
                                                                                    'type'      =>  'error',
                                                                                    'message'   =>  __('Value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . __('already in use for another option.',     'wp-hide-security-enhancer')
                                                                                    );
                                }
                                else
                                {
                                    
                                    //check for base slug e.g. skin/module
                                    $parts  =   explode ( "/" , $data['value'] );
                                    $_settings_to_search    =   $_settings_for_regex;
                                    unset( $_settings_to_search[ $field_name ] );
                                    
                                    //if plugins tab, ignore the other options which might use the same base slug
                                    if ( $tab_slug  ==  'plugins' )
                                        {
                                            foreach (  $processed_fields    as  $processed_field )
                                                unset( $_settings_to_search[ $processed_field ] );   
                                        }
                                        
                                        
                                    //ensure the login url has a minimum length of 5
                                    if ( $tab_slug  ==  'wp-login-php' )
                                        {
                                            if ( $data['module_name']   ==  'New wp-login.php'     &&  strlen ( $data['value'] ) < apply_filters( 'wp-hide/interface/process/minimum_slug_length', 5, $data ) )
                                                {
                                                    $errors =   TRUE;
                                                    $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('The value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . sprintf ( __('must be a minimum of %s characters or longer.',     'wp-hide-security-enhancer'), apply_filters( 'wp-hide/interface/process/minimum_slug_length', 5, $data ) )
                                                                                            );   
                                                    
                                                }  
                                        }
                                    if ( $tab_slug  ==  'admin-url' )
                                        {
                                            if ( $data['module_name']   ==  'New Admin Url'     &&  strlen ( $data['value'] ) < apply_filters( 'wp-hide/interface/process/minimum_slug_length', 5, $data ) )
                                                {
                                                    $errors =   TRUE;
                                                    $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('The value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . sprintf ( __('must be a minimum of %s characters or longer.',     'wp-hide-security-enhancer'), apply_filters( 'wp-hide/interface/process/minimum_slug_length', 5, $data ) )
                                                                                            );   
                                                    
                                                }  
                                        }
                                       
                                    if( array_search( $parts[0] , $_settings_to_search )    !== FALSE )
                                        {
                                            $errors =   TRUE;
                                            $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('Value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . __('use the same base slug ', 'wp-hide-security-enhancer') . '<b>' . $parts[0] . '</b> ' . __('used for another option.',     'wp-hide-security-enhancer')
                                                                                            );
                                        }   
                                    
                                    
                                }
                                
                            //put the value back
                            $_settings_[ $field_name ]    =   $data['value'];
                            
                            $_reserved_values           =   $reserved_values;
                            
                            //check for reserved value
                            foreach ( $_reserved_values  as  $reserved_value   =>  $error_description )
                                {
                                    if( stripos( $reserved_value, $data['value'] )    === 0 )
                                        {
                                            $errors =   TRUE;
                                            $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('Value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . $error_description
                                                                                            );
                                            continue;                                                
                                        }
                                        
                                    if( stripos( $data['value'], $reserved_value )    === 0 )
                                        {
                                            $errors =   TRUE;
                                            $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('Value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . $error_description
                                                                                            );
                                                                                            
                                        }
                                        
          
                                }
                                      
                        }
                    
                    
                    $errors   =   apply_filters('wp-hide/interface/process', $errors, $_settings_, $module_settings);
                                            
                    if( $errors === FALSE)
                        {    
                            //put to main setting holder
                            $this->wph->settings['module_settings']   =   $_settings_;
                            
                            //generate a new write_check_string
                            $write_check_string  =   time() . '_' . mt_rand(100, 99999);
                            $this->wph->settings['write_check_string']   =   $write_check_string;
                                                                                           
                            //update the settings
                            $this->update_settings($this->wph->settings);
                            
                            //trigger the settings changed action
                            do_action('wph/settings_changed', $screen_slug, $tab_slug);
                        }
                    
                    //store the error for display purpose
                    if ( count ( $process_interface_save_errors )   >   0 )
                        set_transient( 'wph-process_interface_save_errors', $process_interface_save_errors, HOUR_IN_SECONDS );
                    
                    //redirect
                    $new_admin_url     =   $this->get_module_item_setting('admin_url'  ,   'admin');
                    
                    //check if the rewrite applied
                    if ( ! empty ( $new_admin_url ) &&  ! $this->rewrite_rules_applied() )
                        $new_admin_url  =   '';
                    
                    if(!empty($new_admin_url)   &&  $this->is_permalink_enabled())
                        $new_location       =   trailingslashit(    home_url()  )   . $new_admin_url .  "/admin.php?page="   .   $screen_slug;
                        else
                        $new_location       =   trailingslashit(    site_url()  )   .  "wp-admin/admin.php?page="   .   $screen_slug;
                    
                    if($tab_slug !==    FALSE)
                        $new_location   .=  '&component=' . $tab_slug;
                    
                    $new_location   .=  '&settings_updated=true';
                        
                    wp_redirect($new_location);
                    die();
                }
            
            
            /**
            * Attempt to copy the mu loader within mu-plugins folder
            * 
            */
            static function copy_mu_loader( $force_overwrite    =   FALSE   )
                {
                    
                    
                    //check if mu-plugins folder exists
                    if(! is_dir( WPMU_PLUGIN_DIR ))
                        {
                            if (! wp_mkdir_p( WPMU_PLUGIN_DIR ) )
                                return FALSE;
                        }
                    
                    //check if file actually exists already
                    if( !   $force_overwrite    )
                        {
                            if( file_exists(WPMU_PLUGIN_DIR . '/wp-hide-loader.php' ))
                                return TRUE;
                        }
                        
                    //attempt to copy the file
                    return @copy( WP_PLUGIN_DIR . '/wp-hide-security-enhancer/mu-loader/wp-hide-loader.php', WPMU_PLUGIN_DIR . '/wp-hide-loader.php' );
                }
                
            
            /**
            * Attempt to remove the mu loader
            *     
            */
            static function unlink_mu_loader()
                {
                    //check if file actually exists already
                    if( !file_exists(WPMU_PLUGIN_DIR . '/wp-hide-loader.php' ))
                        return;
                        
                    //attempt to copy the file
                    @unlink ( WPMU_PLUGIN_DIR . '/wp-hide-loader.php' );
                }
                
                
            function settings_changed_check_for_cache_plugins()
                {
                    
                    $active_plugins = (array) get_option( 'active_plugins', array() ); 
                            
                    //cache plugin nottice
                    if(array_search('w3-total-cache/w3-total-cache.php',    $active_plugins)    !== FALSE)  
                        {
                            //check if just flushed
                            if(!isset($_GET['w3tc_note']))
                                echo "<div class='error'><p>". __('W3 Total Cache Plugin is active, make sure you clear the cache for new changes to apply', 'wp-hide-security-enhancer')  ."</p></div>";
                        }
                    if(array_search('wp-super-cache/wp-cache.php',    $active_plugins)    !== FALSE)  
                        {
                            echo "<div class='error'><p>". __('WP Super Cache Plugin is active, make sure you clear the cache for new changes to apply', 'wp-hide-security-enhancer')  ."</p></div>";
                        }
                        
                       if(array_search('wp-fastest-cache/wpFastestCache.php',    $active_plugins)    !== FALSE)  
                        {
                            echo "<div class='error'><p>". __('WP Fastest Cache Plugin is active, make sure you clear the cache for new changes to apply', 'wp-hide-security-enhancer')  ."</p></div>";
                        }    
                    
                }
                
                
            /**
            * Return the module class by it's slug
            * 
            * @param mixed $module_slug
            */
            function get_module_by_slug($module_slug)
                {
                    global $wph;
                    
                    $found_module   =   FALSE;
                    
                    foreach($wph->modules     as  $module)
                        {
                            $interface_menu_data    =   $module->get_module_slug();
                            
                            if($interface_menu_data ==  $module_slug)
                                {
                                    $found_module   =   $module;
                                    break;                            
                                }
                        }
                        
                    return $found_module;
                }
                
                
            /**
            * Return the module component class instance by it's slug
            * 
            * @param mixed $module_slug
            */
            function get_module_component_by_slug ( $module_slug )
                {
                    global $wph;
                    
                    $found_module   =   FALSE;
                    
                    foreach ( $wph->modules     as  $module )
                        {
                            foreach ( $module->components  as  $component )
                                {
                                    if ( $component->get_component_id() ==  $module_slug )
                                        return $component;
                                }
                        }
                        
                    return FALSE;
                }
                
                
            
            /**
            * Used on early access when WP_Rewrite is not available
            * 
            */
            function is_permalink_enabled()
                {
                    
                    $permalink_structure    =   get_option('permalink_structure');
                    
                    if (    empty($permalink_structure)   )
                        return FALSE;
                        
                    return TRUE;
                        
                }
            
            
            
            /**
            * return the server home path
            * 
            */
            function get_home_path()
                {
                    
                    $home    = set_url_scheme( get_option( 'home' ), 'http' );
                    $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
                    if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) 
                            {
                                
                                $home_path              =   str_replace( '\\', '/', $_SERVER['DOCUMENT_ROOT'] );
                                $home_path              =   rtrim( $home_path , '/');
                                $home_path              .=  $this->wph->default_variables['site_relative_path'];
                                    
                                /*                            
                                $wp_path_rel_to_home    = str_ireplace( $home, '', $siteurl );
                                $pos                    = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
                                
                                if($pos !== FALSE)
                                    {
                                        $home_path              = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
                                        $home_path              =   trim( $home_path , '/\\') . DIRECTORY_SEPARATOR;;        
                                    }
                                    else
                                    {
                                        $wp_path_rel_to_home    =   DIRECTORY_SEPARATOR . trim($wp_path_rel_to_home, '/\\') . DIRECTORY_SEPARATOR;
                                        
                                        $real_apth              =   realpath(ABSPATH) . DIRECTORY_SEPARATOR ;
                                        
                                        $pos                    =   strpos( $real_apth, $wp_path_rel_to_home);
                                        $home_path              =   substr( $real_apth, 0, $pos );
                                        $home_path              =   trim( $home_path , '/\\') . DIRECTORY_SEPARATOR;        
                                    }
                                    
                                */
                            } 
                        else 
                            {
                                $home_path = ABSPATH;
                            }

                    
                    $home_path      =   trim($home_path, '\\/ ');
                    
                    //not for windows
                    if ( DIRECTORY_SEPARATOR    !=  '\\')
                        $home_path      =   DIRECTORY_SEPARATOR . $home_path;
                    
                    return $home_path;
                       
                }
            
            
            
            /**
            * Set server type
            * 
            */
            function set_server_type()
                {
     
                    //Allow to set server type through filter
                    if  ( !  empty ( apply_filters( 'wph/core/set_server_type' , '' ) ) )
                        return;
                    
                    $SERVER_SOFTWARE    =   $_SERVER['SERVER_SOFTWARE'];
                    
                    If ( empty ( $SERVER_SOFTWARE ) )
                        {
                            //unable to identify server type
                            return FALSE;   
                        }

                    //check for WPEngine
                    if (    getenv('IS_WPE')    ==  "1"   ||  getenv('IS_WPE_SNAPSHOT')    == "1" ) 
                        {
                            $this->wph->server_nginx_config  =   TRUE;
                            return;  
                        }
                        
                    //check for Flywheel hosting
                    if ( stripos( $SERVER_SOFTWARE, 'Flywheel') !== FALSE )
                        {
                            $this->wph->server_nginx_config  =   TRUE;
                            return;   
                        }
    
                    if ( $this->is_apache()   ===    TRUE )
                        $this->wph->server_htaccess_config  =   TRUE;
                    
                    if ( $this->is_IIS()  === TRUE )
                        $this->wph->server_web_config  =   TRUE;
                        
                    if ( $this->is_nginx()  === TRUE )
                        $this->wph->server_nginx_config  =   TRUE;
                        
                }

            
            function using_mod_rewrite_permalinks()
                {
                    
                    return $this->is_permalink_enabled() && ! $this->using_index_permalinks();    
                    
                }
            
            
            function using_index_permalinks() 
                {
                    
                    $permalink_structure    =   get_option('permalink_structure');
                    
                    if(empty($permalink_structure))
                        return;

                    $index  =   'index.php';
                        
                    // If the index is not in the permalink, we're using mod_rewrite.
                    return preg_match( '#^/*' . $index . '#', $permalink_structure );
                    
                }
                
            
            /**
            * return whatever the htaccess config file is writable
            *     
            */
            function is_writable_htaccess_config_file()
                {
                    $home_path      = $this->get_home_path();
                    $htaccess_file  = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                    
                    if ((!file_exists($htaccess_file)  && $this->is_permalink_enabled()) || is_writable($htaccess_file))
                        return TRUE;
                        
                    return FALSE;
                    
                }
                
            /**
            * return whatever server using the .htaccess config file
            * 
            */
            function server_use_web_config_file()
                {
                    
                    $is_iis7    = $this->is_IIS7();
                    
                    $supports_permalinks = false;
                    if ( $is_iis7 ) 
                        {

                            $supports_permalinks = class_exists( 'DOMDocument', false ) && isset($_SERVER['IIS_UrlRewriteModule']) && ( PHP_SAPI == 'cgi-fcgi' );
                        }
                    
                    
                    $supports_permalinks    =   apply_filters( 'iis7_supports_permalinks', $supports_permalinks );
                           
                    return $supports_permalinks;
                    
                }
            
            
            /**
            * return whatever the web.config config file is writable
            *     
            */
            function is_writable_web_config_file()
                {
                    $home_path = $this->get_home_path();
                    
                    $web_config_file = $home_path . 'web.config';
                    
                    if ( ( ! file_exists($web_config_file) && $this->is_permalink_enabled() ) || win_is_writable($web_config_file) )
                        return TRUE;
                        
                    return FALSE;
                    
                }          
            
            
            /**
            * Return if the server run Apache
            * 
            */
            function is_apache()
                {
                    $is_apache  =   FALSE;
                    $is_apache  = (stripos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false || stripos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false);
                    
                    return $is_apache;   
                    
                }
                
            
            /**
            * Return if the server run on nginx
            * 
            */
            function is_nginx()
                {
                    $is_nginx   =   FALSE;
                    $is_nginx   = (stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);
                    
                    return $is_nginx;   
                    
                }
            
            /**
            * Return if the server run on IIS
            * 
            */
            function is_IIS()
                {
                    $is_IIS     =   FALSE;
                    $is_IIS     =   !$this->is_apache() && (stripos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false || stripos($_SERVER['SERVER_SOFTWARE'], 'ExpressionDevServer') !== false);     
   
                    return $is_IIS;
                    
                }
                
            
            /**
            * Return if the server run on IIS version 7 and up
            *     
            */
            function is_IIS7()
                {
                    $is_iis7    =   FALSE;
                    $is_iis7    =   $this->is_IIS() && intval( substr( $_SERVER['SERVER_SOFTWARE'], stripos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS/' ) + 14 ) ) >= 7;   
                    
                    return $is_iis7;
                }
                  
            
            
            /**
            * Return if the server run Apache
            * 
            */
            function is_litespeed()
                {
                    $is_litespeed  =   FALSE;
                    $is_litespeed  = ( stripos ( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed' ) !== false );
                    
                    return $is_litespeed;   
                    
                }
                
                
                
            /**
            * Return a list of the issues found on the server
            *     
            */
            function check_server_environment()
                {
                 
                    $results    =   array(
                                            'found_issues'      =>   FALSE,
                                            'critical_issues'   =>   FALSE,
                                            'errors'            =>   array(),
                                            );   
                                       
                    ob_start();
                    
                    ?><ol><?php
                    
                    if( $this->wph->server_htaccess_config    === FALSE && $this->wph->server_web_config   === FALSE)
                        {
                            $results['found_issues']   =   TRUE;
                            $results['critical_issues']    =   TRUE;
                            include ( WPH_PATH . 'include/admin-interfaces/notice-server-not-supported.php' );
                        }
                        
                    if ( is_multisite() )
                        {
                            $results['found_issues']   =   TRUE;
                            include ( WPH_PATH . 'include/admin-interfaces/notice-is_multisite.php' );
                        }
                                            
                    if( $this->is_litespeed()    === TRUE )
                        {
                            $results['found_issues']   =   TRUE;
                            include ( WPH_PATH . 'include/admin-interfaces/notice-is-litespeed.php' );
                        }
                                        
                    if (  ! $this->is_permalink_enabled())
                        {
                            $results['found_issues']   =   TRUE;
                            include ( WPH_PATH . 'include/admin-interfaces/notice-no-permalinks.php' );
                        }
                        
                    
                    if (    getenv('IS_WPE')    ==  "1"   ||  getenv('IS_WPE_SNAPSHOT')    == "1" ) 
                        {
                            $results['found_issues']   =   TRUE;
                            include ( WPH_PATH . 'include/admin-interfaces/notice-is-wpengine.php' );
                        }
         
                    if( ! $this->is_muloader())
                        {
                            $results['found_issues']   =   TRUE;
                            include ( WPH_PATH . 'include/admin-interfaces/notice-mu-loader.php' );
                        }
                    if( $this->is_muloader() &&  defined( 'WPH_MULOADER_VERSION' )  &&  version_compare( WPH_MULOADER_VERSION, '1.3.5', '<' ) &&    ! isset( $this->wph->maintenances['mu_loader'] ) )
                        {
                            $results['found_issues']   =   TRUE;
                            include ( WPH_PATH . 'include/admin-interfaces/notice-mu-loader-update.php' );
                        }
                        
                    if( ! is_writable( WPH_CACHE_PATH ))
                        {
                            $results['found_issues']   =   TRUE;
                            include ( WPH_PATH . 'include/admin-interfaces/notice-cache-path.php' );
                        }
                        
                    //check if the htaccess file is not writable
                    if( ! $this->rewrite_rules_applied() && ( $this->wph->server_htaccess_config    === TRUE || $this->wph->server_web_config   === TRUE ) &&   $results['found_issues'] === FALSE    &&  $results['critical_issues'] === FALSE )
                        {                            
                            $results['found_issues']   =   TRUE;
                            $results['critical_issues']    =   TRUE;
                            $rewrite_file_type = '';
                            if( $this->wph->server_htaccess_config    === TRUE )
                                $rewrite_file_type  =   '.htaccess';
                            
                            if( $this->wph->server_web_config     === TRUE )
                                $rewrite_file_type  =   'web.config';
                            
                            include ( WPH_PATH . 'include/admin-interfaces/notice-write-check.php' );
                        }    
                        
                    if ( empty ( get_option ( 'wph-environment-ignore-rewrite-test' ) ) && $results['found_issues'] === FALSE    &&  $results['critical_issues'] === FALSE )
                        {
                            if ( isset ( $this->wph->settings['rewrite_engine_version'] )   &&  version_compare( $this->wph->settings['rewrite_engine_version'], '1.1', '>=') )
                                {
                                    if ( ( isset ( $this->wph->settings['module_settings']['style_file_clean'] )  &&  $this->wph->settings['module_settings']['style_file_clean'] ==   'yes' ) 
                                        || 
                                        ( isset ( $this->wph->settings['module_settings']['child_style_file_clean'] )  &&  $this->wph->settings['module_settings']['child_style_file_clean'] ==   'yes' ) )
                                        { 
                                            $result = $this->test_sample_rewrite_php_file( );    
                                            if ( $result    === FALSE  ||  ! is_bool( $result ) )
                                                {
                                                    $results['found_issues']    =   TRUE;
                                                    $results['issues_type']     =   array ( '05' );
                                                    include ( WPH_PATH . 'include/admin-interfaces/notice-rewrite-test-static-file.php' );
                                                }
                                        }
                                        
                                    $result = $this->test_sample_rewrite_static_file( );    
                                    if ( $result    === FALSE  ||  ! is_bool( $result ) )
                                        {
                                            $results['found_issues']    =   TRUE;
                                            $results['critical_issues']    =   TRUE;
                                            include ( WPH_PATH . 'include/admin-interfaces/notice-rewrite-test.php' );
                                        }
                                        
                                    if ( $results['critical_issues']    ===   TRUE )
                                        {
                                            $this->wph->custom_permalinks_applied   =   FALSE;
                                            $this->wph->disable_ob_start_callback   =   TRUE;
                                        }
                                }
                                else
                                {
                                    $result = $this->test_sample_rewrite( );    
                                    if ( $result    === FALSE  ||  ! is_bool( $result ) )
                                        {
                                            $results['found_issues']   =   TRUE;
                                            $results['critical_issues']    =   TRUE;
                                            include ( WPH_PATH . 'include/admin-interfaces/notice-rewrite-test.php' );
                                        }
                                }
                        }    
                    
                    ?></ol><?php
                    
                    $errors   =   ob_get_clean();
                    
                    $results['errors']  =   $errors;
                    
                    return $results;
                    
                }
                
            
            function show_recovery()
                {
                    ?>
                            <p class="important framed"><span class="dashicons dashicons-warning important" alt="f534"></span> <?php _e('Copy the following link to a safe place. You can use it later to reset all plugin options if something goes wrong or lost the new login URL.',    'wp-hide-security-enhancer') ?><br /><span id="wph-recovery-link" onClick="WPH.selectText( 'wph-recovery-link' )"><?php echo trailingslashit ( home_url() ) ?>?wph-recovery=<?php  echo $this->get_recovery_code() ?></span></b></p>
                    <?php   
        
                }
                
            
            function get_write_check_string()
                {
                    $home_path      = $this->get_home_path();
                    
                    global $wp_rewrite;
                    
                    $result =   FALSE;
                                        
                    //check for .htaccess 
                    if ( $this->wph->server_htaccess_config === TRUE )
                        {
                            $file_path = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                            if(file_exists( $file_path ))
                                {
                                    if ( $markerdata = explode( "\n", implode( '', file( $file_path ) ) ));
                                        {
                                            foreach ( $markerdata as $markerline ) 
                                                {
                                                    if (strpos($markerline, '#WriteCheckString:') !== false)
                                                        {
                                                            $result =   trim(str_replace( '#WriteCheckString:',  '', $markerline));
                                                            break;
                                                        }
                                                }
                                        }
                                }
                        }
                    
                    //check for web.config
                    if ( $this->wph->server_web_config  === TRUE )
                        {
                            $file_path  =   $home_path . DIRECTORY_SEPARATOR . 'web.config';
                            if(file_exists( $file_path ))
                                {
                                    if ( $markerdata = explode( "\n", implode( '', file( $file_path ) ) ));
                                        {
                                            foreach ( $markerdata as $markerline ) 
                                                {
                                                    preg_match("'<rule name=\"wph-.*?<!-- WriteCheckString:([0-9_]+) --></rule>'si", $markerline, $matches);
                                                    if(isset($matches[1]))
                                                        {
                                                            $result =   $matches[1]; 
                                                        }
                                                        
                                                    if (!isset($matches[1])   &&  strpos($markerline, '<!-- WriteCheckString:') !== false)
                                                        {
                                                            $result =   trim(str_ireplace( '<!-- WriteCheckString:',  '', $markerline));
                                                            $result =   trim(str_replace( '-->',  '', $result));
                                                            $result =   trim($result);
                                                            
                                                            break;
                                                        }
                                                }
                                        }   
      
                                }
                                
                        }
                        
                    return $result;    
                    
                }
            
            
            function rewrite_rules_applied()
                {
                    $status = TRUE;
                    
                    if( isset($this->wph->settings['write_check_string'] )   && ! empty( $this->wph->settings['write_check_string'] ) )
                        {
                            $_write_check_string =   $this->get_write_check_string();
                            if( empty( $_write_check_string )  ||  $_write_check_string    !=  $this->wph->settings['write_check_string'])
                                $status   =   FALSE;
                        }
                                   
                    return $status;
                }
                
                
            
            /**
            * Try to access a specific sample url to test the rewritea functinality
            * 
            */
            function test_sample_rewrite( )
                {
                    
                    if( ! isset( $this->wph->settings['write_check_string'] )   ||  empty( $this->wph->settings['write_check_string'] ) )
                        return TRUE;
                    
                    $test_url   =   apply_filters( 'wp-hide/test_sample_rewrite/url', trailingslashit ( home_url() ) . 'rewrite_test_' . $this->wph->settings['write_check_string'] . '/' );   
                    $response   =   wp_remote_get( $test_url, array( 'sslverify' => false, 'timeout' => 30 ) );
                     
                    $response_message       =   '';
                    $messages['manual_check']     =  __( "Make a fix or manually check the ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a></b>, '.  __( "if the result is a JSON response (contains a name and description), the rewrites are working correctly on your site and you can", 'wp-hide-security-enhancer' ) .' <a href="' . $this->get_current_url()  . '&wph_environment=ignore-rewrite-test">' . __( "Ignore", 'wp-hide-security-enhancer' ) . '</a> ' . __( "this notification", 'wp-hide-security-enhancer' ) .'<br />';
                    $messages['manual_check']     .=  __( "Sample result, the appearance can be different from a browser to another:", 'wp-hide-security-enhancer' ) . '<br /><img src="' . WPH_URL . '/assets/images/rewrite-test-json-response.jpg" /><br />';
                    $messages['manual_check']     .=  __( "The Ignore action will be available until the next plugin options update.", 'wp-hide-security-enhancer' ) . '<br /><br />';
                    $messages['manual_check']     .=  __( "If the Test URL is not functional, the plugin will fail to provide specific features. Check your Hosting provider for more details regarding rewrites and how to activate on your account.", 'wp-hide-security-enhancer' ) . '<br />';
                            
                    if ( is_array( $response ) ) 
                        {
                            
                            if  ( ! isset( $response['response']['code'] ) )
                                return __( "The wp_remote_get() returns invalid Response Code", 'wp-hide-security-enhancer' );
                            
                            if  ( $response['response']['code'] !=  200 )
                                {
                                    if ( $response['response']['code'] ==  404 )
                                        {
                                            $home_path      = $this->get_home_path();
                                            
                                            //check if the .htaccess file include the test rewrite
                                            if ( $this->wph->server_htaccess_config === TRUE )
                                                {
                                                    $file_path = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                                                    if( ! file_exists( $file_path ) )
                                                        {
                                                            return __( "The .htaccess file does not appears to exists on the server. To fix, go to Settings > Permalinks and save once.", 'wp-hide-security-enhancer' );
                                                        }

                                                    if ( ! $this->file_check_for_marker( $file_path, 'rewrite_test_' . $this->wph->settings['write_check_string'] ) )
                                                        {
                                                            $response_message   =   __( "The test rewrite does not exist.", 'wp-hide-security-enhancer' ) . ' ' . __("To fix go to Settings > Permalinks and save once, the core will attempt to update the required rewrites. If the problem persists, check with your host support on the correct .htaccess file write permission.", 'wp-hide-security-enhancer');
                                                            
                                                            return $response_message;
                                                        }
                                                }
                                            
                                            //check for web.config
                                            if ( $this->wph->server_web_config  === TRUE )
                                                {
                                                    $file_path  =   $home_path . DIRECTORY_SEPARATOR . 'web.config';
                                                    if( ! file_exists( $file_path ) )
                                                        return __( "The wp_remote_get() returns a Not Found page, the web.config file does not appears to exists on the server. To fix, go to Settings > Permalinks and save once.", 'wp-hide-security-enhancer' );

                                                    if ( ! $this->file_check_for_marker( $file_path, 'rewrite_test_' . $this->wph->settings['write_check_string'] ) )
                                                        return __( "The wp_remote_get() returns a Not Found page, the test rewrite does not exist. To fix, go to Settings > Permalinks and save once. This can occour if you updated from an old plugin version. ", 'wp-hide-security-enhancer' );
                                                        
                                                }
                                            
                                            $response_message   =   __( "The wp_remote_get() returns a Not Found page, probably the Rewrites are not active on your server!", 'wp-hide-security-enhancer' );
                                            $response_message    .=  '<br />' . $messages['manual_check'];
                                            
                                            return $response_message;
                                        }
                                    
                                    if ( $response['response']['code'] ==  401 )
                                        {
                                            $response_message   =   __( "The wp_remote_get() returns a 401 error code, the request could not be authenticated. Does the site use an httpd password?", 'wp-hide-security-enhancer' );
                                            $response_message    .=  '<br />' . $messages['manual_check'];
                                            
                                            return $response_message;
                                        }
                                        
                                    if ( ! empty ( $response['response']['code'] ) )
                                        {
                                            $response_message    =   __( "The wp_remote_get() returns a", 'wp-hide-security-enhancer' ) . " " . $response['response']['code'] . " " . __( "error code", 'wp-hide-security-enhancer' );
                                            if ( ! empty ($response['response']['message'] ) )
                                                $response_message    .=  ":" . $response['response']['message'];
                                            
                                            $messages['server_check']     =  __( "A custom rewrite line has been inserted into your rewrite file for testing. The ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a></b> '.  __( "expected to return a JSON response (contains a name and description) The server instead replied a", 'wp-hide-security-enhancer' ) . ' <b class="highlight">' . $response['response']['code'] . '</b> ' . __( "error with the message", 'wp-hide-security-enhancer' ) . ' <b class="highlight">' . $response['response']['message'] . '</b><br />';
                                            $messages['server_check']     .= "<br />" . __( "In certain environments ( e.g. Cloudflare) the plugin may not be allowed to check the test rewrite automatically. If checking manually the ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a></b>, ' . __( "if the result is a valid JSON response (contains a name and description), you can", 'wp-hide-security-enhancer' ) .' <a href="' . $this->get_current_url()  . '&wph_environment=ignore-rewrite-test">' . __( "Ignore", 'wp-hide-security-enhancer' ) . '</a> ' . __( "this notification", 'wp-hide-security-enhancer' ) .'<br />';
                                            $messages['server_check']     .=  __( "Sample result, the appearance can be different from a browser to another:", 'wp-hide-security-enhancer' ) . '<br /><img src="' . WPH_URL . '/assets/images/rewrite-test-json-response.jpg" /><br />';
                                            $messages['server_check']     .=  __( "The Ignore action will be available until the next plugin options update.", 'wp-hide-security-enhancer' ) . '<br />';
                                            $messages['server_check']     .= "<br />" . __( "If manually checking the Test URL fails too, you need to get in touch with your server support for a fix. The rewrite engine is either disabled for your account or their internal set-up does not allow such rewrites. ", 'wp-hide-security-enhancer' );
                                                
                                            $response_message    .=  '<br />' . $messages['server_check'];
                                            
                                            return $response_message;
                                        }
                                        
                                    return __( "Unespected error code for wp_remote_get() call.", 'wp-hide-security-enhancer' );
                                }
                                
                            $body       =   json_decode( $response['body'] );
                            if ( $body  === null || !isset( $body->name ) )
                                return __( "The wp_remote_get() returns an invalid JSON data, probably the server blocks custom rewrites.", 'wp-hide-security-enhancer' );
                                
                                
                            return TRUE;
                                
                        }
                        else if ( is_a( $response, 'WP_Error' ))
                        {
                            $response_message    =   $response->get_error_message();
                            
                            $response_message    .=  '<br />' . $messages['manual_check'];
                            
                            return $response_message;
                        }
                          
                    return FALSE;
                
                }
                
                
            
            
            
            /**
            * Try to access a specific sample url to test the rewritea functinality
            * 
            */
            function test_sample_rewrite_php_file( )
                {
                    
                    if( ! isset( $this->wph->settings['write_check_string'] )   ||  empty( $this->wph->settings['write_check_string'] ) )
                        return TRUE;
                    
                    $test_url   =   apply_filters( 'wp-hide/test_sample_rewrite/url', trailingslashit ( home_url() ) . 'rewrite_test_' . $this->wph->settings['write_check_string'] . '/' );   
                    $response   =   wp_remote_get( $test_url, array( 'sslverify' => false, 'timeout' => 30 ) );

                    $response_message       =   '';
                    $messages['manual_check']     =  __( "Make a fix and manually check the ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a>.</b>';
                            
                    if ( is_array( $response ) ) 
                        {
                            
                            if  ( ! isset( $response['response']['code'] ) )
                                return __( "The wp_remote_get() returns invalid Response Code", 'wp-hide-security-enhancer' );
                            
                            if  ( $response['response']['code'] !=  200 )
                                {
                                    if ( $response['response']['code'] ==  404 )
                                        {
                                            $home_path      = $this->get_home_path();
                                            
                                            //check if the .htaccess file include the test rewrite
                                            if ( $this->wph->server_htaccess_config === TRUE )
                                                {
                                                    $file_path = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                                                    if( ! file_exists( $file_path ) )
                                                        {
                                                            return __( "The .htaccess file does not appears to exists on the server. To fix, go to Settings > Permalinks and save once.", 'wp-hide-security-enhancer' );
                                                        }

                                                    if ( ! $this->file_check_for_marker( $file_path, 'rewrite_test_' . $this->wph->settings['write_check_string'] ) )
                                                        {
                                                            $response_message   =   __( "The test rewrite does not exist.", 'wp-hide-security-enhancer' ) . ' ' . __("To fix go to Settings > Permalinks and save once, the core will attempt to update the required rewrites. If the problem persists, check with your host support on the correct .htaccess file write permission.", 'wp-hide-security-enhancer');
                                                            
                                                            return $response_message;
                                                        }
                                                }
                                            
                                            //check for web.config
                                            if ( $this->wph->server_web_config  === TRUE )
                                                {
                                                    $file_path  =   $home_path . DIRECTORY_SEPARATOR . 'web.config';
                                                    if( ! file_exists( $file_path ) )
                                                        return __( "The wp_remote_get() returns a <b>Not Found</b> page, the web.config file does not appears to exists on the server. To fix, go to Settings > Permalinks and save once.", 'wp-hide-security-enhancer' );

                                                    if ( ! $this->file_check_for_marker( $file_path, 'rewrite_test_' . $this->wph->settings['write_check_string'] ) )
                                                        return __( "The wp_remote_get() returns a <b>Not Found</b> page, the test rewrite does not exist. To fix, go to Settings > Permalinks and save once. This can occour if you updated from an old plugin version. ", 'wp-hide-security-enhancer' );
                                                        
                                                }
                                            
                                            $response_message   =   __( "The wp_remote_get() returns a <b>Not Found</b> page, probably the Rewrites are not active on your server!", 'wp-hide-security-enhancer' );
                                            $response_message    .=  '<br />' . $messages['manual_check'];
                                            
                                            return $response_message;
                                        }
                                    
                                    if ( $response['response']['code'] ==  401 )
                                        {
                                            $response_message   =   __( "The wp_remote_get() returns a 401 error code, the request could not be authenticated. Does the site use an httpd password?", 'wp-hide-security-enhancer' );
                                            $response_message    .=  '<br />' . $messages['manual_check'];
                                            
                                            return $response_message;
                                        }
                                        
                                    if ( ! empty ( $response['response']['code'] ) )
                                        {
                                            $response_message    =   __( "The wp_remote_get() returns a", 'wp-hide-security-enhancer' ) . " " . $response['response']['code'] . " " . __( "error code", 'wp-hide-security-enhancer' );
                                            if ( ! empty ($response['response']['message'] ) )
                                                $response_message    .=  ": <b>" . $response['response']['message'] . "</b>";
                                            
                                            $messages['server_check']     =  __( "Make a fix and manually check the ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a>.</b> ';
                                                
                                            $response_message    .=  '<br />' . $messages['server_check'];
                                            
                                            return $response_message;
                                        }
                                        
                                    return __( "Unespected error code for wp_remote_get() call.", 'wp-hide-security-enhancer' );
                                }
                                
                            $body       =   json_decode( $response['body'] );
                            if ( $body  === null || !isset( $body->name ) )
                                return __( "The wp_remote_get() returns an invalid JSON data, probably the server blocks custom rewrites.", 'wp-hide-security-enhancer' );
                                
                                
                            return TRUE;
                                
                        }
                        else if ( is_a( $response, 'WP_Error' ))
                        {
                            $response_message    =   $response->get_error_message();
                            
                            $response_message    .=  '<br />' . $messages['manual_check'];
                            
                            return $response_message;
                        }
                          
                    return FALSE;
                
                }
                
                
            
                
            /**
            * Try to access a static file to test the rewritea functinality
            * 
            */
            function test_sample_rewrite_static_file( )
                {
                    
                    if( ! isset( $this->wph->settings['write_check_string'] )   ||  empty( $this->wph->settings['write_check_string'] ) )
                        return TRUE;
                    
                    $test_url   =   apply_filters( 'wp-hide/test_sample_rewrite/static_file_url', trailingslashit ( home_url() ) . 'rewrite_test_static_file_' . $this->wph->settings['write_check_string'] . '/' );   
                    $response   =   wp_remote_get( $test_url, array( 'sslverify' => false, 'timeout' => 30 ) );
                    
                    $response_message       =   '';
                    $messages['manual_check']     =  __( "Make a fix or manually check the ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a></b>, '.  __( "if the result is a JSON response (contains a name and description), the rewrites are working correctly on your site and you can", 'wp-hide-security-enhancer' ) .' <a href="' . $this->get_current_url()  . '&wph_environment=ignore-rewrite-test">' . __( "Ignore", 'wp-hide-security-enhancer' ) . '</a> ' . __( "this notification", 'wp-hide-security-enhancer' ) .'<br />';
                    $messages['manual_check']     .=  __( "Sample result, the appearance can be different from a browser to another:", 'wp-hide-security-enhancer' ) . '<br /><img src="' . WPH_URL . '/assets/images/rewrite-test-json-response.jpg" /><br />';
                    $messages['manual_check']     .=  __( "The Ignore action will be available until the next plugin options update.", 'wp-hide-security-enhancer' ) . '<br /><br />';
                    $messages['manual_check']     .=  __( "If the Test URL is not functional, the plugin will fail to provide specific features. Check your Hosting provider for more details regarding rewrites and how to activate on your account.", 'wp-hide-security-enhancer' ) . '<br />';
                            
                    if ( is_array( $response ) ) 
                        {
                            
                            if  ( ! isset( $response['response']['code'] ) )
                                return __( "The wp_remote_get() returns invalid Response Code", 'wp-hide-security-enhancer' );
                            
                            if  ( $response['response']['code'] !=  200 )
                                {
                                    if ( $response['response']['code'] ==  404 )
                                        {
                                            $home_path      = $this->get_home_path();
                                            
                                            //check if the .htaccess file include the test rewrite
                                            if ( $this->wph->server_htaccess_config === TRUE )
                                                {
                                                    $file_path = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                                                    if( ! file_exists( $file_path ) )
                                                        {
                                                            return __( "The .htaccess file does not appears to exists on the server. To fix, go to Settings > Permalinks and save once.", 'wp-hide-security-enhancer' );
                                                        }

                                                    if ( ! $this->file_check_for_marker( $file_path, 'rewrite_test_' . $this->wph->settings['write_check_string'] ) )
                                                        {
                                                            $response_message   =   __( "The test rewrite does not exist.", 'wp-hide-security-enhancer' ) . ' ' . __("To fix go to Settings > Permalinks and save once, the core will attempt to update the required rewrites. If the problem persists, check with your host support on the correct .htaccess file write permission.", 'wp-hide-security-enhancer');
                                                            
                                                            return $response_message;
                                                        }
                                                }
                                            
                                            //check for web.config
                                            if ( $this->wph->server_web_config  === TRUE )
                                                {
                                                    $file_path  =   $home_path . DIRECTORY_SEPARATOR . 'web.config';
                                                    if( ! file_exists( $file_path ) )
                                                        return __( "The wp_remote_get() returns a <b>Not Found</b> page, the web.config file does not appears to exists on the server. To fix, go to Settings > Permalinks and save once.", 'wp-hide-security-enhancer' );

                                                    if ( ! $this->file_check_for_marker( $file_path, 'rewrite_test_' . $this->wph->settings['write_check_string'] ) )
                                                        return __( "The wp_remote_get() returns a <b>Not Found</b> page, the test rewrite does not exist. To fix, go to Settings > Permalinks and save once. This can occour if you updated from an old plugin version. ", 'wp-hide-security-enhancer' );
                                                        
                                                }
                                            
                                            $response_message   =   __( "The wp_remote_get() returns a <b>Not Found</b> page, probably the Rewrites are not active on your server!", 'wp-hide-security-enhancer' );
                                            $response_message    .=  '<br />' . $messages['manual_check'];
                                            
                                            return $response_message;
                                        }
                                    
                                    if ( $response['response']['code'] ==  401 )
                                        {
                                            $response_message   =   __( "The wp_remote_get() returns a 401 error code, the request could not be authenticated. Does the site use an httpd password?", 'wp-hide-security-enhancer' );
                                            $response_message    .=  '<br />' . $messages['manual_check'];
                                            
                                            return $response_message;
                                        }
                                        
                                    if ( ! empty ( $response['response']['code'] ) )
                                        {
                                            $response_message    =   __( "The wp_remote_get() returns a", 'wp-hide-security-enhancer' ) . " " . $response['response']['code'] . " " . __( "error code", 'wp-hide-security-enhancer' );
                                            if ( ! empty ($response['response']['message'] ) )
                                                $response_message    .=  ":" . $response['response']['message'];
                                            
                                            $messages['server_check']     =  __( "A custom rewrite line has been inserted into your rewrite file for testing. The ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a></b> '.  __( "expected to return a JSON response (contains a name and description) The server instead replied a", 'wp-hide-security-enhancer' ) . ' <b class="highlight">' . $response['response']['code'] . '</b> ' . __( "error with the message", 'wp-hide-security-enhancer' ) . ' <b class="highlight">' . $response['response']['message'] . '</b><br />';
                                            $messages['server_check']     .= "<br />" . __( "In certain environments ( e.g. Cloudflare) the plugin may not be allowed to check the test rewrite automatically. If checking manually the ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a></b>, ' . __( "if the result is a valid JSON response (contains a name and description), you can", 'wp-hide-security-enhancer' ) .' <a href="' . $this->get_current_url()  . '&wph_environment=ignore-rewrite-test">' . __( "Ignore", 'wp-hide-security-enhancer' ) . '</a> ' . __( "this notification", 'wp-hide-security-enhancer' ) .'<br />';
                                            $messages['server_check']     .=  __( "Sample result, the appearance can be different from a browser to another:", 'wp-hide-security-enhancer' ) . '<br /><img src="' . WPH_URL . '/assets/images/rewrite-test-json-response.jpg" /><br />';
                                            $messages['server_check']     .=  __( "The Ignore action will be available until the next plugin options update.", 'wp-hide-security-enhancer' ) . '<br />';
                                            $messages['server_check']     .= "<br />" . __( "If manually checking the Test URL fails too, you need to get in touch with your server support for a fix. The rewrite engine is either disabled for your account or their internal set-up does not allow such rewrites. ", 'wp-hide-security-enhancer' );
                                                
                                            $response_message    .=  '<br />' . $messages['server_check'];
                                            
                                            return $response_message;
                                        }
                                        
                                    return __( "Unespected error code for wp_remote_get() call.", 'wp-hide-security-enhancer' );
                                }
                                
                            $body       =   json_decode( $response['body'] );
                            if ( $body  === null )
                                {
                                    $messages['server_check']     = __( "The wp_remote_get() returns an invalid JSON data, probably the server blocks custom rewrites.", 'wp-hide-security-enhancer' );
                                    $messages['server_check']     .=  "<br />" . __( "Make a fix and manually check the ", 'wp-hide-security-enhancer' ) . '<b><a target="_blank" href="' . $test_url . '">' . __( "Test URL", 'wp-hide-security-enhancer' ) . '</a>.</b>';
                                    
                                    $response_message    =  $messages['server_check'];
                                            
                                    return $response_message;
                                }
                                
                                
                            return TRUE;
                                
                        }
                        else if ( is_a( $response, 'WP_Error' ))
                        {
                            $response_message    =   $response->get_error_message();
                            
                            $response_message    .=  '<br />' . $messages['manual_check'];
                            
                            return $response_message;
                        }
                          
                    return FALSE;
                
                }
                
                
            
            /**
            * Check a file for a specific marker
            * 
            * @param mixed $file_path
            * @param mixed $marker
            */
            function file_check_for_marker( $file_path, $marker )
                {
                    
                    if ( ! file_exists ( $file_path ) )
                        return FALSE;
                    
                    $markerdata = explode( "\n", implode( '', file( $file_path ) ) );
                    
                    if ( empty ( $markerdata ) )
                        return FALSE; 
                        
                    foreach ( $markerdata as $markerline ) 
                        {
                            if (strpos($markerline, $marker) !== false)
                                return TRUE;
                        }
                        
                    return FALSE;
                
                }  
            
            
            /**
            * Return rewrite base
            * 
            *
            */
            function get_rewrite_base( $saved_field_data, $left_slash   =   TRUE, $right_slash  =   TRUE )
                {
                    global $blog_id;
                    
                    $saved_field_data   =   $this->untrailingslashit_all($saved_field_data);
                    
                    $path           =   !empty($this->wph->default_variables['wordpress_directory']) ? trailingslashit( $this->wph->default_variables['wordpress_directory'] )  :   '';
                           
                    $rewrite_base   =   !empty($path) ? trailingslashit( $path ) . $saved_field_data : ( !empty($saved_field_data) ?  '/' .$saved_field_data : '' );
                    if( !empty($rewrite_base))
                        {
                            $rewrite_base   =   $this->untrailingslashit_all( $rewrite_base );
                            
                            if( $left_slash === TRUE )
                                $rewrite_base   =   '/' .   $rewrite_base;    
                                
                            if( $right_slash === TRUE )
                                $rewrite_base   =   $rewrite_base . '/';
                            
                        }
                    
                    return $rewrite_base;
                    
                }
                
            /**
            * Return rewrite to base
            *
            */
            function get_rewrite_to_base( $field_data, $left_slash   =   TRUE, $right_slash  =   TRUE, $append_path =   '')
                {

                    
                    $field_data         =   $this->untrailingslashit_all( $field_data );
                    
                    $path               =   '';
                    switch($append_path)
                        {
                            case 'site_path'    :
                                                    $path               =   !empty($this->wph->default_variables['site_relative_path']) ? trailingslashit( $this->wph->default_variables['site_relative_path'] )  :   '';
                                                    break;
                            
                            case 'wp_path'    :
                                                    $path              .=   !empty($this->wph->default_variables['wordpress_directory']) ? trailingslashit( $this->wph->default_variables['wordpress_directory'] )  :   '';
                                                    break;
                            case 'full_path'    :
                                                    $path               =   !empty($this->wph->default_variables['site_relative_path']) ? trailingslashit( $this->wph->default_variables['site_relative_path'] )  :   '';
                                                    $path              .=   !empty($this->wph->default_variables['wordpress_directory']) ? trailingslashit( $this->wph->default_variables['wordpress_directory'] )  :   '';
                                                    break;                        
                        }
                                   
                    $rewrite_to_base    =   !empty($path) ? trailingslashit( $path ) . $field_data : ( !empty( $field_data ) ?  '/' . $field_data : '' );
                    if( !empty($rewrite_to_base))
                        {
                            $rewrite_to_base   =   $this->untrailingslashit_all( $rewrite_to_base );
                            
                            if( $left_slash === TRUE )
                                $rewrite_to_base   =   '/' .   $rewrite_to_base;    
                                
                            if( $right_slash === TRUE )
                                $rewrite_to_base   =   $rewrite_to_base . '/';
                            
                        }
                    
                    return $rewrite_to_base;
                    
                }
            
            
            function insert_with_markers_on_top( $filename, $marker, $insertion)
                {
                    
                    if ( ! file_exists( $filename ) ) {
                        if ( ! is_writable( dirname( $filename ) ) ) {
                            return false;
                        }
                        if ( ! touch( $filename ) ) {
                            return false;
                        }
                    } elseif ( ! is_writeable( $filename ) ) {
                        return false;
                    }

                    if ( ! is_array( $insertion ) ) {
                        $insertion = explode( "\n", $insertion );
                    }

                    $start_marker = "# BEGIN {$marker}";
                    $end_marker   = "# END {$marker}";

                    $fp = fopen( $filename, 'r+' );
                    if ( ! $fp ) {
                        return false;
                    }

                    // Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
                    flock( $fp, LOCK_EX );

                    $lines = array();
                    while ( ! feof( $fp ) ) {
                        $lines[] = rtrim( fgets( $fp ), "\r\n" );
                    }

                    // Split out the existing file into the preceding lines, and those that appear after the marker
                    $pre_lines = $post_lines = $existing_lines = array();
                    $found_marker = $found_end_marker = false;
                    foreach ( $lines as $line ) {
                        if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
                            $found_marker = true;
                            continue;
                        } elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
                            $found_end_marker = true;
                            continue;
                        }
                        if ( ! $found_marker ) {
                            $pre_lines[] = $line;
                        } elseif ( $found_marker && $found_end_marker ) {
                            $post_lines[] = $line;
                        } else {
                            $existing_lines[] = $line;
                        }
                    }

                    // Check to see if there was a change
                    if ( $existing_lines === $insertion ) {
                        flock( $fp, LOCK_UN );
                        fclose( $fp );

                        return true;
                    }

                    
                    // Generate the new file data
                    if($found_marker && $found_end_marker)
                        {
                            $new_file_data = implode( "\n", array_merge(
                                $pre_lines,
                                array( $start_marker ),
                                $insertion,
                                array( $end_marker ),
                                $post_lines
                            ) );
                        }
                        else
                        {
                            
                            $new_file_data = implode( "\n", array_merge(
                                array( $start_marker ),
                                $insertion,
                                array( $end_marker ),
                                $pre_lines
                            ) );        
                            
                        }

                    // Write to the start of the file, and truncate it to that length
                    fseek( $fp, 0 );
                    $bytes = fwrite( $fp, $new_file_data );
                    if ( $bytes ) {
                        ftruncate( $fp, ftell( $fp ) );
                    }
                    fflush( $fp );
                    flock( $fp, LOCK_UN );
                    fclose( $fp );

                    return (bool) $bytes;    
                    
                    
                }
            
            
            function clean_with_markers( $filename, $markers)
                {
                    
                    if ( ! file_exists( $filename ) ) {
                        if ( ! is_writable( dirname( $filename ) ) ) {
                            return false;
                        }
                        if ( ! touch( $filename ) ) {
                            return false;
                        }
                    } elseif ( ! is_writeable( $filename ) ) {
                        return false;
                    }
              
                    $start_marker = $markers['start'];
                    $end_marker   = $markers['end'];

                    $fp = fopen( $filename, 'r+' );
                    if ( ! $fp ) {
                        return false;
                    }

                    // Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
                    flock( $fp, LOCK_EX );

                    $lines = array();
                    while ( ! feof( $fp ) ) {
                        $lines[] = rtrim( fgets( $fp ), "\r\n" );
                    }

                    // Split out the existing file into the preceding lines, and those that appear after the marker
                    $pre_lines = $post_lines = $existing_lines = array();
                    $found_marker = $found_end_marker = false;
                    foreach ( $lines as $line ) {
                        if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
                            $found_marker = true;
                            continue;
                        } elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
                            $found_end_marker = true;
                            continue;
                        }
                        if ( ! $found_marker ) {
                            $pre_lines[] = $line;
                        } elseif ( $found_marker && $found_end_marker ) {
                            $post_lines[] = $line;
                        } else {
                            $existing_lines[] = $line;
                        }
                    }
                         
                    // Generate the new file data
                    if($found_marker && $found_end_marker)
                        {
                            $new_file_data = implode( "\n", array_merge(
                                $pre_lines,
                                $post_lines
                            ) );
                            
                            // Write to the start of the file, and truncate it to that length
                            fseek( $fp, 0 );
                            $bytes = fwrite( $fp, $new_file_data );
                            if ( $bytes ) {
                                ftruncate( $fp, ftell( $fp ) );
                            }
                            fflush( $fp );
                            flock( $fp, LOCK_UN );
                            fclose( $fp );

                            return (bool) $bytes; 
                            
                        }
                
                    return FALSE;   
                    
                    
                }
            
            
            
            /**
            * Check if the plugin started through MU plugin loader
            * 
            */
            function is_muloader()
                {
                    
                    if (defined('WPH_MULOADER'))
                        return TRUE;
                        
                    //check if the file actually exists
                    if( file_exists(WPMU_PLUGIN_DIR . '/wp-hide-loader.php' ))
                        return  TRUE;
                        
                    return FALSE;
                       
                }
            
                
            /**
            * 
            * Check if theme is is customize mode
            *     
            */
            function is_theme_customize()
                {
                    
                    if (    strpos($_SERVER['REQUEST_URI'] ,'customize.php')   !== FALSE    )
                        return TRUE;
                        
                    if (    isset($_POST['wp_customize'])  && sanitize_text_field($_POST['wp_customize'])   ==  "on" )   
                        return TRUE;        
                    
                    return FALSE;
                    
                }
                
            
            /**
            * return settings
            *     
            */
            function get_settings()
                {
                    $settings   =   get_option('wph_settings');
                    
                    $defaults   = array (
                                            'module_settings'   =>  array(),
                                            'recovery_code'     =>  ''
                                        );
       
                    
                    $settings   =   wp_parse_args( $settings, $defaults );
                    
                    $settings   =   apply_filters('wp-hide/get_settings', $settings);
                    
                    return $settings;
                    
                }
                
            
            
            /**
            * Return a Module Item value setting
            * 
            * @param mixed $item_id
            */
            function get_module_item_setting( $item_id )
                {
                    
                    $settings   =   $this->get_settings();
                    
                    $value      =   isset($settings['module_settings'][ $item_id ])  ?   $settings['module_settings'][ $item_id] :   '';
                    
                    $value      =   apply_filters('wp-hide/get_module_item_setting', $value, $item_id);
                    
                    return $value;
                    
                }
                
            
            /**
            * Save the settings
            *     
            * @param mixed $settings
            */
            function update_settings($settings)
                {
                    update_option('wph_settings', $settings);
                }
                
            
            /**
            * Get path from url relative to domain root
            *     
            * @param mixed $url
            * @param mixed $is_file_path
            * @param mixed $relative_to_wordpress_directory
            */
            function get_url_path($url, $is_file_path   =   FALSE, $relative_to_wordpress_directory    =   FALSE)
                {
                    if(!$is_file_path)
                        $url            =   trailingslashit(    $url    );
                        
                    $url_parse      =   parse_url(  $url   );
                           
                    $path           =   $url_parse['path'];
                    if( $relative_to_wordpress_directory   === TRUE &&  $this->wph->default_variables['wordpress_directory']    !=  '/') 
                        {
                            $path   =   $this->string_left_replacement( $path , trailingslashit ( $this->wph->default_variables['wordpress_directory'] )) ;
                        }
                    
                    if(!$is_file_path)
                        $path           =   trailingslashit(    $path   );
                    
                    if($path    !=  '/' && strlen($path) > 1)
                        {
                            $path   =   ltrim($path, '/');
                            $path   =   '/' .   $path;
                        }
                    
                    if(isset($url_parse['query']))
                        $path   .=  '?' .   $url_parse['query'];
                    
                    $path   =   str_replace( '\\', '/', $path);
                    
                    return $path;
                    
                }
                
            
            /**
            * return the url relative to domain root
            * 
            * @param mixed $url
            */
            function get_url_path_relative_to_domain_root($url)
                {
                    
                    $url    =   str_replace(trailingslashit(  home_url()  ), "" , $url);
                       
                    return $url;
                    
                }
                
                
            /**
            * Replace all slashes from begining and the end of string
            * 
            * @param mixed $value
            */
            function untrailingslashit_all($value)
                {
                    $value  =   ltrim(rtrim($value, "/"),  "/");
                    
                    return $value;
                }
                
            /**
            * Replace a prefix from the beginning of a text
            *     
            * @param mixed $string
            * @param mixed $prefix
            */
            function string_left_replacement($string, $prefix)    
                {
                    if (substr($string, 0, strlen($prefix)) == $prefix) 
                        {
                            $string = (string) substr($string, strlen($prefix));
                        }
                        
                    return $string;
                        
                }
            
            
            /**
            * saniteize including a possible extension
            * 
            * @param mixed $value
            */    
            function sanitize_file_path_name($value)
                {
                    $value  =   trim($value);
                    
                    if(empty($value))
                        return $value;
                    
                    //check for any extension
                    $pathinfo   =   pathinfo($value);
                    
                    $dirname    =   (!empty($pathinfo['dirname'])    &&  $pathinfo['dirname']    !=  '.')  ?    $pathinfo['dirname']    :   '';
                    $path       =   !empty($dirname)    ?   trailingslashit($dirname)   .   $pathinfo['filename']   :   $pathinfo['filename'];   
                    
                    $parts  =   explode("/",    $path);
                    $parts  =   array_filter($parts);
                    
                    foreach($parts  as  $key    =>  $part_item)
                        {
                            $parts[$key]    =   sanitize_title($part_item);
                        }
                        
                    $value  =   implode("/", $parts);
                    
                    $value  .=   !empty($pathinfo['extension']) ?   '.' . $pathinfo['extension'] :   '';  
                    
                    $value  =   strtolower($value);
                    
                    return $value;
                }
                
            
            /**
            * make sure there's a php extension included within the slug
            * 
            * @param mixed $value
            * @return mixed
            */
            function php_extension_required($value)
                {
                    $value  =   trim($value);
                    
                    if($value   ==  '')
                        return '';
                    
                    $extension  =   substr($value, -4);
                    if(strtolower($extension)   !=  '.php')
                        $value  .=  '.php';    
                                        
                    return $value;
                }
                
            
            /**
            * return current url
            *     
            */
            function get_current_url()
                {
                    
                    $current_url    =   'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    
                    return  $current_url;
                                        
                }
                
            
            
            /**
            * Add replacement withint the list
            * 
            * @param mixed $old_url
            * @param mixed $new_url
            * @param mixed $priority
            */
            function add_replacement($old_url, $new_url, $priority  =   'normal')
                {
                           
                    if($this->replacement_exists($old_url))
                        return;
                        
                    $this->wph->urls_replacement[ $priority ][ $old_url ]  =   $new_url;   
                    
                }
                
            
            /**
            * Return whatever a replacement exists or not
            * The old url should be provided
            *     
            * @param mixed $old_url
            */
            function replacement_exists($old_url)
                {
                    
                    if(count($this->wph->urls_replacement)  <   1)
                        return FALSE;
                    
                    foreach($this->wph->urls_replacement    as  $priority   =>  $replacements_block)
                        {
                            if(isset($this->wph->urls_replacement[$priority][ $old_url ]))
                                return TRUE;
                        }
                        
                    return FALSE;
                                        
                }
                
                
            
            /**
            * Return a list of replacements
            * 
            */
            function get_replacement_list()
                {
                    
                    $replacements   =   array();
                    
                    if(count($this->wph->urls_replacement)  <   1)
                        return $replacements;
                    
                    foreach($this->wph->urls_replacement    as  $priority   =>  $replacements_block)
                        {
                            if(!is_array($replacements_block)   ||  count($replacements_block) < 1)
                                continue;
                            
                            foreach($replacements_block as  $old_url   =>  $new_url)
                                {
                                    $replacements[ $old_url ] =   $new_url;
                                }
                        }
                        
                    return $replacements;   
                    
                }
            
            
            /**
            * Replace the urls within given content
            * 
            * @param mixed $text
            * @param mixed $replacements
            */
            function content_urls_replacement($text, $replacements)
                {
                    //process the replacements
                    if( count($replacements)  <   1)
                        return $text;
                    
                    //exclude scheme to match urls without it
                    $_replacements                      =   array();
                    //no protocol
                    $_replacements_np                   =   array();
                    
                    //single quote ; double quote
                    $_relative_url_replacements_sq      =   array();
                    $_relative_url_replacements_dq      =   array();
                    
                    //single quote ; double quote / domain url / domain ssl
                    $_relative_domain_url_replacements_sq  =   array();
                    $_relative_domain_url_replacements_dq  =   array();
                    //$_relative_domain_url_replacements_ssl_sq  =   array();
                    //$_relative_domain_url_replacements_ssl_dq  =   array();
                    
                    $home_url           =   home_url();
                    $home_url_parsed    =   parse_url($home_url);
                    $domain_url         =   'http://' . $home_url_parsed['host'];
                    $domain_url_ssl     =   'https://' . $home_url_parsed['host'];  
                    
                    /**
                    * 
                    * CDN
                    * 
                    */
                    $CDN_url    =   $this->get_module_item_setting('cdn_url');;
                    if  ( ! empty ( $CDN_url ) )
                        {
                            foreach($replacements   as $old_url =>  $new_url)
                                {
                                    $replacements[ str_replace($home_url_parsed['host'], $CDN_url, $old_url) ]  =   str_replace($home_url_parsed['host'], $CDN_url, $new_url);
                                }
                        } 
                    
                    /**
                    * Preserve absolute paths
                    * 
                    */
                    $text   =   str_ireplace( ABSPATH, '%WPH-PLACEHOLDER-PRESERVE-ABSPATH%', $text);
                    //jsonencoded
                    $text   =   str_ireplace( trim(json_encode(ABSPATH), '"'), '%WPH-PLACEHOLDER-PRESERVE-JSON-ABSPATH%', $text);
                    //urlencode
                    $text   =   str_ireplace( trim(urlencode(ABSPATH), '"'), '%WPH-PLACEHOLDER-PRESERVE-URLENCODE-ABSPATH%', $text);
                    
                    foreach($replacements   as $old_url =>  $new_url)
                        {
                            //add quote to make sure it's actualy a link value and is right at the start of text
                            $_relative_url_replacements_dq[ '"' . str_ireplace(   $home_url,   "", $old_url)   ] =   '"' . str_ireplace(   $home_url,   "", $new_url);
                            $_relative_url_replacements_sq[ "'" . str_ireplace(   $home_url,   "", $old_url)   ] =   "'" . str_ireplace(   $home_url,   "", $new_url);
                            
                            $_relative_domain_url_replacements_dq[ '"' . str_ireplace(   array( $domain_url, $domain_url_ssl ),   "", $old_url)   ] =   '"' . str_ireplace(   array( $domain_url, $domain_url_ssl ),   "", $new_url);
                            $_relative_domain_url_replacements_sq[ "'" . str_ireplace(   array( $domain_url, $domain_url_ssl ),   "", $old_url)   ] =   "'" . str_ireplace(   array( $domain_url, $domain_url_ssl ),   "", $new_url);
                            
                            //match urls without protocol
                            $_old_url    =   str_ireplace(   array('http:', 'https:'),   "", $old_url);
                            $_new_url    =   str_ireplace(   array('http:', 'https:'),   "", $new_url);
                            
                            $_replacements_np[$_old_url]    =   $_new_url;
                            
                            $_old_url    =   str_ireplace(   array('http://', 'https://'),   "", $old_url);
                            $_new_url    =   str_ireplace(   array('http://', 'https://'),   "", $new_url);
                            
                            $_replacements[$_old_url]    =   $_new_url;
                        }
                    
                    
                    /**
                    * Main replaments
                    * 
                    * @var mixed
                    */
                    $text =   str_ireplace(    array_keys($_replacements_np), array_values($_replacements_np)  ,$text   );
                    $text =   str_ireplace(    array_keys($_replacements), array_values($_replacements)  ,$text   );
                    
                    
                    /**
                    * Relative tp domain urls replacements;  using subfolder e.g. 127.0.0.1/wp01/wordpress_site, this will be /wp01/wordpress_site
                    * 
                    * @var mixed
                    */
                    $text =   str_ireplace(    array_keys($_relative_domain_url_replacements_sq), array_values($_relative_domain_url_replacements_sq)  ,$text   );
                    $text =   str_ireplace(    array_keys($_relative_domain_url_replacements_dq), array_values($_relative_domain_url_replacements_dq)  ,$text   );
                                        
                    
                    /**
                    * Relative urls replacements
                    * @var mixed
                    */
                    //single quote
                    $text =   str_ireplace(    array_keys($_relative_url_replacements_sq), array_values($_relative_url_replacements_sq)  ,$text   );
                    $text =   str_ireplace(    array_keys($_relative_url_replacements_dq), array_values($_relative_url_replacements_dq)  ,$text   );
                    
                    
                    //check for json encoded urls
                    foreach($_replacements_np   as $old_url =>  $new_url)
                        {
                            $old_url    =   trim(json_encode($old_url), '"');   
                            $new_url    =   trim(json_encode($new_url), '"'); 
                            
                            $text =   str_ireplace(    $old_url, $new_url  ,$text   );
                            
                            $old_url    =   trim(urlencode($old_url), '"');   
                            $new_url    =   trim(urlencode($new_url), '"'); 
                            
                            $text =   str_ireplace(    $old_url, $new_url  ,$text   );
                        }
                       
                    //check for json encoded urls
                    foreach($_replacements   as $old_url =>  $new_url)
                        {
                            $old_url    =   trim(json_encode($old_url), '"');   
                            $new_url    =   trim(json_encode($new_url), '"'); 
                            
                            $text =   str_ireplace(    $old_url, $new_url  ,$text   );
                            
                            $old_url    =   trim(urlencode($old_url), '"');   
                            $new_url    =   trim(urlencode($new_url), '"'); 
                            
                            $text =   str_ireplace(    $old_url, $new_url  ,$text   );
                        }
                        
                    //check for url encoded urls
                    //Be aware !! if use a slug similar to domain or part of it, it will do wrong replacement.  
                    foreach( $_relative_domain_url_replacements_dq   as $old_url =>  $new_url )
                        {
                            /*
                            *   JSON always use double quotes
                            *   use double quote type at the start of the string (per json encodync) to avoid replacing for non-local domains    
                            *   e.g. "collectionThumbnail":"https:\/\/wp.envatoextensions.com\/kit-57\/wp-content\/uploads\/sites\/60\/2018\/08\/screenshot-20-1540279812-300x997.jpg"
                            */
                            $text   =   str_ireplace(   '"' .  trim( json_encode( trim( $old_url, '"')), '"' ) , '"' . trim( json_encode( trim ( $new_url, '"')), '"' )  ,$text   );
                            
                            $text   =   str_ireplace(    trim( urlencode(trim( $old_url, '"')), '"' ) ,  trim( urlencode(trim ( $new_url, '"')), '"' )  ,$text   );
                        }
                    
                    $text   =   apply_filters( 'wp-hide/content_urls_replacement', $text, $_replacements ); 
                             
                    /**
                    * Restore absolute paths
                    */                      
                    //Preserve absolute paths
                    $text   =   str_ireplace( '%WPH-PLACEHOLDER-PRESERVE-ABSPATH%', ABSPATH, $text);
                    //jsonencoded
                    $text   =   str_ireplace( '%WPH-PLACEHOLDER-PRESERVE-JSON-ABSPATH%', trim(json_encode(ABSPATH), '"'), $text);
                    //urlencode
                    $text   =   str_ireplace( '%WPH-PLACEHOLDER-PRESERVE-URLENCODE-ABSPATH%', trim(urlencode(ABSPATH), '"'), $text);
                                                          
                    return $text;   
                }
                
            
            function default_scripts_styles_replace($object, $replacements)
                {
                    //update default dirs
                    if(isset($object->default_dirs))
                        {
                            foreach($object->default_dirs    as  $key    =>  $value)
                                {
                                    $object->default_dirs[$key]  =   str_replace(array_keys($replacements), array_values($replacements), $value);
                                }
                        }
                       
                    foreach($object->registered    as  $script_name    =>  $script_data)
                        {
                            $script_data->src   =   str_replace(array_keys($replacements), array_values($replacements), $script_data->src);
                            
                            $object->registered[$script_name]  =   $script_data;      
                        }
                        
                    return $object;
                }
                
                
            function check_headers_content_type($header_name, $header_value)
                {
                    
                    $headers    =   headers_list();
                    
                    foreach($headers    as  $header)
                        {
                            if(stripos($header, $header_name)   !== FALSE)
                                {
                                    if(stripos($header, $header_value)   !== FALSE)
                                        return TRUE;     
                                }
                        }
                        
                    
                    return FALSE;
                
                }
                
                
            function array_sort_by_processing_order($a, $b)
                {
                    return $a['processing_order'] - $b['processing_order'];
                }
            
            
            
            /**
            * Return the recovey code
            * 
            */
            function get_recovery_code()
                {
                    
                    $settings   =   $this->get_settings();
                    if(!isset($settings['recovery_code'])   ||  empty($settings['recovery_code']))
                        {
                            $recovery_code  =   $this->generate_recovery_code();
                        }
                        else
                        $recovery_code  =   $settings['recovery_code'];
                    
                    
                    return $recovery_code;
                }
            
            
            /**
            * Generate a recovery code
            * 
            */
            function generate_recovery_code()
                {
                    
                    $settings   =   $this->get_settings();   
                    
                    $recovery_code  =   substr( md5(rand(1,9999) . microtime()), 0, 10 );
                    
                    $settings['recovery_code']  =   $recovery_code;
                    
                    $this->update_settings($settings);
                    
                    //send the link to admin
                    $this->send_recovery_email();
                    
                    return $recovery_code;
                }
                
                
            function send_recovery_email( )
                {
                    
                    $to         =   get_option('admin_email');
                    $subject    =   get_option('blogname') . ' - WP Hide Recovery Link';
                    $message    =   __('Hello',  'wp-hide-security-enhancer') . ", \n\n" 
                                    . __('This is a system automated message to inform that you can always use a recovery link if something go wrong',  'wp-hide-security-enhancer') . ": " . home_url() . '?wph-recovery='.  $this->get_recovery_code() . "\n\n"
                                    . __('Please keep this URL to a safe place.',  'wp-hide-security-enhancer') . ".";
                    $headers = 'From: '.  get_option('blogname') .' <'.  get_option('admin_email')  .'>' . "\r\n";
                    
                    if ( ! function_exists( 'wp_mail' ) ) 
                        require_once ABSPATH . WPINC . '/pluggable.php';
                        
                    wp_mail( $to, $subject, $message, $headers );   
                    
                }
                
                
            /**
            * Trigger the recovery actions
            * 
            */
            function do_recovery()
                {
                    //prevent hammering
                    sleep(2);
                    
                    //feetch a new set of settings
                    $settings   =   $this->get_settings();
                    
                    $wph_recovery   =   isset($_GET['wph-recovery']) ?  preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['wph-recovery'] )  :   '';
                    if(empty($wph_recovery) ||  $wph_recovery   !=  $this->wph->settings['recovery_code'])
                        return;
                    
                    $resetOnlyHeaders   =   isset ( $_GET['reset_headers'] )    &&  $_GET['reset_headers']  ==  '1'    ?      TRUE: FALSE;
                    
                    if ( $resetOnlyHeaders === TRUE )
                        {
                            $modules_settings   =   $settings['module_settings'];
                            
                            $headers    =   array ( 
                                            'cross_origin_embedder_policy',
                                            'cross_origin_opener_policy',
                                            'cross_origin_resource_policy',
                                            'referrer-policy',
                                            'x_content_type_options',
                                            'x_download_options',
                                            'x_frame_options',
                                            'x_permitted_cross_domain_policies',
                                            'x_xss_protection'                                            
                                            );
                            foreach ( $headers as $header )
                                {
                                    if ( ! isset ( $modules_settings[ $header ] )   ||  ! is_array ( $modules_settings[ $header ]  ) )
                                        $modules_settings[ $header ]   =   array (
                                                                                'enabled'   =>  'no' 
                                                                                );
                                    
                                    $modules_settings[ $header ]['enabled']    =   'no';
                                }
                                
                            $settings['module_settings']    =   $modules_settings;
                        }
                        else
                        $settings['module_settings']   =   $this->reset_settings();
                    
                    //update the settings
                    $this->update_settings( $settings );
                    $this->wph->settings    =   $settings;
                    
                    //available for mu-plugins
                    do_action('wph/do_recovery');                    
                    
                    //add filter for rewriting the rules
                    if ( $resetOnlyHeaders === TRUE )
                        add_action('wp_loaded',  array($this,    'wp_loaded_trigger_do_recovery_headers'));
                        else
                        add_action('wp_loaded',  array($this,    'wp_loaded_trigger_do_recovery'));
                    
                }
                
                
            
            function reset_settings()
                {
                    
                    $settings   =   array();
                        
                    foreach($this->wph->modules   as  $module)
                        {
                            //proces the fields
                            $module_settings    =   $this->filter_settings(   $module->get_module_components_settings(), TRUE    );
                            
                            foreach($module_settings as $module_setting)
                                {
                                    if(isset($module_setting['type'])   &&  $module_setting['type'] ==  'split')
                                        continue;
                                    
                                    $field_name =   $module_setting['id'];
                                    
                                    $value      =   isset($module_setting['default_value'])  ?   $module_setting['default_value'] :   '';
                         
                                    //save the value
                                    $settings[ $field_name ]  =   $value;
                                }   
                            
                        }
                    
                    return $settings;
                    
                }
            
                
            function wp_loaded_trigger_do_recovery()
                {
                    /** WordPress Misc Administration API */
                    require_once(ABSPATH . 'wp-admin/includes/misc.php');
                    
                    /** WordPress Administration File API */
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    
                    flush_rewrite_rules();
                    
                    delete_option( 'wph-previous-login-url' );
                        
                    ?><!DOCTYPE html>
                    <html lang="en-US">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <meta name="viewport" content="width=device-width">
                        <meta name='robots' content='noindex,follow' />
                        <title>WP-Hide - Recovery</title>
                        <style type="text/css">
                            html{background:#f1f1f1}body{background:#fff;color:#444;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.13);box-shadow:0 1px 3px rgba(0,0,0,.13)}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font-size:24px;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}#error-page .wp-die-message,#error-page p{font-size:14px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:14px}a{color:#0073aa}a:active,a:hover{color:#006799}a:focus{color:#124964;-webkit-box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);outline:0}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:13px;line-height:2;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:0 1px 0 #ccc;box-shadow:0 1px 0 #ccc;vertical-align:top}.button.button-large{height:30px;line-height:2.15384615;padding:0 12px 2px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#23282d}.button:focus{border-color:#5b9dd9;-webkit-box-shadow:0 0 3px rgba(0,115,170,.8);box-shadow:0 0 3px rgba(0,115,170,.8);outline:0}.button:active{background:#eee;border-color:#999;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}    
                        </style>
                    </head>                            
                    <body>
  
                        <h1>WP-Hide - <?php _e('Recovery', 'wp-hide-security-enhancer') ?></h1>
                        <p><b><?php _e('The plugin options have been reset successfully.', 'wp-hide-security-enhancer') ?></b></p>
                        <br />
                        <?php
                        
                        if (  $this->wph->server_htaccess_config  === TRUE )
                            {
                                ?>
                                <p><?php _e('Ensure the .htaccess file does not contain any WP-Hide rewrite lines. The plugin already attempts to clear the lines, if the operation fails, they are required to be removed manually. ', 'wp-hide-security-enhancer') ?></p>
                                <?php 
                            }
                            
                        if (  $this->wph->server_web_config  === TRUE )
                            {
                                ?>
                                <p><?php _e('Ensure the web.config file does not contain any WP-Hide rewrite lines. The plugin already attempts to clear the lines, if the operation fails, they are required to be removed manually. ', 'wp-hide-security-enhancer') ?></p>
                                <?php 
                            }
                               
                        ?>
                                                    
                        <p><br /></p>
                        <p><a class="button" href="<?php echo get_site_url() ?>"><?php _e('Continue to your Site', 'wp-hide-security-enhancer') ?></a></p>
                 
                    
                    </body>
                    </html>
                    <?php
 
                    wp_logout();
                        
                    die();
                    
                }
                
                
            function wp_loaded_trigger_do_recovery_headers()
                {
                    /** WordPress Misc Administration API */
                    require_once(ABSPATH . 'wp-admin/includes/misc.php');
                    
                    /** WordPress Administration File API */
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    
                    flush_rewrite_rules();
                        
                    ?><!DOCTYPE html>
                    <html lang="en-US">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <meta name="viewport" content="width=device-width">
                        <meta name='robots' content='noindex,follow' />
                        <title>WP-Hide - <?php _e('Recovery', 'wp-hide-security-enhancer') ?></title>
                        <style type="text/css">
                            html{background:#f1f1f1}body{background:#fff;color:#444;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.13);box-shadow:0 1px 3px rgba(0,0,0,.13)}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font-size:24px;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}#error-page .wp-die-message,#error-page p{font-size:14px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:14px}a{color:#0073aa}a:active,a:hover{color:#006799}a:focus{color:#124964;-webkit-box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);outline:0}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:13px;line-height:2;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:0 1px 0 #ccc;box-shadow:0 1px 0 #ccc;vertical-align:top}.button.button-large{height:30px;line-height:2.15384615;padding:0 12px 2px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#23282d}.button:focus{border-color:#5b9dd9;-webkit-box-shadow:0 0 3px rgba(0,115,170,.8);box-shadow:0 0 3px rgba(0,115,170,.8);outline:0}.button:active{background:#eee;border-color:#999;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}    
                        </style>
                    </head>                            
                    <body>
  
                        <h1>WP-Hide - <?php _e('Headers Recovery', 'wp-hide-security-enhancer') ?></h1>
                        <p><b><?php _e('The plugin Headers options have been disabled successfully.', 'wp-hide-security-enhancer') ?></b></p>
                        <br />
                        <?php
                        
                        if (  $this->wph->server_htaccess_config  === TRUE )
                            {
                                ?>
                                <p><?php _e('Ensure the .htaccess file does not contain any rewrite Header lines. The plugin already attempted to clear the data. If the operation fails, manual removal is required.', 'wp-hide-security-enhancer') ?></p>
                                <?php 
                            }
                            
                        if (  $this->wph->server_web_config  === TRUE )
                            {
                                ?>
                                <p><?php _e('Ensure the web.config file does not contain any rewrite Header lines. The plugin already attempted to clear the data. If the operation fails, manual removal is required.', 'wp-hide-security-enhancer') ?></p>
                                <?php 
                            }
                            
                        if (  $this->wph->server_nginx_config  === TRUE )
                            {
                                
                                //Check if use Wpengine
                                if (    $this->wph->functions->server_is_wpengine() )
                                    {
                                        ?>
                                        <p><?php _e('Your site use WPEngine! You need to get in touch with live support and ask to remove the custom Nginx Header rewrite code from your account.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    }
                                else if (    $this->wph->functions->server_is_kinsta() )
                                    {
                                        ?>
                                        <p><?php _e('Your site use Kinsta! You need to get in touch with live support and ask to remove the custom Nginx Header rewrite code from your account.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    }
                                    else
                                    {
                                
                                        ?>
                                        <p><?php _e('Check with your Nginx config file located usually at', 'wp-hide-security-enhancer') ?> /etc/nginx/sites-available/ <?php _e('and remove any Header rewrite rules within', 'wp-hide-security-enhancer') ?> <strong># BEGIN WP Hide & Security Enhancer</strong> <?php _e('and', 'wp-hide-security-enhancer') ?> <strong># END WP Hide & Security Enhancer</strong></p>
                                        <p><?php _e('After the configuration file update', 'wp-hide-security-enhancer') ?>, <strong><?php _e('Test', 'wp-hide-security-enhancer') ?></strong> <?php _e('the new data using ', 'wp-hide-security-enhancer') ?> <strong>nginx -t</strong>. <?php _e('If successfully compiled, restart the Nginx service.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    } 
                            }
                            
                        
                        
                        ?>
                                                    
                        <p><br /></p>
                        <p><a class="button" href="<?php echo get_site_url() ?>"><?php _e('Continue to your Site', 'wp-hide-security-enhancer') ?></a></p>
                 
                    
                    </body>
                    </html>
                    <?php
                     
                    wp_logout();
                        
                    die();
      
                }
                
            
            function create_headers_sample_setup()
                {
                    
                    $nonce  =   $_POST['wph-interface-nonce'];
                    if ( ! wp_verify_nonce( $nonce, 'wph/interface_fields' ) )
                        return FALSE;
                        
                    //only for admins
                    If ( !  current_user_can ( 'manage_options' ) )
                        return FALSE;
                        
                    $screen_slug  =   isset ( $_GET['page'] )         ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['page'] )         :   '';
                    $tab_slug     =   isset ( $_GET['component'] )    ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['component'] )    :   '';
                    
                    $site_settings      =   $this->get_settings();
                    $modules_settings   =   $site_settings['module_settings'];
                    
                    //reset the options
                    $headers    =   array ( 
                                            'cross_origin_embedder_policy',
                                            'cross_origin_opener_policy',
                                            'cross_origin_resource_policy',
                                            'referrer_policy', 
                                            'x_content_type_options',
                                            'x_download_options',
                                            'x_frame_options',
                                            'x_permitted_cross_domain_policies',
                                            'x_xss_protection'                                            
                                            );
                    foreach ( $headers as $header )
                        {
                            if ( ! isset ( $modules_settings[ $header ] )   ||  ! is_array ( $modules_settings[ $header ]  ) )
                                $modules_settings[ $header ]   =   array (
                                                                        'enabled'   =>  'no' 
                                                                        );
                            
                            $modules_settings[ $header ]['enabled']    =   'no';
                        }
                        
                        
                    //add the custom headers
                    $modules_settings[ 'cross_origin_embedder_policy' ]['enabled']      =   'yes';
                    $modules_settings[ 'cross_origin_embedder_policy' ]['value']        =   'unsafe-none';
                    
                    $modules_settings[ 'cross_origin_opener_policy' ]['enabled']        =   'yes';
                    $modules_settings[ 'cross_origin_opener_policy' ]['value']          =   'unsafe-none';
                    
                    $modules_settings[ 'cross_origin_resource_policy' ]['enabled']      =   'yes';
                    $modules_settings[ 'cross_origin_resource_policy' ]['value']        =   'cross-origin';
                    
                    $modules_settings[ 'referrer_policy' ]['enabled']                   =   'yes';
                    $modules_settings[ 'referrer_policy' ]['value']                     =   'strict-origin-when-cross-origin';
                                       
                    $modules_settings[ 'x_download_options' ]['enabled']                =   'yes';
                    $modules_settings[ 'x_download_options' ]['value']                  =   'noopen';
                    
                    $modules_settings[ 'x_frame_options' ]['enabled']                   =   'yes';
                    $modules_settings[ 'x_frame_options' ]['value']                     =   'SAMEORIGIN';
                    
                    $modules_settings[ 'x_xss_protection' ]['enabled']                  =   'yes';
                    $modules_settings[ 'x_xss_protection' ]['value']                    =   '1; mode=block';
                    
                    $site_settings['module_settings']  =   $modules_settings;
                        
                    //$this->update_settings( $site_settings );
                    $this->wph->settings    =   $site_settings;
                    
                    //generate a new write_check_string
                    $write_check_string  =   time() . '_' . mt_rand(100, 99999);
                    $this->wph->settings['write_check_string']   =   $write_check_string;
                                                                                   
                    //update the settings
                    $this->update_settings( $this->wph->settings );
                    
                    //trigger the settings changed action
                    do_action('wph/settings_changed', $screen_slug, $tab_slug);
                    
                                        
                    //redirect
                    $new_admin_url     =   $this->get_module_item_setting('admin_url'  ,   'admin');
                    
                    //check if the rewrite applied
                    if ( ! empty ( $new_admin_url ) &&  ! $this->rewrite_rules_applied() )
                        $new_admin_url  =   '';
                    
                    if(!empty($new_admin_url)   &&  $this->is_permalink_enabled())
                        $new_location       =   trailingslashit(    home_url()  )   . $new_admin_url .  "/admin.php?page="   .   $screen_slug;
                        else
                        $new_location       =   trailingslashit(    site_url()  )   .  "wp-admin/admin.php?page="   .   $screen_slug;
                    
                    if($tab_slug !==    FALSE)
                        $new_location   .=  '&component=' . $tab_slug;
                        
                    $new_location   .=  '&settings_updated=true&headers_sample_setup=true';
                    wp_redirect( $new_location );
                    
                    die();
                    
                }
            
            
                    
                        
            /**
            * Replace a filter / action from anonymous object
            * 
            * @param mixed $tag
            * @param mixed $class
            * @param mixed $method
            */
            function remove_anonymous_object_filter( $tag, $class, $method ) 
                {
                    $filters = false;

                    if ( isset( $GLOBALS['wp_filter'][$tag] ) )
                        $filters = $GLOBALS['wp_filter'][$tag];

                    if ( $filters )
                    foreach ( $filters as $priority => $filter ) 
                        {
                            foreach ( $filter as $identifier => $function ) 
                                {
                                    if ( ! is_array( $function ) )
                                        continue;
                                    
                                    if ( ! $function['function'][0] instanceof $class )
                                        continue;
                                    
                                    if ( $method == $function['function'][1] ) 
                                        {
                                            remove_filter($tag, array( $function['function'][0], $method ), $priority);
                                        }
                                }
                        }
                }
                
        
            function return_component_instance( $component_class_name )
                {
                    
                    foreach ( $this->wph->modules   as  $priority   =>  $data )
                        {
                            if ( is_array ( $data->components ) &&  count ( $data->components ) > 0 )
                                {
                                    foreach ( $data->components     as  $component )
                                        {
                                            if ( get_class( $component )    ==  $component_class_name )
                                                return $component;
                                        }
                                }
                        }
                    
                    return FALSE;
                       
                }
        
        
            /**
            * Check the plugins directory and retrieve all plugin files with plugin data.
            *
            * WordPress only supports plugin files in the base plugins directory
            * (wp-content/plugins) and in one directory above the plugins directory
            * (wp-content/plugins/my-plugin). The file it looks for has the plugin data
            * and must be found in those two locations. It is recommended to keep your
            * plugin files in their own directories.
            *
            * The file with the plugin data is the file that will be included and therefore
            * needs to have the main execution for the plugin. This does not mean
            * everything must be contained in the file and it is recommended that the file
            * be split for maintainability. Keep everything in one file for extreme
            * optimization purposes.
            *
            * @since 1.5.0
            *
            * @param string $plugin_folder Optional. Relative path to single plugin folder.
            * @return array Key is the plugin file path and the value is an array of the plugin data.
            */
            function get_plugins($plugin_folder = '') 
                {
                 
                    $wp_plugins = array ();
                    $plugin_root = WP_PLUGIN_DIR;
                    if ( !empty($plugin_folder) )
                        $plugin_root .= $plugin_folder;

                    // Files in wp-content/plugins directory
                    $plugins_dir = @ opendir( $plugin_root);
                    $plugin_files = array();
                    if ( $plugins_dir ) {
                        while (($file = readdir( $plugins_dir ) ) !== false ) {
                            if ( substr($file, 0, 1) == '.' )
                                continue;
                            if ( is_dir( $plugin_root.'/'.$file ) ) {
                                $plugins_subdir = @ opendir( $plugin_root.'/'.$file );
                                if ( $plugins_subdir ) {
                                    while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
                                        if ( substr($subfile, 0, 1) == '.' )
                                            continue;
                                        if ( substr($subfile, -4) == '.php' )
                                            $plugin_files[] = "$file/$subfile";
                                    }
                                    closedir( $plugins_subdir );
                                }
                            } else {
                                if ( substr($file, -4) == '.php' )
                                    $plugin_files[] = $file;
                            }
                        }
                        closedir( $plugins_dir );
                    }

                    if ( empty($plugin_files) )
                        return $wp_plugins;

                    foreach ( $plugin_files as $plugin_file ) {
                        if ( !is_readable( "$plugin_root/$plugin_file" ) )
                            continue;

                        $plugin_data = $this->get_plugin_data( "$plugin_root/$plugin_file", false, false ); //Do not apply markup/translate as it'll be cached.

                        if ( empty ( $plugin_data['Name'] ) )
                            continue;

                        $wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
                    }

                    return $wp_plugins;
                }
                
            
            /**
            * Parse plugin headers data
            *     
            * @param mixed $plugin_file
            * @param mixed $markup
            * @param mixed $translate
            */
            function get_plugin_data( $plugin_file, $markup = true, $translate = true ) 
                {

                    $default_headers = array(
                        'Name' => 'Plugin Name',
                        'PluginURI' => 'Plugin URI',
                        'Version' => 'Version',
                        'Description' => 'Description',
                        'Author' => 'Author',
                        'AuthorURI' => 'Author URI',
                        'TextDomain' => 'Text Domain',
                        'DomainPath' => 'Domain Path',
                        'Network' => 'Network',
                        // Site Wide Only is deprecated in favor of Network.
                        '_sitewide' => 'Site Wide Only',
                    );

                    $plugin_data = get_file_data( $plugin_file, $default_headers, 'plugin' );

                    // Site Wide Only is the old header for Network
                    if ( ! $plugin_data['Network'] && $plugin_data['_sitewide'] ) {
                        /* translators: 1: Site Wide Only: true, 2: Network: true */
                        _deprecated_argument( __FUNCTION__, '3.0', sprintf( __( 'The %1$s plugin header is deprecated. Use %2$s instead.' ), '<code>Site Wide Only: true</code>', '<code>Network: true</code>' ) );
                        $plugin_data['Network'] = $plugin_data['_sitewide'];
                    }
                    $plugin_data['Network'] = ( 'true' == strtolower( $plugin_data['Network'] ) );
                    unset( $plugin_data['_sitewide'] );

                    if ( $markup || $translate ) {
                        $plugin_data = $this->_get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup, $translate );
                    } else {
                        $plugin_data['Title']      = $plugin_data['Name'];
                        $plugin_data['AuthorName'] = $plugin_data['Author'];
                    }

                    return $plugin_data;
                }
                
                
                
            /**
            * Sanitizes plugin data, optionally adds markup, optionally translates.
            *
            * @since 2.7.0
            * @access private
            * @see get_plugin_data()
            */
            function _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup = true, $translate = true ) 
                {

                    // Sanitize the plugin filename to a WP_PLUGIN_DIR relative path
                    $plugin_file = plugin_basename( $plugin_file );

                    // Translate fields
                    if ( $translate ) {
                        if ( $textdomain = $plugin_data['TextDomain'] ) {
                            if ( ! is_textdomain_loaded( $textdomain ) ) {
                                if ( $plugin_data['DomainPath'] ) {
                                    load_plugin_textdomain( $textdomain, false, dirname( $plugin_file ) . $plugin_data['DomainPath'] );
                                } else {
                                    load_plugin_textdomain( $textdomain, false, dirname( $plugin_file ) );
                                }
                            }
                        } elseif ( 'hello.php' == basename( $plugin_file ) ) {
                            $textdomain = 'default';
                        }
                        if ( $textdomain ) {
                            foreach ( array( 'Name', 'PluginURI', 'Description', 'Author', 'AuthorURI', 'Version' ) as $field )
                                $plugin_data[ $field ] = translate( $plugin_data[ $field ], $textdomain );
                        }
                    }

                    // Sanitize fields
                    $allowed_tags = $allowed_tags_in_links = array(
                        'abbr'    => array( 'title' => true ),
                        'acronym' => array( 'title' => true ),
                        'code'    => true,
                        'em'      => true,
                        'strong'  => true,
                    );
                    $allowed_tags['a'] = array( 'href' => true, 'title' => true );

                    // Name is marked up inside <a> tags. Don't allow these.
                    // Author is too, but some plugins have used <a> here (omitting Author URI).
                    $plugin_data['Name']        = wp_kses( $plugin_data['Name'],        $allowed_tags_in_links );
                    $plugin_data['Author']      = wp_kses( $plugin_data['Author'],      $allowed_tags );

                    $plugin_data['Description'] = wp_kses( $plugin_data['Description'], $allowed_tags );
                    $plugin_data['Version']     = wp_kses( $plugin_data['Version'],     $allowed_tags );

                    $plugin_data['PluginURI']   = esc_url( $plugin_data['PluginURI'] );
                    $plugin_data['AuthorURI']   = esc_url( $plugin_data['AuthorURI'] );

                    $plugin_data['Title']      = $plugin_data['Name'];
                    $plugin_data['AuthorName'] = $plugin_data['Author'];

                    // Apply markup
                    if ( $markup ) {
                        if ( $plugin_data['PluginURI'] && $plugin_data['Name'] )
                            $plugin_data['Title'] = '<a href="' . $plugin_data['PluginURI'] . '">' . $plugin_data['Name'] . '</a>';

                        if ( $plugin_data['AuthorURI'] && $plugin_data['Author'] )
                            $plugin_data['Author'] = '<a href="' . $plugin_data['AuthorURI'] . '">' . $plugin_data['Author'] . '</a>';

                        $plugin_data['Description'] = wptexturize( $plugin_data['Description'] );

                        if ( $plugin_data['Author'] )
                            $plugin_data['Description'] .= ' <cite>' . sprintf( __('By %s.'), $plugin_data['Author'] ) . '</cite>';
                    }

                    return $plugin_data;
                }
                
                
            /**
            * Alternative when apache_response_headers() not available
            * 
            */
            function parseRequestHeaders() 
                {
                    $headers = array();
                    foreach($_SERVER as $key => $value) 
                        {
                            if (substr($key, 0, 5) <> 'HTTP_') 
                                continue;
                                
                            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                            $headers[$header] = $value;
                        }
                    
                    return $headers;
                }
            
            
            
            /**
            * Attempt to update the outputed headers
            * 
            * @param mixed $headers
            * @param mixed $response_headers
            */
            function update_headers( $headers, $response_headers )
                {
                    
                    $replacement_list   =   $this->get_replacement_list();
                    
                    foreach ( $headers as $header )
                        {
                            if(isset($response_headers[ $header ]))
                                {
                                    $header_value   =   $response_headers[ $header ];
                                    $new_header_value   =   $this->content_urls_replacement($header_value,  $replacement_list );
                                    
                                    if($header_value    !=  $new_header_value)
                                        {
                                            header_remove("Location");
                                            header( 'Location: ' . $new_header_value );
                                        }
                                }
                        }
                    
                }
            
            
            
            /**
            * Check if current content is filterable, depending on header content type
            * 
            */
            function is_filterable_content_type()
                {
                   
                    $is_filterable  =   TRUE;

                    $headers_content_type    =   $this->get_headers_list_content_type();
                    
                    if ( $headers_content_type ===  FALSE )
                        return $is_filterable;
                    
                    $allow_type    =   array(
                                                'text/plain',
                                                'text/css',
                                                'text/html',
                                                'text/csv',
                                                'text/javascript',
                                                'application/javascript',
                                                'application/json'
                                                );
                    if  ( ! in_array( $headers_content_type , $allow_type ) )
                        $is_filterable  =   FALSE;
                        
                    return $is_filterable;    
                    
                }
                
                
            function get_headers_list_content_type()
                {
                    $headers        =   headers_list();
                    
                    //there is no header to check
                    if  ( ! is_array( $headers )  ||  count ( $headers ) < 1 )
                        return FALSE;
                        

                    $found  =   preg_grep('/^Content-Type\s?:.*/i', $headers);
                    if  ( ! is_array ( $found ) ||    count ( $found ) <  1   )
                        return FALSE;
                        
                    reset( $found );
                    $header_field           =   $headers[ key( $found ) ];
                    $header_field           =   preg_replace('/Content-Type\s?:/i', '', $header_field);
                    $header_field           =   trim ( $header_field );
                    $header_field_parts     =   explode(";", $header_field);
                    $header_content_type    =   trim( $header_field_parts[0] );   
                    
                    return $header_content_type;
                }
            
            
            /**
            * Get available themes
            * 
            * @param mixed $args
            */
            function get_themes( $args = array() ) 
                {
                    global $wp_theme_directories;

                    $defaults = array( 'errors' => false, 'allowed' => null, 'blog_id' => 0 );
                    $args = wp_parse_args( $args, $defaults );
                                   
                    // Register the default theme directory root
                    if ( ! is_array ( $wp_theme_directories ) ||   count( $wp_theme_directories ) < 1  ) 
                        register_theme_directory( get_theme_root() );
                    
                    $theme_directories = search_theme_directories();

                    if ( count( $wp_theme_directories ) > 1 ) {
                        // Make sure the current theme wins out, in case search_theme_directories() picks the wrong
                        // one in the case of a conflict. (Normally, last registered theme root wins.)
                        $current_theme = get_stylesheet();
                        if ( isset( $theme_directories[ $current_theme ] ) ) {
                            $root_of_current_theme = get_raw_theme_root( $current_theme );
                            if ( ! in_array( $root_of_current_theme, $wp_theme_directories ) )
                                $root_of_current_theme = WP_CONTENT_DIR . $root_of_current_theme;
                            $theme_directories[ $current_theme ]['theme_root'] = $root_of_current_theme;
                        }
                    }

                    if ( empty( $theme_directories ) )
                        return array();

                    if ( is_multisite() && null !== $args['allowed'] ) {
                        $allowed = $args['allowed'];
                        if ( 'network' === $allowed )
                            $theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed_on_network() );
                        elseif ( 'site' === $allowed )
                            $theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed_on_site( $args['blog_id'] ) );
                        elseif ( $allowed )
                            $theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed( $args['blog_id'] ) );
                        else
                            $theme_directories = array_diff_key( $theme_directories, WP_Theme::get_allowed( $args['blog_id'] ) );
                    }

                    return $theme_directories;
                    
                }
            
            
            /**
            * Parse available themes headers
            * 
            */
            function parse_themes_headers( $all_templates )
                {
                    foreach( $all_templates as  $directory  =>  $theme_data)
                        {
                            $theme_style_path   =   trailingslashit( $theme_data['theme_root']) . $theme_data['theme_file'];
                            
                            if ( ! file_exists( $theme_style_path ))
                                continue;
                            
                            $theme_headers      =   $this->get_theme_headers( $theme_style_path );
                            $all_templates[$directory]['headers']   =  $theme_headers;
                            
                        }
                    
                    return $all_templates;
                       
                }
            
            
            function get_theme_headers($stylesheet_path)
                {
                    
                    $file_headers = array(
                                            'Name'        => 'Theme Name',
                                            'ThemeURI'    => 'Theme URI',
                                            'Description' => 'Description',
                                            'Author'      => 'Author',
                                            'AuthorURI'   => 'Author URI',
                                            'Version'     => 'Version',
                                            'Template'    => 'Template',
                                            'Status'      => 'Status',
                                            'Tags'        => 'Tags',
                                            'TextDomain'  => 'Text Domain',
                                            'DomainPath'  => 'Domain Path',
                                        );
                    
                    $theme_headers = get_file_data( $stylesheet_path, $file_headers, 'theme' );   
                    
                    return $theme_headers;
                    
                }
            
            
            /**
            * Return if a theme is child or not
            * 
            * @param mixed $theme_slug
            * @param mixed $all_themes
            */
            function is_child_theme($theme_slug, $all_themes)
                {
                    
                    $theme_data =   $all_themes[$theme_slug];
                        
                    if( isset($theme_data['headers']['Template']) &&  !empty($theme_data['headers']['Template']))
                        return TRUE;
                        
                    return FALSE;
                      
                }
                
                
            /**
            * Return main theme directory slug
            * 
            * @param mixed $theme_slug
            * @param mixed $all_themes
            */
            function get_main_theme_directory($theme_slug, $all_themes)
                {
                      
                    $theme_data         =   $all_themes[$theme_slug];
                    $theme_directory    =   $theme_slug;
                    
                    if( isset($theme_data['headers']['Template']) &&  !empty($theme_data['headers']['Template']))
                        {
                            $theme_directory    =   $theme_data['headers']['Template'];
                        }        
                    
                    return $theme_directory;
                    
                }
            
            
            /**
            * Recreate a url from a parsed array
            * 
            * @param mixed $parts
            */
            function build_parsed_url( $parse_url )
                {
                    $url    =   (isset($parse_url['scheme']) ? "{$parse_url['scheme']}:" : '') . 
                                ((isset($parse_url['user']) || isset($parse_url['host'])) ? '//' : '') . 
                                (isset($parse_url['user']) ? "{$parse_url['user']}" : '') . 
                                (isset($parse_url['pass']) ? ":{$parse_url['pass']}" : '') . 
                                (isset($parse_url['user']) ? '@' : '') . 
                                (isset($parse_url['host']) ? "{$parse_url['host']}" : '') . 
                                (isset($parse_url['port']) ? ":{$parse_url['port']}" : '') . 
                                (isset($parse_url['path']) ? "{$parse_url['path']}" : '') . 
                                (isset($parse_url['query']) ? "?{$parse_url['query']}" : '') . 
                                (isset($parse_url['fragment']) ? "#{$parse_url['fragment']}" : '');
   
                    return $url;
                    
                }
            
            
            /**
            * Init the cache directory where static files will be saved
            * 
            */
            function init_cache_dir()
                {
                    
                    if ( ! is_dir( WPH_CACHE_PATH ) ) 
                        {
                           wp_mkdir_p( WPH_CACHE_PATH );
                        }   
                    
                    
                }
                
            
            /**
            * Clear the cache content
            *     
            */
            function cache_clear()
                {
                    
                    do_action('wp-hide/before_cache_clear');
                        
                    $this->rrmdir( WPH_CACHE_PATH, TRUE );
                    
                    do_action('wp-hide/after_cache_clear');
                    
                }
                
                
            
            /**
            * Clear any cache plugins
            *     
            */
            function site_cache_clear()
                {
                    if (function_exists('wp_cache_clear_cache'))
                        wp_cache_clear_cache();
                    
                    if (function_exists('w3tc_flush_all'))
                        w3tc_flush_all();
                        
                    if (function_exists('opcache_reset')    &&  ! ini_get( 'opcache.restrict_api' ) )
                        @opcache_reset();
                    
                    if ( function_exists( 'rocket_clean_domain' ) )
                        rocket_clean_domain();
                        
                    if (function_exists('wp_cache_clear_cache')) 
                        wp_cache_clear_cache();
                
                    global $wp_fastest_cache;
                    if ( method_exists( 'WpFastestCache', 'deleteCache' ) && !empty( $wp_fastest_cache ) )
                        $wp_fastest_cache->deleteCache();
                
                    //If your host has installed APC cache this plugin allows you to clear the cache from within WordPress
                    if (function_exists('apc_clear_cache'))
                        apc_clear_cache();
                        
                    if (function_exists('fvm_purge_all'))
                        fvm_purge_all();

                    if ( class_exists( 'autoptimizeCache' ) )     
                        autoptimizeCache::clearall();
                        
                    //WPEngine
                    if ( class_exists( 'WpeCommon' ) ) 
                        {
                            if ( method_exists( 'WpeCommon', 'purge_memcached' ) )
                                WpeCommon::purge_memcached();
                            if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) )
                                WpeCommon::clear_maxcdn_cache();
                            if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) )
                                WpeCommon::purge_varnish_cache();
                        }
                        
                    if (class_exists('Cache_Enabler_Disk') && method_exists('Cache_Enabler_Disk', 'clear_cache'))
                        Cache_Enabler_Disk::clear_cache();
                        
                    //Perfmatters
                    if (class_exists('Perfmatters\CSS') && method_exists('Perfmatters\CSS', 'clear_used_css'))
                        Perfmatters\CSS::clear_used_css();
                    
                    if ( defined( 'BREEZE_VERSION' ) )
                        do_action( 'breeze_clear_all_cache' );
                        
                    if (function_exists('sg_cachepress_purge_everything'))
                        sg_cachepress_purge_everything();
                        
                    if ( defined ( 'FLYING_PRESS_VERSION' ) )
                        {
                            do_action('flying_press_purge_everything:before');

                            @unlink(FLYING_PRESS_CACHE_DIR . '/preload.txt');

                            // Delete all files and subdirectories
                            $this->rrmdir( FLYING_PRESS_CACHE_DIR );

                            @mkdir(FLYING_PRESS_CACHE_DIR, 0755, true);

                            do_action('flying_press_purge_everything:after');
                        }
                        
                    if (class_exists('\LiteSpeed\Purge'))
                        {
                            \LiteSpeed\Purge::purge_all();
                        }
                        
                }
                
                
            /**
            * Recursivelly remove all fodlers and files within a directory
            * 
            * @param mixed $dir
            */
            function rrmdir( $dir, $xclude_parent   =   FALSE ) 
                {
                    if (is_dir($dir)) 
                        {
                            $objects = scandir($dir);
                            
                            foreach ($objects as $object) 
                                {
                                    if ($object != "." && $object != "..") 
                                        {
                                            if (filetype($dir."/".$object) == "dir") 
                                                    $this->rrmdir($dir."/".$object); 
                                                else unlink   ($dir."/".$object);
                                        }
                                }
                                
                            reset($objects);
                            
                            if($xclude_parent   !== TRUE)
                                rmdir($dir);
                        }
                }
            
            
            function random_word( $length = 8 ) 
                {
                    $cons = array( 'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'z', 'pt', 'gl', 'gr', 'ch', 'ph', 'ps', 'sh', 'st', 'th', 'wh' );
                    $cons_cant_start = array( 'ck', 'cm', 'dr', 'ds','ft', 'gh', 'gn', 'kr', 'ks', 'ls', 'lt', 'lr', 'mp', 'mt', 'ms', 'ng', 'ns','rd', 'rg', 'rs', 'rt', 'ss', 'ts', 'tch');
                    $vows = array( 'a', 'e', 'i', 'o', 'u', 'y','ee', 'oa', 'oo');
                    $current = ( mt_rand( 0, 1 ) == '0' ? 'cons' : 'vows' );
                    $word = '';
                    while( strlen( $word ) < $length ) {
                        if( strlen( $word ) == 2 ) $cons = array_merge( $cons, $cons_cant_start );
                        $rnd = ${$current}[ mt_rand( 0, count( ${$current} ) -1 ) ];
                        if( strlen( $word . $rnd ) <= $length ) {
                            $word .= $rnd;
                            $current = ( $current == 'cons' ? 'vows' : 'cons' );
                        }
                    }
                    return $word;
                }
                
            
            /**
            * Dirty check if a specified caller is in the backtrace debug list
            * 
            * @param mixed $type
            * @param mixed $name
            */
            function _is_caller_in_backtrace( $elements )
                {
                    
                    $stack  =   debug_backtrace();
                    
                    foreach ( $stack    as  $stack_item )
                        {
                            $elements_seek  =   $elements;
                            foreach ( $elements as  $key    =>  $value )
                                {
                                    if ( isset( $stack_item[ $key ] )   &&  $stack_item[ $key ] ==  $value )
                                        unset( $elements_seek[$key]);
                                }
                                
                            if  ( count ( $elements_seek ) < 1 )
                                return TRUE;
                        }
                          
                    return FALSE;    
                }
            
                
            function get_ad_banner()
                {
                    ob_start();
                    ?>
                    
                        <div id="info_box">
                            <div class="image">
                                <img src="<?php echo WPH_URL ?>/assets/images/computer.png" />
                            </div>
                            
                            <div class="text">
                                <p> </p>
                                <p><?php    _e('Help us to maintain this plugin by sending improvements, suggestions and reporting any issues at ', 'wp-hide-security-enhancer')  ?><a target="_blank" href="https://wp-hide.com/">wp-hide.com</a></p>
                                <span class="split">&nbsp;</span>
                                <h4><?php   _e('Did you know there is a', 'wp-hide-security-enhancer')  ?> <span class="wph-pro">PRO</span> <?php   _e('version of this plug-in?', 'wp-hide-security-enhancer')  ?> <a target="_blank" href="https://wp-hide.com/wp-hide-pro-now-available/">Read more</a></h4>
                                <span class="split">&nbsp;</span>
                                <p><?php    _e('Did you find this plugin useful? Please support our work by submitting a review, spread the word about the code, or write an article about the plugin in your blog with a link to development site', 'wp-hide-security-enhancer') ?> <a href="https://wp-hide.com/" target="_blank"><strong>https://wp-hide.com/</strong></a></p>
                            </div>
                            
                            
                        </div>
                                        
                    <?php
                    
                    $content    =   ob_get_contents();
                    ob_end_clean();
                    
                    return $content;
                    
                }
        
            
        }
        
?>