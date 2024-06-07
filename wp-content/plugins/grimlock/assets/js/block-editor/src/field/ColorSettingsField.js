import { useSelect } from "@wordpress/data";
import ColorSettingsControl from '../component/ColorSettingsControl';

export default ( { name, label, settings, enableGradient, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	// Get color palette
	const colorPickerSettings = useSelect( ( select ) => {
		const settings = select( 'core/block-editor' ).getSettings();
		return { colors: settings.colors, gradients: settings.gradients };
	} );

	// Get field settings
	let colorSettings;
	if ( ! settings ) {
		const color = attributes[ name ];
		const isGradient = typeof color === 'string' && color.includes( 'gradient' );
		let isPendingChange = false; // Flag to prevent color and gradient from overriding each other

		colorSettings = [ {
			label: label,
			colorValue: ! isGradient ? color : undefined,
			onColorChange: ( newValue ) => {
				if ( newValue !== undefined )
					isPendingChange = true;
				else if ( isPendingChange )
					return;

				setAttributes( { [ name ]: newValue } );
			},
		} ];

		if ( enableGradient ) {
			colorSettings[0] = {
				...colorSettings[0],
				gradientValue: isGradient ? color : undefined,
				onGradientChange: ( newValue ) => {
					if ( newValue !== undefined )
						isPendingChange = true;
					else if ( isPendingChange )
						return;

					setAttributes( { [ name ]: newValue } );
				},
			};
		}
	}
	else {
		colorSettings = settings.map( ( setting ) => {
			const color = attributes[ name ][ setting.name ];
			const isGradient = typeof color === 'string' && color.includes( 'gradient' );
			let isPendingChange = false; // Flag to prevent color and gradient from overriding each other

			let colorSetting = {
				label: setting.label,
				colorValue: ! isGradient ? color : undefined,
				onColorChange: ( newValue ) => {
					if ( newValue !== undefined )
						isPendingChange = true;
					else if ( isPendingChange )
						return;

					setAttributes( {
						[ name ]: {
							...attributes[ name ],
							[ setting.name ]: newValue
						}
					} );
				},
			};

			if ( setting['enableGradient'] ) {
				colorSetting = {
					...colorSetting,
					gradientValue: isGradient ? color : undefined,
					onGradientChange: ( newValue ) => {
						if ( newValue !== undefined )
							isPendingChange = true;
						else if ( isPendingChange )
							return;

						setAttributes( {
							[ name ]: {
								...attributes[ name ],
								[ setting.name ]: newValue
							}
						} );
					},
				}
			}

			return colorSetting;
		} );
	}

	return (
		<ColorSettingsControl
			label={ !!settings && label }
			settings={ colorSettings }
			colors={ [ { colors: colorPickerSettings.colors, name: 'Theme' } ] }
			gradients={ [ { gradients: colorPickerSettings.gradients, name: 'Theme' } ] }
			enableAlpha
			__experimentalIsRenderedInSidebar
			__experimentalHasMultipleOrigins
		/>
	);
};
