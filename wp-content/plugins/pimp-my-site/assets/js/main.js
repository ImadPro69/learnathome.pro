jQuery( function( $ ) {
	if ( window.pimpMySite ) {

		let particlesOptions = {
			"autoPlay": true,
			"fullScreen": {
				"enable": true,
				"zIndex": 9999
			},
			"detectRetina": true,
			"duration": 0,
			"fpsLimit": 120,
			"motion": {
				"disable": false,
				"reduce": {
					"factor": 4,
					"value": true
				}
			},
			"particles": {
				"color": {
					"value": pimpMySite.colors,
				},
				"destroy": {
					"mode": "none",
				},
				"life": {
					"duration": {
						"value": {
							"min": parseFloat( pimpMySite.lifetime.min ),
							"max": parseFloat( pimpMySite.lifetime.max ),
						},
					},
				},
				"move": {
					"angle": {
						"offset": 0,
						"value": 90
					},
					"decay": 0,
					"distance": {},
					"direction": "bottom",
					"drift": 0,
					"enable": true,
					"outModes": {
						"default": "out",
						"bottom": "out",
						"left": "out",
						"right": "out",
						"top": "out"
					},
					"random": false,
					"size": false,
					"speed": {
						"min": parseFloat( pimpMySite.speed.min ),
						"max": parseFloat( pimpMySite.speed.max )
					},
					"straight": false,
					"trail": {
						"enable": false,
						"length": 10,
						"fillColor": {
							"value": "#000000"
						}
					},
				},
				"number": {
					"density": {
						"enable": true,
						"area": 2000,
						"factor": 1000
					},
					"limit": 1000,
					"value": parseFloat( pimpMySite.density )
				},
				"opacity": {
					"random": {
						"enable": true,
						"minimumValue": parseFloat( pimpMySite.opacity.min ) / 100
					},
					"value": {
						"min": parseFloat( pimpMySite.opacity.min ) / 100,
						"max": parseFloat( pimpMySite.opacity.max ) / 100,
					},
					"animation": {
						"count": 0,
						"enable": false,
						"speed": 1,
						"decay": 0,
						"sync": false,
						"destroy": "none",
						"startValue": "random",
						"minimumValue": 0.1
					}
				},
				"rotate": {
					"random": {
						"enable": false,
						"minimumValue": 0
					},
					"value": 0,
					"animation": {
						"enable": false,
						"decay": 0,
						"sync": false
					},
					"direction": "random",
					"path": false
				},
				"shape": {
					"options": {},
					"type": "image",
				},
				"size": {
					"random": {
						"enable": true,
						"minimumValue": parseFloat( pimpMySite.size.min )
					},
					"value": {
						"min": parseFloat( pimpMySite.size.min ),
						"max": parseFloat( pimpMySite.size.max ),
					},
					"animation": {
						"count": 0,
						"enable": false,
						"speed": 40,
						"decay": 0,
						"sync": false,
						"destroy": "none",
						"startValue": "random",
						"minimumValue": 0.1
					}
				},
				"zIndex": {
					"random": {
						"enable": false,
						"minimumValue": 0
					},
					"value": 0,
					"opacityRate": 1,
					"sizeRate": 1,
					"velocityRate": 1
				}
			},
			"pauseOnBlur": true,
			"pauseOnOutsideViewport": true,
			"responsive": [],
			"style": {},
			"themes": [],
			"zLayers": 100
		};

		// Calculate particles direction and angle range
		particlesOptions.particles.move.direction = pimpMySite.direction.max - ( ( pimpMySite.direction.max - pimpMySite.direction.min ) / 2 );
		particlesOptions.particles.move.angle.value = ( pimpMySite.direction.max - pimpMySite.direction.min );

		// Populate particles SVG images for each color
		let particlesImages = [];
		pimpMySite.colors.forEach( ( color, index ) => {
			particlesImages = particlesImages.concat(
				pimpMySite.shapes.map( ( value ) => {
					return {
						src: pimpMySite.pluginUrl + "assets/images/particles/" + value + ".svg#" + index + ".svg",
						width: 32,
						height: 32,
						replaceColor: true,
						particles: {
							color: {
								value: color
							}
						}
					};
				} )
			);
		} );

		particlesOptions.particles.shape.options = { images: particlesImages };

		// Init particles
		if ( pimpMySite.enabled ) {
			$( 'body' )
				.particles()
				.init( particlesOptions );
		}
	}
} );