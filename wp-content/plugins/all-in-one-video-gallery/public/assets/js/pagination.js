(function( $ ) {
	'use strict';	

	var aiovg = window.aiovg_pagination || window.aiovg_public;

	/**
	 * Called when the page has loaded.
	 */
	$(function() {		

		// Pagination
		$( document ).on( 'click', '.aiovg-pagination-ajax a.page-numbers', function( event ) {
			event.preventDefault();

			var $this = $( this );	
			var $pagination = $this.closest( '.aiovg-pagination-ajax' );			
			var current = parseInt( $pagination.data( 'current' ) );			
			
			var params = $pagination.data( 'params' );
			params.action = 'aiovg_load_more_' + params.source;
			params.security = aiovg.ajax_nonce;
			
			var paged = parseInt( $this.html() );
			params.paged = paged++;

			if ( $this.hasClass( 'prev' ) ) {
				params.paged = current - 1;
			}
			
			if ( $this.hasClass( 'next' ) ) {
				params.paged = current + 1;
			}

			var $gallery = $( '#aiovg-' + params.uid );	

			$pagination.addClass( 'aiovg-spinner' );

			$.post( aiovg.ajax_url, params, function( response ) {
				if ( response.success ) {
					var html = $( response.data.html ).html();
					$gallery.html( html );
					
					$pagination.trigger( 'AIOVG.onGalleryUpdated' );

					$( 'html, body' ).animate({
						scrollTop: $gallery.offset().top - parseInt( aiovg.scroll_to_top_offset )
					}, 500);
				} else {
					$pagination.removeClass( 'aiovg-spinner' );
				}
			});
		});

		// Load More.
		$( document ).on( 'click', '.aiovg-more-ajax button', function( event ) {
			event.preventDefault();

			var $this = $( this );
			var $pagination = $this.closest( '.aiovg-more-ajax' );			
			var numpages = parseInt( $this.data( 'numpages' ) );			
			
			var params = $pagination.data( 'params' );
			params.action = 'aiovg_load_more_' + params.source;
			params.security = aiovg.ajax_nonce;	
			
			var paged = parseInt( $this.data( 'paged' ) );
			params.paged = ++paged;
			
			$pagination.addClass( 'aiovg-spinner' );

			$.post( aiovg.ajax_url, params, function( response ) {
				$pagination.removeClass( 'aiovg-spinner' );

				if ( paged < numpages ) {
					$this.data( 'paged', params.paged );	
				} else {
					$this.hide();
				}			
				
				if ( response.success ) {	
					var html = $( response.data.html ).find( '.aiovg-grid' ).html();				
					$( '#aiovg-' + params.uid + ' .aiovg-grid' ).append( html );
					
					$pagination.trigger( 'AIOVG.onGalleryUpdated' );
				}
			});
		});
		
	});

})( jQuery );
