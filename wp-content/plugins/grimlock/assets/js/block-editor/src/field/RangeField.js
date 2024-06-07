import { RangeControl } from '@wordpress/components';

export default ( { name, label, help, min, max, step, attributes, setAttributes } ) => {
	// Bail if field has no name or no choice
	if ( ! name )
		return null;

	return (
		<RangeControl label={ label || '' }
					  help={ help || '' }
					  value={ attributes[ name ] || 0 }
					  onChange={ ( value ) => setAttributes( { [ name ] : value } ) }
					  min={ min }
					  max={ max }
					  step={ step } />
	);
};
