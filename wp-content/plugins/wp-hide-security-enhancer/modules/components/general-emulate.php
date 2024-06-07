<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_emulate extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Emulate CMS";
                }
                                        
            function get_module_settings()
                {
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'emulate_cms',
                                                                    'label'         =>  __('Emulate CMS',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Use the option to output specific CMSs HTML traces to mislead any peculiar check.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Emulate CMS',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Using the option the system try to misguide the used WordPress by outputting the wrong traces, of the selected CMS.",    'wp-hide-security-enhancer') . "<br />" .
                                                                                                                                __("Misleading and making a false lead provides an extra security, as the attacker search and attempt to hack something which not exist.",    'wp-hide-security-enhancer') . "<br />" .
                                                                                                                                __("The plugin can emulate systems like:",    'wp-hide-security-enhancer') . "<br />" .
                                                                                                                                __("- Drupal",    'wp-hide-security-enhancer') . "<br />" . 
                                                                                                                                __("- Ghost",    'wp-hide-security-enhancer') . "<br />" .
                                                                                                                                __("- HubSpot",    'wp-hide-security-enhancer') . "<br />" .
                                                                                                                                __("- Joomla",    'wp-hide-security-enhancer') . "<br />" .
                                                                                                                                __("- TYPO3",    'wp-hide-security-enhancer') . "<br />" .
                                                                                                                                __("- Wix",    'wp-hide-security-enhancer') . "<br />" ,
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/general-emulate-cms/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'            =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'drupal_8'      =>  __('Drupal 8',    'wp-hide-security-enhancer'),
                                                                                                'drupal_9'      =>  __('Drupal 9',    'wp-hide-security-enhancer'),
                                                                                                'ghost_4_15'    =>  __('Ghost 4.15',    'wp-hide-security-enhancer'),
                                                                                                'ghost_4_31'    =>  __('Ghost 4.31',    'wp-hide-security-enhancer'),
                                                                                                'hubspot'       =>  __('HubSpot',    'wp-hide-security-enhancer'),
                                                                                                'joomla'        =>  __('Joomla',    'wp-hide-security-enhancer'),
                                                                                                'typo3'         =>  __('TYPO3',    'wp-hide-security-enhancer'),
                                                                                                'wix'           =>  __('Wix',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower')
                                                                    
                                                                    ); 
                  
                                                                    
                    return $this->module_settings;   
                }
                
                
                
            function _init_emulate_cms( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( is_admin() )
                        return;
                        
                    add_filter( 'wp-hide/ob_start_callback',         array( &$this, 'emulate_cms_meta' ), 999 );
                }
                
            
            function emulate_cms_meta( $buffer )
                {
                    //ensure this is a html content
                    if ( preg_match ( '/<[\/\s]?head/', $buffer ) !== 1 ||  preg_match ( '/<[\/\s]?body/', $buffer ) !== 1 )
                        return $buffer;
                        
                    $data_split =   preg_split('/<body/i', $buffer );   
                    $header_content =   $data_split[0];
                    unset ( $data_split[0] );
                    $body_content   =   implode ( '<body', $data_split);
                    
                    $emulate_cms     =   $this->wph->functions->get_module_item_setting('emulate_cms');
                    switch ( $emulate_cms )
                        {
                            case "drupal_8":    
                                                $headers    =   '<meta name="Generator" content="Drupal 8 (https://www.drupal.org)" />' . "\n" .
                                                                '<meta name="MobileOptimized" content="width" />' . "\n" .
                                                                '<meta name="HandheldFriendly" content="true" />';
                                                if ( stripos ( $header_content, '<meta' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<meta[^>]*>)/i', $headers . "\n" . '$1', $header_content, 1 );
                                                    else if ( stripos ( $header_content, '<head' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<head[^>]*>)/is', '$1' . "\n" . $headers , $header_content, 1 );
                                                break;
                            
                            case "drupal_9":    
                                                $headers    =   '<meta name="Generator" content="Drupal 9 (https://www.drupal.org)" />' . "\n" .
                                                                '<meta name="MobileOptimized" content="width" />' . "\n" .
                                                                '<meta name="HandheldFriendly" content="true" />';
                                                if ( stripos ( $header_content, '<meta' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<meta[^>]*>)/i', $headers . "\n" . '$1', $header_content, 1 );
                                                    else if ( stripos ( $header_content, '<head' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<head[^>]*>)/is', '$1' . "\n" . $headers , $header_content, 1 );
                                                break;
                            
                            case "ghost_4_15":    
                                                $headers    =   '<meta name="generator" content="Ghost 4.15" />';
                                                if ( stripos ( $header_content, '<meta' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<meta[^>]*>)/i', $headers . "\n" . '$1', $header_content, 1 );
                                                    else if ( stripos ( $header_content, '<head' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<head[^>]*>)/is', '$1' . "\n" . $headers , $header_content, 1 );
                                                break;
                            
                            case "ghost_4_31":    
                                                $headers    =   '<meta name="generator" content="Ghost 4.31" />';
                                                if ( stripos ( $header_content, '<meta' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<meta[^>]*>)/i', $headers . "\n" . '$1', $header_content, 1 );
                                                    else if ( stripos ( $header_content, '<head' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<head[^>]*>)/is', '$1' . "\n" . $headers , $header_content, 1 );
                                                break;
                                                
                            case "hubspot":    
                                                $headers    =   '<meta name="generator" content="HubSpot" />';
                                                if ( stripos ( $header_content, '<meta' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<meta[^>]*>)/i', $headers . "\n" . '$1', $header_content, 1 );
                                                    else if ( stripos ( $header_content, '<head' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<head[^>]*>)/is', '$1' . "\n" . $headers , $header_content, 1 );
                                                break;
                            
                            case "joomla":    
                                                $headers    =   '<meta name="generator" content="Joomla! - Open Source Content Management" />';
                                                if ( stripos ( $header_content, '<meta' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<meta[^>]*>)/i', $headers . "\n" . '$1', $header_content, 1 );
                                                    else if ( stripos ( $header_content, '<head' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<head[^>]*>)/is', '$1' . "\n" . $headers , $header_content, 1 );
                                                    
                                                $header_content =   $header_content . "\n" . '<!-- URL Normalizer (by JoomlaWorks) -->';
                                                break;
                                                
                            case "typo3":    
                                                $headers    =   '<meta name="generator" content="TYPO3 CMS" />' . "\n" .
                                                                '<!-- 
    This website is powered by TYPO3 - inspiring people to share!
    TYPO3 is a free open source Content Management Framework initially created by Kasper Skaarhoj and licensed under GNU/GPL.
    TYPO3 is copyright 1998-2021 of Kasper Skaarhoj. Extensions are copyright of their respective owners.
    Information and contribution at https://typo3.org/
-->';
                                                if ( stripos ( $header_content, '<meta' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<meta[^>]*>)/i', $headers . "\n" . '$1', $header_content, 1 );
                                                    else if ( stripos ( $header_content, '<head' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<head[^>]*>)/is', '$1' . "\n" . $headers , $header_content, 1 );
                                                    
                                                break;
                                                
                            case "wix":    
                                                $headers    =   '<meta name="generator" content="Wix.com Website Builder" />';
                                                if ( stripos ( $header_content, '<meta' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<meta[^>]*>)/i', $headers . "\n" . '$1', $header_content, 1 );
                                                    else if ( stripos ( $header_content, '<head' ) !== FALSE )
                                                    $header_content =   preg_replace( '/(<head[^>]*>)/is', '$1' . "\n" . $headers , $header_content, 1 );
                                                break;
                            
                        }
                    
                    
                    $buffer =   $header_content .   '<body'  .   $body_content;
                                        
                    return $buffer;
                    
                }
                    
         
        }
?>