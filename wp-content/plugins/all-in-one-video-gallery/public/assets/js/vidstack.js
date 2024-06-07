'use strict';

class AIOVGVideoElement extends HTMLElement {

    /**
     * Element created.
     */
    constructor() {
        super();        
        
        // Set references to the private properties used by the component
        this._isRendered = false;
		this._isCookieConsentLoaded = false;
		this._isPosterImageLoaded = false;
        this._isPlayerLoaded = false; 
		this._player = null;
		this._playerEl = null;
		this._cookieConsentEl = null;
		this._intersectionObserver = null;
		this._isInViewport = false;
    }

    /**
     * Browser calls this method when the element is added to the document.
     * (can be called many times if an element is repeatedly added/removed)
     */
    connectedCallback() { 			
		this._uid = parseInt( this.getAttribute( 'reference_id' ) );	
		this._params = window[ 'aiovg_player_' + this._uid ];	
		this._playerId = 'aiovg-player-' + this._uid; 
     
		this._render();  
    }

    /**
     * Array of attribute names to monitor for changes.
     */
    static get observedAttributes() {
        return [ 'cookieconsent' ];
    }   
    
    /**
     * Called when one of the observed attributes listed above is modified.
     */
    attributeChangedCallback( name, oldValue, newValue ) {
        if ( oldValue == newValue ) return false;

        switch ( name ) {
            case 'cookieconsent':                     
                if ( ! this.cookieConsent && ! this._isRendered ) {
					this._cookieConsentEl.remove();
                	this._render();
                }
                break;
        }
    }

    /**
     * Define getters and setters for attributes.
     */

    get cookieConsent() {
        return this.hasAttribute( 'cookieconsent' );
    }

    set cookieConsent( value ) {
        const isEnabled = Boolean( value );

        if ( isEnabled ) {
            this.setAttribute( 'cookieconsent', '' );
        } else {
            this.removeAttribute( 'cookieconsent' );
        }
    }    

    /**
     * Define private methods.
     */

    _render() {    
        if ( this._isRendered ) return false;		

		if ( this._params.lazyloading && ! this._isInViewport ) {
            this._initIntersectionObserver();
            return false;
        }

		if ( this.cookieConsent ) {
			this._addCookieConsent();           
			return false;
        }

		this._isRendered = true;

		this._addPlayer();  
    }

	_addCookieConsent() {
        if ( this._isCookieConsentLoaded ) return false; 
        this._isCookieConsentLoaded = true;

        this._cookieConsentEl = this.querySelector( '.aiovg-privacy-wrapper' );
		this._addPosterImage(); 

        this._cookieConsentEl.querySelector( '.aiovg-privacy-consent-button' ).addEventListener( 'click', () => this._onCookieConsent() );
    }

	_onCookieConsent() {
		this._isRendered = true;

		this._cookieConsentEl.remove();  
		this.cookieConsent = false;   
		
		this._params.player.autoplay = true;
		this._addPlayer();  

        this._setCookie();
    }

	_addPosterImage() {
        if ( this._isPosterImageLoaded ) return false; 
		this._isPosterImageLoaded = true;

		const poster = this._cookieConsentEl.getAttribute( 'data-poster' ) || '';
		if ( AIOVGVideoElement.isValidUrl( poster ) ) {
			this._cookieConsentEl.style.backgroundImage = `url("${poster}")`; 
		}  
    }

