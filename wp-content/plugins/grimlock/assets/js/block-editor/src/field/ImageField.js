import ImageSelectorControl from '../component/ImageSelectorControl';

export default ( { name, label, help, return_as_id, enablePosition, enableSize, gallery, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	return (
		<ImageSelectorControl label={ label || '' }
							  help={ help || '' }
							  value={ attributes[ name ] || 0 }
							  enablePosition={ enablePosition }
							  enableSize={ enableSize }
							  returnAsId={ return_as_id }
							  onChange={ ( value ) => setAttributes( { [ name ]: value } ) }
							  gallery={ !! gallery } />
	);
};
