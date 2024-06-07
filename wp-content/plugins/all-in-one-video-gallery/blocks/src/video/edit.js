/**
 * Import block dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';

import classnames from 'classnames';

import { 
	getBlobByURL, 
	isBlobURL 
} from '@wordpress/blob';

import { 
	BlockControls,
	BlockIcon,	
	InspectorControls,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,	
	store as blockEditorStore,
	useBlockProps
} from '@wordpress/block-editor';

import {
	BaseControl,
	Button,
	Disabled,
	PanelBody,
	PanelRow,	
	Spinner,
	TextControl,	
	ToggleControl
} from '@wordpress/components';

import { 
	useEffect,
	useRef
} from '@wordpress/element';

import { useInstanceId } from '@wordpress/compose';

import { 
	useDispatch, 
	useSelect 
} from '@wordpress/data';

import { video as icon } from '@wordpress/icons';

import { store as noticesStore } from '@wordpress/notices';

const ALLOWED_MEDIA_TYPES = [ 'video' ];
const VIDEO_POSTER_ALLOWED_MEDIA_TYPES = [ 'image' ];

/**
 * Describes the structure of the block in the context of the editor.
 * This represents what the editor will render when the block is used.
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, className, setAttributes } ) {

	const instanceId = useInstanceId( Edit );

	const videoPlayer = useRef();

	const posterImageButton = useRef();

	const {		
		src,
		id,
		poster,
		width,
		ratio,
		autoplay,
		loop,
		muted,
		playpause,
		current,
		progress,
		duration,		
		speed,
		quality,
		volume,
		fullscreen,
		share,
		embed,
		download
	} = attributes;	

	const isTemporaryVideo = ! id && isBlobURL( src );

	const mediaUpload = useSelect(
		( select ) => select( blockEditorStore ).getSettings().mediaUpload,
		[]
	);

	useEffect( () => {
		if ( isTemporaryVideo ) {
			const file = getBlobByURL( src );
			if ( file ) {
				mediaUpload( {
					filesList: [ file ],
					onFileChange: ( [ media ] ) => onSelectVideo( media ),
					onError: onUploadError,
					allowedTypes: ALLOWED_MEDIA_TYPES,
				} );
			}
		}
	}, [] );

	useEffect( () => {
		// Placeholder may be rendered.
		if ( videoPlayer.current ) {
			videoPlayer.current.load();
		}
	}, [ poster ] );

	function onSelectVideo( media ) {
		if ( ! media || ! media.url ) {
			// In this case there was an error
			// previous attributes should be removed
			// because they may be temporary blob urls.
			setAttributes( {
				src: undefined,
				id: undefined,
				poster: undefined
			} );
			return;
		}

		// Sets the block's attribute and updates the edit component from the
		// selected media.
		setAttributes( {
			src: media.url,
			id: media.id,
			poster:	media.image?.src !== media.icon ? media.image?.src : undefined
		} );
	}

	function onSelectURL( newSrc ) {
		if ( newSrc !== src ) {
			setAttributes( { 
				src: newSrc, 
				id: undefined, 
				poster: undefined 
			} );
		}
	}

	const { createErrorNotice } = useDispatch( noticesStore );
	function onUploadError( message ) {
		createErrorNotice( message, { type: 'snackbar' } );
	}

	const classes = classnames( className, {
		'is-transient': isTemporaryVideo,
	} );

	const blockProps = useBlockProps( {
		className: classes,
	} );	

	if ( ! src ) {
		return (
			<div { ...blockProps }>
				<MediaPlaceholder
					icon={ <BlockIcon icon={ icon } /> }
					onSelect={ onSelectVideo }
					onSelectURL={ onSelectURL }
					accept="video/*"
					allowedTypes={ ALLOWED_MEDIA_TYPES }
					value={ attributes }
					onError={ onUploadError }
				/>
			</div>
		);
	}

	function onSelectPoster( image ) {
		setAttributes( { poster: image.url } );
	}

	function onRemovePoster() {
		setAttributes( { poster: undefined } );

		// Move focus back to the Media Upload button.
		posterImageButton.current.focus();
	}

	const videoPosterDescription = `video-block__poster-image-description-${ instanceId }`;
	
	return (
		<>
			<BlockControls>
				<MediaReplaceFlow
					mediaId={ id }
					mediaURL={ src }
					allowedTypes={ ALLOWED_MEDIA_TYPES }
					accept="video/*"
					onSelect={ onSelectVideo }
					onSelectURL={ onSelectURL }
					onError={ onUploadError }
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ aiovg_blocks.i18n.general_settings }>
					<PanelRow>
						<TextControl
							label={ aiovg_blocks.i18n.width }
							help={ aiovg_blocks.i18n.width_help }
							value={ width > 0 ? width : '' }
							onChange={ ( value ) => setAttributes( { width: isNaN( value ) ? 0 : value } ) }
						/>
					</PanelRow>
					
					<PanelRow>
						<TextControl
							label={ aiovg_blocks.i18n.ratio }
							help={ aiovg_blocks.i18n.ratio_help }
							value={ ratio > 0 ? ratio : '' }
							onChange={ ( value ) => setAttributes( { ratio: isNaN( value ) ? 0 : value } ) }
						/>
					</PanelRow>					

					<PanelRow>
						<MediaUploadCheck>
							<BaseControl className="editor-video-poster-control">
								<BaseControl.VisualLabel>
									{ aiovg_blocks.i18n.poster_image }
								</BaseControl.VisualLabel>
								<MediaUpload
									title={ aiovg_blocks.i18n.select_image }
									onSelect={ onSelectPoster }
									allowedTypes={ VIDEO_POSTER_ALLOWED_MEDIA_TYPES	}
									render={ ( { open } ) => (
										<Button
											variant="primary"
											onClick={ open }
											ref={ posterImageButton }
											aria-describedby={ videoPosterDescription }
										>
											{ ! poster ? aiovg_blocks.i18n.select_image : aiovg_blocks.i18n.replace_image }
										</Button>
									) }
								/>
								<p id={ videoPosterDescription } hidden>
									{ poster ? sprintf( 'The current poster image url is %s', poster ) : 'There is no poster image currently selected' }
								</p>
								{ !! poster && (
									<Button
										onClick={ onRemovePoster }
										variant="tertiary"
									>
										{ aiovg_blocks.i18n.remove_image }
									</Button>
								) }
							</BaseControl>
						</MediaUploadCheck>
					</PanelRow>	

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.autoplay }							
							checked={ autoplay }
							onChange={ () => setAttributes( { autoplay: ! autoplay } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.loop }							
							checked={ loop }
							onChange={ () => setAttributes( { loop: ! loop } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.muted }							
							checked={ muted }
							onChange={ () => setAttributes( { muted: ! muted } ) }
						/>
					</PanelRow>
				</PanelBody>	

				<PanelBody title={ aiovg_blocks.i18n.player_controls }>	
					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.play_pause }							
							checked={ playpause }
							onChange={ () => setAttributes( { playpause: ! playpause } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.current_time }							
							checked={ current }
							onChange={ () => setAttributes( { current: ! current } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.progressbar }							
							checked={ progress }
							onChange={ () => setAttributes( { progress: ! progress } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.duration }							
							checked={ duration }
							onChange={ () => setAttributes( { duration: ! duration } ) }
						/>
					</PanelRow>					

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.speed }							
							checked={ speed }
							onChange={ () => setAttributes( { speed: ! speed } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.quality }							
							checked={ quality }
							onChange={ () => setAttributes( { quality: ! quality } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.volume }							
							checked={ volume }
							onChange={ () => setAttributes( { volume: ! volume } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.fullscreen }							
							checked={ fullscreen }
							onChange={ () => setAttributes( { fullscreen: ! fullscreen } ) }
						/>	
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.share }							
							checked={ share }
							onChange={ () => setAttributes( { share: ! share } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.embed }							
							checked={ embed }
							onChange={ () => setAttributes( { embed: ! embed } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.download }							
							checked={ download }
							onChange={ () => setAttributes( { download: ! download } ) }
						/>
					</PanelRow>	
				</PanelBody>		
			</InspectorControls>

			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block="aiovg/video"
						attributes={ attributes }
					/>
				</Disabled>	
				{ isTemporaryVideo && <Spinner /> }
			</div>
		</>
	);
}
