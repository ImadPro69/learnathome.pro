import { __experimentalBorderBoxControl as BorderBoxControl } from '@wordpress/components';
import { useSelect } from "@wordpress/data";

export default ( { name, label, default: defaultVal, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	// Get color palette
	const colorPickerSettings = useSelect( ( select ) => {
		const settings = select( 'core/block-editor' ).getSettings();
		return { colors: settings.colors, gradients: settings.gradients };
	} );

	return (
		<BorderBoxControl label={ label || '' }
						  colors={ [ { colors: colorPickerSettings.colors, name: 'Theme' } ] }
						  enableAlpha
						  enableStyle
						  __experimentalIsRenderedInSidebar
						  __experimentalHasMultipleOrigins
						  __next36pxDefaultSize={ false }
						  value={ attributes[ name ] }
						  onChange={ ( value ) => setAttributes( { [ name ]: value } ) } />
	);
};
