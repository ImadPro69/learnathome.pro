<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_check_headers extends WPH_module_component
        {
            
            public $headers = array ();
            
            function get_component_title()
                {
                    return "Check Headers";
                }
                                    
            function get_module_settings()
                {
                    
                    $this->_set_headers();
                    
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'check_headers',
                                                                    'label'         =>  __('Check Headers',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'description'               =>  '<h4 class="important">'. __("HTTP Response Headers are a powerful tool to Harden Your Website.<br />Misusing the headers, can easily break the site layout and functionality. Ensure you understand the proper usage for each option before configuring. Once the Headers setup completed, a thorough check for the front side is recommended.",    'wp-hide-security-enhancer') . '</h4>' .
                                                                                                                                
                                                                                                                                "<div class='help-section'><h4>" . __( "Recovery", 'wp-hide-security-enhancer' ) . '</h4>' .
                                                                                                                                '<p class="important"><span class="dashicons dashicons-warning important" alt="f534"></span> ' . __('Copy the following link to a safe place. You can use it to reset the header options if something goes wrong:',    'wp-hide-security-enhancer') . '</p><p> <b><span id="wph-recovery-link" onClick="WPH.selectText( \'wph-recovery-link\' )">' . trailingslashit ( home_url() ) . '?wph-recovery=' . $this->wph->functions->get_recovery_code() .'&reset_headers=1&rand=' . rand( 10000,9999999) .'</span></b></p></div>' .    
                                                                                                                                
                                                                                                                                "<div class='help-section'><h4>" . __( "Sample Setup", 'wp-hide-security-enhancer' ) . '</h4>' .
                                                                                                                                '<p>' . __('Create a sample setup for Headers. That will overwrite any Headers settings previously created through the plugin options. The sample setup creates a basic Headers implementation that is commonly safe on any site. For better performances, further manual adjustments are necesarelly.',    'wp-hide-security-enhancer') .'</p><p><input type="hidden" name="wph-headers-sample-setup" value="true" /><input type="button" class="button-secondary" value="' . __('Create Sample Setup',    'wp-hide-security-enhancer') .'" onclick="WPH.runSampleHeaders();"></p></div>' .
                                                                                                                                
                                                                                                                                "<br /><br />" .__("The Hypertext Transfer Protocol (HTTP) is based on a client-server architecture, in which the client ( typically a web browser application ) establishes a connection with the server through a destination URL and waits for a response.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />" .__("The HTTP Headers allow the client and the server send additional pieces of information with the HTTP request or response.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />" .__("The HTTP Headers are categorised by their purpose: Authentication, Caching, Client hints, Conditionals, Connection management, Content negotiation, Controls, Cookies, CORS, Downloads, Message body information, Proxies, Redirects, Request context, Response context, Range requests, <b>Security</b>, Server-sent events, Transfer coding, WebSockets, Other",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />" . __("This area provides support for the <b>",    'wp-hide-security-enhancer').  '<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security" target="_blank">Security Headers</b></a>' . __(" type. Those are the ones responsible for the security implementation for any page.",    'wp-hide-security-enhancer') ,
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/harden-your-website-using-security-headers/'
                                                                                                ),
                                                                    
                                                                    'interface_help_split'  =>  FALSE,
                                                                    
                                                                    'require_save'          =>  FALSE,
                                                                                        
                                                                    'input_type'            =>  'custom',
                                                                    'default_value'         =>  array(),
                                                                    
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                    
                                                                    );
                 
                    
                                                                    
                    return $this->module_settings;   
                }
                
            
            private function _set_headers()
                {
                    $this->headers['cross-origin-embedder-policy']    =   array ( 
                                                                                'title'         =>  'Cross-Origin-Embedder-Policy',
                                                                                'description'   =>  __('Allows a server to declare an embedder policy for a given document.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'all'
                                                                                );   
                    $this->headers['cross-origin-opener-policy']    =   array ( 
                                                                                'title'         =>  'Cross-Origin-Opener-Policy',
                                                                                'description'   =>  __('Prevents other domains from opening/controlling a window.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'all'
                                                                                );
                    $this->headers['cross-origin-resource-policy']    =   array ( 
                                                                                'title'         =>  'Cross-Origin-Resource-Policy',
                                                                                'description'   =>  __('Prevents other domains from reading the response of the resources to which this header is applied.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'all'
                                                                                );
                    $this->headers['content-security-policy']    =   array ( 
                                                                                'title'         =>  'Content-Security-Policy',
                                                                                'description'   =>  __('Controls resources the user agent is allowed to load for a given page.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'pro'
                                                                                );
                    $this->headers['content-security-policy-report-only']    =   array ( 
                                                                                'title'         =>  'Content-Security-Policy-Report-Only',
                                                                                'description'   =>  __('Allows web developers to experiment with policies by monitoring, but not enforcing, their effects. These violation reports consist of JSON documents sent via an HTTP POST request to the specified URI.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'pro'
                                                                                );
                    $this->headers['permissions-policy']    =   array ( 
                                                                                'title'         =>  'Permissions-Policy',
                                                                                'description'   =>  __('Provides a mechanism to allow and deny the use of browser features in its own frame, and in iframes that it embeds.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'pro'
                                                                                );
                    $this->headers['referrer-policy']    =   array ( 
                                                                                'title'         =>  'Referrer-Policy',
                                                                                'description'   =>  __('A policy that controls how much information is shared through the HTTP referrer header. Helps to protect user privacy.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy',
                                                                                'availability'  =>  'all'
                                                                                );
                    $this->headers['strict-transport-security']    =   array ( 
                                                                                'title'         =>  'Strict-Transport-Security',
                                                                                'description'   =>  __('Force communication using HTTPS instead of HTTP.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'pro'
                                                                                );
                    $this->headers['x-content-type-options']    =   array ( 
                                                                                'title'         =>  'X-Content-Type-Options',
                                                                                'description'   =>  __('Disables MIME sniffing and forces browser to use the type given in Content-Type.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'all'
                                                                                );
                    $this->headers['x-download-options']    =   array ( 
                                                                                'title'         =>  'X-Download-Options',
                                                                                'description'   =>  __('The X-Download-Options HTTP header indicates that the browser (Internet Explorer) should not display the option to "Open" a file that has been downloaded from an application, to prevent phishing attacks as the file otherwise would gain access to execute in the context of the application.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'all'
                                                                                );
                    $this->headers['x-frame-options']    =   array ( 
                                                                                'title'         =>  'X-Frame-Options',
                                                                                'description'   =>  __('Indicates whether a browser should be allowed to render a page in a &#60;frame&#62;, &#60;iframe&#62;, &#60;embed&#62; or &#60;object&#62;',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'all'
                                                                                );
                    $this->headers['x-permitted-cross-domain-policies']    =   array ( 
                                                                                'title'         =>  'X-Permitted-Cross-Domain-Policies',
                                                                                'description'   =>  __('Specifies if a cross-domain policy file (crossdomain.xml) is allowed. The file may define a policy to grant clients, such as Adobe\'s Flash Player (now obsolete), Adobe Acrobat, Microsoft Silverlight (now obsolete), or Apache Flex, permission to handle data across domains that would otherwise be restricted due to the Same-Origin Policy. See the Cross-domain Policy File Specification for more information.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'all'
                                                                                );
                    $this->headers['x-xss-protection']    =   array ( 
                                                                                'title'         =>  'X-XSS-Protection',
                                                                                'description'   =>  __('Created for browsers equipped with XSS filters, this non-standard header was intended as a way to control the filtering functionality.',    'wp-hide-security-enhancer'),
                                                                                'link'          =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security',
                                                                                'availability'  =>  'all'
                                                                                );
                }
            
            
            function _init_check_headers( $saved_field_data )
                {
                    add_action( 'wp_ajax_wph_check_headers', array ( $this, 'wp_ajax_wph_check_headers' ) ); 
                }
                
                
            function _module_option_html( $module_setting )
                {
                    ?>
                    <br />
                    <h4><?php _e( 'The current protection level is', 'wp-hide-security-enhancer') ?></h4>
                    <br />
                    <link rel="stylesheet" href="<?php echo WPH_URL; ?>/assets/css/graph.css" />
                    <div id="wph-graph">
                        <div class="wph-graph-container">
                            <div class="wph-graph-bg"></div>
                            <div class="wph-graph-text"></div>
                            <div class="wph-graph-progress"></div>
                            <div class="wph-graph-data"><span>Check Headers First.</span></div>
                        </div>
                    </div>
                    <div id="wph-check-headers">
                        <button id="wph-check-headers-button" type="button" class="button button-primary" onClick="WPH.check_headers( '<?php echo esc_attr ( wp_create_nonce( 'wph/check_headers') ) ?>')"><?php _e('Check Current Headers',    'wp-hide-security-enhancer') ?></button><span class="spinner"></span>
                    </div>
                    <div id="wph-headers-container"></div>
                    <script type="text/javascript">
                        jQuery('#wph-check-headers-button').click();    
                    </script>
                    <?php
                }
                
                
            function wp_ajax_wph_check_headers()
                {
                   
                    if ( ! wp_verify_nonce( $_POST['nonce'], 'wph/check_headers' ) ) 
                        die();    
                    
                    $_JSON_response    =   array();
                    
                    $site_url   =   apply_filters( 'wp-hide/check_headers/url', home_url() );
                    $response   =   wp_remote_head( $site_url, array( 'sslverify' => false, 'timeout' => 40 )  );
                    
                    if ( ! is_array( $response ) ) 
                        {
                            $_JSON_response['html']  =   __( "<br />Unable to parse the site Headers. The wp_remote_head() returned an invalid Response, check with your host support for more details.  Unable to identify your site Headers.", 'wp-hide-security-enhancer' );
                            if ( is_wp_error( $response ) )
                                $_JSON_response['html']  .= "<br /><b>" . $response->get_error_message() . '</b>';
                            $_JSON_response['graph']['message'] = 'Error';
                            $_JSON_response['graph']['value']   = '0';
                            echo json_encode( $_JSON_response );
                            die();   
                        }
                        
                    $http_response =   $response['http_response'];
                    if ( ! is_object( $http_response ) )
                        {
                            $_JSON_response['html']  =   __( "<br />Invalid WP_HTTP_Requests_Response object. The wp_remote_head() returned an invalid Response, check with your host support for more details.", 'wp-hide-security-enhancer' );
                            $_JSON_response['graph']['message'] = 'Error';
                            $_JSON_response['graph']['value']   = '0';
                            echo json_encode( $_JSON_response );
                            die();   
                        }   
                    
                    if  ( empty ( $http_response->get_status() ) )
                        {
                            $_JSON_response['html']  =   __( "<br />Unable to parse the site Headers. The wp_remote_head() returns invalid Response Code, check with your host support for more details.", 'wp-hide-security-enhancer' );
                            $_JSON_response['graph']['message'] = 'Error';
                            $_JSON_response['graph']['value']   = '0';
                            echo json_encode( $_JSON_response );
                            die();
                        }
                    if  ( $http_response->get_status() !=  200 )
                        {
                            if ( $http_response->get_status() ==  401 )
                                {
                                    $_JSON_response['html']  =   __( "<br />Unable to parse the site Headers. The wp_remote_head() returns a 401 error code, the request could not be authenticated. Does the site use an httpd password?", 'wp-hide-security-enhancer' );
                                    $_JSON_response['graph']['message'] = 'Error';
                                    $_JSON_response['graph']['value']   = '0';
                                    echo json_encode( $_JSON_response );
                                    die();
                                }
                            
                            $_JSON_response['html']  =   __( "<br />Unable to parse the site Headers. The wp_remote_head() returns wrong Response Code", 'wp-hide-security-enhancer' ) . $http_response->get_status() . __(", check with your host support for more details.", 'wp-hide-security-enhancer' );
                            $_JSON_response['graph']['message'] = 'Error';
                            $_JSON_response['graph']['value']   = '0';
                            echo json_encode( $_JSON_response );
                            die();
                        }    
                    
                    $headers    =   $http_response->get_headers();
                    
                    ob_start();
                    
                    ?>
                    <div id="wph-headers">
                        <table class="found-headers">
                            <thead>
                                <tr>
                                    <th style="width: 30%"><?php _e('Header', 'wp-hide-security-enhancer') ?></th>
                                    <th><?php _e('Value', 'wp-hide-security-enhancer') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    
                                    $found_headers  =   array ( );
                                    
                                    foreach ( $headers->getAll() as $header_key =>  $header_value )
                                        {
                                            $header_key =   strtolower ( $header_key ) ;
                                            $header_key =   trim ( $header_key );
                                            
                                            $is_security_header =   FALSE;
                                            
                                            if ( isset( $this->headers[ $header_key ] ) )
                                                {
                                                    $is_security_header =   TRUE;   
                                                    $found_headers[]    =   $header_key;
                                                }
                                            ?>
                                            <tr<?php if ( $is_security_header ){ echo ' class="security-header" ';} ?>>
                                                <td style="width: 30%"><?php echo $header_key ?><?php if ( $is_security_header ){ echo ' <span class="dashicons dashicons-saved"></span>';} ?></td>
                                                <td><?php 
                                                    
                                                    if (  is_array ( $header_value ) )
                                                        echo implode( "<br />", array_map( 'htmlspecialchars', $header_value ) ) ;
                                                        else
                                                        echo htmlspecialchars ( $header_value ); 
                                                ?></td>
                                            </tr>
                                            <?php
                                        }
                                       ?>            
                            </tbody>
                        </table>
                    </div>
                    <p class="found-headers-info"><small>[ Found <?php echo count ( $found_headers ) ?> security headers ]</small></p>
                    <?php
                    
                        //check if all expected headers
                        $site_settings      =   $this->wph->settings;
                        $modules_settings   =   $site_settings['module_settings'];
                        
                        $expected_headers   =   array ();
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
                                    continue;
                                
                                if ( $modules_settings[ $header ]['enabled']    ==   'yes' )
                                    $expected_headers[]    =   str_replace( "_", "-", $header );
                            }
                    
                        $headers_not_found  =   array_diff( $expected_headers, $found_headers );
                        if ( count ( $headers_not_found ) > 0 )
                            {
                                ?>
                                <h4 class="important"><?php _e('Warning! The following headers could not be found:', 'wp-hide-security-enhancer' ); echo "<br />" . implode( '<br />', $headers_not_found); ?></h4>
                                <p class="important"><?php _e('Ensure the server mod_headers module is active.', 'wp-hide-security-enhancer' ); ?></p>
                                <?php   
                                
                            }
                    
                    ?>
                    <p>&nbsp;</p>
                    <h4><?php _e('Consider adding more security headers:', 'wp-hide-security-enhancer') ?></h4>
                    <?php
                    
                        foreach ( $this->headers    as $header_key  =>  $header_data )
                            {
                                if ( in_array ( $header_key, $found_headers ) )
                                    continue;
                                    
                                ?><p><a href="<?php echo $header_data['link'] ?>" target="_blank"><code><?php echo $header_key ?></code></a><?php  if ( $header_data['availability'] == 'pro' ) { echo ' <span class="wph-pro">PRO</span>'; } ?><br /><?php echo $header_data['description'] ?></p><?php   
                            }

                    $_JSON_response['html']  =   ob_get_clean();
                    
                    $progress   =   round ( count ( $found_headers ) * 100 / 12 );
                    if ( $progress < 1 )
                        $progress   =   1;
                    $_JSON_response['graph']['value']   =   round ( $progress * 180 / 100 );
                    
                    $_JSON_response['graph']['message'] =   "<b>" . $progress . '%</b>';
                    $_JSON_response['graph']['message'] .= '<br />';
                    if ( $progress < 20 )
                        $_JSON_response['graph']['message'] .=  'Poor';
                        else if ( $progress >= 20 and $progress < 40 )
                        $_JSON_response['graph']['message'] .=  'Fair';
                        else if ( $progress >= 40 and $progress < 60 )
                        $_JSON_response['graph']['message'] .=  'Good';
                        else if ( $progress >= 60 and $progress < 80 )
                        $_JSON_response['graph']['message'] .=  'Great';
                        else if ( $progress >= 80 )
                        $_JSON_response['graph']['message'] .=  'Excellent';
                    
                    echo json_encode( $_JSON_response );
                    
                    die();
                    
                }
            

        }
?>