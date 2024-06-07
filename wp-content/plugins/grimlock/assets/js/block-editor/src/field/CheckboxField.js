import { CheckboxControl } from '@wordpress/components';

export default ( { name, label, help, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	return (
		<CheckboxControl label={ label || '' }
						 help={ help || '' }
						 checked={ !! attributes[ name ] }
						 onChange={ ( value ) => setAttributes( { [ name ] : value } ) } />
	);
};
