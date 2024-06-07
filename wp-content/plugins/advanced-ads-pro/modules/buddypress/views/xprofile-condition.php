<?php
/**
 * Render xprofile visitor condition settions.
 *
 * @var string $name               Base name of the setting.
 * @var array  $options            Condition options.
 * @var array  $groups             BuddyPress field groups.
 * @var int    $field              Field option.
 * @var string $value              Value option.
 * @var array  $type_options       Options for the condition type.
 * @var string $current_field_type Current field type.
 */

$manual_link = Advanced_Ads_Pro_Module_BuddyPress::is_buddyboss()
	? 'https://wpadvancedads.com/manual/buddyboss-ads/?utm_source=advanced-ads&utm_medium=link&utm_campaign=condition-buddyboss-profile-fields'
	: 'https://wpadvancedads.com/ads-on-buddypress-pages/?utm_source=advanced-ads&utm_medium=link&utm_campaign=condition-buddypress-profile-fields';

?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo esc_attr( $options['type'] ); ?>"/>

<?php
if ( $groups ) :
	?>
	<div class="advads-conditions-select-wrap">
		<select class="advads-pro-buddyboss-xprofile-field-type" data-field-name="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>[field]">
			<?php
			foreach ( $groups as $_group ) :
				?>
				<optgroup label="<?php echo esc_html( $_group->name ); ?>">
					<?php
					if ( $_group->fields ) {
						foreach ( $_group->fields as $_field ) :
							$field_type = $_field->type === self::FIELD_MEMBERTYPES ? self::FIELD_MEMBERTYPES : self::FIELD_TEXTBOX;
							?>
							<option value="<?php echo esc_attr( $_field->id ); ?>" data-field-type="<?php echo esc_attr( $field_type ); ?>" <?php selected( $field, $_field->id ); ?>>
							<?php echo esc_html( $_field->name ); ?>
							</option>
							<?php
						endforeach;
					};
					?>
				</optgroup>
				<?php
			endforeach;
			?>
		</select>
	</div>
	<?php
else :
	?>
	<p class="advads-notice-inline advads-error">
		<?php
		/* translators: "profile fields" relates to BuddyPress profile fields */
		esc_html_e( 'No profile fields found', 'advanced-ads-pro' );
		?>
	</p>
	<?php
endif;

if ( 0 <= version_compare( ADVADS_VERSION, '1.9.1' ) ) {
	include ADVADS_BASE_PATH . 'admin/views/ad-conditions-string-operators.php';
}

Advanced_Ads_Pro_Module_BuddyPress_Admin::render_xprofile_field( $name, $current_field_type, $value );
?>
<br class="clear" />
<br />
<p class="description">
	<?php echo esc_html( $type_options[ $options['type'] ]['description'] ); ?>
	<a href="<?php echo esc_url( $manual_link ); ?>" class="advads-manual-link" target="_blank"><?php esc_html_e( 'Manual', 'advanced-ads-gam' ); ?></a>
</p>
