import { __experimentalBorderStyleControl as BorderStyleControl } from '@wordpress/block-editor';

export default ( { name, label, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	return (
		<BorderStyleControl label={ label || '' }
							values={ attributes[ name ] }
							onChange={ ( value ) => setAttributes( { [ name ]: value } ) } />
	);
};
