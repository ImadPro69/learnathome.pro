import { applyFilters } from '@wordpress/hooks';

export default applyFilters( 'grimlock.blocks.wrapper.inspectorControls', {
	background: {
		label: 'Background',
		fields: [
			{
				type: 'image',
				name: 'backgroundImage',
				enableSize: true,
				enablePosition: true,
				label: 'Background Image',
				default: { size: 'full' },
				attributeType: 'object',
			},
			{
				type: 'colorSettings',
				name: 'backgroundColor',
				settings: [ {
					label: 'Background Color',
					name: 'background',
					enableGradient: true,
				}, {
					label: 'Overlay Color',
					name: 'overlay',
					enableGradient: true,
				} ],
				default: { background: '', overlay: '' },
				attributeType: 'object',
			},
		],
	},
	text: {
		label: 'Text',
		fields: [
			{
				type: 'colorSettings',
				name: 'textColor',
				settings: [ {
					label: 'Headings Color',
					name: 'headings',
					enableGradient: true,
				}, {
					label: 'Text Color',
					name: 'text',
					enableGradient: true,
				} ],
				default: { text: '', headings: '' },
				attributeType: 'object',
			},
		],
	},
	spacing: {
		label: 'Spacing',
		fields: [
			{
				type: 'boxControl',
				name: 'margin',
				label: 'Margin',
				sides: [ 'top', 'bottom' ],
				default: { top: '0', bottom: '0' },
				attributeType: 'object',
			},
			{
				type: 'boxControl',
				name: 'padding',
				label: 'Padding',
				sides: [ 'top', 'right', 'bottom', 'left' ],
				min: 0,
				default: { top: '0', right: '0', bottom: '0', left: '0' },
				attributeType: 'object',
			},
		]
	},
	borders: {
		label: 'Borders',
		fields: [
			{
				type: 'borderBoxControl',
				name: 'borders',
				label: 'Borders',
				default: {
					top: { color: '#00000000', width: '0', style: 'solid' },
					bottom: { color: '#00000000', width: '0', style: 'solid' },
					left: { color: '#00000000', width: '0', style: 'solid' },
					right: { color: '#00000000', width: '0', style: 'solid' },
				},
				attributeType: 'object',
			},
			{
				type: 'borderRadius',
				name: 'borderRadius',
				label: 'Radius',
				default: { topLeft: 0, topRight: 0, bottomRight: 0, bottomLeft: 0 },
				attributeType: 'object',
			},
		],
	},
} );
