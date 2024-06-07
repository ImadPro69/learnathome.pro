(function( $ ) {		
	'use strict';

	/**
 	 * Replace element classes with pattern matching.
 	 */
	$.fn.aiovgReplaceClass = function( pattern, additions ) {
		this.removeClass(function( index, classes ) {
			var matches = classes.match( pattern );
			return matches ? matches.join( ' ' ) : '';	
		}).addClass( additions );

		return this;
	};	

	/**
	 * Render media uploader.
	 */
	function renderMediaUploader( callback ) { 
		var fileFrame;

		// If an instance of fileFrame already exists, then we can open it rather than creating a new instance.
		if ( fileFrame ) {
			fileFrame.open();
			return false;
		}

		// Use the wp.media library to define the settings of the media uploader.
		fileFrame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			multiple: false
		});		

		// Setup an event handler for what to do when a media has been selected.
		fileFrame.on( 'insert', function() { 
			// Read the JSON data returned from the media uploader.
			var json = fileFrame.state().get( 'selection' ).first().toJSON();
		
			// First, make sure that we have the URL of the media to display.
			if ( json.url.trim().length == 0 ) {
				return false;
			}
		
			callback( json );			 
		});

		fileFrame.state( 'embed' ).on( 'select', function() {
			// Read the JSON data returned from the media uploader.
			var json = fileFrame.state().props.toJSON();

			// First, make sure that we have the URL of the media to display.
			if ( json.url.trim().length == 0 ) {
				return false;
			}

			json.id = 0;
			callback( json );			 
		});

		// Now display the actual fileFrame.
		fileFrame.on( 'open', function() { 
			jQuery( '#menu-item-gallery, #menu-item-playlist, #menu-item-video-playlist' ).hide();
		});

		fileFrame.open(); 
	}

	/**
	 * Make tracks in the video form sortable.
	 */
	function sortTracks() {				
		var $el = $( '#aiovg-tracks tbody' );
			
		if ( $el.hasClass( 'ui-sortable' ) ) {
			$el.sortable( 'destroy' );
		}
			
		$el.sortable({
			handle: '.aiovg-handle'
		});
	}

	/**
	 * Make chapters in the video form sortable.
	 */
	function sortChapters() {				
		var $el = $( '#aiovg-chapters tbody' );
			
		if ( $el.hasClass( 'ui-sortable' ) ) {
			$el.sortable( 'destroy' );
		}
			
		$el.sortable({
			handle: '.aiovg-handle'
		});
	}

	/**
 	 * Init autocomplete ui to search videos. 
 	 */
	function initAutocomplete( $el ) {
		$el.autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: ajaxurl,
					dataType: 'json',
					method: 'post',
					data: {
						action: 'aiovg_autocomplete_get_videos',
						security: aiovg_admin.ajax_nonce,
						term: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.post_title,
								value: item.post_title,
								data: item
							}
						}));
					}
				});
			},
			autoFocus: true,
			minLength: 0,
			select: function( event, ui ) {
				var $field = $( this ).closest( '.aiovg-widget-field' );
				var html = '';

				if ( ui.item.data.ID != 0 ) {
					html  = '<span class="dashicons dashicons-yes-alt"></span> ';
					html += '<span>' + ui.item.data.post_title + '</span> ';
					html += '<a href="javascript:void(0);" class="aiovg-remove-autocomplete-result">' + aiovg_admin.i18n.remove + '</a>';
				} else {
					html  = '<span class="dashicons dashicons-info"></span> ';
					html += '<span>' + aiovg_admin.i18n.no_video_selected + '</span>';
				}
				
				$field.find( '.aiovg-widget-input-id' ).val( ui.item.data.ID ).trigger( 'change' );
				$field.find( '.aiovg-autocomplete-result' ).html( html );
			},
			open: function() {
				$( this ).removeClass( 'ui-corner-all' ).addClass( 'ui-corner-top' );
			},
			close: function() {
				$( this ).removeClass( 'ui-corner-top' ).addClass( 'ui-corner-all' );
				$( this ).val( '' );
			}
		});		
	}

	/**
 	 * Copy to Clipboard.
 	 */
	function copyToClipboard( value ) {
		var input = document.createElement( 'input' );
		input.value = value;

		document.body.appendChild( input );		
		input.focus();
		input.select();

		document.execCommand( 'copy' );

		input.remove();
		alert( aiovg_admin.i18n.copied + "\n" + value );
	}

	/**
	 * Called when the page has loaded.
	 */
	$(function() {
		
		// Common: Upload files.
		$( document ).on( 'click', '.aiovg-upload-media', function( event ) { 
            event.preventDefault();

			var $this = $( this );

            renderMediaUploader(function( json ) {
				$this.closest( '.aiovg-media-uploader' )
					.find( 'input[type=text]' )
					.val( json.url )
					.trigger( 'file.uploaded' );
			}); 
		});
		
		// Common: Init color picker.
		if ( $.fn.wpColorPicker ) {
			$( '.aiovg-color-picker' ).wpColorPicker();

			$( document ).on( 'widget-added widget-updated', function( event, widget ) {
				widget.find( '.aiovg-color-picker' ).wpColorPicker({
					change: _.throttle( function() { // For Customizer
						$( this ).trigger( 'change' );
					}, 3000)
				});
			});
		}

		// Common: Init the popup.
		if ( $.fn.magnificPopup ) {
			$( '.aiovg-modal-button' ).magnificPopup({
				type: 'inline',
				mainClass: 'mfp-fade'
			});
		}

		// Dashboard: Toggle shortcode forms.
		$( '#aiovg-shortcode-selector input[type=radio]' ).on( 'change', function() {
			var value = $( '#aiovg-shortcode-selector input[type=radio]:checked' ).val();

			$( '.aiovg-shortcode-form' ).hide();
			$( '#aiovg-shortcode-form-' + value ).show();

			$( '.aiovg-shortcode-instructions' ).hide();			
			$( '#aiovg-shortcode-instructions-' + value ).show();
		}).trigger( 'change' );

		// Dashboard: Toggle field sections.
		$( '#aiovg-shortcode-forms .aiovg-shortcode-section-header' ).on( 'click', function() {
			var $this   = $( this );
			var $parent = $this.parent();

			if ( ! $parent.hasClass( 'aiovg-active' ) ) {
				$this.closest( '.aiovg-shortcode-form' )
					.find( '.aiovg-active' )
					.removeClass( 'aiovg-active' )
					.find( '.aiovg-shortcode-controls' )
					.slideToggle();
			}			

			$parent.toggleClass( 'aiovg-active' )
				.find( '.aiovg-shortcode-controls' )
				.slideToggle();
		});		

		// Dashboard: Toggle fields based on the selected video source type.
		$( '#aiovg-shortcode-form-video select[name=type]' ).on( 'change', function() {			
			var value = $( this ).val();			
			$( '#aiovg-shortcode-form-video' ).aiovgReplaceClass( /\aiovg-type-\S+/ig, 'aiovg-type-' + value );
		});

		// Dashboard: Toggle fields based on the selected videos template.
		$( '#aiovg-shortcode-form-videos select[name=template]' ).on( 'change', function() {			
			var value = $( this ).val();			
			$( '#aiovg-shortcode-form-videos' ).aiovgReplaceClass( /\aiovg-template-\S+/ig, 'aiovg-template-' + value );
		}).trigger( 'change' );

		// Dashboard: Toggle fields based on the selected categories template.
		$( '#aiovg-shortcode-form-categories select[name=template]' ).on( 'change', function() {			
			var value = $( this ).val();			
			$( '#aiovg-shortcode-form-categories' ).aiovgReplaceClass( /\aiovg-template-\S+/ig, 'aiovg-template-' + value );
		}).trigger( 'change' );

		// Dashboard: Generate shortcode.
		$( '#aiovg-generate-shortcode' ).on( 'click', function( event ) { 
			event.preventDefault();			

			// Shortcode
			var shortcode = $( '#aiovg-shortcode-selector input[type=radio]:checked' ).val();

			// Build attributes.
			var attributes = shortcode;
			var obj = {};
			
			$( '.aiovg-shortcode-field', '#aiovg-shortcode-form-' + shortcode ).each(function() {							
				var $this = $( this );
				var type  = $this.attr( 'type' );
				var name  = $this.attr( 'name' );				
				var value = $this.val();
				var def   = 0;
				
				if ( typeof $this.data( 'default' ) !== 'undefined' ) {
					def = $this.data( 'default' );
				}				
				
				// Is checkbox?
				if ( type == 'checkbox' ) {
					value = $this.is( ':checked' ) ? 1 : 0;
				} else {
					// Is category or tag?
					if ( name == 'category' || name == 'tag' ) {					
						value = $this.find( 'input[type=checkbox]:checked' ).map(function() {
							return this.value;
						}).get().join( ',' );
					}
				}
				
				// Add only if the user input differ from the global configuration.
				if ( value != def ) {
					obj[ name ] = value;
				}				
			});
			
			for ( var name in obj ) {
				if ( obj.hasOwnProperty( name ) ) {
					attributes += ( ' ' + name + '="' + obj[ name ] + '"' );
				}
			}

			// Shortcode output.	
			$( '#aiovg-shortcode').val( '[aiovg_' + attributes + ']' ); 
		});
		
		// Dashboard: Toggle checkboxes in the issues table.
		$( '#aiovg-issues-check-all' ).on( 'change', function() {
			var value = $( this ).is( ':checked' ) ? true : false;	
			$( '#aiovg-issues .aiovg-issue' ).prop( 'checked', value );
		});	

		// Dashboard: Validate the issues form.
		$( '#aiovg-issues-form' ).submit(function() {
			var hasValue = $( '#aiovg-issues .aiovg-issue:checked' ).length > 0;

			if ( ! hasValue ) {
				alert( aiovg_admin.i18n.no_issues_selected );
				return false;
			}			
		});

		// Videos: Copy URL.
		$( '.aiovg-copy-url' ).on( 'click', function() {
			var url = $( this ).data( 'url' );
			copyToClipboard( url );			
		});

		// Videos: Copy shortcode.
		$( '.aiovg-copy-shortcode' ).on( 'click', function() {
			var id = parseInt( $( this ).data( 'id' ) );
			var shortcode = '[aiovg_video id="' + id + '"]';

			copyToClipboard( shortcode );
		});
		
		// Videos: Toggle fields based on the selected video source type.
		$( '#aiovg-video-type' ).on( 'change', function( event ) { 
            event.preventDefault();
 
			var value = $( this ).val();
			
			$( '.aiovg-toggle-fields' ).hide();
			$( '.aiovg-type-' + value ).show( 300 );
		}).trigger( 'change' );
		
		// Videos: Add new source fields when "Add More Quality Levels" link is clicked.
		$( '#aiovg-add-new-source' ).on( 'click', function( event ) {
			event.preventDefault();				
			
			var $this = $( this );

			var limit  = parseInt( $( this ).data( 'limit' ) );
			var length = $( '#aiovg-field-mp4 .aiovg-quality-selector' ).length;	
			var index  = length - 1;
			
			if ( index == 0 ) {
				$( '#aiovg-field-mp4 .aiovg-quality-selector' ).show();
			}

			var $field = $( '#aiovg-source-clone .aiovg-source' ).clone();	
			$field.find( 'input[type=radio]' ).attr( 'name', 'quality_levels[' + index + ']' );
			$field.find( 'input[type=text]' ).attr( 'name', 'sources[' + index + ']' );

			$this.before( $field ); 		
			
			if ( ( length + 1 ) >= limit ) {
				$this.hide();
			}
		});

		// Videos: On quality level selected.
		$( '#aiovg-field-mp4' ).on( 'change', '.aiovg-quality-selector input[type=radio]', function() {
			var $this = $( this );
			var values = [];

			$( '.aiovg-quality-selector' ).each(function() {
				var value = $( this ).find( 'input[type=radio]:checked' ).val();

				if (  value ) {
					if ( values.includes( value ) ) {
						$this.prop( 'checked', false );
						alert( aiovg_admin.i18n.quality_exists );
					} else {
						values.push( value );
					}					
				}
			});
		});
		
		// Videos: Add new track fields when "Add New File" button is clicked.
		$( '#aiovg-add-new-track' ).on( 'click', function( event ) { 
            event.preventDefault();
			
			var $field = $( '#aiovg-tracks-clone tr' ).clone();			
            $( '#aiovg-tracks' ).append( $field ); 
        });
		
		if ( $( '#aiovg-tracks .aiovg-tracks-row' ).length == 0 ) {
			$( '#aiovg-add-new-track' ).trigger( 'click' );
		}

		// Videos: Upload tracks.	
		$( document ).on( 'click', '.aiovg-upload-track', function( event ) { 
            event.preventDefault();

			var $this = $( this );

            renderMediaUploader(function( json ) {
				$this.closest( 'tr' )
					.find( '.aiovg-track-src input[type=text]' )
					.val( json.url );
			}); 
        });
		
		// Videos: Delete tracks.
		$( document ).on( 'click', '.aiovg-delete-track', function( event ) { 
            event.preventDefault();			
            $( this ).closest( 'tr' ).remove(); 
        });
		
		// Videos: Make the tracks fields sortable.
		if ( $.fn.sortable ) {
			sortTracks();
		}
		
		// Videos: Add new chapter fields when "Add New Chapter" button is clicked.
		$( '#aiovg-add-new-chapter' ).on( 'click', function( event ) { 
            event.preventDefault();
			
			var $field = $( '#aiovg-chapters-clone tr' ).clone();			
            $( '#aiovg-chapters' ).append( $field ); 
        });
		
		if ( $( '#aiovg-chapters .aiovg-chapters-row' ).length == 0 ) {
			$( '#aiovg-add-new-chapter' ).trigger( 'click' );
		}
		
		// Videos: Delete chapters.
		$( document ).on( 'click', '.aiovg-delete-chapter', function( event ) { 
            event.preventDefault();			
            $( this ).closest( 'tr' ).remove(); 
        });
		
		// Videos: Make the chapters fields sortable.
		if ( $.fn.sortable ) {
			sortChapters();
		}

		// Categories: Upload Image.
		$( '#aiovg-categories-upload-image' ).on( 'click', function( event ) { 
            event.preventDefault();

			renderMediaUploader(function( json ) {
				$( '#aiovg-categories-image-wrapper' ).html( '<img src="' + json.url + '" alt="" />' );

				$( '#aiovg-categories-image' ).val( json.url );
				$( '#aiovg-categories-image_id' ).val( json.id );				
			
				$( '#aiovg-categories-upload-image' ).hide();
				$( '#aiovg-categories-remove-image' ).show();
			}); 
        });
		
		// Categories: Remove Image.
		$( '#aiovg-categories-remove-image' ).on( 'click', function( event ) {														 
            event.preventDefault();					
			
			$( '#aiovg-categories-image-wrapper' ).html( '' );

			$( '#aiovg-categories-image' ).val( '' );
			$( '#aiovg-categories-image_id' ).val( '' );			
			
			$( '#aiovg-categories-remove-image' ).hide();
			$( '#aiovg-categories-upload-image' ).show();	
		});
		
		// Categories: Clear the custom fields.
		$( document ).ajaxComplete(function( e, xhr, settings ) {			
			if ( $( '#aiovg-categories-image' ).length && settings.data ) {	
				var queryStringArr = settings.data.split( '&' );
			   
				if ( $.inArray( 'action=add-tag', queryStringArr ) !== -1 ) {
					var response = $( xhr.responseXML ).find( 'term_id' ).text();

					if ( response ) {						
						$( '#aiovg-categories-image-wrapper' ).html( '' );	
						
						$( '#aiovg-categories-image' ).val( '' );	
						$( '#aiovg-categories-image_id' ).val( '' );				
						
						$( '#aiovg-categories-remove-image' ).hide();
						$( '#aiovg-categories-upload-image' ).show();

						$( '#aiovg-categories-exclude_search_form' ).prop( 'checked', false );
						$( '#aiovg-categories-exclude_video_form' ).prop( 'checked', false );
					}
				}		
			}			
		});

		// Settings: Bind section ID.
		$( '#aiovg-settings .form-table' ).each(function() { 
			var str = $( this ).find( 'tr:first th label' ).attr( 'for' );
			var id  = str.split( '[' );
			id = id[0].replace( /_/g, '-' );

			$( this ).attr( 'id', id );
		});
		
		// Settings: Toggle fields based on the selected categories template.
		$( '#aiovg-categories-settings tr.template select' ).on( 'change', function() {			
			var value = $( this ).val();			
			$( '#aiovg-categories-settings' ).aiovgReplaceClass( /\aiovg-template-\S+/ig, 'aiovg-template-' + value );
		}).trigger( 'change' );

		// Settings: Toggle fields based on the selected videos template.
		$( '#aiovg-videos-settings tr.template select' ).on( 'change', function() {			
			var value = $( this ).val();			
			$( '#aiovg-videos-settings' ).aiovgReplaceClass( /\aiovg-template-\S+/ig, 'aiovg-template-' + value );
		}).trigger( 'change' );	

		// Categories Widget: Toggle fields based on the selected categories template.
		$( document ).on( 'change', '.aiovg-widget-form-categories .aiovg-widget-input-template', function() {			
			var value = $( this ).val();	
			$( this ).closest( '.aiovg-widget-form-categories' ).aiovgReplaceClass( /\aiovg-template-\S+/ig, 'aiovg-template-' + value );
		});

		// Videos Widget: Toggle fields based on the selected videos template.
		$( document ).on( 'change', '.aiovg-widget-form-videos .aiovg-widget-input-template', function() {			
			var value = $( this ).val();
			$( this ).closest( '.aiovg-widget-form-videos' ).aiovgReplaceClass( /\aiovg-template-\S+/ig, 'aiovg-template-' + value );
		});

		// Video Widget: Init autocomplete.
		if ( $.fn.autocomplete ) {
			$( '.aiovg-autocomplete-input' ).each(function() {
				initAutocomplete( $( this ) );
			});

			$( document ).on( 'widget-added widget-updated', function( event, widget ) {
				var $el = widget.find( '.aiovg-autocomplete-input' );
				
				if ( $el.length > 0 ) {
					initAutocomplete( $el );
				}
			});

			$( document ).on( 'click', '.aiovg-remove-autocomplete-result', function() {
				var $field = $( this ).closest( '.aiovg-widget-field' );				

				var html = '<span class="dashicons dashicons-info"></span> ';
				html += '<span>' + aiovg_admin.i18n.no_video_selected + '</span>';

				$field.find( '.aiovg-widget-input-id' ).val( 0 ).trigger( 'change' );
				$field.find( '.aiovg-autocomplete-result' ).html( html );
			});
		}
			   
	});	

})( jQuery );
