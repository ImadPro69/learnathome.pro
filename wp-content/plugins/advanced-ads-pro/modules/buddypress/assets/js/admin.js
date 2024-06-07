/**
 * Renders a html field corresponding to the currently selected field type.
 */
jQuery( document ).on( 'change', '.advads-pro-buddyboss-xprofile-field-type', function() {
	const $self = jQuery( this ),
	$loading = jQuery( '<span class="advads-loader"></span>' );

	// Replace the dynamic field with the loader.
	$self.parent().nextAll( '.advanced-ads-buddyboss-xprofile-dynamic-field' ).replaceWith( $loading );

	jQuery.ajax( {
		type: 'POST',
		url: ajaxurl,
		data: {
			action: 'advads-pro-buddyboss-render-xprofile-field',
			field_name: $self.data( 'field-name' ),
			field_type: $self.find( 'option:selected' ).data( 'field-type' ),
			nonce: advadsglobal.ajax_nonce
		},
	} ).done( function ( data ) {
		$loading.replaceWith( data );
	} ).fail( function ( jqXHR, textStatus, errorThrown ) {
		$loading.replaceWith( textStatus + ': ' + errorThrown );
	} );
} );
