import inspectorControls from './inspector-controls';
import { getInspectorControlsAttributes } from '../utils';

export default {
	...getInspectorControlsAttributes( inspectorControls ),
	align: {
		type: 'string',
	},
	zIndex: {
		type: 'string',
		default: '0',
	},
	overflow: {
		type: 'string',
		default: 'hidden',
	},
	spread: {
		type: 'string',
		default: '',
	}
};