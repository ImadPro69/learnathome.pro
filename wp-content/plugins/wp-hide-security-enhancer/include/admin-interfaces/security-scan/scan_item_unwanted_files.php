<?php


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan_unwanted_files    extends WPH_security_scan_item
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
                    return 'unwanted_files';
                }
                
                
            public function get_settings()
                {
                    
                    return array(
                                    'title'         =>  __( 'Dangerous Files', 'wp-hide-security-enhancer' ),
                                    'icon'          =>  'dashicons-admin-generic',
                                    
                                    'help'          =>  __("This security test checks for any dangerous files on your WordPress root. You should avoid keeping any unnecessary files on domain root.",    'wp-hide-security-enhancer'),
                                    
                                    'score_points'  =>  15,
                                    
                                    'callback'      =>  'scan_item_php_version',
                                    );
                }
                
            
            function scan()
                {
                    $_JSON_response     =   array();
                    
                    $found_issue        =   FALSE;
                    
                    $unwanted_files = array(
                                            'wp-config.php'             =>  array(
                                                                                    'regex'         =>  '/(wp-config\.php|wp-config-sample\.php)(*SKIP)(*FAIL)|(^wp-config.*)/m',
                                                                                    'error_description'    =>  __('PHP executable file', 'wp-hide-security-enhancer')
                                                                                    ),
                                            'php_errorlog'              => array(
                                                                                    'regex'         =>  '/php_errorlog/m',
                                                                                    'error_description'    =>  __('System Error log file', 'wp-hide-security-enhancer')
                                                                                    ),
                                            '*.log'                     => array(
                                                                                    'regex'         => '/.*\.log$.*/m',
                                                                                    'error_description'    =>  __('System log file', 'wp-hide-security-enhancer')
                                                                                    ),
                                            '*.sql'                     => array(
                                                                                    'regex'         => '/.*\.sql$.*/m',
                                                                                    'error_description'    =>  __('MySQL database file', 'wp-hide-security-enhancer')
                                                                                    ),
                                            '*.bak'                     => array(
                                                                                    'regex'         => '/.*\.sql$.*/m',
                                                                                    'error_description'    =>  __('Backup file', 'wp-hide-security-enhancer')
                                                                                    ),
                                            '*.zip'                     => array(
                                                                                    'regex'         => '/.*\.zip$.*/m',
                                                                                    'error_description'    =>  __('ZIP Archive file', 'wp-hide-security-enhancer')
                                                                                    ),
                                            '*.txt'                     => array(
                                                                                    'regex'         => '/(license\.txt|robots\.txt)(*SKIP)(*FAIL)|.*\.txt/m',
                                                                                    'error_description'    =>  __('Text file, may contain sensitive data', 'wp-hide-security-enhancer')
                                                                                    ),
                                            'other php'                 => array(
                                                                                    'regex'         => '/(index\.php|wp-activate\.php|wp-blog-header\.php|wp-comments-post\.php|wp-config\.php|wp-config-sample\.php|wp-cron\.php|wp-links-opml\.php|wp-load\.php|wp-login\.php|wp-mail\.php|wp-settings\.php|wp-signup\.php|wp-trackback\.php|xmlrpc\.php|wordfence-waf\.php)(*SKIP)(*FAIL)|.*\.php/m',
                                                                                    'error_description'    =>  __('PHP executable file', 'wp-hide-security-enhancer')
                                                                                    )
                                        );
                    
                    $founds =   array();
                    
                    $files  =   scandir ( ABSPATH );
                    foreach ( $files as $file )
                        {
                            if ( ! is_file ( ABSPATH . $file ) )
                                continue;
                            
                            foreach ( $unwanted_files   as  $key    =>  $data )
                                {
                                    if ( preg_match ( $data['regex'], $file ) )
                                        {
                                            $founds[]   =   array(
                                                                'type'  =>  $key,
                                                                'value' =>  $file
                                                                );
                                            break;
                                        }
                                    
                                }
                        }
                    
                    if ( count ( $founds )  >   0 )
                        $found_issue    =   TRUE;
                    
                    if ( $found_issue )
                        {
                            $_JSON_response['status']       =   FALSE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-no"></span> Your WordPress root still includes dangerous files which may contain valuable pieces of information regarding your environment.', 'wp-hide-security-enhancer' );
                            $_JSON_response['description']  .=   '<br /><br />'    .   __( 'Consider re-locating the followng files from your site root:', 'wp-hide-security-enhancer' );
                            $_JSON_response['description']  .=   '<br /><br />';
                            
                            foreach ( $founds   as  $data )
                                {
                                    
                                    $_JSON_response['description']  .=  '<p class="important">';              
                                    $_JSON_response['description']  .=   '<b> <span class="dashicons dashicons-search"></span> ' . $data['value'] . ' (' . $unwanted_files[ $data['type'] ]['error_description'] . ')</b>';
                                    $_JSON_response['description']  .=  '</p>';
                                    
                                }
                            
                            $_JSON_response['actions']      =   array (
                                                                        'ignore'            =>  '//--post-generated--',
                                                                        'restore'           =>  '//--post-generated--',
                                                                        );
                            
                        }
                        else
                        {
                            $_JSON_response['status']       =   TRUE;
                            $_JSON_response['description']  =   __( '<span class="dashicons dashicons-yes"></span> Your WordPress root still includes dangerous files which may contain valuable pieces of information regarding your environment.', 'wp-hide-security-enhancer' );
                            
                        }  
                        
                    return $this->return_json_response( $_JSON_response );
                
                }    
            
        }
        
        
?>