import { RadioControl } from '@wordpress/components';

export default ( { name, label, help, choices, attributes, setAttributes } ) => {
	// Bail if field has no name or no choice
	if ( ! name || ! choices )
		return null;

	const options = Object.keys( choices ).map( ( option ) => {
		return { value: option, label: <img src={ choices[ option ] } alt={ option } /> };
	} );

	return (
		<RadioControl label={ label || '' }
					  help={ help || '' }
					  selected={ attributes[ name ] || '' }
					  onChange={ ( value ) => setAttributes( { [ name ] : value } ) }
					  options={ options }
					  className={ `grimlock-radio-image grimlock-block-field-${name}` } />
	);
};
