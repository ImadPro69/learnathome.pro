<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_widgets
        {
            var $wph;
                                  
            function __construct()
                {
                    global $wph;
                    $this->wph          =   &$wph;
                }
            
            function _get_dashboard_overview_widget_id()
                {
                    return 'wp-hide-overview';
                }
            
            
            function dashboard_overview_styles()
                {
                    wp_register_style('wph-graphs', WPH_URL . '/assets/css/graph.css');
                    wp_enqueue_style( 'wph-graphs');
                    
                    wp_register_style('wph-security-scan', WPH_URL . '/assets/css/security-scan.css');
                    wp_enqueue_style( 'wph-security-scan');
                    
                    wp_register_style('wph-dashboard-widget', WPH_URL . '/assets/css/dashboard-widget.css');
                    wp_enqueue_style( 'wph-dashboard-widget');
                }
                
                
            function dashboard_overview_widget_content()
                {   
                    $this->dashboard_overview_styles();
                    
                    $site_scan  =   (array)get_site_option('wph/site_scan');
                    $this->wph->security_scan->render_overview( $site_scan, 'widget' );
                    
                    $site_score =    $this->wph->security_scan->get_site_score( $site_scan );
                    
                    if ( isset ( $site_scan['last_scan'] )   &&  ! empty ( $site_scan['last_scan'] ) )
                        {
                            ?>
                            <p><?php _e( 'Your current estimated protection is',    'wp-hide-security-enhancer') ?> <b><?php _e( $site_score['protection'],    'wp-hide-security-enhancer') ?></b>.<br /><?php
                            
                                echo $this->wph->security_scan->get_security_hints( $site_score, 'widget' );
                            
                            ?></p>
                            <?php
                        }
                        else
                        {
                            ?>
                            <p><?php _e( 'Run a fist scan to determine the current protection level of your website.',    'wp-hide-security-enhancer') ?><br /><br /><a class="button button-primary" href="<?php echo network_admin_url ( 'admin.php?page=wp-hide-security-scan' ) ?>"><?php _e( 'Security Scan', 'wp-hide-security-enhancer') ?></a></p>
                            <?php
                        }
                    
                }
                
        }
        
        
?>