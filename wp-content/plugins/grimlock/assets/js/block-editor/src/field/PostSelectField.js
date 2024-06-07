import SortablePostsMultiSelectControl from '../component/SortablePostsMultiSelectControl';
import { useState } from '@wordpress/element';

export default ( { name, label, help, post_type, attributes, setAttributes } ) => {
	// Bail if field has no name or no taxonomy
	if ( ! name || ! post_type )
		return null;

	const [ inputValue, setInputValue ] = useState( '' );
	const [ selectValue, setSelectValue ] = useState( false );

	return (
		<SortablePostsMultiSelectControl label={ label || '' }
										 help={ help || '' }
										 selectedPostIds={ attributes[ name ] || [] }
										 selectedPosts={ selectValue }
										 postType={ post_type }
										 onChange={ ( value ) => {
											 setSelectValue( value );
											 setAttributes( { [ name ] : value && value.length ? value.map( ( option ) => option.value ) : [] } );
										 } }
										 inputValue={ inputValue }
										 onInputChange={ setInputValue } />
	);
};
