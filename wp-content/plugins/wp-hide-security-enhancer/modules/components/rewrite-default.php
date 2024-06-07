<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_default extends WPH_module_component
        {
            
            function get_component_id()
                {
                    return '_rewrite_default_';
                    
                }
                                    
            function get_module_settings()
                {
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'rewrite_default',
                                                                    'visible'       =>  FALSE,
                                                                    'processing_order'  =>  1
                                                                    );
                                                                    
                    return $this->module_settings;   
                }
                
                
                
            function _init_rewrite_default (   $saved_field_data   )
                {    
                    
                    //ensure to revert any urls of the superglobalvariables
                    add_action( 'wp-hide/modules_components_run/completed', array( $this, '_modules_components_run_completed' ) );
                        
                }
                

                
            function _callback_saved_rewrite_default($saved_field_data)
                {
                    $processing_response    =   array();

                                
                    return  $processing_response;   
                }
                
        
                
            /**
            * re-Map the replacements to GET/POST/REQUET
            *     
            */
            function _do_superglobal_variables_replacements( $replacements )
                {
                    
                    if ( count ( $_GET ) >  0   )
                        {
                            foreach  ( $_GET            as  $key    =>  $value)
                                {
                                    if  ( is_array($value) )
                                        {
                                            $_GET[ $key ]  =   $this->_array_replacements_recursivelly( $_GET[ $key ], $replacements, 'GET' );
                                                                        
                                            $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                            if  ( $_key !=  $key )
                                                $_GET[ $_key ]  =   $_GET[ $key ];
                                                
                                            continue;
                                        }
                                        
                                    if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, 'GET' ) )
                                        continue;
                                    
                                    $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                    $_value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    
                                    if  ( $_key !=  $key    ||  $_value !=  $value )
                                        $_GET[ $_key ]  =   $_value;
                                }
                        }
                        
                    if ( count ( $_POST ) >  0   )
                        {
                            foreach  ( $_POST            as  $key    =>  $value)
                                {
                                    if  ( is_array($value) )
                                        {
                                            $_POST[ $key ]  =   $this->_array_replacements_recursivelly( $_POST[ $key ], $replacements, 'POST' );
                                                                        
                                            $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                            if  ( $_key !=  $key )
                                                $_POST[ $_key ]  =   $_POST[ $key ];
                                                
                                            continue;
                                        }
                                    
                                    if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, 'POST' ) )
                                        continue;
                                        
                                    $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                    $_value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    
                                    if  ( $_key !=  $key    ||  $_value !=  $value )
                                        $_POST[ $_key ]  =   $_value;
                                }
                        }
                        
                    if ( count ( $_REQUEST ) >  0   )
                        {
                            foreach  ( $_REQUEST            as  $key    =>  $value)
                                {
                                    if  ( is_array($value) )
                                        {
                                            $_REQUEST[ $key ]  =   $this->_array_replacements_recursivelly( $_REQUEST[ $key ], $replacements, 'REQUEST' );
                                                                        
                                            $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                            if  ( $_key !=  $key )
                                                $_REQUEST[ $_key ]  =   $_REQUEST[ $key ];
                                                
                                            continue;
                                        }
                                    
                                    if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, 'REQUEST' ) )
                                        continue;
                                        
                                    $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                    $_value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    
                                    if  ( $_key !=  $key    ||  $_value !=  $value )
                                        $_REQUEST[ $_key ]  =   $_value;
                                }
                        }   
                        
                        
                    if ( count ( $_FILES ) >  0   )
                        {
                            foreach  ( $_FILES            as  $key    =>  $value)
                                {
                                    if  ( is_array($value) )
                                        {
                                            $_FILES[ $key ]  =   $this->_array_replacements_recursivelly( $_FILES[ $key ], $replacements, 'FILES' );
                                                                        
                                            $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                            if  ( $_key !=  $key )
                                                $_FILES[ $_key ]  =   $_FILES[ $key ];
                                                
                                            continue;
                                        }
                                    
                                    if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, 'FILES' ) )
                                        continue;
                                        
                                    $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                    $_value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    
                                    if  ( $_key !=  $key    ||  $_value !=  $value )
                                        $_FILES[ $_key ]  =   $_value;
                                }
                        } 
                    
                    
                }
                
                
            
            function _modules_components_run_completed()
                {
                    
                    $replacement_list   =   $this->wph->functions->get_replacement_list();
                    foreach ( $replacement_list as $key =>  $value )
                        {
                            $replacement_list[ $key ]   =   '/' . preg_quote ( $value, '/' ) . '/';
                        }
                        
                    $this->_do_superglobal_variables_replacements( $replacement_list );   
                    
                }
                
                
            function _array_replacements_recursivelly ( $array, $replacements, $superglobal_type ) 
                {
                    if ( !is_array( $array ) ) 
                        return $array;
                    
                    $helper = array();
                    
                    foreach ($array as $key => $value) 
                        {
                            if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, $superglobal_type ) )
                                continue;
                            
                            $key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                            
                            if ( is_array( $value ) )
                                $value  =   $this->_array_replacements_recursivelly( $value, $replacements, $superglobal_type );
                                else 
                                {
                                    //preserve the type
                                    $value_type =   gettype( $value );
                                    $value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    settype( $value, $value_type );
                                }
                            
                            $helper[ $key ] = $value;
                        }
                    
                    return $helper;
                }
                
            
        }
?>