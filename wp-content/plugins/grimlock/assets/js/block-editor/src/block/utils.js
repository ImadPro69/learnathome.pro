import { PanelBody } from '@wordpress/components';
import he from 'he';
import * as fieldsComponents from '../field';
import { InspectorControls } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Generate supported attributes for all fields in InspectorControls
 *
 * @return {{}}
 */
export const getInspectorControlsAttributes = ( inspectorControls ) => {
	let attributes = {};

	if ( ! inspectorControls )
		return attributes;

	Object.keys( inspectorControls ).forEach( ( panelKey ) => {
		if ( ! inspectorControls[ panelKey ].fields )
			return;

		inspectorControls[ panelKey ].fields.forEach( ( field ) => {
			if ( ! field.attributeType || ! field.name )
				return;

			attributes[ field.name ] = { type: field.attributeType };

			if ( field.default !== undefined )
				attributes[ field.name ].default = field.default;
		} );
	} );

	return attributes;
};

/**
 * Process conditional logic to determine whether a field should be displayed or not
 *
 * @param conditionalLogic Array of conditional logic
 * @param attributes Block attributes from which we pull values to do the comparisons
 * @param relation Relation to apply between conditions. Automatically alternates between AND and OR for each nested array.
 * @return {*}
 */
const processConditions = ( conditionalLogic, attributes, relation = 'AND' ) => {
	let finalResult;

	conditionalLogic.forEach( ( conditions ) => {
		let result;

		if ( conditions.length ) {
			// If we have a nested array, use recursion to get the result of the nested conditions
			result = processConditions( conditions, attributes, relation === 'AND' ? 'OR' : 'AND' );
		}
		else if ( conditions['field'] && conditions['operator'] && conditions['value'] !== undefined ) {
			if ( undefined === attributes[ conditions['field'] ] ) {
				// If the attribute is undefined (the field doesn't exist) we can't check the condition properly, so we assume true
				result = true;
			}
			else {
				// Get the result of the condition
				switch ( conditions[ 'operator' ] ) {
					case '===':
						result = attributes[ conditions[ 'field' ] ] === conditions[ 'value' ];
						break;
					case '==':
						result = attributes[ conditions[ 'field' ] ] == conditions[ 'value' ];
						break;
					case '!==':
						result = attributes[ conditions[ 'field' ] ] !== conditions[ 'value' ];
						break;
					case '!=':
						result = attributes[ conditions[ 'field' ] ] != conditions[ 'value' ];
						break;
					case '>':
						result = attributes[ conditions[ 'field' ] ] > conditions[ 'value' ];
						break;
					case '>=':
						result = attributes[ conditions[ 'field' ] ] >= conditions[ 'value' ];
						break;
					case '<':
						result = attributes[ conditions[ 'field' ] ] < conditions[ 'value' ];
						break;
					case '<=':
						result = attributes[ conditions[ 'field' ] ] <= conditions[ 'value' ];
						break;
				}
			}
		}

		if ( undefined !== result ) {
			if ( undefined === finalResult )
				finalResult = result;
			else
				finalResult = relation === 'AND' ? finalResult && result : finalResult || result;
		}
	} );

	return finalResult;
};

/**
 * Render inspector controls fields
 *
 * @param fields Array of fields to render
 * @param attributes Object containing the block attributes
 * @param setAttributes Function used to update the block attributes
 *
 * @return Array of react elements
 */
const renderFields = ( fields, attributes, setAttributes ) => {
	return fields.map( ( field, key ) => {
		// Bail if field has no type
		if ( ! field[ 'type' ] )
			return;

		// Get the render function for this field type
		const fieldComponentName = field['type'][0].toUpperCase() + field['type'].slice(1) + 'Field';
		const FieldComponent = fieldsComponents[ fieldComponentName ];

		let displayed = true;
		if ( field['conditional_logic'] && field['conditional_logic'].length )
			displayed = processConditions( field['conditional_logic'], attributes );

		// Bail if a render function doesn't exist for this field type
		if ( ! FieldComponent || ! displayed )
			return;

		// Hack to clone the field parameter before modifying it
		let fieldArgs = { ...field };

		if ( fieldArgs['label'] )
			fieldArgs[ 'label' ] = he.decode( fieldArgs['label'] );

		// Replace field args with attribute value where necessary
		Object.keys( fieldArgs ).forEach( ( fieldArgKey ) => {
			let fieldArg = fieldArgs[ fieldArgKey ];
			// If fieldArg is a string contained in brackets, we replace it by the value of the attribute with the name in brackets
			if ( typeof fieldArg === 'string' && fieldArg.match( /^{.+}$/g ) ) {
				// Remove the brackets
				let attributeName = fieldArg.slice( 1, fieldArg.length - 1 );

				// Replace with attribute value
				fieldArgs[ fieldArgKey ] = attributes[ attributeName ];
			}
		} );

		return (
			<FieldComponent key={ key } attributes={ attributes } setAttributes={ setAttributes } {...fieldArgs} />
		);
	} );
};

/**
 * Render panels and fields in the inspector controls area.
 *
 * @param inspectorControls Object containing the panels and fields to render
 * @param attributes Object containing the block attributes
 * @param setAttributes Function used to update the block attributes
 *
 * @return array of react elements
 */
export const renderInspectorControls = ( inspectorControls, attributes, setAttributes ) => {
	return Object.keys( inspectorControls ).map( ( panelKey ) => {
		const panel = inspectorControls[ panelKey ];

		// Bail if panel has no field
		if ( ! panel.fields )
			return;

		return (
			<PanelBody title={ panel.label } key={ panelKey } initialOpen={false}>

				{ renderFields( panel.fields, attributes, setAttributes ) }

			</PanelBody>
		);
	} );
};

/**
 * Get the inspector controls object from the global variable for a specified dynamic block
 *
 * @param blockName Name of the block for which we need the inspector controls object
 * @return object containing the settings to build the inspector controls for the specified block
 */
export const getDynamicBlockInspectorControls = ( blockName ) => {
	if ( ! grimlockBlockEditor.blocks || ! grimlockBlockEditor.blocks[ blockName ] || ! grimlockBlockEditor.blocks[ blockName ].inspectorControls )
		return {};

	return grimlockBlockEditor.blocks[ blockName ].inspectorControls;
};

export const dynamicBlockSettings = {
	edit: ( { attributes, setAttributes, name } ) => {
		const inspectorControls = getDynamicBlockInspectorControls( name );

		return (
			<>
				<InspectorControls>

					{ renderInspectorControls( inspectorControls, attributes, setAttributes ) }

				</InspectorControls>

				<div { ...useBlockProps() }>
					<Disabled>
						<ServerSideRender block={ name }
										  attributes={ attributes } />
					</Disabled>
				</div>
			</>
		);
	},
	save: () => null,
};
