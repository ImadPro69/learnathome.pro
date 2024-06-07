(function( $ ) {
	'use strict';	

	/**
	 * Init player.
	 */
	function initPlayer( $el ) {
		// Vars		
		var playerId = $el.data( 'id' );		
		var settings = $el.data( 'params' );			

		// Player
		var loadPlayer = function() {
			settings.player.html5 = {
				vhs: {
					overrideNative: ! videojs.browser.IS_ANY_SAFARI,
				}
			};

			var player = videojs( playerId, settings.player );				

			var overlays = [];
			var hasVideoStarted = false;
			
			// Trigger ready event.
			var options = {					
				id: playerId, // Backward compatibility to 3.3.0
				player_id: playerId,
				config: settings, // Backward compatibility to 3.3.0
				settings: settings,
				player: player					
			};

			$el.trigger( 'player.init', options );

			// Fired when the player is ready.
			player.ready(function() {
				$el.removeClass( 'vjs-waiting' );
				
				$el.find( '.vjs-big-play-button' ).one( 'click', function() {
					if ( ! hasVideoStarted ) {
						$el.addClass( 'vjs-waiting' );
					}
				});				
			});

			// On metadata loaded.
			player.one( 'loadedmetadata', function() {
				// Standard quality selector.
				$el.find( '.vjs-quality-selector .vjs-menu-item' ).each(function() {
					var $this = $( this );

					var text = $this.find( '.vjs-menu-item-text' ).html();
					var resolution = text.replace( /\D/g, '' );

					if ( resolution >= 2160 ) {
						$this.append( '<span class="vjs-quality-menu-item-sub-label">4K</span>' );
					} else if ( resolution >= 720 ) {
						$this.append( '<span class="vjs-quality-menu-item-sub-label">HD</span>' );
					}
				});

				// Add support for SRT.
				if ( settings.hasOwnProperty( 'tracks' ) ) {
					for ( var i = 0, max = settings.tracks.length; i < max; i++ ) {
						var track = settings.tracks[ i ];

						var mode = '';
						if ( i == 0 && settings.cc_load_policy == 1 ) {
							mode = 'showing';
						}

						if ( /srt/.test( track.src.toLowerCase() ) ) {
							addSrtTextTrack( player, track, mode );
						} else {
							var obj = {
								kind: 'subtitles',
								src: track.src,									
								label: track.label,
								srclang: track.srclang
							};

							if ( mode ) {
								obj.mode = mode;
							}

							player.addRemoteTextTrack( obj, true ); 
						}					               
					}
				}
				
				// Chapters
				if ( settings.hasOwnProperty( 'chapters' ) ) {
					addMarkers( player, settings.chapters );
				}
			});

			// Chapters
			if ( settings.hasOwnProperty( 'chapters' ) ) {
				try {
					player.getDescendant([
						'controlBar',
						'progressControl',
						'seekBar',
						'mouseTimeDisplay',
						'timeTooltip',
					]).update = function( seekBarRect, seekBarPoint, time ) {
						var markers = settings.chapters;
						var markerIndex = markers.findIndex(({ time: markerTime }) => markerTime == formatedTimeToSeconds( time ));
				
						if ( markerIndex > -1 ) {
							var label = markers[ markerIndex ].label;
					
							videojs.dom.emptyEl( this.el() );
							videojs.dom.appendContent( this.el(), [labelEl( label ), timeEl( time )] );
					
							return false;
						}
				
						this.write( time );
					};
				} catch ( error ) {
					/** console.log( error ); */
				}
			}

			// Fired the first time a video is played.
			player.one( 'play', function( e ) {
				hasVideoStarted = true;
				$el.removeClass( 'vjs-waiting' );

				updateViewsCount( settings );

				$( '.aiovg-player-standard' ).trigger( 'playRequested', { playerId: playerId } );
			});

			$el.on( 'playRequested', function( event, args ) {
				if ( playerId != args.playerId ) {
					player.pause();
				}
			});

			player.on( 'playing', function() {
				player.trigger( 'controlsshown' );
			});

			player.on( 'ended', function() {
				player.trigger( 'controlshidden' );
			});

			// Standard quality selector.
			player.on( 'qualitySelected', function( event, source ) {
				var resolution = source.label.replace( /\D/g, '' );

				player.removeClass( 'vjs-4k' );
				player.removeClass( 'vjs-hd' );

				if ( resolution >= 2160 ) {
					player.addClass( 'vjs-4k' );
				} else if ( resolution >= 720 ) {
					player.addClass( 'vjs-hd' );
				}
			});

			// HLS quality selector.
			var src = player.src();

			if ( /.m3u8/.test( src ) || /.mpd/.test( src ) ) {
				if ( settings.player.controlBar.children.indexOf( 'qualitySelector' ) !== -1 ) {
					player.qualityMenu();
				}
			}

			// Offset
			var offset = {};

			if ( settings.hasOwnProperty( 'start' ) ) {
				offset.start = settings.start;
			}

			if ( settings.hasOwnProperty( 'end' ) ) {
				offset.end = settings.end;
			}
			
			if ( Object.keys( offset ).length > 1 ) {
				offset.restart_beginning = false;
				player.offset( offset );
			}				

			// Share / Embed.
			if ( settings.hasOwnProperty( 'share' ) || settings.hasOwnProperty( 'embed' ) ) {
				overlays.push({
					content: '<button type="button" class="vjs-share-embed-button" title="Share"><span class="vjs-icon-share" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">Share</span></button>',
					class: 'vjs-share',
					align: 'top-right',
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});					
			}

			// Download
			if ( settings.hasOwnProperty( 'download' ) ) {
				var className = 'vjs-download';

				if ( settings.hasOwnProperty( 'share' ) || settings.hasOwnProperty( 'embed' ) ) {
					className += ' vjs-has-share';
				}

				overlays.push({
					content: '<a href="' + settings.download.url + '" class="vjs-download-button" title="Download" target="_blank" style="text-decoration:none;"><span class="aiovg-icon-download" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">Download</span></a>',
					class: className,
					align: 'top-right',
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});
			}

			// Logo
			if ( settings.hasOwnProperty( 'logo' ) ) {
				var attributes = [];
				attributes['src'] = settings.logo.image;

				if ( settings.logo.margin ) {
					settings.logo.margin = settings.logo.margin - 5;
				}

				var align = 'bottom-left';
				attributes['style'] = 'margin: ' + settings.logo.margin + 'px;';

				switch ( settings.logo.position ) {
					case 'topleft':
						align = 'top-left';
						attributes['style'] = 'margin: ' + settings.logo.margin + 'px;';
						break;

					case 'topright':
						align = 'top-right';
						attributes['style'] = 'margin: ' + settings.logo.margin + 'px;';
						break;

					case 'bottomright':
						align = 'bottom-right';
						attributes['style'] = 'margin: ' + settings.logo.margin + 'px;';
						break;				
				}

				var logo = '<img ' +  mergeAttributes( attributes ) + ' alt="" />';
				if ( settings.logo.link ) {
					logo = '<a href="' + settings.logo.link + '" style="text-decoration:none;">' + logo  + '</a>';
				}

				overlays.push({
					content: logo,
					class: 'vjs-logo',
					align: align,
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});
			}

			// Overlay
			if ( overlays.length > 0 ) {
				player.overlay({
					content: '',
					overlays: overlays
				});

				if ( settings.hasOwnProperty( 'share' ) || settings.hasOwnProperty( 'embed' ) ) {
					var options = {};
					options.content = $el.find( '.vjs-share-embed' ).get(0);
					options.temporary = false;

					var ModalDialog = videojs.getComponent( 'ModalDialog' );
					var modal = new ModalDialog( player, options );
					modal.addClass( 'vjs-modal-dialog-share-embed' );

					player.addChild( modal );

					var wasPlaying = true;
					$el.find( '.vjs-share-embed-button' ).on( 'click', function() {
						wasPlaying = ! player.paused;
						modal.open();						
					});

					modal.on( 'modalclose', function() {
						if ( wasPlaying ) {
							player.play();
						}						
					});
				}

				if ( settings.hasOwnProperty( 'embed' ) ) {
					$el.find( '.vjs-copy-embed-code' ).on( 'focus', function() {
						$( this ).select();	
						document.execCommand( 'copy' );					
					});
				}
			}

			// Keyboard hotkeys.
			if ( settings.hotkeys ) {
				player.hotkeys();
			}

			// Custom contextmenu.
			if ( settings.hasOwnProperty( 'contextmenu' ) ) {
				if ( ! $( '#aiovg-contextmenu' ).length ) {
					$( 'body' ).append( '<div id="aiovg-contextmenu" style="display: none;"><div class="aiovg-contextmenu-content">' + settings.contextmenu.content + '</div></div>' );
				}
	
				var contextmenu = document.getElementById( 'aiovg-contextmenu' );
				var timeoutHandler = '';
				
				$( '#' + playerId ).on( 'contextmenu', function( e ) {						
					if ( e.keyCode == 3 || e.which == 3 ) {
						e.preventDefault();
						e.stopPropagation();
						
						var width = contextmenu.offsetWidth,
							height = contextmenu.offsetHeight,
							x = e.pageX,
							y = e.pageY,
							doc = document.documentElement,
							scrollLeft = ( window.pageXOffset || doc.scrollLeft ) - ( doc.clientLeft || 0 ),
							scrollTop = ( window.pageYOffset || doc.scrollTop ) - ( doc.clientTop || 0 ),
							left = x + width > window.innerWidth + scrollLeft ? x - width : x,
							top = y + height > window.innerHeight + scrollTop ? y - height : y;
				
						contextmenu.style.display = '';
						contextmenu.style.left = left + 'px';
						contextmenu.style.top = top + 'px';
						
						clearTimeout( timeoutHandler );
	
						timeoutHandler = setTimeout(function() {
							contextmenu.style.display = 'none';
						}, 1500);				
					}														 
				});
				
				document.addEventListener( 'click', function() {
					contextmenu.style.display = 'none';								 
				});
			}
		};

		// ...
		if ( settings.cookie_consent ) {
			$el.find( '.aiovg-privacy-consent-button' ).on( 'click', function() {
				$( this ).html( '...' );

				settings.player.autoplay = true;

				setCookie();

				loadPlayer();
				$el.find( '.aiovg-privacy-wrapper' ).remove();

				$( '.aiovg-player-standard' ).trigger( 'cookieConsent' );
				$( '.aiovg-player-element' ).removeAttr( 'cookieconsent' );
			});

			$el.on( 'cookieConsent', function( event, args ) {
				if ( $el.find( '.aiovg-privacy-wrapper' ).length > 0 ) {
					loadPlayer();
					$el.find( '.aiovg-privacy-wrapper' ).remove();
				}
			});
		} else {
			loadPlayer();
		}		
	}	 	

	/**
	 * Add SRT Text Track.
	 */
	function addSrtTextTrack( player, track, mode ) {
		var xmlhttp;

		if ( window.XMLHttpRequest ) {
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
		}
		
		xmlhttp.onreadystatechange = function() {				
			if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 && xmlhttp.responseText ) {					
				var text = srtToWebVTT( xmlhttp.responseText );

				if ( text ) {
					var blob = new Blob([ text ], { type : 'text/vtt' });
					var src = URL.createObjectURL( blob );

					var obj = {
						kind: 'subtitles',
						src: src,							
						label: track.label,
						srclang: track.srclang							
					};

					if ( mode ) {
						obj.mode = mode;
					}

					player.addRemoteTextTrack( obj, true );
				}
			}					
		};

		xmlhttp.open( 'GET', track.src, true );
		xmlhttp.send();							
	}	
	
	/**
	 * Convert SRT to WebVTT.
	 */
	function srtToWebVTT( data ) {
		// Remove dos newlines.
		var srt = data.replace( /\r+/g, '' );

		// Trim white space start and end.
		srt = srt.replace( /^\s+|\s+$/g, '' );

		// Get cues.
		var cuelist = srt.split( '\n\n' );
		var result  = '';

		if ( cuelist.length > 0 ) {
		  	result += "WEBVTT\n\n";

			for ( var i = 0; i < cuelist.length; i++ ) {
				result += convertSrtCue( cuelist[ i ] );
			}
		}

		return result;
  	}

	function convertSrtCue( caption ) {
		// Remove all html tags for security reasons.
		// srt = srt.replace( /<[a-zA-Z\/][^>]*>/g, '' );

		var cue = '';
		var s = caption.split( /\n/ );

		// Concatenate muilt-line string separated in array into one.
		while ( s.length > 3 ) {
			for ( var i = 3; i < s.length; i++ ) {
				s[2] += "\n" + s[ i ];
			}

			s.splice( 3, s.length - 3 );
		}

		var line = 0;

		// Detect identifier.
		if ( ! s[0].match( /\d+:\d+:\d+/ ) && s[1].match( /\d+:\d+:\d+/ ) ) {
		  	cue  += s[0].match( /\w+/ ) + "\n";
		  	line += 1;
		}

		// Get time strings.
		if ( s[ line ].match( /\d+:\d+:\d+/ ) ) {
			// Convert time string.
			var m = s[1].match( /(\d+):(\d+):(\d+)(?:,(\d+))?\s*--?>\s*(\d+):(\d+):(\d+)(?:,(\d+))?/ );

			if ( m ) {
				cue  += m[1] + ":" + m[2] + ":" + m[3] + "." + m[4] + " --> " + m[5] + ":" + m[6] + ":" + m[7] + "." + m[8] + "\n";
				line += 1;
			} else {
				// Unrecognized timestring.
				return '';
			}
		} else {
		  	// File format error or comment lines.
		  	return '';
		}

		// Get cue text.
		if ( s[ line ] ) {
		  	cue += s[ line ] + "\n\n";
		}

		return cue;
  	}

	/**
	 * Helper functions for chapters.
	 */
	function addMarkers( player, markers ) {
		var total   = player.duration();
		var seekBar = document.querySelector( '.vjs-progress-control .vjs-progress-holder' );

		if ( seekBar !== null ) {
			for ( var i = 0; i < markers.length; i++ ) {
				var elem = document.createElement( 'div' );
				elem.className = 'vjs-marker';
				elem.style.left = ( markers[ i ].time / total ) * 100 + '%';

				seekBar.appendChild( elem );
			}
		}
	}

	function formatedTimeToSeconds( time ) {
		var timeSplit = time.split( ':' );
		var seconds   = +timeSplit.pop();
  
		return timeSplit.reduce(( acc, curr, i, arr ) => {
			if ( arr.length === 2 && i === 1 ) return acc + +curr * 60 ** 2;
		  	else return acc + +curr * 60;
		}, seconds);
	}

	function timeEl( time ) {
		return videojs.dom.createEl( 'span', undefined, undefined, '(' + time + ')' );
	}

	function labelEl( label ) {
		return videojs.dom.createEl( 'strong', undefined, undefined, label );
	}

	/**
	 * Set GDPR cookie.
	 */
	function setCookie() {		
		var data = {
			'action': 'aiovg_set_cookie',
			'security': aiovg_player.ajax_nonce
		};

		$.post( aiovg_player.ajax_url, data, function( response ) {
			/** console.log( response ); */
		});
	}

	/**
	 * Update video views count.
	 */
	function updateViewsCount( settings ) {
		if ( 'aiovg_videos' !== settings.post_type ) {
			return false;
		}

		var data = {
			'action': 'aiovg_update_views_count',
			'post_id': settings.post_id,
			'security': aiovg_player.ajax_nonce
		};

		$.post( aiovg_player.ajax_url, data, function( response ) {
			/** console.log( response ); */
		});
	}

	/**
	 * Merge attributes.
	 */
	function mergeAttributes( attributes ) {
		var str = '';

		for ( var key in attributes ) {
			str += ( key + '="' + attributes[ key ] + '" ' );
		}

		return str;
	} 

	/**
	 * Refresh iframe player elements upon cookie confirmation.
	 */
	window.onmessage = function( event ) {
		if ( event.data == 'aiovg-cookie-consent' ) {
			$( '.aiovg-player-iframe iframe' ).each(function() {
				var src = $( this ).attr( 'src' );

				if ( src.indexOf( 'refresh=1' ) == -1 ) {
                    var separator = src.indexOf( '?' ) > -1 ? '&' : '?';
					$( this ).attr( 'src', src + separator + 'refresh=1' );
				}
			});
		}
	};

	/**
	 * Called when the page has loaded.
	 */
	$(function() {
		
		// Update views count for the non-iframe embeds
		$( '.aiovg-player-raw' ).each(function() {
			var settings = $( this ).data( 'params' );
			updateViewsCount( settings );
		});

		// Init player.
		$( '.aiovg-player-standard' ).each(function() {
			initPlayer( $( this ) );
		});		

		// Custom error message.
		if ( typeof videojs !== "undefined" ) {
			videojs.hook( 'beforeerror', function( player, error ) {
				// Prevent current error from being cleared out.
				if ( error == null ) {
					return player.error();
				}

				// But allow changing to a new error.
				if ( error.code == 2 || error.code == 4 ) {
					var src = player.src();

					if ( /.m3u8/.test( src ) || /.mpd/.test( src ) ) {
						return {
							code: error.code,
							message: aiovg_player.i18n.stream_not_found
						}
					}
				}
				
				return error;
			});
		}

	});

})( jQuery );
