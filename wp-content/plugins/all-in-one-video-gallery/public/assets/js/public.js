(function( $ ) {	
	'use strict';

	// Load script files.
	var loadScript = ( file ) => {
		return new Promise(( resolve, reject ) => { 
			if ( document.querySelector( '#' + file.id ) !== null ) {
				resolve();
				return false;
			}

			const script = document.createElement( 'script' );

			script.id  = file.id;
			script.src = file.src;

			script.onload  = () => resolve();
			script.onerror = () => reject();

			document.body.appendChild( script );
		});
	}

	/**
	 * Called when the page has loaded.
	 */
	$(function() {

		// Load the required script files.
		var plugin_url = aiovg_public.plugin_url;
		var plugin_version = aiovg_public.plugin_version;

		var scripts = [
			{ 
				selector: '.aiovg-autocomplete', 
				id: 'all-in-one-video-gallery-autocomplete-js',
				src: plugin_url + 'public/assets/js/autocomplete.min.js?ver=' + plugin_version
			}, 
			{
				selector: '.aiovg-more-ajax', 
				id: 'all-in-one-video-gallery-pagination-js',
				src: plugin_url + 'public/assets/js/pagination.min.js?ver=' + plugin_version 
			},
			{
				selector: '.aiovg-pagination-ajax',
				id: 'all-in-one-video-gallery-pagination-js', 
				src: plugin_url + 'public/assets/js/pagination.min.js?ver=' + plugin_version 
			}
		];

		for ( var i = 0; i < scripts.length; i++ ) {
			var script = scripts[ i ];
			if ( document.querySelector( script.selector ) !== null ) {
				loadScript( script );
			}
		}
		
		// Categories Dropdown.
		$( '.aiovg-categories-template-dropdown select' ).on( 'change', function() {
			var selectedEl = this.options[ this.selectedIndex ];

			if ( parseInt( selectedEl.value ) == 0 ) {
				window.location.href = $( this ).closest( '.aiovg-categories-template-dropdown' ).data( 'uri' );
			} else {
				window.location.href = selectedEl.getAttribute( 'data-uri' );
			}
		});		
		
	});

})( jQuery );
