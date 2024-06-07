import AlignmentMatrixControl from '../component/AlignmentMatrixControl';

export default ( { name, label, help, attributes, setAttributes } ) => {
	// Bail if field has no name or no choice
	if ( ! name )
		return null;

	return (
		<AlignmentMatrixControl label={ label || '' }
								help={ help || '' }
								value={ attributes[ name ] || '' }
								onChange={ ( value ) => setAttributes( { [ name ] : value } ) } />
	);
};
