 /**
 * Get video block attributes 
 */
export function getVideoAttributes() {
	var attributes = {};
	
	for ( var key in aiovg_blocks.videos ) {
		var fields = aiovg_blocks.videos[ key ].fields;

		for ( var field in fields ) {
			var name = fields[ field ].name;

			attributes[ name ] = {
				type: getAttributeType( fields[ field ].type ),
				default: fields[ field ].value
			};
		}
	}

	return attributes;
}

/**
 * Get attribute type
 */
function getAttributeType( type ) {
	var _type = 'string';

	if ( 'categories' == type ) {
		_type = 'array';
	} else if ( 'number' == type ) {
		_type = 'number';
	} else if ( 'checkbox' == type ) {
		_type = 'boolean';
	}

	return _type;
}

/**
 * Group terms by parent
 */
export function GroupByParent( terms ) {
	var map = {}, node, roots = [];
	
	for ( var i = 0; i < terms.length; i += 1 ) {
		map[ terms[ i ].id ] = i; // initialize the map
		terms[ i ].children = []; // initialize the children		
	}	

	for ( var i = 0; i < terms.length; i += 1 ) {
		node = terms[ i ];
		if ( node.parent == 0 ) {
			roots.push( node );			
		} else if ( map.hasOwnProperty( node.parent ) ) {
			terms[ map[ node.parent ] ].children.push( node );
		}
	}	

	return roots;
}

/**
 * Build tree array from flat array
 */
export function BuildTree( terms, tree = [], prefix = '' ) {
	for ( var i = 0; i < terms.length; i += 1 ) {
		tree.push({
			label: prefix + terms[ i ].name,
			value: terms[ i ].id,
		});	

		if ( terms[ i ].children.length > 0 ) {
			BuildTree( terms[ i ].children, tree, prefix.trim() + '—' );
		}
	}	

	return tree;
}