import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { SelectControl, BaseControl, Button, ResponsiveWrapper, Spinner, FocalPointPicker } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';

const ALLOWED_MEDIA_TYPES = [ 'image' ];
const UNAUTHORIZED = <p>{ __( 'To edit the image, you need permission to upload media.', 'grimlock' ) }</p>;

class ImageSelectorControl extends Component {

	handleSelectImage( image ) {
		const { value, onChange, returnAsId } = this.props;
		let imageData;

		if ( image.length !== undefined ) { // Gallery
			const images = image.map( ( item ) => {
				let { id, url, sizes } = item;

				if ( returnAsId )
					return id;

				return {
					id,
					url: sizes[ value.size ] ? sizes[ value.size ].url : url,
				}
			} );

			if ( returnAsId )
				imageData = images;
			else
				imageData = { size: value.size, images };
		}
		else { // Single image
			let { id, url, sizes } = image;

			if ( returnAsId )
				imageData = id;
			else {
				imageData = {
					...value,
					id,
					url: sizes[ value.size ] ? sizes[ value.size ].url : url,
				};
			}
		}

		onChange( imageData );
	}

	handleChangeSize( size ) {
		let { image, onChange, value } = this.props;

		if ( image.length !== undefined ) { // Gallery
			const images = image.map( ( imageItem ) => {
				let { id, source_url, media_details } = imageItem;

				return {
					id,
					url: media_details.sizes[ size ] ? media_details.sizes[ size ].source_url : source_url,
				};
			} );

			onChange( {
				size,
				images,
			} );
		}
		else { // Single image
			let { source_url, media_details } = image;

			onChange( {
				...value,
				size,
				url: media_details.sizes[ size ] ? media_details.sizes[ size ].source_url : source_url,
			} );
		}
	}

	handleChangePosition( position ) {
		let { onChange, value } = this.props;

		onChange( {
			...value,
			position,
		} );
	}

    render() {
        let { label, help, value, onChange, gallery, image, imageSizes, enableSize, enablePosition } = this.props;

        const hasValue = !!gallery ? !!value.images : !!value.id;

        return (
            <BaseControl label={ label } help={ help } className="grimlock-image-selector-control">

                <MediaUploadCheck fallback={ UNAUTHORIZED }>
                    <MediaUpload allowedTypes={ ALLOWED_MEDIA_TYPES }
                                 multiple={ !! gallery }
                                 gallery={ !! gallery }
                                 value={ hasValue && ( !!gallery ? value.images.map( ( image ) => image.id ) : value.id ) }
								 onSelect={ ( image ) => this.handleSelectImage( image ) }
                                 render={ ( { open } ) => (
                                     <>
										 { ! hasValue ? (
											 <Button className="editor-post-featured-image__toggle"
													 onClick={ open }>
												 { __( 'No image selected', 'grimlock' ) }
											 </Button>
										 ) : (
											 <div className="editor-post-featured-image__preview">
												 { hasValue && ! image && <Spinner /> }

												 { hasValue && image && ( image.length ?
														 <div style={ { textAlign: 'left' } }>
															 { image.map( ( item ) => !! item && (
																 <div style={ { display: 'inline-block', width: '33%', padding: '2px' } } key={ item.id }>
																	 <ResponsiveWrapper naturalWidth={ item.media_details.sizes[ value.size ].width }
																						naturalHeight={ item.media_details.sizes[ value.size ].height }>
																		 <img src={ item.media_details.sizes[ value.size ].source_url } alt={ label } />
																	 </ResponsiveWrapper>
																 </div>
															 ) ) }
														 </div> : ( enablePosition && ! gallery ?
															 <div>
																 <FocalPointPicker url={ image.media_details.sizes[ value.size ] ? image.media_details.sizes[ value.size ].source_url : image.source_url }
																				   value={ value.position }
																				   onDrag={ ( value ) => this.handleChangePosition( value ) }
																				   onChange={ ( value ) => this.handleChangePosition( value ) } />
															 </div> :
															 <ResponsiveWrapper naturalWidth={ image.media_details.sizes[ value.size ] ? image.media_details.sizes[ value.size ].width : image.media_details.width }
																				naturalHeight={ image.media_details.sizes[ value.size ] ? image.media_details.sizes[ value.size ].height : image.media_details.height }>
																 <div style={ { textAlign: 'center' } }>
																	 <img src={ image.media_details.sizes[ value.size ] ? image.media_details.sizes[ value.size ].source_url : image.source_url } alt={ label } />
																 </div>
															 </ResponsiveWrapper> )
												 ) }

												 { enableSize && !! imageSizes && imageSizes.length && image &&
												 <>
													 <SelectControl label={ __( 'Size', 'grimlock' ) }
																	options={ imageSizes.map( ( imageSize ) => ( { label: imageSize.name, value: imageSize.slug } ) ) }
																	value={ value.size || 'full' }
																	onChange={ ( size ) => this.handleChangeSize( size ) } />
												 </> }
											 </div>
										 ) }

                                         <Button style={ { margin: '10px 10px 0 0' } } onClick={ open } isPrimary>
                                             { ! gallery ?
                                                 ( ! hasValue ? __( 'Select Image', 'grimlock' ) : __( 'Change Image', 'grimlock' ) ) :
                                                 ( ! hasValue ? __( 'Select Images', 'grimlock' ) : __( 'Change Images', 'grimlock' ) ) }
                                         </Button>

                                         { hasValue &&
                                         <Button onClick={ () => onChange( 0 ) } isLink isDestructive>
                                             { ! gallery ? __( 'Remove image', 'grimlock' ) : __( 'Remove all images', 'grimlock' ) }
                                         </Button> }
									 </>
                                 ) } />
                </MediaUploadCheck>

            </BaseControl>
        );
    }
}

export default compose(
    withSelect( ( select, props ) => {
        const { getMedia } = select( 'core' );
		const settings = select( 'core/block-editor' ).getSettings();
        let { value, returnAsId, gallery, enableSize } = props;

        // If field is configured to return only id,
		// we need to transform the value into the format expected by the ImageSelectorControl first
		if ( returnAsId && ! gallery )
			value = { id: value };
		else if ( returnAsId && gallery )
			value = { images: !! value && value.map( id => ( { id } ) ) };

		// If size option is disabled, use full size (for image preview)
		if ( !! value && ! enableSize )
			value.size = 'full';

        let image = null;

        // Load gallery images
        if ( value?.images?.length ) {
            image = value.images.map( ( item ) => getMedia( item.id ) );

            // If some images are falsy (meaning not finished loading) we force the value to null
            if ( image.some( ( item ) => ! item ) )
                image = null;
        }
        // Load single image
        else if ( value )
            image = getMedia( value.id );

        return { value, image, imageSizes: settings.imageSizes };
    } ),
)( ImageSelectorControl );