	_addPlayer() {
		if ( this._isPlayerLoaded ) return false; 
		this._isPlayerLoaded = true;

		const video = document.getElementById( this._playerId );
		this._player = new Plyr( video, this._params.player );

		this._playerEl = this.querySelector( '.plyr' );
		
		// Dispatch an event
		const options = {	
			player: this._player,
			settings: this._params					
		};

		this.dispatchEvent(new CustomEvent('player.init', {
            detail: options,
            bubbles: true,
            cancelable: true,
        }));

		// On ready
		this._player.on( 'ready', () => {
			// Share / Embed				
			if ( this._params.hasOwnProperty( 'share' ) || this._params.hasOwnProperty( 'embed' ) ) {					
				this._initShareEmbed();
			}

			// Logo
			if ( this._params.hasOwnProperty( 'logo' ) ) {
				this._initLogo();
			}
		});

		// Update views count
		let viewed = false;

		this._player.on( 'playing', () => {
			if ( viewed ) return false;
			viewed = true;

			this._updateViewsCount();
		});

		// On ended
		this._player.on( 'ended', () => {
			this._playerEl.className += ' plyr--stopped';
		});

		// HLS
		if ( this._params.hasOwnProperty( 'hls' ) ) {
			const hls = new Hls();
			hls.loadSource( this._params.hls );
			hls.attachMedia( video );
			window.hls = hls;
			
			// Handle changing captions
			this._player.on( 'languagechange', () => {
				setTimeout( () => hls.subtitleTrack = this._player.currentTrack, 50 );
			});
		}

		// Dash
		if ( this._params.hasOwnProperty( 'dash' ) ) {
			const dash = dashjs.MediaPlayer().create();
			dash.initialize( video, this._params.dash, true );
			window.dash = dash;
		}

		// Init Ads
		if ( this._params.player.hasOwnProperty( 'ads' ) ) {
			this._initAds();					
		}

		// Custom ContextMenu
		if ( this._params.hasOwnProperty( 'contextmenu' ) ) {
			this._initContextMenu();					
		}
	}

	_initAds() {
		this._player.ads.config.tagUrl = this._getVastUrl();
		
		let loaded = false;

		this._player.ads.on( 'loaded', () => {			
			if ( loaded ) return false;
			loaded = true;                        

			const adsManager = this._player.ads.manager;

			let playButton = document.createElement( 'button' );
			playButton.type = 'button';
			playButton.className = 'plyr__control plyr__control--overlaid';
			playButton.style.display = 'none';
			playButton.innerHTML = '<svg aria-hidden="true" focusable="false"><use xlink:href="#plyr-play"></use></svg><span class="plyr__sr-only">Play</span>';
			
			this.querySelector( '.plyr__ads' ).appendChild( playButton );                        

			playButton.addEventListener( 'click', () => {
				playButton.style.display = 'none';
				adsManager.resume();
			});

			adsManager.addEventListener( google.ima.AdEvent.Type.STARTED, ( event ) => {
				if ( this._params.player.ads.companion ) {
					this._initCompanionAds( event );
				}								
			});

			adsManager.addEventListener( google.ima.AdEvent.Type.PAUSED, ( event ) => {
				playButton.style.display = '';
			});
		});
	}

	_getVastUrl() {
		let url = this._params.player.ads.tagUrl;

		url = url.replace( '[domain]', encodeURIComponent( this._params.site_url ) );
		url = url.replace( '[player_width]', this._player.elements.container.offsetWidth );
		url = url.replace( '[player_height]', this._player.elements.container.offsetHeight );
		url = url.replace( '[random_number]', Date.now() );
		url = url.replace( '[timestamp]', Date.now() );
		url = url.replace( '[page_url]', encodeURIComponent( window.location ) );
		url = url.replace( '[referrer]', encodeURIComponent( document.referrer ) );
		url = url.replace( '[ip_address]', this._params.ip_address );
		url = url.replace( '[post_id]', this._params.post_id );
		url = url.replace( '[post_title]', encodeURIComponent( this._params.post_title ) );
		url = url.replace( '[post_excerpt]', encodeURIComponent( this._params.post_excerpt ) );
		url = url.replace( '[video_file]', encodeURIComponent( this._player.source ) );
		url = url.replace( '[video_duration]', this._player.duration || '' );
		url = url.replace( '[autoplay]', this._params.player.autoplay );

		return url;
	}

