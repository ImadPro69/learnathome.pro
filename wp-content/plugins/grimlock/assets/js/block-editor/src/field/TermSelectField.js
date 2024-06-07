import SortableTermsMultiSelectControl from '../component/SortableTermsMultiSelectControl';
import TermSelectControl from '../component/TermSelectControl';
import { useState } from '@wordpress/element';

export default ( { name, label, help, taxonomy, multiple, query_args, empty_choice, attributes, setAttributes } ) => {
	// Bail if field has no name or no taxonomy
	if ( ! name || ! taxonomy )
		return null;

	const [ inputValue, setInputValue ] = useState( '' );
	const [ selectValue, setSelectValue ] = useState( false );

	if ( multiple ) {

		return (
			<SortableTermsMultiSelectControl label={ label || '' }
											 help={ help || '' }
											 selectedTermIds={ attributes[ name ] || [] }
											 selectedTerms={ selectValue }
											 taxonomy={ taxonomy }
											 onChange={ ( value ) => {
												 setSelectValue( value );
												 setAttributes( { [ name ] : value && value.length ? value.map( ( option ) => option.value ) : [] } );
											 } }
											 inputValue={ inputValue }
											 onInputChange={ setInputValue } />
		);
	}
	else {
		return (
			<TermSelectControl label={ label || '' }
							   help={ help || '' }
							   value={ attributes[ name ] || '' }
							   taxonomy={ taxonomy }
							   queryArgs={ query_args }
							   emptyChoice={ empty_choice }
							   onChange={ ( value ) => setAttributes( { [ name ]: value } ) } />
		);
	}
};
