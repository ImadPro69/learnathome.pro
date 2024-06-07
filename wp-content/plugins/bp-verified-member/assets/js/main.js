jQuery( function( $ ) {
	if ( window.bpVerifiedMember ) {
		/**
		 * Init badges of a given type throughout the page
		 *
		 * @param {string} type Type of badge to init (verified, unverified)
		 */
		var initBadges = function ( type ) {

			// Remove badge HTML from "title" attributes
			$( '[title*=\'' + bpVerifiedMember[ type + 'BadgeHtml' ] + '\']' ).each( function() {
				$( this ).attr( 'title', $( this ).attr( 'title' ).replace( bpVerifiedMember[ type + 'BadgeHtml' ], '' ) );
			} );

			// Remove badge HTML from "data-bp-tooltip" attributes
			$( '[data-bp-tooltip*=\'' + bpVerifiedMember[ type + 'BadgeHtml' ] + '\']' ).each( function() {
				$( this ).attr( 'data-bp-tooltip', $( this ).attr( 'data-bp-tooltip' ).replace( bpVerifiedMember[ type + 'BadgeHtml' ], '' ) );
			} );

			// Add the badge html to replace the after-element where necessary
			$(
				'.bp-' + type + '-member:not(.bp-' + type + '-member-badge-loaded) .member-name-item > a,' +
				'.bp-' + type + '-member:not(.bp-' + type + '-member-badge-loaded) .item-title > a,' +
				'.bp-' + type + '-member:not(.bp-' + type + '-member-badge-loaded) > .author > a,' +
				'.bp-' + type + '-member:not(.bp-' + type + '-member-badge-loaded) .member-name > a',
			)
				.append( bpVerifiedMember[ type + 'BadgeHtml' ] )
				.closest( '.bp-' + type + '-member' )
				.addClass( 'bp-' + type + '-member-badge-loaded' );

			// Handle tooltips
			var $badges = $( '.bp-' + type + '-badge' );
			$badges.each( function() {
				if ( $( this ).siblings( '.bp-' + type + '-badge-tooltip' ).length )
					return;

				// Add tooltip to dom
				var $tooltip = $(
					'<span class="bp-' + type + '-badge-tooltip" role="tooltip" style="visibility: hidden;">' +
						bpVerifiedMember[ type + 'Tooltip' ] +
						'<span class="bp-' + type + '-badge-tooltip-arrow" data-popper-arrow></span>' +
					'</span>'
				);
				$( this ).after( $tooltip );

				// Initialize Popper to handle tooltip
				if ( Popper.createPopper ) // Popper 2.x.x
					var badgeTooltip = Popper.createPopper( this, $tooltip.get( 0 ), {
						placement: 'top',
						modifiers: [
							{
								name: 'offset',
								options: {
									offset: [0, 5],
								},
							},
						],
					} );
				else // Popper 1.x.x
					var badgeTooltip = new Popper( this, $tooltip.get( 0 ), {
						placement: 'top',
						modifiers: {
							offset: {
								offset: '0, 5px',
							},
						},
					} );

				// Force update tooltip placement to prevent weird offset
				setTimeout( function() {
					badgeTooltip.update();
				}, 100 );

				// Show tooltip on hover
				$( this ).hover( function() {
					$tooltip.css( 'visibility', 'visible' );
				}, function() {
					$tooltip.css( 'visibility', 'hidden' );
				} );
			} );
		};

	}
	;

	/**
	 * Init verified and unverified badges
	 */
	var initBadgeTypes = function() {
		initBadges( 'verified' );
		initBadges( 'unverified' );
	};

	initBadgeTypes();

	// Init badges when JetPack infinite loading loads a new set of posts
	$( document.body ).on( 'post-load', initBadgeTypes );

	// Init badges when sending a reply in BP messages
	var $replyForm = $( '#send-reply' );
	if ( $replyForm.length ) {
		var replyObserver = new MutationObserver( initBadgeTypes );
		replyObserver.observe( $replyForm.parent().get( 0 ), { childList: true } );
	}

	// Init badges when opening an rtMedia lightbox
	$(
		'.rtmedia-list-media.rtm-gallery-list,' +
		'.rtmedia-activity-container ul.rtmedia-list,' +
		'#bp-media-list,.bp-media-sc-list,' +
		'li.media.album_updated ul,ul.bp-media-list-media,' +
		'li.activity-item div.activity-content div.activity-inner div.bp_media_content,' +
		'.rtm-bbp-container,' +
		'ul.rtm-comment-container'
	)
		.on( 'mfpAjaxContentAdded', function() {
			initBadgeTypes();
		} );

	var requestLoading = false;
	$( 'button.bp-verified-member-request-button:not(.bp-verified-member-verification-pending)' )
		.on( 'click', function() {
			if ( requestLoading )
				return;

			requestLoading = true;

			var nonce = $( this ).data( 'bp-verified-member-request-nonce' );
			var $this = $( this );

			if ( !nonce )
				return;

			$this.html( '<span class="dashicons dashicons-update bp-verified-member-spin"></span>' );

			$.post( window.bpVerifiedMember.ajaxUrl, {
				action: 'bp_verified_member_request',
				nonce: nonce,
			}, function( result ) {
				if ( result.success ) {
					$this.html( result.data );
					$this.addClass( 'bp-verified-member-verification-pending' );
				}
			} );
		} );
} );