<?php
/*
* load common and WordPress based resources
*
* @since 1.0.0
*/
class Advanced_Ads_Geo_Plugin {

	const OPTIONS_SLUG = 'geo';

	/**
	 *
	 * @var Advanced_Ads_Geo_Plugin
	 */
	protected static $instance;

	/**
	 * Plugin options
	 *
	 * @var     array
	 */
	protected $options;


	/**
	 * Subdirectory in wp-content/uploads in which the db files are saved
	 *
	 * @car     string
	 */
	public $upload_dir = '/advanced-ads-geo';


	private function __construct() {
		// add new visitor condition
		add_filter( 'advanced-ads-visitor-conditions', [ $this, 'visitor_conditions' ] );
	}

	/**
	 *
	 * @return Advanced_Ads_Geo_Plugin
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get all options array for Geo or specific option.
	 *
	 * @param string $name    Only get a specific option.
	 * @param mixed  $default If name is set, allow to pass a default value.
	 *
	 * @return mixed
	 */
	public function options( $name = '', $default = null ) {
		if ( ! isset( $this->options ) ) {
			$this->populate_options();
		}

		if ( ! empty( $name ) ) {
			return isset( $this->options[ $name ] ) ? $this->options[ $name ] : $default;
		}

		return $this->options;
	}

	private function populate_options() {
		$standalone_prefix   = ADVADS_SLUG . '-' . self::OPTIONS_SLUG;
		$main_plugin_options = Advanced_Ads_Plugin::get_instance()->options();
		$geo_options         = [];
		if ( array_key_exists( $standalone_prefix, $main_plugin_options ) ) {
			$geo_options = $main_plugin_options[ $standalone_prefix ];
		}

		$pro_options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( array_key_exists( self::OPTIONS_SLUG, $pro_options ) ) {
			$geo_options = array_merge( $geo_options, $pro_options[ self::OPTIONS_SLUG ] );
		}

		$this->options = $geo_options;
	}

	/**
	 * Add visitor condition
	 *
	 * @param array $conditions visitor conditions of the main plugin
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function visitor_conditions( $conditions ) {
		$conditions['geo_targeting'] = [
			'label'        => __( 'geo location', 'advanced-ads-pro' ),
			'description'  => __( 'Display ads based on geo location.', 'advanced-ads-pro' ),
			'metabox'      => [ 'Advanced_Ads_Geo_Admin', 'metabox_geo' ], // callback to generate the visitor condition
			'check'        => [ 'Advanced_Ads_Geo', 'check_geo' ], // callback for frontend check
			'passive_info' => [
				'hash_fields' => null,
				'function'    => [ 'Advanced_Ads_Geo', 'get_passive' ],
			],
		];

		return $conditions;
	}

	/**
	 * Get available targeting methods
	 */
	public static function get_targeting_methods() {
		$methods = [
			'default' => [
				'description' => __( 'MaxMind database (default)', 'advanced-ads-pro' ),
			],
		];

		if ( isset( $_SERVER['HTTP_X_SUCURI_COUNTRY'] ) ) {
			$methods['sucuri'] = [
				'description' => __( 'Sucuri Header (country only)', 'advanced-ads-pro' ),
			];
		}

		return $methods;
	}

	/**
	 * Get current targeting method
	 *
	 * @return string
	 */
	public static function get_current_targeting_method() {
		$methods = self::get_targeting_methods();
		$method  = self::get_instance()->options( 'method' );

		if ( empty( $method ) || ! isset( $methods[ $options[ self::OPTIONS_SLUG ]['method'] ] ) ) {
			return 'default';
		}

		return $method;
	}

	/**
	 * Get Sucuri country code
	 */
	public static function get_sucuri_country() {
		return isset( $_SERVER['HTTP_X_SUCURI_COUNTRY'] ) ? $_SERVER['HTTP_X_SUCURI_COUNTRY'] : '';
	}

	/**
	 * Get the upload subdirectory
	 */
	public static function get_upload_dir() {
		// allow to manipulate the upload dir
		return apply_filters( 'advanced-ads-geo-upload-dir', self::get_instance()->upload_dir );
	}

	/**
	 * Get the full path to the upload subdirectory.
	 */
	public static function get_upload_full() {
		// Get the WordPress upload directory information, which is where we have stored the MaxMind database.
		$upload_dir = wp_upload_dir();
		if ( ! isset( $upload_dir['basedir'] ) ) {
			return false;
		}

		return $upload_dir['basedir'] . trailingslashit( self::get_upload_dir() );
	}

	/**
	 * Get the prefix of the maxmind databases.
	 */
	public static function get_maxmind_file_prefix() {
		$prefix = get_option( 'advanced-ads-geo-maxmind-file-prefix', '' );
		// Allow only digits and letters to prevent going up using `../`.
		return preg_replace( '/[^a-z0-9]/i', '', $prefix );
	}

}
