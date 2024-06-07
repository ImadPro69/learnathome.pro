import { __ } from '@wordpress/i18n';
import {
	createBlocksFromInnerBlocksTemplate,
	store as blocksStore,
} from '@wordpress/blocks';
import {
	ToolbarGroup,
} from '@wordpress/components';
import {
	InspectorControls,
	InspectorAdvancedControls,
	InnerBlocks,
	BlockControls,
	__experimentalBlockVariationPicker as BlockVariationPicker,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect, useDispatch } from "@wordpress/data";
import inspectorControls from './inspector-controls';
import { renderInspectorControls } from '../utils';
import { SelectField, NumberField } from '../../field';
import { ReactComponent as ContainerFullIcon } from '../../../../../images/container-full.svg';
import { ReactComponent as ContainerDefaultIcon } from '../../../../../images/container-default.svg';
import { ReactComponent as ContainerNarrowIcon } from '../../../../../images/container-narrow.svg';
import { ReactComponent as ContainerNarrowerIcon } from '../../../../../images/container-narrower.svg';

const spreadOptions = [
	{
		name: 'full',
		title: 'Full',
		icon: ContainerFullIcon,
	},
	{
		name: '',
		title: 'Default',
		icon: ContainerDefaultIcon,
	},
	{
		name: 'narrow',
		title: 'Narrow',
		icon: ContainerNarrowIcon,
	},
	{
		name: 'narrower',
		title: 'Narrower',
		icon: ContainerNarrowerIcon,
	},
];

export default ( { attributes, setAttributes, clientId, name, blockProps } ) => {

	const hasInnerBlocks = useSelect(
		( select ) =>
			select( blockEditorStore ).getBlocks( clientId ).length > 0,
		[ clientId, blockEditorStore ]
	);

	const { blockType, variations } = useSelect(
		( select ) => {
			const {
				getBlockVariations,
				getBlockType,
			} = select( blocksStore );

			return {
				blockType: getBlockType( name ),
				variations: getBlockVariations( name, 'block' ),
			};
		},
		[ name, blocksStore ]
	);

	const { replaceInnerBlocks } = useDispatch( blockEditorStore );

	const selectedSpreadOption = spreadOptions.find( ( option ) => option.name === attributes.spread );
	const defaultSpreadOption = spreadOptions.find( ( option ) => option.name === '' );

	return (
		<>
			<InspectorControls>

				{ renderInspectorControls( inspectorControls, attributes, setAttributes ) }

			</InspectorControls>

			<InspectorAdvancedControls>

				<NumberField name="zIndex"
							 label="Z-index"
							 attributes={ attributes }
							 setAttributes={ setAttributes } />

				<SelectField name="overflow"
							 label="Overflow"
							 attributes={ attributes }
							 setAttributes={ setAttributes }
							 choices={ {
								 auto: 'Auto',
								 visible: 'Visible',
								 hidden: 'Hidden',
							 } } />

			</InspectorAdvancedControls>

			<BlockControls>

				<ToolbarGroup icon={ selectedSpreadOption ? selectedSpreadOption.icon : defaultSpreadOption.icon }
							  label={ __( 'Spread', 'grimlock' ) }
							  popoverProps={ { isAlternate: true } }
							  toggleProps={ { describedBy: __( 'Change spread', 'grimlock' ) } }
							  controls={ spreadOptions.map( ( control ) => {
								  const { name, ...controlProps } = control;

								  return {
									  ...controlProps,
									  isActive: attributes.spread === name,
									  role: 'menuitemradio',
									  onClick: () => setAttributes( { spread: attributes.spread === name ? undefined : name } ),
								  };
							  } ) }
							  isCollapsed />

			</BlockControls>

			{ ! hasInnerBlocks ?
				(
					<BlockVariationPicker icon={ blockType.icon.src }
										  label={ blockType.title }
										  variations={ variations }
										  onSelect={ ( selectedVariation ) => {
											  if ( ! selectedVariation ) {
												  replaceInnerBlocks(
													  clientId,
													  createBlocksFromInnerBlocksTemplate( [ [ 'core/paragraph' ] ] ),
													  true
												  );
												  return;
											  }

											  if ( selectedVariation.attributes ) {
												  setAttributes( selectedVariation.attributes );
											  }

											  if ( selectedVariation.innerBlocks ) {
												  replaceInnerBlocks(
													  clientId,
													  createBlocksFromInnerBlocksTemplate(
														  selectedVariation.innerBlocks
													  ),
													  true
												  );
											  }
										  } }
										  allowSkip />
				) : (
					<div { ...blockProps }>

						<div className="wp-block-grimlock-wrapper__bg" />
						<div className="wp-block-grimlock-wrapper__bg-overlay" />

						<div className="wp-block-grimlock-wrapper__inner" >
							<InnerBlocks />
						</div>

					</div>
				)
			}
		</>
	);
};
