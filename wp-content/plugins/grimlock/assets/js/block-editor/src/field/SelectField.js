import SelectControlWithOptGroup from '../component/SelectControlWithOptGroup';
import { SelectControl } from '@wordpress/components';
import he from 'he';

export default ( { name, label, help, choices, multiple, attributes, setAttributes } ) => {
	// Bail if field has no name or no choice
	if ( ! name || ! choices )
		return null;

	let hasSubOptions = false;
	const options = Object.keys( choices ).map( ( option ) => {
		if ( choices[ option ]['subchoices'] ) {
			hasSubOptions = true;

			const subOptions = Object.keys( choices[ option ]['subchoices'] ).map( ( subOption ) => {
				return { value: subOption, label: choices[ option ]['subchoices'][ subOption ] };
			} );

			return { label: he.decode( choices[ option ]['label'] ), options: subOptions };
		}

		return { value: option, label: he.decode( choices[ option ] ) };
	} );

	return hasSubOptions || multiple ? (
		<SelectControlWithOptGroup label={ label || '' }
								   help={ help || '' }
								   value={ attributes[ name ] || '' }
								   onChange={ ( value ) => setAttributes( { [ name ] : value } ) }
								   options={ options }
								   multiple={ multiple } />
	) : (
		<SelectControl label={ label || '' }
					   help={ help || '' }
					   value={ attributes[ name ] || '' }
					   onChange={ ( value ) => setAttributes( { [ name ] : value } ) }
					   options={ options } />
	);
};
