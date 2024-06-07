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
	RangeControl,
	SelectControl,
	ToggleControl
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';

import { 
	BuildTree,
	GroupByParent
 } from '../helper.js';

/**
 * Describes the structure of the block in the context of the editor.
 * This represents what the editor will render when the block is used.
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {

	const { 		
		template, 
		id,
		columns,
		limit, 
		orderby, 
		order,
		hierarchical, 
		show_description, 
		show_count, 
		hide_empty,
		show_pagination 
	} = attributes;

	const categories = useSelect( ( select ) => {
		const categoriesList = select( 'core' ).getEntityRecords( 'taxonomy', 'aiovg_categories', {
			'per_page': -1
		});

		let options = [{ 
			label: '— ' + aiovg_blocks.i18n.select_parent + ' —', 
			value: 0
		}];

		if ( categoriesList && categoriesList.length > 0 ) {		
			let grouped = GroupByParent( categoriesList );
			let tree = BuildTree( grouped );
			
			options = [ ...options, ...tree ];
		}

		return options;
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={ aiovg_blocks.i18n.general_settings }>
					<PanelRow>
						<SelectControl
							label={ aiovg_blocks.i18n.select_template }
							value={ template }
							options={ [
								{ label: aiovg_blocks.i18n.grid, value: 'grid' },
								{ label: aiovg_blocks.i18n.list, value: 'list' },
								{ label: aiovg_blocks.i18n.dropdown, value: 'dropdown' }
							] }
							onChange={ ( value ) => setAttributes( { template: value } ) }
						/>
					</PanelRow>

					<PanelRow>
						<SelectControl
							label={ aiovg_blocks.i18n.select_parent }
							value={ id }
							options={ categories }
							onChange={ ( value ) => setAttributes( { id: Number( value ) } ) }
						/>	
					</PanelRow>

					{ 'grid' == template && (
						<PanelRow>
							<RangeControl
								label={ aiovg_blocks.i18n.columns }
								value={ columns }							
								min={ 1 }
								max={ 12 }
								onChange={ ( value ) => setAttributes( { columns: value } ) }
							/>
						</PanelRow>
					) }

					{ 'grid' == template && (
						<PanelRow>
							<RangeControl
								label={ aiovg_blocks.i18n.limit }
								value={ limit }							
								min={ 0 }
								max={ 500 }
								onChange={ ( value ) => setAttributes( { limit: value } ) }
							/>
						</PanelRow>
					) }

					<PanelRow>
						<SelectControl
							label={ aiovg_blocks.i18n.order_by }
							value={ orderby }
							options={ [
								{ label: aiovg_blocks.i18n.id, value: 'id' },
								{ label: aiovg_blocks.i18n.count, value: 'count' },
								{ label: aiovg_blocks.i18n.name, value: 'name' },
								{ label: aiovg_blocks.i18n.slug, value: 'slug' },
								{ label: aiovg_blocks.i18n.menu_order, value: 'menu_order' }
							] }
							onChange={ ( value ) => setAttributes( { orderby: value } ) }
						/>
					</PanelRow>

					<PanelRow>
						<SelectControl
							label={ aiovg_blocks.i18n.order }
							value={ order }
							options={ [
								{ label: aiovg_blocks.i18n.asc, value: 'asc' },
								{ label: aiovg_blocks.i18n.desc, value: 'desc' }
							] }
							onChange={ ( value ) => setAttributes( { order: value } ) }
						/>
					</PanelRow>

					{ ( 'list' == template || 'dropdown' == template ) && (
						<PanelRow>
							<ToggleControl
								label={ aiovg_blocks.i18n.show_hierarchy }
								checked={ hierarchical }
								onChange={ () => setAttributes( { hierarchical: ! hierarchical } ) }
							/>
						</PanelRow>
					) }

					{ 'grid' == template && (
						<PanelRow>
							<ToggleControl
								label={ aiovg_blocks.i18n.show_description }
								checked={ show_description }
								onChange={ () => setAttributes( { show_description: ! show_description } ) }
							/>
						</PanelRow>
					) }

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.show_videos_count }
							checked={ show_count }
							onChange={ () => setAttributes( { show_count: ! show_count } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ aiovg_blocks.i18n.hide_empty_categories }
							checked={ hide_empty }
							onChange={ () => setAttributes( { hide_empty: ! hide_empty } ) }
						/>
					</PanelRow>

					{ 'grid' == template && (
						<PanelRow>
							<ToggleControl
								label={ aiovg_blocks.i18n.show_pagination }
								checked={ show_pagination }
								onChange={ () => setAttributes( { show_pagination: ! show_pagination } )  }
							/>
						</PanelRow>
					) }
				</PanelBody>
			</InspectorControls>
			
			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						block="aiovg/categories"
						attributes={ attributes }
					/>
				</Disabled>	
			</div>					
		</>
	);
}
