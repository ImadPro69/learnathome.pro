'use strict';

/*global
    jQuery, Swiper
 */

/**
 * swiper.js
 *
 * Handle sliders with the Swiper lib
 */
( function( $ ) {

    $( document ).ready( function() {

        /**
         * Section Slider
         */
        var $swiperContainer = $( '.grimlock-section .swiper-container, .wp-block-grimlock-query .swiper-container, .wp-block-grimlock-woocommerce-products .swiper-container, .wp-block-grimlock-term-query .swiper-container' );

        if ( $swiperContainer.length ) {

            $swiperContainer.each( function() {
                var $block = $( this ).closest( '.grimlock-section, .wp-block-grimlock-query, .wp-block-grimlock-term-query, .wp-block-grimlock-woocommerce-products' );
				var autoSlideEnabled = $block.data( 'auto-slide-enabled' );
				var arrowsEnabled = $block.data( 'slider-arrows-enabled' );
				var loopEnabled = $block.data( 'slider-loop-enabled' );
				var pagination = $block.data( 'slider-pagination' );
				var transition = $block.data( 'slider-transition' );
				var slidesPerView = $block.data( 'slides-per-view' );

				var swiper = new Swiper( this, {
					effect: transition || 'slide',
					observer: true,
					slidesPerView: slidesPerView,
					loop: !! loopEnabled,
					spaceBetween: 0,
					navigation: arrowsEnabled ? {
						nextEl: $block.find( '.swiper-button-next' ) .get( 0 ),
						prevEl: $block.find( '.swiper-button-prev' ).get( 0 ),
					} : false,
					pagination: pagination !== '' ? {
						el: $block.find( '.swiper-pagination' ).get( 0 ),
						type: pagination,
						clickable: true,
					} : false,
					autoplay: !! autoSlideEnabled ? { delay: 5000 } : false,
					breakpoints: {
						0: {
							slidesPerView: slidesPerView > 1 ? 1 : slidesPerView,
							spaceBetween: 0,
						},
						580: {
							slidesPerView: slidesPerView > 2 ? 2 : slidesPerView,
							spaceBetween: 0,
						},
						768: {
							slidesPerView: slidesPerView > 3 ? 3 : slidesPerView,
							spaceBetween: 0,
						},
						992: {
							slidesPerView: slidesPerView > 4 ? 4 : slidesPerView,
							spaceBetween: 0,
						},
						1200: {
							slidesPerView: slidesPerView > 5 ? 5 : slidesPerView,
							spaceBetween: 0,
						},
					},
				} );

				$( '.wp-block-getwid-tabs .ui-tabs-anchor' ).on( 'click', function() {
					swiper.update();
				} );

            } );

        }
    } );

} )( jQuery );
