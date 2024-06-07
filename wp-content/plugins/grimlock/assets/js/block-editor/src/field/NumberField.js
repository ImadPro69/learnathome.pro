import { TextControl } from '@wordpress/components';

export default ( { name, label, help, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	return (
		<TextControl label={ label || '' }
					 help={ help || '' }
					 value={ attributes[ name ] || 0 }
					 type="number"
					 onChange={ ( value ) => setAttributes( { [ name ]: `${value}` /* Hack to force value to be a string */ } ) } />
	);
};
