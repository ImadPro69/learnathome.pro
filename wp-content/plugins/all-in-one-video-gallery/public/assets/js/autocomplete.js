(function( $ ) {
	'use strict';

	var aiovg = window.aiovg_autocomplete || window.aiovg_public;

	/**
	 * Init Autocomplete.
	 */
	function initAutocomplete( uid, items, callback ) {
		var inputEl = document.querySelector( '#aiovg-autocomplete-input-' + uid );
		var tagsEl  = document.querySelector( '#aiovg-autocomplete-tags-' + uid );

		var currentFocus = -1;					

		var showDropdown = function() {				
			var options = [];
			currentFocus = -1;

			// Close other dropdowns.
			closeDropdown();			

			// Create a dropdown element.
			var dropdownEl = document.createElement( 'div' );
			dropdownEl.setAttribute( 'id', 'aiovg-autocomplete-items-' + uid );
			dropdownEl.setAttribute( 'class', 'aiovg-autocomplete-items' );

			// Append the dropdown element as a child to the autocomplete container.
			inputEl.parentNode.appendChild( dropdownEl );

			// Bind options.
			items.forEach(( item, index ) => {
				var value = item.value;
				var label = item.label;

				var isValid = false;

				if ( ! inputEl.value ) {
					isValid = true;
				} else if ( label.toLowerCase().indexOf( inputEl.value.toLowerCase() ) !== -1 ) {
					isValid = true;
				}				

				if ( ! isValid && index == items.length - 1 && options.length == 0 ) {
					value = 0;
					label = aiovg.i18n.no_tags_found;

					isValid = true;
				}

			  	if ( isValid ) {
					// Create an option element.
					var option = document.createElement( 'div' );
					option.innerHTML = label;

					if ( value == 0 ) {
						option.className = 'aiovg-text-muted';
					}

					var tagEl = tagsEl.querySelector( '.aiovg-tag-item-' + value );
					if ( tagEl !== null ) {
						option.setAttribute( 'class', 'aiovg-autocomplete-selected' );
					}
					
					// Called when the user clicks on the option.
					option.addEventListener( 'click', function() {
						// Reset the input field.
						inputEl.value = '';

						// Insert the value.					
						callback( value, label );

						// Close the dropdown.
						closeDropdown();
					});

					dropdownEl.appendChild( option );
					options.push( option );
			  	}
			});
		};

		var closeDropdown = function( el = null ) {
			var id = 0;

			if ( el && el.id ) {
				id = el.id.replace( 'aiovg-autocomplete-input-', '' );
			}

			document.querySelectorAll( '.aiovg-autocomplete-items' ).forEach(( dropdownEl ) => {
				if ( dropdownEl.getAttribute( 'id' ) != ( 'aiovg-autocomplete-items-' + id ) ) {
					dropdownEl.remove();
				}
			});
		};

		var addActive = function( dropdownEl ) {
			removeActive( dropdownEl );

			if ( currentFocus >= dropdownEl.length ) {
			  	currentFocus = 0;
			}

			if ( currentFocus < 0 ) {
			  	currentFocus = dropdownEl.length - 1;
			}

			dropdownEl[ currentFocus ].classList.add( 'aiovg-autocomplete-active' );
	  	};

		var removeActive = function( dropdownEl ) {
			dropdownEl.forEach(( el ) => {
				el.classList.remove( 'aiovg-autocomplete-active' );
			});
		};

		// Called when the user focuses on the input field.
		inputEl.addEventListener( 'focus', function( event ) { 
			if ( event.target.value == '' ) {
				showDropdown();
			}
		});

		// Called when the user writes on the input field.
		inputEl.addEventListener( 'input', function() {
			showDropdown();
		});

		// Called when the user presses a key on the keyboard.
		inputEl.addEventListener( 'keydown', function( event ) {
			var dropdownEl = document.querySelector( '#aiovg-autocomplete-items-' + uid );

			if ( dropdownEl ) {
				dropdownEl = dropdownEl.querySelectorAll( 'div' );
			}

			if ( ! dropdownEl ) {
				return false;
			}

			if ( event.keyCode == 40 ) {
			  	// If the arrow DOWN key is pressed,
			  	// increase the currentFocus variable
			  	currentFocus++;
			  	// and and make the current item more visible
			  	addActive( dropdownEl );
			} else if ( event.keyCode == 38 ) {
			  	// If the arrow UP key is pressed,
			  	// decrease the currentFocus variable
			  	currentFocus--;
			  	// and and make the current item more visible
				addActive( dropdownEl );
			} else if ( event.keyCode == 13 ) {
			  	// If the ENTER key is pressed, prevent the form from being submitted,
			  	event.preventDefault();

			  	if ( currentFocus > -1 ) {
					// and simulate a click on the 'active' item
					dropdownEl[ currentFocus ].click();
			  	}
			}
		});

		// Called when the user clicks on the document outside the autocomplete element.
		if ( ! aiovg.hasOwnProperty( 'autocomplete' ) ) {
			aiovg.autocomplete = true;
			
			document.addEventListener( 'click', function( event ) {
				closeDropdown( event.target );
			});
		}		
	}

	/**
	 * Called when the page has loaded.
	 */
	$(function() {	

		$( '.aiovg-autocomplete' ).each(function() {
			var uid   = $( this ).data( 'uid' );
			var items = [];			

			$( 'option', '#aiovg-autocomplete-select-' + uid ).each(function() {
				items.push({
					value: $( this ).val(),
					label: $( this ).text()
				});
			});

			if ( items.length == 0 ) {
				items.push({
					value: 0,
					label: aiovg.i18n.no_tags_found
				});
			}

			var callback = function( value, label ) {
				value = parseInt( value );

				if ( value != 0 ) {				
					var $tags  = $( '#aiovg-autocomplete-tags-' + uid );	
					var length = $tags.find( '.aiovg-tag-item-' + value ).length;

					if ( length == 0 ) {
						var html = '<span class="aiovg-tag-item aiovg-tag-item-' + value + '">';						
						html += '<a href="javascript:void(0);">';
						html += '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="aiovg-flex-shrink-0">' +
							'<path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />' +
						'</svg>';
						html += label;
						html += '</a>';
						html += '<input type="hidden" name="ta[]" value="' + value + '" />';
						html += '</span>';
						
						$tags.append( html );
					}
				}
			};

			initAutocomplete( uid, items, callback );
		});

		$( document ).on( 'click', '.aiovg-tag-item a', function( event ) {
			event.preventDefault();
			$( this ).parent().remove();
		});	
		
	});

})( jQuery );
