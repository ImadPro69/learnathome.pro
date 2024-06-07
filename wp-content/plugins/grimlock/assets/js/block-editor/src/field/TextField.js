import { TextControl } from '@wordpress/components';

export default ( { name, label, help, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	return (
		<TextControl label={ label || '' }
					 help={ help || '' }
					 value={ attributes[ name ] || '' }
					 onChange={ ( value ) => setAttributes( { [ name ] : value } ) } />
	);
};
