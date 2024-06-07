<?php   


    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_security_scan
        {
            
            var $wph;
            var $functions;
            
            var $scan_items =   array();
            
            var $remote_started =   FALSE;
            var $remote_html    =   FALSE;
            var $remote_headers =   FALSE;
            var $remote_errors  =   FALSE;
            
            
            function __construct()
                {                    
                    add_action ( 'init', array ( $this, 'init') );
                }
                
            function init()
                {
                    if ( is_admin() &&  current_user_can ( 'manage_options' ) )
                        $this->run();
                }
            
            function get_scan_items()
                {
                    $scan_items =   array ( 
                                            'wp_version',
                                            'wp_version_stability',
                                            'php_version',
                                            'mysql_version',
                                            'wp_debug',
                                            'db_debug',
                                            'use_admin_user',
                                            'outdated_themes',
                                            'outdated_plugins',
                                            'old_plugins',
                                            'disable_file_edit',
                                            
                                            'firewall',
                                            'unwanted_files',
                                            
                                            'php_display_errors',
                                            'php_register_globals',
                                            'php_safe_mode',
                                            'php_allow_url_include',
                                            'php_expose',
                                            'database_prefix',
                                            'keys_and_salts',
                                            
                                            'headers',
                                            
                                            'hide_check_theme',
                                            'hide_check_theme_style',
                                            'hide_check_child_theme',
                                            'hide_check_child_theme_style',
                                            'hide_check_wp_content',
                                            'hide_check_wp_includes',
                                            'hide_check_plugins',
                                            'hide_check_comments',
                                            'hide_xml_rpc',
                                            'hide_json',
                                            'hide_json_clean_api',
                                            'hide_registration',
                                            'hide_license_txt',
                                            'hide_readme_html',
                                            'hide_wordpress_tagline',
                                            'hide_wordpress_generator',
                                            'hide_other_generator',
                                            'hide_wlwmanifest',
                                            'hide_emulate',
                                            'hide_robots',
                                            'hide_remove_header_link',
                                            'hide_remove_headers',
                                            'hide_remove_html_comments',
                                            'hide_new_wp_login',
                                            'hide_admin_url',
                                            'hide_admin_ajax',
                                            'hide_postprocessing',
                                            'hide_replacements'
                                            );    
                    
                    return $scan_items;
                }
                
            
            function menu_warning()
                {
                    $site_scan  =   (array)get_site_option('wph/site_scan');
                    
                    $page_visited   =   isset ( $site_scan['visited'] ) ?   $site_scan['visited']   :   '';   
                    
                    if ( ( ! isset ( $site_scan['last_scan'] )    ||  empty ( $site_scan['last_scan'] ) )   &&  empty (  $page_visited ) )
                        {
                            if ( isset (  $_GET['page'] )   &&  $_GET['page']   ==  'wp-hide-security-scan')
                                return FALSE;
                            
                            return TRUE;
                        }
                        
                    $found_new_scan_items   =   FALSE;
                    $scan_items =   $this->get_scan_items();
                          
                    if ( ! empty ( $page_visited )  &&  md5 (  json_encode( $scan_items ) )    !=  $page_visited )
                        return TRUE;
                    
                    return FALSE;   
                }
                   
            function run()
                {
                    global $wph;
                    $this->wph          =   &$wph;
                    
                    $this->functions    =   new WPH_functions();
                    
                    include_once( WPH_PATH . '/include/admin-interfaces/security-scan/scan_item.class.php' );
                    
                    $scan_item  =   $this->get_scan_items();                        
                    foreach ( $scan_item as   $scan_item )
                        {
                                                        
                            include_once( WPH_PATH . '/include/admin-interfaces/security-scan/scan_item_' .   $scan_item    .   '.php' );
                            
                            $item_instance_class_name      =   'WPH_security_scan_'   .   $scan_item;
                            $item_instance                 =   new $item_instance_class_name;
                            
                            $this->scan_items[ $scan_item ] =   $item_instance;

                        }
  
                    add_action( 'admin_notice',                     array ( $this, 'admin_notices' ) );  
                    
                    add_action( 'wp_ajax_wph_site_scan',            array ( $this, 'wp_ajax_wph_site_scan' ) );
                    add_action( 'wp_ajax_wph_site_scan_progress',   array ( $this, 'wp_ajax_wph_site_scan_progress' ) );
                    add_action( 'wp_ajax_wph_site_scan_ignore',     array ( $this, 'wp_ajax_wph_site_scan_ignore' ) ); 
                    add_action( 'wp_ajax_wph_site_scan_restore',    array ( $this, 'wp_ajax_wph_site_scan_restore' ) );
                    
                }
               
            function admin_print_styles()
                {
                    wp_enqueue_style( 'tipsy.css', WPH_URL . '/assets/css/tipsy.css');
                    
                    wp_register_style('WPHStyle', WPH_URL . '/assets/css/wph.css');
                    wp_enqueue_style( 'WPHStyle'); 
                    
                    wp_register_style('wph-graphs', WPH_URL . '/assets/css/graph.css');
                    wp_enqueue_style( 'wph-graphs');
                    
                    wp_register_style('wph-security-scan', WPH_URL . '/assets/css/security-scan.css');
                    wp_enqueue_style( 'wph-security-scan');
                
                }
                
                
            function admin_print_scripts()
                {
                    wp_enqueue_script('jquery.tipsy.js', WPH_URL . '/assets/js/jquery.tipsy.js' );
                    
                    wp_enqueue_script( 'jquery');
                    wp_register_script('wph', WPH_URL . '/assets/js/wph.js', array(), WPH_CORE_VERSION );
                    
                    // Localize the script with new data
                    $translation_array = array(
                                            
                                        );
                    wp_localize_script( 'wph', 'wph_vars', $translation_array );
                    
                    wp_enqueue_script( 'wph'); 
                
                }
            
                                 
            
            function _render()
                {
                    
                    $site_scan  =   (array)get_site_option('wph/site_scan');   
                    $site_scan['visited']   =   md5 ( json_encode( $this->get_scan_items() ) );
                    update_site_option ( 'wph/site_scan', $site_scan );   
                                                 
                    ?>
                    <div id="wph" class="wrap">
                        <h1>WP Hide & Security Enhancer - <?php _e( "Security Scan", 'wp-hide-security-enhancer' ) ?></h1>
                                          
                        <?php echo $this->functions->get_ad_banner(); ?>
                        
                 
                        <div class="start-container title security_scan">
                            <h2><?php _e( "Security Scan", 'wp-hide-security-enhancer' ) ?></h2>
                        </div>
                        <div id="security-scan">
              
                                <?php  $this->render_overview( $site_scan ); ?>
                            
                                <p><br /></p>
              
                                <div id="all-scann-items">
                                    <div id="scann-items">
                                    <?php
                                    
                                        $wph_site_scan_ignore   =   isset ( $site_scan['ignore'] ) ?    (array)$site_scan['ignore'] :   array();
                                                
                                        foreach ( $this->scan_items as  $scan_item_id  =>  $item_instance )
                                            {
                                                
                                                if ( in_array ( $scan_item_id, $wph_site_scan_ignore ) )
                                                    continue;
                                                
                                                $scan_item_data     =   $item_instance->get_settings();
                                                $scan_response      =   isset ( $site_scan['results'][ $scan_item_id ] ) ?     $site_scan['results'][ $scan_item_id ] :   FALSE ;
                                                      
                                                if ( ! $scan_response )
                                                    {
                                                        $scan_response  =   new stdClass();
                                                        $scan_response->status      =   'unknown';
                                                        $scan_response->info        =   '';
                                                        $scan_response->description =   '<h5>' . __( 'Unknow - Start a new Scan', 'wp-hide-security-enhancer' ) .'</h5>';
                                                        $scan_response->actions     =   array();
                                                    }
                                                                                                   
                                                $this->render_item( $scan_item_id, $scan_item_data, $scan_response );
                                                
                                            }
                                    ?>
                                    </div>    
                                    <div id="hidden-items">
                                    <?php

                                        foreach ( $this->scan_items as  $scan_item_id  =>  $item_instance )
                                            {
                                                
                                                if ( ! in_array ( $scan_item_id, $wph_site_scan_ignore ) )
                                                    continue;
                                                
                                                $scan_item_data     =   $item_instance->get_settings();
                                                $scan_response      =   isset ( $site_scan['results'][ $scan_item_id ] ) ?     $site_scan['results'][ $scan_item_id ] :   FALSE ;
                                                
                                                /*      
                                                if ( ! $scan_response )
                                                    {
                                                        $scan_response   =   json_decode ( $item_instance->scan() );
                                                        $site_scan['results'][ $scan_item_id ] =   $scan_response;
                                                    }
                                                */
                                                       
                                                $this->render_item( $scan_item_id, $scan_item_data, $scan_response );
                                                
                                            }
                                    ?>
                                    </div>          
                                </div>
                        </div>               
                        

                    <?php
                    
                }
                
            
            public function render_overview( $site_scan, $context = '' )
                {
                    ?>
                    <div id="scan_overview" class="wph-postbox header">
                        <div class="wph_graph wph_input widefat">
                            <div class="row cell label">
                    <?php
                    
                    
                        if ( ! isset ( $site_scan['last_scan'] )    ||  empty ( $site_scan['last_scan'] ) )
                            {
                                ?>
                                    
                                    <div id="wph-graph">
                                        <div class="wph-graph-container">
                                            <div class="wph-graph-bg"></div>
                                            <div class="wph-graph-text"></div>
                                            <div class="wph-graph-progress" style="transform: rotate(0deg);"></div>
                                            <div class="wph-graph-data"><b>0%</b><br><span class="protection"><?php _e('Unknown',    'wp-hide-security-enhancer') ?></span></div>
                                        </div>
                                    </div>
                                    <p class="hint"><span class="dashicons dashicons-plugins-checked"></span> <?php _e( 'Running first Scan.. Please wait!',    'wp-hide-security-enhancer') ?></p>
                                <?php   
                            }
                            else
                            {
                                $site_score    =   $this->get_site_score( $site_scan );
                                
                                ?>
                                    
                                    <div id="wph-graph">
                                        <div class="wph-graph-container">
                                            <div class="wph-graph-bg"></div>
                                            <div class="wph-graph-text"></div>
                                            <div class="wph-graph-progress" style="transform: rotate(<?php echo $site_score['graph_progress'] ?>deg);"></div>
                                            <div class="wph-graph-data"><b><?php echo $site_score['progress'] ?>%</b><br><span class="protection"><?php _e( $site_score['protection'],    'wp-hide-security-enhancer') ?></span></div>
                                        </div>
                                    </div>
                                    <p class="hint"><span class="dashicons dashicons-plugins-checked"></span> <?php _e( 'Your current estimated protection is', 'wp-hide-security-enhancer' ); ?> <span class="protection"><?php _e( $site_score['protection'],    'wp-hide-security-enhancer') ?></span>.</p>
                                <?php
                            }
                            
                    ?>
                        </div>

                        </div>
                        <div class="wph_results">
                            <div class="text">
                            <?php
                            
                                reset ( $this->scan_items );
                                $first_scan_item_id =   ucwords ( key ( $this->scan_items ) );
                                
                                //check for scann in progress
                                $scan_in_progress   =   FALSE;
                                if ( isset ( $site_scan['last_scan_progress'] )     &&  $site_scan['last_scan_progress']    >   0   &&  $site_scan['last_scan_progress']    >   time() - 60 )
                                    $scan_in_progress   =   TRUE;
                            
                                if ( ! isset ( $site_scan['last_scan'] )    ||  empty ( $site_scan['last_scan'] ) )
                                    {
                                        ?>
                                            <p class="actions">
                                                <button id="wph-site-scan-button" type="button" class="button <?php  if ( $scan_in_progress ) { echo 'disabled'; } ?> button-primary" onClick="WPH.site_scan( '<?php echo esc_attr ( wp_create_nonce( 'wph/site_scan') ) ?>')"><?php _e( 'Start First Scan', 'wp-hide-security-enhancer' ); ?></button>
                                                <span class="spinner" style="visibility: hidden;"></span>
                                                <span class="working"><?php _e( 'Working', 'wp-hide-security-enhancer' ); ?> <span class="progress">0</span> <?php _e( 'of', 'wp-hide-security-enhancer' ); ?> <span class="total_items"><?php echo count ( $this->scan_items ) ?></span> <?php _e( 'total tests', 'wp-hide-security-enhancer' ); ?></span> 
                                                <br />
                                                <b><?php _e( 'Running first Scan.. Please wait!',    'wp-hide-security-enhancer') ?></b></p>
                                            <p class="last_scan"><b><?php _e( 'Last Scan', 'wp-hide-security-enhancer' ); ?>:</b> <?php _e( 'Unavailable', 'wp-hide-security-enhancer' ); ?></p>
                                            <script type="text/javascript">
                                                jQuery( document ).ready(function() {
                                                    jQuery('#wph-site-scan-button').click();
                                                });
                                            </script>
                                        <?php
                                        
                                            //check for scann in progress
                                            if ( $scan_in_progress )
                                                {
                                                    ?><p class="new-items"><?php _e( 'Another Scan instance in progress. Refresh the page in a minute.', 'wp-hide-security-enhancer' ) ?></p><?php
                                                }
                                    }
                                    else
                                    {
                                        ?>
                                            <div id="wph-scan-score">
                                                <table><tbody><tr>
                                                            <td class="passed">
                                                                <h4><?php _e( 'Passed', 'wp-hide-security-enhancer' ); ?></h4>
                                                                <h5><?php echo $site_score['success'] ?></h5>
                                                            </td>
                                                            <td class="failed">
                                                                <h4><?php _e( 'Failed', 'wp-hide-security-enhancer' ); ?></h4>
                                                                <h5><?php echo $site_score['failed'] ?></h5>
                                                            </td>
                                                        </tr></tbody></table>
                                            </div>
                                            <p class="actions">
                                                <button id="wph-site-scan-button" type="button" class="button <?php  if ( $scan_in_progress ) { echo 'disabled'; } ?> button-primary" onClick="WPH.site_scan( '<?php echo esc_attr ( wp_create_nonce( 'wph/site_scan') ) ?>')"><?php _e( 'Start New Scan', 'wp-hide-security-enhancer' ); ?></button>
                                                <span class="spinner" style="visibility: hidden;"></span>
                                                <span class="working"><?php _e( 'Working', 'wp-hide-security-enhancer' ); ?> <span class="progress">0</span> <?php _e( 'of', 'wp-hide-security-enhancer' ); ?> <span class="total_items"><?php echo count ( $this->scan_items ) ?></span> <?php _e( 'total tests', 'wp-hide-security-enhancer' ); ?></span> 
                                            </p>
                                            <?php
                                            
                                                //check if new items
                                                $found_new_scan_items   =   FALSE;
                                                foreach ( $this->scan_items as  $scan_item_id  =>  $item_instance )
                                                    {
                                                        $scan_item_data     =   $item_instance->get_settings();
                                                        $scan_response      =   isset ( $site_scan['results'][ $scan_item_id ] ) ?     $site_scan['results'][ $scan_item_id ] :   FALSE ;
                                                              
                                                        if ( ! $scan_response )
                                                            {
                                                                $found_new_scan_items   =   TRUE;
                                                                break;
                                                            }   
                                                    }
                                                
                                                //check for scann in progress
                                                if ( $scan_in_progress )
                                                    {
                                                        ?><p class="new-items"><?php _e( 'Another Scan instance in progress. Refresh the page in a minute.', 'wp-hide-security-enhancer' ) ?></p><?php
                                                    }
                                                    
                                                if ( ! $scan_in_progress  &&  $found_new_scan_items )
                                                    {
                                                        ?><p class="new-items"><?php _e( 'Found new Items, a new Security Scann is recommended.', 'wp-hide-security-enhancer' ) ?></p><?php
                                                    }
                                            
                                            ?>
                                            <p class="last_scan"><b><?php _e( 'Last Scan', 'wp-hide-security-enhancer' ); ?>:</b> <?php echo date( "Y-m-d H:i:s", $site_scan['last_scan'] );  ?></p>
                                            <?php if  ( empty ( $context ) ) { ?>
                                            <p class="security_hints"><?php echo $this->get_security_hints( $site_score ) ?></p>
                                            <?php } ?>
                                        <?php
                                    }
                            ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    
                }
                
                
            function get_site_score( $site_scan )
                {
                    $results    =   array();
                    $results['success']         =   0;
                    $results['failed']          =   0;
                    $results['total_score']     =   0;
                    $results['achieved_score']  =   0;
                    
                    $site_scan              =   (array)get_site_option('wph/site_scan');
                    $wph_site_scan_ignore   =   isset ( $site_scan['ignore'] )  ?   (array)$site_scan['ignore'] :   array();
                    
                    foreach ( $this->scan_items as  $scan_item_id  =>  $item_instance )
                        {
                            if ( in_array ( $scan_item_id, $wph_site_scan_ignore ) )
                                continue;
                                                        
                            $scan_item_data      =  $item_instance->get_settings();
                            $results['total_score']        +=  $scan_item_data['score_points'];
                            
                            if ( isset ( $site_scan['results'][$scan_item_id ] )   &&  $site_scan['results'][$scan_item_id ]->status    === TRUE )
                                $results['achieved_score'] +=  $scan_item_data['score_points'];
                                
                            if ( isset ( $site_scan['results'][$scan_item_id] ) &&  $site_scan['results'][$scan_item_id]->status  === FALSE )
                                $results['failed']     +=  1;
                                else if ( isset ( $site_scan['results'][$scan_item_id] )    &&  $site_scan['results'][$scan_item_id]->status  === TRUE )
                                $results['success']    +=   1;
                        }
                    
                    if ( $results['total_score'] > 0 )
                        $results['progress']    =   intval ( $results['achieved_score'] *   100 /   $results['total_score'] );
                        else
                        $results['progress']    =   0;
                        
                    $results['protection'] =   '';
                    if ( $results['progress'] < 30 )
                        $results['protection'] =  __( 'Very Poor' , 'wp-hide-security-enhancer' );
                    else if ( $results['progress'] >= 30 and $results['progress'] < 50 )
                        $results['protection'] =  __( 'Poor', 'wp-hide-security-enhancer' );
                    else if ( $results['progress'] >= 50 and $results['progress'] < 70 )
                        $results['protection'] =  __( 'Fair', 'wp-hide-security-enhancer' );
                    else if ( $results['progress'] >= 70 and $results['progress'] < 80 )
                        $results['protection'] =  __( 'Good', 'wp-hide-security-enhancer' );
                    else if ( $results['progress'] >= 80 and $results['progress'] < 90 )
                        $results['protection'] =  __( 'Great', 'wp-hide-security-enhancer' );
                    else if ( $results['progress'] >= 90 and $results['progress'] <= 99 )
                        $results['protection'] =  __( 'Excellent', 'wp-hide-security-enhancer' );
                    else if ( $results['progress'] > 99 )
                        $results['protection'] =  __( 'Perfect', 'wp-hide-security-enhancer' );
                        
                    $results['graph_progress'] =   round ( $results['progress'] * 180 / 100 );
                    
                    return $results;
                } 
            
                
            private function render_item( $scan_item_id, $scan_item_data, $response  )
                {
                    
                    ?>
                        <div id="item-<?php echo $scan_item_id ?>" class="postbox wph-postbox item<?php if ( $response->status ) { echo ' valid-item'; } ?>">
                            <div class="wph_input widefat<?php 
                                        if ( ! $response->status ) { echo ' issue_found';}
                                        else if ( $response->status ===  'unknown' ) { echo ' unknown';}
                                    ?>">
                                <div class="row cell label">
                                    <label><span class="dashicons <?php echo $scan_item_data['icon'] ?>"></span> <?php echo $scan_item_data['title'] ?></label>
                                    <p class="info"><?php echo $response->info; ?></p>
                                    <div class="description"><?php echo $response->description; ?></div>
                                    <div class="actions">
                                    <?php
                                        if ( count ( (array)$response->actions ) > 0 )
                                        foreach ( $response->actions    as  $action_type =>  $action )
                                            {
                                                echo  " " . $this->get_action_html( $action_type, $action, $scan_item_id );  
                                            }
                                        ?></div>
                                </div>

                            </div>
                            <div class="wph_help option_help">
                                <div class="text">
                                    <?php echo wpautop( $scan_item_data['help'] ) ?>
                                </div>
                            </div>
                        </div>    
                    <?php
                    
                }
                
            
            function get_security_hints( $site_score, $context  =   'security-scan-interface' )
                {
                    if (! is_array ( $site_score ) )
                        {
                            $site_scan  =   (array)get_site_option('wph/site_scan');
                            $site_score =   $this->get_site_score( $site_scan );
                        }
                    
                    $hints  =   '';
                        
                    if ( $site_score['progress'] < 90)
                        {
                            $level =    '';
                            switch ( $site_score['progress'] )
                                {
                                    case ( $site_score['progress'] >= 75 ):
                                                                            $level =    __( 'unsatisfactory', 'wp-hide-security-enhancer');
                                                                            break;
                                    case ( $site_score['progress'] > 40     &&   $site_score['progress'] < 75 ):
                                                                            $level =    __( 'unsatisfactory', 'wp-hide-security-enhancer');
                                                                            break;
                                    case ( $site_score['progress'] <= 40  ):
                                                                            $level =    __( 'dangerously low, an imminent security breach is highly likely.', 'wp-hide-security-enhancer');
                                                                            break;
                                }
                            
                            $hints  .= __( 'The current protection level is ' , 'wp-hide-security-enhancer' ) . $level . ' ' .__ ('Consider improving the overall security by fixing the issues reported by the Scan', 'wp-hide-security-enhancer' );
                            
                            if ( $context !=  'security-scan-interface'  )
                                $hints  .=  '<br /><br /><a class="button button-primary" href="' . network_admin_url ( 'admin.php?page=wp-hide-security-scan' ) . '">'. __( 'Security Scan', 'wp-hide-security-enhancer') .'</a>';
                        }
                        
                    return $hints;
                    
                }
                
                    
            private function get_action_html( $action_type, $action, $scan_item_id  )
                {
                    $html   =   '';
                    
                    switch( $action_type )
                        {
                            case 'ignore'   :
                                                $html   =   '<a class="button ignore" href="javascript: void(0)" onclick="WPH.scan_ignore_item(\'' . $scan_item_id . '\', \''. esc_attr ( wp_create_nonce( 'wph/site_scan/ignore') ) .'\')">'.  __( 'Ignore', 'wp-hide-security-enhancer' ) .'</a>';   
                                                break;
                            case 'restore'   :
                                                $html   =   '<a class="button restore" href="javascript: void(0)" onclick="WPH.scan_restore_item(\'' . $scan_item_id . '\', \''. esc_attr ( wp_create_nonce( 'wph/site_scan/restore') ) .'\')">'.  __( 'Restore', 'wp-hide-security-enhancer' )  .'</a>';   
                                                break;                            
                            default:            
                                                $html   =   $action;
                        }
                    
                    return $html;
                    
                }
                        
            
            function wp_ajax_wph_site_scan()
                {
                   
                    if ( ! wp_verify_nonce( $_POST['nonce'], 'wph/site_scan' ) ) 
                        die();    
                    
                    $this->get_HTML();
                    
                    $site_scan              =   (array)get_site_option('wph/site_scan');
                    
                    $response   =   array();
                    
                    //allow a timeout of 60 secconds
                    if ( isset ( $site_scan['last_scan_progress'] )     &&  $site_scan['last_scan_progress']    >   0   &&  $site_scan['last_scan_progress']    >   time() - 60 )
                        {
                            return __( 'Another Scan instance in progress. Please wait until completed.', 'wp-hide-security-enhancer' );
                        }
                    
                    $site_scan['results']   =   array();
                    
                    $progress   =   1;
                    
                    foreach ( $this->scan_items as  $scan_item  =>  $item_instance )
                        {
                            $site_scan['last_scan_progress']    =   time();
                            
                            $scan_item_data         =   $item_instance->get_settings();
                            $scan_response          =   json_decode( $item_instance->scan() );
                            
                            $site_scan['results'][ $scan_item ] =   $scan_response;
        
              
                            usleep ( 400000 );
                            
                            update_site_option( 'wph/site_scan', $site_scan );
                              
                            $progress++;
                        } 
                    
                    $site_scan['last_scan']             =   time();
                    $site_scan['visited']               =   md5 ( json_encode( $this->get_scan_items() ) );
                    $site_scan['last_scan_progress']    =   FALSE;
                    
                    update_site_option( 'wph/site_scan', $site_scan );
                    
                    _e( 'Scan completed.', 'wp-hide-security-enhancer' );
                    
                    die();
                
                }
                
            
            function wp_ajax_wph_site_scan_progress()
                {
                   
                    if ( ! wp_verify_nonce( $_POST['nonce'], 'wph/site_scan' ) ) 
                        die();    
                    
                    wp_ob_end_flush_all();
                                        
                    $site_scan              =   (array)get_site_option('wph/site_scan');
                    
                    $response   =   array();
                    $response['results']    =   $site_scan['results'];
                    $response['scann_in_progress']  =   ( isset ( $site_scan['last_scan_progress'] )  &&  $site_scan['last_scan_progress']    >   0 )  ?   TRUE: FALSE;
                    
                    if ( $response['scann_in_progress'] )
                        $response['scann_status']   =   'Working';
                        else
                        $response['scann_status']   =   'Idle';
                        
                    if ( count ( (array)$response['results'] ) > 0 )
                        {
                            foreach ( $response['results']  as  $scan_item_id =>  $item_scan_data )
                                {
                                    if ( count ( (array)$item_scan_data->actions ) > 0 )
                                        {
                                            $actions    =   '';
                                            foreach ( $item_scan_data->actions    as  $action_type =>  $action )
                                                {
                                                    $actions    .=  ' ' .   $this->get_action_html( $action_type, $action, $scan_item_id );  
                                                }
                                            $response['results'][$scan_item_id]->actions    =   $actions;
                                        }
                                }
                        }
                    
                    //check if timeout
                    if ( isset ( $site_scan['last_scan_progress'] )     &&  $site_scan['last_scan_progress']    >   0   &&  $site_scan['last_scan_progress']    <   time() - 60 )
                        {
                            $response['scann_in_progress']  =   FALSE;
                            $response['scann_status']       =   'Timed Out';
                        }
                        
                    $response['total']          =   count ( $this->scan_items );
                    $response['items_progress'] =   count ( $response['results'] );
                    
                    $results    =   $this->get_site_score( $site_scan );
                    
                    $response['success']        =   $results['success'];
                    $response['failed']         =   $results['failed'];
                    $response['graph_progress'] =   $results['graph_progress'];
                    $response['progress']       =   $results['progress'];
                    $response['protection']     =   __( $results['protection'],    'wp-hide-security-enhancer');
                                        
                    echo json_encode( $response );
                    
                    die();
                
                }
                
            function wp_ajax_wph_site_scan_ignore()
                {
                    
                    if ( ! wp_verify_nonce( $_POST['nonce'], 'wph/site_scan/ignore' ) ) 
                        die();    
                    
                    $item_id    =   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_POST['item_id'] );
                    
                    if ( ! empty ( $item_id ) )
                        {
                            $site_scan              =   (array)get_site_option('wph/site_scan');
                            $wph_site_scan_ignore   =   (array)$site_scan['ignore'];
                            $wph_site_scan_ignore[] =   $item_id;
                            $wph_site_scan_ignore   =   array_unique ( array_filter ( $wph_site_scan_ignore ) );
                            
                            $wph_site_scan_ignore   =   array_unique ( array_filter ( $wph_site_scan_ignore ) );
                            
                            $site_scan['ignore']    =   $wph_site_scan_ignore;
                            
                            update_site_option ( 'wph/site_scan', $site_scan );
                        }
                    
                    $response   =   array();
                    $response['item_id']    =   $item_id;
                    
                    $site_scan  =   (array)get_site_option('wph/site_scan');
                    $site_score     =   $this->get_site_score( $site_scan );
                    $response       =   $response   +   $site_score;
                    
                    echo json_encode( $response );
                        
                    die(); 
                }
                
            function wp_ajax_wph_site_scan_restore()
                {
                    
                    if ( ! wp_verify_nonce( $_POST['nonce'], 'wph/site_scan/restore' ) ) 
                        die();    
                    
                    $item_id    =   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_POST['item_id'] );
                    
                    if ( ! empty ( $item_id ) )
                        {
                            $site_scan              =   (array)get_site_option('wph/site_scan');
                            $wph_site_scan_ignore   =   (array)$site_scan['ignore'];
                            $index  =   array_search( $item_id, $wph_site_scan_ignore );
                            if ( $index !== FALSE )
                                unset ( $wph_site_scan_ignore[$index] );
                                
                            $wph_site_scan_ignore   =   array_unique ( array_filter ( $wph_site_scan_ignore ) );
                            
                            $site_scan['ignore']    =   $wph_site_scan_ignore;
                            
                            update_site_option ( 'wph/site_scan', $site_scan );
                        }
                        
                    $response   =   array();
                    $response['item_id']    =   $item_id;
                    
                    $site_scan  =   (array)get_site_option('wph/site_scan');
                    $site_score     =   $this->get_site_score( $site_scan );
                    $response       =   $response   +   $site_score;
                    
                    echo json_encode( $response );
                        
                    die(); 
                }
                
            function get_remote_content()
                {
                    if ( $this->remote_errors   !== FALSE )
                        return FALSE;
                        
                    if ( $this->remote_html   === FALSE )
                        $this->get_HTML();
                
                    return $this->remote_html;
                }
                
            
            function get_remote_headers()
                {
                    if ( $this->remote_errors   !== FALSE )
                        return FALSE;
                        
                    return $this->remote_headers;
                }
                    
            function get_HTML()
                {
                    $this->remote_started   =   TRUE;
                    
                    $args    =   array( 
                                        'sslverify' => false, 
                                        'timeout' => 30 
                                        );
                    $site_url   =   apply_filters( 'wp-hide/security-scan/url', home_url() );
                    $response   =   wp_remote_get( $site_url, $args  );
                    
                    if ( is_a( $response, 'WP_Error' ))
                        {
                            $this->remote_errors   =   $response->get_error_message();
                            return FALSE;
                        }
                    
                    if ( is_array( $response ) ) 
                        {
                            
                            if  ( ! isset( $response['response']['code'] ) )
                                return FALSE;
                            
                            if  ( $response['response']['code'] !=  200 )
                                {
                                    if ( $response['response']['code'] ==  404 )
                                        {
                                            $this->remote_errors   =   __( "The wp_remote_get() returns a Not Found page.", 'wp-hide-security-enhancer' );
                                            return FALSE;
                                        }
                                    
                                    if ( $response['response']['code'] ==  401 )
                                        {
                                            $this->remote_errors   =   __( "The wp_remote_get() returns a 401 error code, the request could not be authenticated. Does the site use an httpd password?", 'wp-hide-security-enhancer' );
                                            return FALSE;
                                        }
                                        
                                    if ( ! empty ( $response['response']['code'] ) )
                                        {
                                            $this->remote_errors    =   __( "The wp_remote_get() returns a", 'wp-hide-security-enhancer' ) . " " . $response['response']['code'] . " " . __( "error code", 'wp-hide-security-enhancer' );
                                            return FALSE;
                                        }
                                        
                                    $this->remote_errors   =    __( "Unespected error code for wp_remote_get() call.", 'wp-hide-security-enhancer' );
                                    return FALSE;
                                }
                                
                            $this->remote_html      =   $response['body'];
                            $this->remote_headers   =   $response['http_response']->get_headers();
                                
                            return TRUE;
                                
                        }
                        
                    return FALSE;
                    
                }
                
                
                

    
     
              
            
        }


?>