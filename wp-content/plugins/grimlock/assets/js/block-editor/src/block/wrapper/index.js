import attributes from './attributes';
import variations from './variations';
import getBlockProps from './props';
import Edit from './edit';
import Save from './save';
import { ReactComponent as BlockIcon } from '../../../../../images/wrapper-block-icon.svg';
import * as darkMode from '../../../../../../../grimlock-dark-mode/assets/js/block-editor/src/block/wrapper/dark-mode';

export default {
	attributes,
	variations,
	icon: BlockIcon,
	edit: ( props ) => <Edit { ...{ blockProps: getBlockProps( props.attributes ), ...props } } />,
	save: ( props ) => <Save { ...{ blockProps: getBlockProps( props.attributes, true ), ...props } } />,
	deprecated: [ darkMode.deprecator ],
};
