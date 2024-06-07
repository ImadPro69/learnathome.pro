import { __experimentalColorGradientControl as ColorGradientControl } from '@wordpress/block-editor';

export default ( { name, label, help, attributes, setAttributes } ) => {
	// Bail if field has no name or no choice
	if ( ! name )
		return null;

	return (
		<ColorGradientControl label={ label || '' }
							  help={ help || '' }
							  gradientValue={ attributes[ name ] || '' }
							  onGradientChange={ ( value ) => setAttributes( { [ name ] : value } ) } />
	);
};
