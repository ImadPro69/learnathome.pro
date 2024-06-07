import { __experimentalBorderRadiusControl as BorderRadiusControl } from '@wordpress/block-editor';

export default ( { name, label, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	return (
		<BorderRadiusControl label={ label || '' }
							 values={ attributes[ name ] }
							 onChange={ ( value ) => setAttributes( { [ name ]: typeof value === 'string' ? { topLeft: value, topRight: value, bottomRight: value, bottomLeft: value } : value } ) } />
	);
};
