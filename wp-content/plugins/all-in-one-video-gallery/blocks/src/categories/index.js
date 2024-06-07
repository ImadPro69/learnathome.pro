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
		template: {
			type: 'string',
			default: aiovg_blocks.categories.template
		},
		id: {
			type: 'number',
			default: aiovg_blocks.categories.id
		},		
		columns: {
			type: 'number',
			default: aiovg_blocks.categories.columns
		},
		limit: {
			type: 'limit',
			default: aiovg_blocks.categories.limit
		},
		orderby: {
			type: 'string',
			default: aiovg_blocks.categories.orderby
		},
		order: {
			type: 'string',
			default: aiovg_blocks.categories.order
		},
		hierarchical: {
			type: 'boolean',
			default: aiovg_blocks.categories.hierarchical
		},
		show_description: {
			type: 'boolean',
			default: aiovg_blocks.categories.show_description
		},
		show_count: {
			type: 'boolean',
			default: aiovg_blocks.categories.show_count
		},
		hide_empty: {
			type: 'boolean',
			default: aiovg_blocks.categories.hide_empty
		},
		show_pagination: {
			type: 'boolean',
			default: aiovg_blocks.categories.show_pagination
		}
	},

	edit: Edit
} );
