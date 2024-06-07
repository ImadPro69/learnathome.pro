jQuery( function( $ ) {

	/**
	 * Handle min-max fields
	 */
	if ( $( '.pimp-my-site-min-max-field' ).length ) {
		$( '.pimp-my-site-min-max-field button' ).on( 'click', function( e ) {
			e.preventDefault();

			let $minField = $( this ).siblings( '.pimp-my-site-min-max-field__min' );
			let $maxField = $( this ).siblings( '.pimp-my-site-min-max-field__max' );
			let $mergedField = $( this ).siblings( '.pimp-my-site-min-max-field__merged' );

			if ( $mergedField.is( '[style*="display: none"]' ) ) {
				$mergedField.find( 'input' ).val( $minField.find( 'input' ).val() );
				$mergedField.css( 'display', 'inline-block' );
				$minField.hide();
				$maxField.hide();
				$( this ).find( 'i' ).removeClass( 'dashicons-admin-links' ).addClass( 'dashicons-editor-unlink' );
			}
			else {
				$mergedField.hide();
				$minField.css( 'display', 'inline-block' );
				$maxField.css( 'display', 'inline-block' );
				$( this ).find( 'i' ).removeClass( 'dashicons-editor-unlink' ).addClass( 'dashicons-admin-links' );
			}
		} );

		$( '.pimp-my-site-min-max-field' ).each( function() {
			let $minInput    = $( this ).find( '.pimp-my-site-min-max-field__min input' );
			let $maxInput    = $( this ).find( '.pimp-my-site-min-max-field__max input' );
			let $mergeButton = $( this ).find( 'button' );

			if ( parseFloat( $minInput.val() ) !== parseFloat( $maxInput.val() ) )
				$mergeButton.trigger( 'click' );
		} );

		$( '.pimp-my-site-min-max-field .pimp-my-site-min-max-field__min input' ).on( 'change', function() {
			let $maxInput = $( this ).parents( '.pimp-my-site-min-max-field' ).find( '.pimp-my-site-min-max-field__max input' );

			if ( parseFloat( $maxInput.val() ) < parseFloat( $( this ).val() ) )
				$maxInput.val( $( this ).val() );
		} );

		$( '.pimp-my-site-min-max-field .pimp-my-site-min-max-field__max input' ).on( 'change', function() {
			let $minInput = $( this ).parents( '.pimp-my-site-min-max-field' ).find( '.pimp-my-site-min-max-field__min input' );

			if ( parseFloat( $minInput.val() ) > parseFloat( $( this ).val() ) )
				$minInput.val( $( this ).val() );
		} );

		$( '.pimp-my-site-min-max-field .pimp-my-site-min-max-field__merged input' ).on( 'change', function() {
			$( this ).parents( '.pimp-my-site-min-max-field' ).find( '.pimp-my-site-min-max-field__min input, .pimp-my-site-min-max-field__max input' ).val( $( this ).val() );
		} );
	}

	/**
	 * Handle multi color field
	 */
	$( '.pimp-my-site-multi-color-field' ).each( function() {
		let $container = $( this );

		// Get field id
		let fieldId = $container.data( 'field-id' );
		if ( !fieldId )
			return;

		// Get color values
		let colorValues = $container.data( 'color-values' );

		// Generate color fields for each saved value
		if ( colorValues && colorValues.length && Array.isArray( colorValues ) ) {
			$.each( colorValues, function( index, value ) {
				$container.find( '.pimp-my-site-multi-color-field__add-color' ).before(
					'<div class="pimp-my-site-multi-color-field__item">' +
					'<input type="text" id="' + fieldId + '[]" name="' + fieldId + '[]" value="' + value + '" class="pimp-my-site-color-picker" />' +
					'<button class="pimp-my-site-multi-color-field__remove-color button alt"><i class="dashicons dashicons-no-alt"></i></button>' +
					'</div>'
				);
			} );

			if ( $container.find( '.pimp-my-site-multi-color-field__item' ).length <= 1 )
				$container.find( '.pimp-my-site-multi-color-field__remove-color' ).hide();
		}
		// Generate a default field if there's no saved value
		else {
			$container.find( '.pimp-my-site-multi-color-field__add-color' ).before(
				'<div class="pimp-my-site-multi-color-field__item">' +
				'<input type="text" id="' + fieldId + '[]" name="' + fieldId + '[]" value="#ffffff" class="pimp-my-site-color-picker" />' +
				'<button class="pimp-my-site-multi-color-field__remove-color button alt"><i class="dashicons dashicons-no-alt"></i></button>' +
				'</div>'
			);

			if ( $container.find( '.pimp-my-site-multi-color-field__item' ).length <= 1 )
				$container.find( '.pimp-my-site-multi-color-field__remove-color' ).hide();
		}

		// Handle "Add color" button
		$container.on( 'click', '.pimp-my-site-multi-color-field__add-color', function( e ) {
			e.preventDefault();

			$( this ).before(
				'<div class="pimp-my-site-multi-color-field__item">' +
				'<input type="text" id="' + fieldId + '[]" name="' + fieldId + '[]" value="#ffffff" class="pimp-my-site-color-picker" />' +
				'<button class="pimp-my-site-multi-color-field__remove-color button alt"><i class="dashicons dashicons-no-alt"></i></button>' +
				'</div>'
			);

			$( '.pimp-my-site-color-picker' ).wpColorPicker();

			if ( $container.find( '.pimp-my-site-multi-color-field__item' ).length > 1 )
				$container.find( '.pimp-my-site-multi-color-field__remove-color' ).show();
		} );

		// Handle "Remove color" button
		$container.on( 'click', '.pimp-my-site-multi-color-field__remove-color', function( e ) {
			e.preventDefault();

			$( this ).parent().remove();

			if ( $container.find( '.pimp-my-site-multi-color-field__item' ).length <= 1 )
				$container.find( '.pimp-my-site-multi-color-field__remove-color' ).hide();
		} )
	} );

	/**
	 * Init color fields
	 */
	$( '.pimp-my-site-color-picker' ).wpColorPicker();

	/**
	 * Presets form
	 */
	$( '.pimp-my-site-presets-form' ).on( 'submit', function( e ) {
		if ( ! confirm( pimpMySiteSettings.confirmPresetMessage ) )
			e.preventDefault();
	} );

	/**
	 * Scroll bar customization conditional display
 	 */
	var $scrollbarCustomizationEnabled = $( 'input[name="pimp_my_site_scrollbar_customization_enabled"]' );
	var handleChangeScrollbarCustomizationEnabled = function() {
		var checked = $( this ).is( ':checked' );
		$( 'input[name="pimp_my_site_scrollbar_handle_color"]' ).closest( '.pimp-my-site-field' ).toggle( checked );
		$( 'input[name="pimp_my_site_scrollbar_track_color"]' ).closest( '.pimp-my-site-field' ).toggle( checked );
	};
	$scrollbarCustomizationEnabled.on( 'change', handleChangeScrollbarCustomizationEnabled );
	(handleChangeScrollbarCustomizationEnabled.bind( $scrollbarCustomizationEnabled ))();

	/**
	 * Particles options conditional display
	 */
	var $scrollbarCustomizationEnabled = $( 'input[name="pimp_my_site_particles_enabled"]' );
	var handleChangeScrollbarCustomizationEnabled = function() {
		var checked = $( this ).is( ':checked' );
		$( 'ul#input_pimp_my_site_particles_shapes' ).closest( '.pimp-my-site-field' ).toggle( checked );
		$( 'div[data-field-id="pimp_my_site_particles_colors"]' ).closest( '.pimp-my-site-field' ).toggle( checked );
		$( 'input#pimp_my_site_particles_opacity' ).closest( '.pimp-my-site-field' ).toggle( checked );
		$( 'input#pimp_my_site_particles_size' ).closest( '.pimp-my-site-field' ).toggle( checked );
		$( 'input#pimp_my_site_particles_direction' ).closest( '.pimp-my-site-field' ).toggle( checked );
		$( 'input#pimp_my_site_particles_speed' ).closest( '.pimp-my-site-field' ).toggle( checked );
		$( 'input#pimp_my_site_particles_lifetime' ).closest( '.pimp-my-site-field' ).toggle( checked );
		$( 'input[name="pimp_my_site_particles_density"]' ).closest( '.pimp-my-site-field' ).toggle( checked );
	};
	$scrollbarCustomizationEnabled.on( 'change', handleChangeScrollbarCustomizationEnabled );
	(handleChangeScrollbarCustomizationEnabled.bind( $scrollbarCustomizationEnabled ))();

	/**
	 * Init modals
	 */
	MicroModal.init();
	$( 'a[data-micromodal-trigger]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( ! $( '.pimp-my-site-preset img' ).hasClass( 'ff-image' ) )
			new Freezeframe( '.pimp-my-site-preset img' );
	} );

	/**
	 * Handle selector fields
	 */
	// Replace displayed selected image when selecting a new one
	$( '.pimp-my-site-selector-field .pimp-my-site-radio-image-control input' ).on( 'change', function() {
		var selectedImgSrc = $( this ).parent( 'li' ).find( 'img' ).attr( 'src' );
		$( this ).closest( '.pimp-my-site-selector-field' ).find( '.pimp-my-site-selector-field__selected-option img' ).attr( 'src', selectedImgSrc );
	} );

	// Init isotope when opening the modal
	$( '.pimp-my-site-selector-field .pimp-my-site-selector-field__modal-button, .pimp-my-site-presets .pimp-my-site-presets-modal-button' ).on( 'click', function( e ) {
		$( this ).closest( '.pimp-my-site-selector-field, .pimp-my-site-presets-form' ).find( '.pimp-my-site-options-categories' ).isotope( {
			itemSelector: '.pimp-my-site-options-category',
			filter: '*',
			visibleStyle: {
				opacity: 1,
				transform: 'translateY(0)',
			},
			hiddenStyle: {
				opacity: 0,
				transform: 'translateY(100px)',
			},
		} );

		$( this ).closest( '.pimp-my-site-selector-field, .pimp-my-site-presets-form' ).find( '.pimp-my-site-selector-field__filters a, .pimp-my-site-presets-modal__filters a' ).removeClass( 'active' ).first().addClass( 'active' );
	} );

	// Apply isotope filter when clicking on one of the sidebar filters
	$( '.pimp-my-site-selector-field .pimp-my-site-selector-field__filters a, .pimp-my-site-presets-modal .pimp-my-site-presets-modal__filters a' ).on( 'click', function( e ) {
		e.preventDefault();

		$( this ).closest( '.pimp-my-site-selector-field__filters, .pimp-my-site-presets-modal__filters' ).find( 'a' ).removeClass( 'active' );
		$( this ).addClass( 'active' );
		$( this ).closest( '.pimp-my-site-selector-field, .pimp-my-site-presets-form' ).find( '.pimp-my-site-options-categories' ).isotope( {
			itemSelector: '.pimp-my-site-options-category',
			filter: $( this ).attr( 'href' ),
			visibleStyle: {
				opacity: 1,
				transform: 'translateY(0)',
			},
			hiddenStyle: {
				opacity: 0,
				transform: 'translateY(100px)',
			},
		} );
	} );

	/**
	 * Handle promo slider
	 */
	let $themosaurusGlider = $( '.themosaurus-promo .glider' );

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