	_initCompanionAds( event ) {
		const ad = event.getAd();					
		let elements = [];

		try {
			elements = window.AIOVGGetCompanionElements();
		} catch ( error ) { 
			/** console.log( error ); */
		}
		
		if ( elements.length ) {		
			let criteria = new google.ima.CompanionAdSelectionSettings();
			criteria.resourceType = google.ima.CompanionAdSelectionSettings.ResourceType.ALL;
			criteria.creativeType = google.ima.CompanionAdSelectionSettings.CreativeType.ALL;
			criteria.sizeCriteria = google.ima.CompanionAdSelectionSettings.SizeCriteria.SELECT_NEAR_MATCH;        
			
			for ( let i = 0; i < elements.length; i++ ) {													
				let id     = elements[ i ].id;
				let width  = elements[ i ].width;
				let height = elements[ i ].height;
				
				try {
					// Get a list of companion ads for an ad slot size and CompanionAdSelectionSettings
					const companionAds = ad.getCompanionAds( width, height, criteria );
					let companionAd = companionAds[0];
				
					// Get HTML content from the companion ad.
					const content = companionAd.getContent();
			
					// Write the content to the companion ad slot.
					let div = document.getElementById( id );
					div.innerHTML = content;
				} catch ( adError ) { 
					/** console.log( error ); */
				}				
			}
		}
	}    

	_initShareEmbed() {
		let shareButton = document.createElement( 'button' );
		shareButton.type = 'button';
		shareButton.className = 'plyr__controls__item plyr__control plyr__share-embed-button aiovg-icon-share';
		shareButton.innerHTML = '<span class="plyr__sr-only">Share</span>';

		this._playerEl.appendChild( shareButton );	
			
		let closeButton = this.querySelector( '.plyr__share-embed-modal-close-button' );

		let modal = this.querySelector( '.plyr__share-embed-modal' );
		this._playerEl.appendChild( modal );	
		modal.style.display = '';

		// Show Modal
		let wasPlaying = false;

		shareButton.addEventListener( 'click', () => {
			if ( this._player.playing ) {
				wasPlaying = true;
				this._player.pause();
			} else {
				wasPlaying = false;
			}                    

			shareButton.style.display = 'none';						
			modal.className += ' fadein';				
		});

		// Hide Modal
		closeButton.addEventListener( 'click', () => {
			if ( wasPlaying ) {
				this._player.play();
			}
			
			modal.className = modal.className.replace( ' fadein', '' );
			setTimeout(function() {
				shareButton.style.display = ''; 
			}, 500 );					                           	
		});

		// Copy Embedcode
		if ( this._params.hasOwnProperty( 'embed' ) ) {
			this.querySelector( '.plyr__embed-code-input' ).addEventListener( 'focus', function() {
				this.select();	
				document.execCommand( 'copy' );					
			});
		}
	}

	_initLogo() {
		let style = 'bottom:50px; left:' +  this._params.logo.margin +'px;';

		switch ( this._params.logo.position ) {
			case 'topleft':
				style = 'top:' +  this._params.logo.margin +'px; left:' +  this._params.logo.margin +'px;';
				break;
			case 'topright':
				style = 'top:' + this._params.logo.margin +'px; right:' + this._params.logo.margin +'px;';
				break;					
			case 'bottomright':
				style = 'bottom:50px; right:' +  this._params.logo.margin +'px;';
				break;		
		}

		let logo = document.createElement( 'div' );
		logo.className = 'plyr__logo';
		logo.innerHTML = '<a href="' + this._params.logo.link + '" style="' + style + '" target="_blank"><img src="' + this._params.logo.image + '" alt="" /><span class="plyr__sr-only">Logo</span></a>';

		this._playerEl.appendChild( logo );	
	}

