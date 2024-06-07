/**
 * Import block dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';

import {	 
	InspectorControls,
	PanelColorSettings,
	useBlockProps
} from '@wordpress/block-editor';

import {
	Disabled,
	PanelBody,
	PanelRow,
	RangeControl,
	SelectControl,
	TextControl,
	TextareaControl,
	ToggleControl
} from '@wordpress/components';

import { 
	useEffect,
	useRef
} from '@wordpress/element';

import { applyFilters } from '@wordpress/hooks';

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

	const videos = aiovg_blocks.videos;	

	const categoriesList = useSelect( ( select ) => {
		const terms = select( 'core' ).getEntityRecords( 'taxonomy', 'aiovg_categories', {
			'per_page': -1
		});

		let options = [];

		if ( terms && terms.length > 0 ) {		
			let grouped = GroupByParent( terms );
			let tree = BuildTree( grouped );
			
			options = [ ...options, ...tree ];
		}

		return options;
	});

	const tagsList = useSelect( ( select ) => {
		const terms = select( 'core' ).getEntityRecords( 'taxonomy', 'aiovg_tags', {
			'per_page': -1
		});		

		let options = [];

		if ( terms ) {
			for ( var i = 0; i < terms.length; i += 1 ) {
				options.push({
					label: terms[ i ].name,
					value: terms[ i ].id,
				});	
			}			
		}

		return options;
	});

	const getControl = ( field, index ) => {
		if ( ! canShowControl( field.name ) ) {
			return '';
		}

		const placeholder = field.placeholder ? field.placeholder : '';
		const description = field.description ? field.description : '';

		switch ( field.type ) {
			case 'header':
				return <PanelRow key={ index }>
						<h3 className="aiovg-no-margin">{ field.label }</h3>
					</PanelRow>	
			case 'categories':
				return <PanelRow key={ index } className="aiovg-block-multiselect">
					<SelectControl
						multiple
						label={ field.label }
						help={ description }						
						options={ categoriesList }
						value={ attributes[ field.name ] }
						onChange={ onChange( field.name ) }
					/>
				</PanelRow>
			case 'tags':
				return <PanelRow key={ index } className="aiovg-block-multiselect">
					<SelectControl
						multiple
						label={ field.label }
						help={ description }						
						options={ tagsList }
						value={ attributes[ field.name ] }
						onChange={ onChange( field.name ) }
					/>
				</PanelRow>		
			case 'number':
				return <PanelRow key={ index }>
					<RangeControl	
						label={ field.label }
						help={ description }
						placeholder={ placeholder }
						value={ attributes[ field.name ] }
						min={ field.min }
						max={ field.max }
						onChange={ onChange( field.name ) }
					/>
				</PanelRow>
			case 'textarea':
				return <PanelRow key={ index }>
					<TextareaControl
						label={ field.label }
						help={ description }
						placeholder={ placeholder }
						value={ attributes[ field.name ] }
						onChange={ onChange( field.name ) }
					/>
				</PanelRow>
			case 'select':
			case 'radio':
				let options = [];

				for ( let key in field.options ) {
					options.push({
						label: field.options[ key ],
						value: key
					});
				};

				return <PanelRow key={ index }>
					<SelectControl
						label={ field.label }
						help={ description }						
						options={ options }
						value={ attributes[ field.name ] }
						onChange={ onChange( field.name ) }
					/>
				</PanelRow>
			case 'checkbox':
				return <PanelRow key={ index }>
					<ToggleControl
						label={ field.label }
						help={ description }
						checked={ attributes[ field.name ] }
						onChange={ toggleAttribute( field.name ) }
					/>
				</PanelRow>
			case 'color':
				return <PanelRow key={ index }>
					<PanelColorSettings						
						title={ field.label }
						colorSettings={ [
							{
								label: aiovg_blocks.i18n.select_color,
								value: attributes[ field.name ],
								onChange: onChange( field.name )					
							}
						] }
					>							
					</PanelColorSettings>
				</PanelRow>
			default:
				return <PanelRow key={ index }>
					<TextControl	
						label={ field.label }
						help={ description }
						placeholder={ placeholder }
						value={ attributes[ field.name ] }
						onChange={ onChange( field.name ) }
					/>
				</PanelRow>
		}		
	}

	const canShowPanel = ( panel ) => {
		return applyFilters( 'aiovg_block_toggle_panels', true, panel, attributes );
	}

	const canShowControl = ( control ) => {
		var value = true;
		if ( 'show_more' == control || 'more_label' == control || 'more_link' == control ) {
			value = false;
		}

		return applyFilters( 'aiovg_block_toggle_controls', value, control, attributes );
	}

	const onChange = ( attribute ) => {
		return ( newValue ) => {
			setAttributes( { [ attribute ]: newValue } );		
		};
	}

	const toggleAttribute = ( attribute ) => {
		return ( newValue ) => {
			setAttributes( { [ attribute ]: newValue } );
		};
	}	

	const mounted = useRef();	
	useEffect( () => {
		if ( ! mounted.current ) {
			// Do componentDidMount logic
			mounted.current = true;
		} else {
			// Do componentDidUpdate logic
			applyFilters( 'aiovg_block_init', attributes );
		}
	} );

	return (
		<>
			<InspectorControls>
				{Object.keys( videos ).map(( key, index ) => {
					return (
						canShowPanel( key ) && <PanelBody 
							key={ 'aiovg-block-panel-' + index } 
							title={ videos[ key ].title }
							initialOpen={ 0 == index ? true : false }
							className="aiovg-block-panel">
								
							{Object.keys( videos[ key ].fields ).map(( _key, _index ) => {
								return getControl( videos[ key ].fields[ _key ], 'aiovg-block-control-' + _index );
							})}

						</PanelBody>
					)
				})}
			</InspectorControls>

			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						block="aiovg/videos"
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}
