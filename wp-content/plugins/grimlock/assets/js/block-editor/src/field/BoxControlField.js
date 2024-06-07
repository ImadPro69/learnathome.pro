import { __experimentalBoxControl as BoxControl } from '@wordpress/components';

export default ( { name, label, sides, min, max, default: defaultVal, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	return (
		<BoxControl label={ label || '' }
					sides={ sides }
					resetValues={ defaultVal || {} }
					inputProps={ { min: min || -Infinity, max: max || Infinity } }
					values={ attributes[ name ] }
					onChange={ ( value ) => setAttributes( { [ name ]: value } ) } />
	);
};