	_initContextMenu() {
		if ( ! window.AIOVGIsContextMenuAdded ) {
			window.AIOVGIsContextMenuAdded = true;

			let contextmenu = document.createElement( 'div' );
			contextmenu.id = 'aiovg-contextmenu';
			contextmenu.style.display = 'none';
			contextmenu.innerHTML = '<div class="aiovg-contextmenu-content">' + this._params.contextmenu.content + '</div>'; 

			document.body.appendChild( contextmenu );
		}

		const contextmenuEl = document.getElementById( 'aiovg-contextmenu' );
		let timeoutHandler = '';
		
		this._playerEl.addEventListener( 'contextmenu', function( e ) {						
			if ( e.keyCode == 3 || e.which == 3 ) {
				e.preventDefault();
				e.stopPropagation();
				
				const width = contextmenuEl.offsetWidth,
					height = contextmenuEl.offsetHeight,
					x = e.pageX,
					y = e.pageY,
					doc = document.documentElement,
					scrollLeft = ( window.pageXOffset || doc.scrollLeft ) - ( doc.clientLeft || 0 ),
					scrollTop = ( window.pageYOffset || doc.scrollTop ) - ( doc.clientTop || 0 ),
					left = x + width > window.innerWidth + scrollLeft ? x - width : x,
					top = y + height > window.innerHeight + scrollTop ? y - height : y;
		
				contextmenuEl.style.display = '';
				contextmenuEl.style.left = left + 'px';
				contextmenuEl.style.top = top + 'px';
				
				clearTimeout( timeoutHandler );
				timeoutHandler = setTimeout(function() {
					contextmenuEl.style.display = 'none';
				}, 1500 );				
			}														 
		});
		
		if ( this._params.hasOwnProperty( 'logo' ) ) {
			contextmenuEl.addEventListener( 'click', () => {
				window.location.href = this._params.logo.link;
			});
		}
		
		document.addEventListener( 'click', () => {
			contextmenuEl.style.display = 'none';								 
		});
	}
    
    _initIntersectionObserver() {
		if ( this._intersectionObserver ) return false;

        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0
        };

        this._intersectionObserver = new IntersectionObserver(( entries, observer ) => {
            entries.forEach(entry => {
                if ( entry.isIntersecting ) {
					this._isInViewport = true;
                    this._render();
                    
					if ( this._isRendered ) observer.unobserve( this );
                } else {
                    this._isInViewport = false;
                }
            });
        }, options);

		this._intersectionObserver.observe( this );
    } 
 
    /**
     * Define private async methods.
     */

    async _updateViewsCount() {
        if ( this._params.post_type == 'aiovg_videos' && AIOVGVideoElement.isValidUrl( this._params.ajax_url ) ) {
            let formData = new FormData();
            formData.append( 'action', 'aiovg_update_views_count' );
            formData.append( 'post_id', parseInt( this._params.post_id ) );
            formData.append( 'security', this._params.ajax_nonce );

            fetch( this._params.ajax_url, { method: 'POST', body: formData } );
        }
    }
    
    async _setCookie() {
        try {
            if ( AIOVGVideoElement.isValidUrl( this._params.ajax_url ) ) {
                let formData = new FormData();
                formData.append( 'action', 'aiovg_set_cookie' );
                formData.append( 'security', this._params.ajax_nonce );

                fetch( this._params.ajax_url, { method: 'POST', body: formData } );
            }

            const nodeList = document.querySelectorAll( '.aiovg-player-element[cookieconsent]' );
            for ( let i = 0; i < nodeList.length; i++ ) {
                nodeList[ i ].removeAttribute( 'cookieconsent' );
            }
        } catch ( error ) {
			/** console.log( error ); */
        }
    }

    /**
     * Define public static methods.
     */

    static isValidUrl( url ) {
        if ( url == '' ) return false;

        try {
            new URL( url );
            return true;
        } catch ( error ) {
            return false;
        }
    }

}

window.AIOVGIsContextMenuAdded = false;

// Register custom element.
document.addEventListener( 'DOMContentLoaded', function() {
	customElements.define( 'aiovg-video', AIOVGVideoElement );
});
