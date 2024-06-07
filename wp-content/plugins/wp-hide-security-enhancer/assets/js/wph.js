

    class WPH_Class  {
        
            constructor() {
                this.SiteScanProgress_interval = false;
                this.AJAX_data  =   false  
            }
            
            selectText(node) 
                {
                    
                    node = document.getElementById(node);

                    if (document.body.createTextRange) {
                        const range = document.body.createTextRange();
                        range.moveToElementText(node);
                        range.select();
                    } else if (window.getSelection) {
                        const selection = window.getSelection();
                        const range = document.createRange();
                        range.selectNodeContents(node);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    } else {
                        console.warn("Could not select text in node: Unsupported browser.");
                    }
                }
                
            showAdvanced( element )
                {
                    jQuery( element ).closest('.wph_input').find('div.advanced').show('fast');
                    jQuery( element ).closest('.wph_anotice').slideUp('fast', function() { jQuery(this).hide()  });
                    
                    
                }
                
            randomWord( element, extension ) 
                {
                    var length  =   7;
                    var consonants = 'bcdfghjlmnpqrstv',
                        vowels = 'aeiou',
                        rand = function(limit) {
                            return Math.floor(Math.random()*limit);
                        },
                        i, word='', length = parseInt(length,10),
                        consonants = consonants.split(''),
                        vowels = vowels.split('');
                        
                    for (i=0;i<length/2;i++) 
                        {
                            var randConsonant = consonants[rand(consonants.length)],
                                randVowel = vowels[rand(vowels.length)];
                            word += randConsonant;
                            word += i*2<length-1 ? randVowel : '';
                        }
                    
                    if ( extension != '' )
                        word    =   word.concat( '.' + extension );
                    
                    jQuery( element ).closest('.wph_input').find('.entry input.text').val( word );                    
                }
                
            
            clear ( element )
                {
                    jQuery( element ).closest('.wph_input').find('.entry input.text').val( '' );    
                }
                
                
            confirm_sample_setup()
                {
                    
                    var agree   =   confirm( wph_vars.confirm_message );
                    if (agree)
                        {
                            jQuery('#wph-run-sample-setup').submit();
                        }
                        else
                        {
                            return false ;
                        }        
                }
                
                
            check_headers( nonce )
                {
                    jQuery('#wph-check-headers .spinner').css( 'visibility', 'visible');
                    
                    jQuery('#wph-headers-container').html('');
                    jQuery('#wph-graph .wph-graph-data').html( 'Loading..' );
                    jQuery('#wph-graph .wph-graph-progress').css( 'transform', 'rotate(0deg)')
                    
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        dataType: "json",
                        data: {
                            'action':'wph_check_headers',
                            'nonce' : nonce
                        },
                        success:function(data) {
                            jQuery('#wph-check-headers .spinner').css( 'visibility', 'hidden');
                            jQuery('#wph-headers-container').html( data.html );
                            jQuery('#wph-graph .wph-graph-data').html( data.graph.message );
                            jQuery('#wph-graph .wph-graph-progress').css( 'transform', 'rotate(' +  data.graph.value +'deg)')
                        },  
                        error: function(errorThrown){
                            jQuery('#wph-check-headers .spinner').css( 'visibility', 'hidden');
                            jQuery('#wph-headers-container').html( 'Unable to call AJAX.' );
                            jQuery('#wph-graph .wph-graph-data').html( data.graph.message );
                            jQuery('#wph-graph .wph-graph-progress').css( 'transform', 'rotate(' + data.graph.value + 'deg);')
                        }
                    });
                }
                
                
            runSampleHeaders ()
                {
                    var agree   =   confirm( wph_vars.run_sample_headers );
                    if ( !agree )
                        return false;
                        
                    document.getElementById("wph-form").submit();  
                    
                }
                
                
            
            site_scan( nonce )
                {
                    if ( jQuery('#wph-site-scan-button').hasClass( 'disabled' ) )
                        return;
                    
                    jQuery('#wph-site-scan-button').addClass( 'disabled' );
                    jQuery('#security-scan #scan_overview .spinner').css( 'visibility', 'visible');
                    jQuery('#security-scan #scan_overview .working').css( 'display', 'inline-block');
                    
                    jQuery('#wph-scan-score .passed h5').html('0');
                    jQuery('#wph-scan-score .failed h5').html('0');
                    
                    jQuery('#wph-graph .wph-graph-progress' ).css( 'transform', 'rotate(0deg)' );
                    jQuery('#wph-graph .wph-graph-data b' ).html( '0%' );
                    jQuery('#scan_overview .protection' ).html( 'Unknown' );
                    
                    jQuery('#all-scann-items div.item').not('.ajax_updated').each ( function ( ) {
                        jQuery(this).find(' > .wph_input').addClass('unknown').removeClass('issue_found');
                        jQuery(this).find('.info').html('');
                        jQuery(this).find('.description').html('');
                        jQuery(this).find('.actions').html('');
                    })
                     
                    var LastResponseLength  =   false;
                    var Response        =   '';
                    
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        dataType: "html",
                        data: {
                            'action':'wph_site_scan',
                            'nonce' : nonce
                        },
                        success:function(data) {

                            //jQuery('#wph-site-scan-button').removeClass( 'disabled' );
                            jQuery('#security-scan #scan_overview  p.new-items').removeClass( 'new-items' );
                            jQuery('#security-scan #scan_overview .spinner').css( 'visibility', 'hidden');
                            jQuery('#security-scan #scan_overview .working').css( 'display', 'none');

                            setTimeout ( function(){ location.reload(); }, 2000);
                            
                        },  
                        error: function(errorThrown){
                            //jQuery('#wph-site-scan-button').removeClass( 'disabled' );
                            jQuery('#security-scan #scan_overview .spinner').css( 'visibility', 'hidden');
                            jQuery('#security-scan #scan_overview .working').css( 'display', 'none');
                            
                            clearInterval( WPH.SiteScanProgress_interval );
                        }
                    });
                    
                    setTimeout( function() { WPH.site_scan_progress_start( nonce ) }, 3000 );
                    
                }
            
            site_scan_progress_start ( nonce )
                {
                    this.SiteScanProgress_interval =   setInterval( function() { WPH.site_scan_progres( nonce ) }, 2000);
                }    
                
            site_scan_progres ( nonce )
                {
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        dataType: "json",
                        data: {
                            'action':'wph_site_scan_progress',
                            'nonce' : nonce
                        },
                        success:function(data) {
                            
                            WPH.AJAX_data  =   data;
                            
                            jQuery ( '#security-scan #scan_overview .working .progress' ).html( data.items_progress );
                            jQuery('#wph-scan-score .passed h5').html( data.success );
                            jQuery('#wph-scan-score .failed h5').html( data.failed );
                            
                            jQuery('#wph-graph .wph-graph-progress' ).css( 'transform', 'rotate(' + data.graph_progress + 'deg)' );
                            jQuery('#wph-graph .wph-graph-data b' ).html( data.progress + '%' );
                            jQuery('#scan_overview .protection' ).html( data.protection );
                            
                            if ( data.scann_in_progress  ==   false )
                                clearInterval( WPH.SiteScanProgress_interval );
                                
                            jQuery('#all-scann-items div.item').not('.ajax_updated').each ( function ( ) {
                                var item_id =   jQuery(this).attr('id');
                                var el_item_id     =   item_id.replace("item-", "")
                                if ( eval ( "WPH.AJAX_data.results." + el_item_id  )  != undefined )    
                                    {
                                        var item_response   =   eval ( "WPH.AJAX_data.results." + el_item_id  );
                                        
                                        jQuery('#' + item_id ).removeClass('valid-item');
                                        
                                        if ( item_response.status  != undefined )
                                            {    
                                                jQuery('#' + item_id ).addClass( item_response.status );
                                                
                                                jQuery('#' + item_id + " > .wph_input").removeClass( 'unknown' );
                                                
                                                if ( item_response.status == true )
                                                    jQuery('#' + item_id ).addClass('valid-item');
                                                else if ( item_response.status == false )
                                                    jQuery('#' + item_id + " > .wph_input").addClass( 'issue_found' );
                                            }
                                        
                                        jQuery('#' + item_id + " .info").html( '' );
                                        if ( item_response.info  != undefined )
                                            {    
                                                jQuery('#' + item_id + " .info").html( item_response.info );
                                            }
                                        
                                        jQuery('#' + item_id + " .description").html( '' );
                                        if ( item_response.description  != undefined )
                                            {    
                                                jQuery('#' + item_id + " .description").html( item_response.description );
                                            }
                                            
                                        jQuery('#' + item_id + " .actions").html( '' );
                                        if ( item_response.actions  != undefined )
                                            {    
                                                jQuery('#' + item_id + " .actions").html( item_response.actions );
                                            }
                                            
                                        jQuery('#' + item_id ).addClass('ajax_updated');
                                                                                
                                    }
                                
                            })
                            
                        },  
                        error: function(errorThrown){
                            //jQuery('#scan_overview .wph_results').append( '<p>Error while retrieving the AJAX update.</p>');
                        }
                    });
                }
                
                
            scan_ignore_item ( item_id, nonce )
                {
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        dataType: "json",
                        data: {
                            'action'    :   'wph_site_scan_ignore',
                            'item_id'   :   item_id,
                            'nonce'     :   nonce
                        },
                        success:function( data ) {
                            jQuery('html, body').animate({
                                scrollTop: jQuery("#info_box").offset().top
                            }, 500);
                            jQuery('#item-' + data.item_id ).appendTo("#hidden-items");
                            jQuery('#wph-graph .wph-graph-data' ).html("<b>" + data.progress + "%</b><br>" + data.protection );
                            jQuery('#wph-graph .wph-graph-progress' ).css( 'transform', 'rotate(' + data.graph_progress + 'deg)' );
                            jQuery('#wph-scan-score .passed h5' ).html( data.success );
                            jQuery('#wph-scan-score .failed h5' ).html( data.failed );
                        },  
                        error: function(errorThrown){

                        }
                    });
                    
                }
                
                
            scan_restore_item ( item_id, nonce )
                {
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        dataType: "json",
                        data: {
                            'action'    :   'wph_site_scan_restore',
                            'item_id'   :   item_id,
                            'nonce'     :   nonce
                        },
                        success:function( data ) {
                            jQuery('html, body').animate({
                                scrollTop: jQuery("#info_box").offset().top
                            }, 500);
                            jQuery('#item-' + data.item_id ).appendTo("#scann-items");
                            jQuery('#wph-graph .wph-graph-data' ).html("<b>" + data.progress + "%</b><br>" + data.protection );
                            jQuery('#wph-graph .wph-graph-progress' ).css( 'transform', 'rotate(' + data.graph_progress + 'deg)' );
                            jQuery('#wph-scan-score .passed h5' ).html( data.success );
                            jQuery('#wph-scan-score .failed h5' ).html( data.failed );
                        },  
                        error: function(errorThrown){

                        }
                    });
                    
                }
                
            captcha_test () 
                {
                    jQuery( '#api_test' ).val( 'true' );
                    jQuery( '#api_test' ).closest('form').requestSubmit();
                }
            
    }
    
    var WPH = new WPH_Class();
    
    
    
    
    jQuery( document ).ready( function() {
        
        jQuery('.options span').tipsy({fade: false, gravity: 's'});    
    })
    