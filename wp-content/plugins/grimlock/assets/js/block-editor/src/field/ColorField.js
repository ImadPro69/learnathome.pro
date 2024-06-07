import ColorPickerControl from '../component/ColorPickerControl';

export default ( { name, label, help, attributes, setAttributes } ) => {
	// Bail if field has no name or no choice
	if ( ! name )
		return null;

	return (
		<ColorPickerControl label={ label || '' }
							help={ help || '' }
							value={ attributes[ name ] || '#ffffff' }
							onChange={ ( value ) => setAttributes( { [ name ] : value } ) } />
	);
};
