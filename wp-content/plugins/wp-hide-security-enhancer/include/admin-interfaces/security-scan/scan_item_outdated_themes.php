<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_outdated_themes    extends WPH_security_scan_item
        {
            var $wph;
                     
            function __construct()
                {
                    $this->id       =   $this->get_id();
                   
                    global $wph;
                    
                    $this->wph  =   $wph;
                }   
            
            public function get_id()
                {
                    return 'outdated_themes';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                    'title'         =>  'Outdated Themes',
                                    'icon'          =>  'dashicons-admin-appearance',
                                    
                                    'help'          =>  __("The biggest reason to keep your WordPress website up to date is Security. When you do not update your WordPress themes, you create a security risk and expose your site to existing vulnerabilities and imminent attacks. 
                                                        The WordPress developers are constantly fixing security breaches or improving security.",    'wp-hide-security-enhancer'),
                                    
                                    'score_points'  =>  15,
                                    
                                    'callback'      =>  'scan_item_outdated_themes',
                                    'use_transient' =>  TRUE
                                    );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    wp_update_themes();
                    
                    $update_themes     = get_site_transient('update_themes');
                    $found_outdated =   array();
                    if ( $update_themes && is_array( $update_themes->response ) && count ( $update_themes->response ) > 0 ) 
                        $found_outdated =   $update_themes->response;
                    
                    if ( is_array( $found_outdated ) && count ( $found_outdated ) > 0 )
                        $_JSON_response['info']  =   __( 'Found outdated themes: ', 'wp-hide-security-enhancer' ) . count ( $found_outdated  );

                    if ( is_array( $found_outdated ) && count ( $found_outdated ) > 0   )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span>The following plugins are found outdated on your site:', 'wp-hide-security-enhancer' );
                                                        
                            foreach ( $found_outdated   as  $theme_slug    =>  $theme_data )
                                {
                                    $theme          =   wp_get_theme( $theme_slug );
                                    
                                    $_JSON_response['description']  .=  '<p class="outdated_plugin">';
                                    
                                    $_JSON_response['description']  .=   '<img class="icon" src="'. $theme->get_screenshot() .'" /> ';
                                                                                
                                    $_JSON_response['description']  .=   '<b>' . $theme->get('Name') .'</b><br />' . __( ' Upgrade from ', 'wp-hide-security-enhancer' ) . $theme->get('Version') .  __( ' to ', 'wp-hide-security-enhancer' ) . $theme_data['new_version'];
                                    
                                    $_JSON_response['description']  .=  '</p>';
                                    
                                }
                            
                            $_JSON_response['description']  .=   __( '<br /><p class="description">The  inactive themes require updating as well, as may contain harmful vulnerabilities, exploaitable even if the code is not active.</p>', 'wp-hide-security-enhancer' );
                            
                            $_JSON_response['actions']      =   array (
                                                                        'fix'       =>  '<a class="button-primary" href="'. get_dashboard_url( '', 'themes.php', 'admin' ) .'">Fix</a>',
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span>All themes are Up to Date.', 'wp-hide-security-enhancer' );
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>