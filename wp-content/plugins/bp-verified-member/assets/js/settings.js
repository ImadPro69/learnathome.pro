jQuery( function( $ ) {
	var $themosaurusGlider = $( '.themosaurus-promo .glider' );

	if ( $themosaurusGlider.length ) {
		new Glider( $themosaurusGlider.get( 0 ), {
			dots: '.dots',
			arrows: {
				prev: '.glider-prev',
				next: '.glider-next',
			},
		} );
	}
} );