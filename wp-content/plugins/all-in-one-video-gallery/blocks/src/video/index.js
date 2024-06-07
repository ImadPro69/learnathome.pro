/**
 * Import block dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';

/**
 * Register the block.
 */
registerBlockType( metadata.name, {
	attributes: {
		src: {
			type: 'string'
		},
		id: {
			type: 'number'
		},
		poster: {
			type: 'string'
		},
		width: {
			type: 'number',
			default: aiovg_blocks.video.width
		},
		ratio: {
			type: 'number',
			default: aiovg_blocks.video.ratio
		},
		autoplay: {
			type: 'boolean',
			default: aiovg_blocks.video.autoplay
		},
		loop: {
			type: 'boolean',
			default: aiovg_blocks.video.loop
		},
		muted: {
			type: 'boolean',
			default: aiovg_blocks.video.muted
		},
		playpause: {
			type: 'boolean',
			default: aiovg_blocks.video.playpause
		},
		current: {
			type: 'boolean',
			default: aiovg_blocks.video.current
		},
		progress: {
			type: 'boolean',
			default: aiovg_blocks.video.progress
		},
		duration: {
			type: 'boolean',
			default: aiovg_blocks.video.duration
		},
		speed: {
			type: 'boolean',
			default: aiovg_blocks.video.speed
		},
		quality: {
			type: 'boolean',
			default: aiovg_blocks.video.quality
		},			
		volume: {
			type: 'boolean',
			default: aiovg_blocks.video.volume
		},
		fullscreen: {
			type: 'boolean',
			default: aiovg_blocks.video.fullscreen
		},
		share: {
			type: 'boolean',
			default: aiovg_blocks.video.share
		},
		embed: {
			type: 'boolean',
			default: aiovg_blocks.video.embed
		},
		download: {
			type: 'boolean',
			default: aiovg_blocks.video.download
		}
	},

	edit: Edit
} );
