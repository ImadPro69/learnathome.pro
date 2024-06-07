<?php
/**
 * @var array  $methods
 * @var string $method
 */
foreach ( $methods as $_key => $_method ) :
	?>
	<label>
		<input type="radio" name="<?php echo Advanced_Ads_Pro::OPTION_KEY . '[' . Advanced_Ads_Geo_Plugin::OPTIONS_SLUG . '][method]'; ?>" value="<?php echo $_key; ?>" <?php checked( $_key, $method ); ?>/>
		<?php echo $_method['description']; ?></label><br/>
<?php
endforeach;
