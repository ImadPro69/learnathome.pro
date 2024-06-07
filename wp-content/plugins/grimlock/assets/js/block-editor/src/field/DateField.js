import { __ } from '@wordpress/i18n';
import {
	DatePicker,
	BaseControl,
	Dropdown,
	Button,
} from '@wordpress/components';

export default ( { name, label, help, attributes, setAttributes } ) => {
	// Bail if field has no name
	if ( ! name )
		return null;

	return (
		<BaseControl label={ label || '' } help={ help || '' }>
			<div>
				<Dropdown position="bottom right"
						  renderToggle={ ( { onToggle } ) => (
							  <>
								  <input type="text" readOnly value={ attributes[ name ] || '' } onClick={ onToggle } />
								  <Button style={ { marginLeft: '8px' } } isSecondary isSmall onClick={ () => setAttributes( { [ name ] : '' } ) }>
									  { __( 'Clear', 'grimlock' ) }
								  </Button>
							  </>
						  ) }
						  renderContent={ () => (
							  <DatePicker currentDate={ attributes[ name ] || null }
										  onChange={ ( date ) => { setAttributes( { [ name ] : date.split( 'T' )[0] } ); } } />
						  ) } />
			</div>
		</BaseControl>
	);
};
