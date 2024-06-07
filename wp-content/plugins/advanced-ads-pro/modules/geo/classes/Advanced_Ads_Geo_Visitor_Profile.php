<?php

/**
 * @property-read bool   $has_visitor_profile
 * @property-read string $city
 * @property-read string $region
 * @property-read string $country_code
 * @property-read string $continent_code
 * @property-read bool   $is_eu_state
 * @property-read float  $lat
 * @property-read float  $lon
 */
class Advanced_Ads_Geo_Visitor_Profile {
	const SERVER_INFO_COOKIE_NAME = 'advanced_ads_pro_server_info';
	/**
	 * Whether there is a saved profile.
	 * @var bool
	 */
	private $has_visitor_profile = false;
	/**
	 * The visitor's city.
	 * @var string
	 */
	private $city;

	/**
	 * The visitor's region.
	 * @var string
	 */
	private $region;

	/**
	 * The visitor's country code, ISO-3166-1 alpha-2.
	 * @var string
	 */
	private $country_code;

	/**
	 * The visitor's continent code, cf. https://www.php.net/manual/en/function.geoip-continent-code-by-name.php.
	 * @var string
	 */
	private $continent_code;

	/**
	 * Whether the visitor is in the EU.
	 * @var bool
	 */
	private $is_eu_state;

	/**
	 * The visitor's geolocation latitude.
	 * @var float
	 */
	private $lat;

	/**
	 * The visitor's geolocation longitude.
	 * @var float
	 */
	private $lon;

	/**
	 * Check if the cookie is set and in the correct format.
	 * If not, return early.
	 */
	public function __construct() {
		if ( ! isset( $_COOKIE[ self::SERVER_INFO_COOKIE_NAME ] ) ) {
			return;
		}
		try {
			$cookie_value = $this->parse_raw_cookie();
		} catch ( RuntimeException $e ) {
			return;
		}

		$this->has_visitor_profile = true;
		$this->city                = $cookie_value['visitor_city'];
		$this->region              = $cookie_value['visitor_region'];
		$this->country_code        = $cookie_value['country_code'];
		$this->continent_code      = $cookie_value['continent_code'];
		$this->is_eu_state         = $cookie_value['is_eu_state'];
		$this->lat                 = $cookie_value['current_lat'];
		$this->lon                 = $cookie_value['current_lon'];
	}

	/**
	 * Parse the cookie value and see if it contains the keys we expect.
	 *
	 * @return array
	 * @throws RuntimeException
	 */
	private function parse_raw_cookie() {
		$value = json_decode( wp_unslash( $_COOKIE[ self::SERVER_INFO_COOKIE_NAME ] ), true );

		if ( array_key_exists( 'conditions', $value ) && array_key_exists( 'geo_targeting', $value['conditions'] ) ) {
			// get the random key inside geo_targeting
			$value = reset( $value['conditions']['geo_targeting'] );
			if ( is_array( $value ) && array_key_exists( 'data', $value ) ) {
				return $value['data'];
			}
		}

		throw new RuntimeException( 'Cookie does not have expected values' );
	}

	/**
	 * Get all values readonly.
	 * @return mixed
	 * @noinspection MagicMethodsValidityInspection
	 */
	public function __get( $name ) {
		return $this->{$name};
	}

	/**
	 * Check if the saved user location is different from the passed values.
	 *
	 * @param float $lat Latitude to check against.
	 * @param float $lon Longitude to check against.
	 *
	 * @return bool
	 */
	public function is_different_from_current_location( $lat, $lon ) {
		return $this->lat !== $lat || $this->lon !== $lon;
	}

	/**
	 * Get the full name for ISO-3166-1 alpha-2 country code.
	 *
	 * @return string
	 */
	public function get_country() {
		return Advanced_Ads_Geo_Api::get_countries()[ $this->country_code ];
	}
}
