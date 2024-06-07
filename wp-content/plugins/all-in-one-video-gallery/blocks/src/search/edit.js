/**
 * Import block dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';

import { 	
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import {
	Disabled,
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	
	const { 
		template, 
		keyword,
		category,
		tag 
	} = attributes;
	
	return (
		<>
			<InspectorControls>
				<PanelBody title={ aiovg_blocks.i18n.general_settings }>
					<PanelRow>
						<SelectControl
							label={ aiovg_blocks.i18n.select_template }
							value={ template }
							options={ [
								{ label: aiovg_blocks.i18n.vertical, value: 'vertical' },
								{ label: aiovg_blocks.i18n.horizontal, value: 'horizontal' }
							] }
							onChange={ ( value ) => setAttributes( { template: value } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.search_by_keywords }
							checked={ keyword }
							onChange={ () => setAttributes( { keyword: ! keyword } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.search_by_categories }
							checked={ category }
							onChange={ () => setAttributes( { category: ! category } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.search_by_tags }
							checked={ tag }
							onChange={ () => setAttributes( { tag: ! tag } ) }
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>

			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						block="aiovg/search"
						attributes={ attributes }
					/>
				</Disabled>	
			</div>
		</>
	);
}
