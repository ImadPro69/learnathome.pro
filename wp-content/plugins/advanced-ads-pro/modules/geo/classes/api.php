<?php

use AdvancedAdsPro\GeoIp2\Database\Reader;
use AdvancedAdsPro\GeoIp2\Exception\AddressNotFoundException;

/**
 * API for geo location based functions
 *
 * @package     Advanced Ads Geo
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, Thomas Maier, webgilde GmbH
 * @since       1.0
 */

class Advanced_Ads_Geo_Api {

	/**
	 *
	 * @var Advanced_Ads_Geo_Api
	 */
	protected static $instance;

	/**
	 * Save if the city reader was used already
	 */
	public $used_city_reader = false;

	/**
	 * Save ip of current visitor
	 */
	protected $current_ip;

	/**
	 * Current visitor continent
	 */
	public $current_continent;

	/**
	 * Current visitor country
	 */
	public $current_country;

	/**
	 * Current visitor state/region
	 */
	public $current_region;

	/**
	 * Current visitor city
	 */
	public $current_city;

	/**
	 * Current visitor latitude
	 */
	public $current_lat;

	/**
	 * Current visitor longitude
	 */
	public $current_lon;



	/**
	 * @var Array of GST states
	 * @since 1.0.0
	 */
	public static $gst_countries = [ 'AU', 'NZ', 'CA', 'CN' ];

	/**
	 * @var Array of languages used in the MaxMind database
	 * @since 1.1
	 */
	public static $locales = [
		'en'    => 'English',
		'de'    => 'Deutsch',
		'fr'    => 'Français',
		'es'    => 'Español',
		'ja'    => '日本語',
		'pr-BR' => 'Português',
		'ru'    => 'Русский',
		'zh-CN' => '华语',
	];

	/**
	 * @var array of EU states
	 * @since 1.0
	 */
	public static $eu_states = [ 'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GB', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE' ];

	/**
	 *
	 * @return Advanced_Ads_Geo_Api
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 Get default country
	 *
	 * @since 1.0.0
	 * @return string $country two letter country code for default country
	 */
	static function default_country() {
		return $country = null;
	}

	/**
	 * Get country list
	 *
	 * @since 1.0.0
	 * @return array $countries list of the available countries
	 */
	public static function get_countries() {
		$countries = [
			''        => '',
			'US'      => __( 'United States', 'advanced-ads-pro' ),
			'GB'      => __( 'United Kingdom', 'advanced-ads-pro' ),
			'EU'      => __( 'European Union', 'advanced-ads-pro' ),
			'DE'      => __( 'Germany', 'advanced-ads-pro' ),
			'-'       => '---',
			'CONT_NA' => __( 'North America', 'advanced-ads-pro' ),
			'CONT_SA' => __( 'South America', 'advanced-ads-pro' ),
			'CONT_EU' => __( 'Europe', 'advanced-ads-pro' ),
			'CONT_AF' => __( 'Africa', 'advanced-ads-pro' ),
			'CONT_AS' => __( 'Asia', 'advanced-ads-pro' ),
			'CONT_OC' => __( 'Oceania', 'advanced-ads-pro' ),
			'--'      => '---',
			'AF'      => __( 'Afghanistan', 'advanced-ads-pro' ),
			'AX'      => __( '&#197;land Islands', 'advanced-ads-pro' ),
			'AL'      => __( 'Albania', 'advanced-ads-pro' ),
			'DZ'      => __( 'Algeria', 'advanced-ads-pro' ),
			'AS'      => __( 'American Samoa', 'advanced-ads-pro' ),
			'AD'      => __( 'Andorra', 'advanced-ads-pro' ),
			'AO'      => __( 'Angola', 'advanced-ads-pro' ),
			'AI'      => __( 'Anguilla', 'advanced-ads-pro' ),
			'AQ'      => __( 'Antarctica', 'advanced-ads-pro' ),
			'AG'      => __( 'Antigua and Barbuda', 'advanced-ads-pro' ),
			'AR'      => __( 'Argentina', 'advanced-ads-pro' ),
			'AM'      => __( 'Armenia', 'advanced-ads-pro' ),
			'AW'      => __( 'Aruba', 'advanced-ads-pro' ),
			'AU'      => __( 'Australia', 'advanced-ads-pro' ),
			'AT'      => __( 'Austria', 'advanced-ads-pro' ),
			'AZ'      => __( 'Azerbaijan', 'advanced-ads-pro' ),
			'BS'      => __( 'Bahamas', 'advanced-ads-pro' ),
			'BH'      => __( 'Bahrain', 'advanced-ads-pro' ),
			'BD'      => __( 'Bangladesh', 'advanced-ads-pro' ),
			'BB'      => __( 'Barbados', 'advanced-ads-pro' ),
			'BY'      => __( 'Belarus', 'advanced-ads-pro' ),
			'BE'      => __( 'Belgium', 'advanced-ads-pro' ),
			'BZ'      => __( 'Belize', 'advanced-ads-pro' ),
			'BJ'      => __( 'Benin', 'advanced-ads-pro' ),
			'BM'      => __( 'Bermuda', 'advanced-ads-pro' ),
			'BT'      => __( 'Bhutan', 'advanced-ads-pro' ),
			'BO'      => __( 'Bolivia', 'advanced-ads-pro' ),
			'BQ'      => __( 'Bonaire, Saint Eustatius and Saba', 'advanced-ads-pro' ),
			'BA'      => __( 'Bosnia and Herzegovina', 'advanced-ads-pro' ),
			'BW'      => __( 'Botswana', 'advanced-ads-pro' ),
			'BV'      => __( 'Bouvet Island', 'advanced-ads-pro' ),
			'BR'      => __( 'Brazil', 'advanced-ads-pro' ),
			'IO'      => __( 'British Indian Ocean Territory', 'advanced-ads-pro' ),
			'BN'      => __( 'Brunei Darrussalam', 'advanced-ads-pro' ),
			'BG'      => __( 'Bulgaria', 'advanced-ads-pro' ),
			'BF'      => __( 'Burkina Faso', 'advanced-ads-pro' ),
			'BI'      => __( 'Burundi', 'advanced-ads-pro' ),
			'KH'      => __( 'Cambodia', 'advanced-ads-pro' ),
			'CM'      => __( 'Cameroon', 'advanced-ads-pro' ),
			'CA'      => __( 'Canada', 'advanced-ads-pro' ),
			'CV'      => __( 'Cape Verde', 'advanced-ads-pro' ),
			'KY'      => __( 'Cayman Islands', 'advanced-ads-pro' ),
			'CF'      => __( 'Central African Republic', 'advanced-ads-pro' ),
			'TD'      => __( 'Chad', 'advanced-ads-pro' ),
			'CL'      => __( 'Chile', 'advanced-ads-pro' ),
			'CN'      => __( 'China', 'advanced-ads-pro' ),
			'CX'      => __( 'Christmas Island', 'advanced-ads-pro' ),
			'CC'      => __( 'Cocos Islands', 'advanced-ads-pro' ),
			'CO'      => __( 'Colombia', 'advanced-ads-pro' ),
			'KM'      => __( 'Comoros', 'advanced-ads-pro' ),
			'CD'      => __( 'Congo, Democratic People\'s Republic', 'advanced-ads-pro' ),
			'CG'      => __( 'Congo, Republic of', 'advanced-ads-pro' ),
			'CK'      => __( 'Cook Islands', 'advanced-ads-pro' ),
			'CR'      => __( 'Costa Rica', 'advanced-ads-pro' ),
			'CI'      => __( 'Cote d\'Ivoire', 'advanced-ads-pro' ),
			'HR'      => __( 'Croatia/Hrvatska', 'advanced-ads-pro' ),
			'CU'      => __( 'Cuba', 'advanced-ads-pro' ),
			'CW'      => __( 'Cura&Ccedil;ao', 'advanced-ads-pro' ),
			'CY'      => __( 'Cyprus', 'advanced-ads-pro' ),
			'CZ'      => __( 'Czech Republic', 'advanced-ads-pro' ),
			'DK'      => __( 'Denmark', 'advanced-ads-pro' ),
			'DJ'      => __( 'Djibouti', 'advanced-ads-pro' ),
			'DM'      => __( 'Dominica', 'advanced-ads-pro' ),
			'DO'      => __( 'Dominican Republic', 'advanced-ads-pro' ),
			'TP'      => __( 'East Timor', 'advanced-ads-pro' ),
			'EC'      => __( 'Ecuador', 'advanced-ads-pro' ),
			'EG'      => __( 'Egypt', 'advanced-ads-pro' ),
			'GQ'      => __( 'Equatorial Guinea', 'advanced-ads-pro' ),
			'SV'      => __( 'El Salvador', 'advanced-ads-pro' ),
			'ER'      => __( 'Eritrea', 'advanced-ads-pro' ),
			'EE'      => __( 'Estonia', 'advanced-ads-pro' ),
			'ET'      => __( 'Ethiopia', 'advanced-ads-pro' ),
			'FK'      => __( 'Falkland Islands', 'advanced-ads-pro' ),
			'FO'      => __( 'Faroe Islands', 'advanced-ads-pro' ),
			'FJ'      => __( 'Fiji', 'advanced-ads-pro' ),
			'FI'      => __( 'Finland', 'advanced-ads-pro' ),
			'FR'      => __( 'France', 'advanced-ads-pro' ),
			'GF'      => __( 'French Guiana', 'advanced-ads-pro' ),
			'PF'      => __( 'French Polynesia', 'advanced-ads-pro' ),
			'TF'      => __( 'French Southern Territories', 'advanced-ads-pro' ),
			'GA'      => __( 'Gabon', 'advanced-ads-pro' ),
			'GM'      => __( 'Gambia', 'advanced-ads-pro' ),
			'GE'      => __( 'Georgia', 'advanced-ads-pro' ),
			'DE'      => __( 'Germany', 'advanced-ads-pro' ),
			'GR'      => __( 'Greece', 'advanced-ads-pro' ),
			'GH'      => __( 'Ghana', 'advanced-ads-pro' ),
			'GI'      => __( 'Gibraltar', 'advanced-ads-pro' ),
			'GL'      => __( 'Greenland', 'advanced-ads-pro' ),
			'GD'      => __( 'Grenada', 'advanced-ads-pro' ),
			'GP'      => __( 'Guadeloupe', 'advanced-ads-pro' ),
			'GU'      => __( 'Guam', 'advanced-ads-pro' ),
			'GT'      => __( 'Guatemala', 'advanced-ads-pro' ),
			'GG'      => __( 'Guernsey', 'advanced-ads-pro' ),
			'GN'      => __( 'Guinea', 'advanced-ads-pro' ),
			'GW'      => __( 'Guinea-Bissau', 'advanced-ads-pro' ),
			'GY'      => __( 'Guyana', 'advanced-ads-pro' ),
			'HT'      => __( 'Haiti', 'advanced-ads-pro' ),
			'HM'      => __( 'Heard and McDonald Islands', 'advanced-ads-pro' ),
			'VA'      => __( 'Holy See (City Vatican State)', 'advanced-ads-pro' ),
			'HN'      => __( 'Honduras', 'advanced-ads-pro' ),
			'HK'      => __( 'Hong Kong', 'advanced-ads-pro' ),
			'HU'      => __( 'Hungary', 'advanced-ads-pro' ),
			'IS'      => __( 'Iceland', 'advanced-ads-pro' ),
			'IN'      => __( 'India', 'advanced-ads-pro' ),
			'ID'      => __( 'Indonesia', 'advanced-ads-pro' ),
			'IR'      => __( 'Iran', 'advanced-ads-pro' ),
			'IQ'      => __( 'Iraq', 'advanced-ads-pro' ),
			'IE'      => __( 'Ireland', 'advanced-ads-pro' ),
			'IM'      => __( 'Isle of Man', 'advanced-ads-pro' ),
			'IL'      => __( 'Israel', 'advanced-ads-pro' ),
			'IT'      => __( 'Italy', 'advanced-ads-pro' ),
			'JM'      => __( 'Jamaica', 'advanced-ads-pro' ),
			'JP'      => __( 'Japan', 'advanced-ads-pro' ),
			'JE'      => __( 'Jersey', 'advanced-ads-pro' ),
			'JO'      => __( 'Jordan', 'advanced-ads-pro' ),
			'KZ'      => __( 'Kazakhstan', 'advanced-ads-pro' ),
			'KE'      => __( 'Kenya', 'advanced-ads-pro' ),
			'KI'      => __( 'Kiribati', 'advanced-ads-pro' ),
			'KW'      => __( 'Kuwait', 'advanced-ads-pro' ),
			'KG'      => __( 'Kyrgyzstan', 'advanced-ads-pro' ),
			'LA'      => __( 'Lao People\'s Democratic Republic', 'advanced-ads-pro' ),
			'LV'      => __( 'Latvia', 'advanced-ads-pro' ),
			'LB'      => __( 'Lebanon', 'advanced-ads-pro' ),
			'LS'      => __( 'Lesotho', 'advanced-ads-pro' ),
			'LR'      => __( 'Liberia', 'advanced-ads-pro' ),
			'LY'      => __( 'Libyan Arab Jamahiriya', 'advanced-ads-pro' ),
			'LI'      => __( 'Liechtenstein', 'advanced-ads-pro' ),
			'LT'      => __( 'Lithuania', 'advanced-ads-pro' ),
			'LU'      => __( 'Luxembourg', 'advanced-ads-pro' ),
			'MO'      => __( 'Macau', 'advanced-ads-pro' ),
			'MK'      => __( 'Macedonia', 'advanced-ads-pro' ),
			'MG'      => __( 'Madagascar', 'advanced-ads-pro' ),
			'MW'      => __( 'Malawi', 'advanced-ads-pro' ),
			'MY'      => __( 'Malaysia', 'advanced-ads-pro' ),
			'MV'      => __( 'Maldives', 'advanced-ads-pro' ),
			'ML'      => __( 'Mali', 'advanced-ads-pro' ),
			'MT'      => __( 'Malta', 'advanced-ads-pro' ),
			'MH'      => __( 'Marshall Islands', 'advanced-ads-pro' ),
			'MQ'      => __( 'Martinique', 'advanced-ads-pro' ),
			'MR'      => __( 'Mauritania', 'advanced-ads-pro' ),
			'MU'      => __( 'Mauritius', 'advanced-ads-pro' ),
			'YT'      => __( 'Mayotte', 'advanced-ads-pro' ),
			'MX'      => __( 'Mexico', 'advanced-ads-pro' ),
			'FM'      => __( 'Micronesia', 'advanced-ads-pro' ),
			'MD'      => __( 'Moldova, Republic of', 'advanced-ads-pro' ),
			'MC'      => __( 'Monaco', 'advanced-ads-pro' ),
			'MN'      => __( 'Mongolia', 'advanced-ads-pro' ),
			'ME'      => __( 'Montenegro', 'advanced-ads-pro' ),
			'MS'      => __( 'Montserrat', 'advanced-ads-pro' ),
			'MA'      => __( 'Morocco', 'advanced-ads-pro' ),
			'MZ'      => __( 'Mozambique', 'advanced-ads-pro' ),
			'MM'      => __( 'Myanmar', 'advanced-ads-pro' ),
			'NA'      => __( 'Namibia', 'advanced-ads-pro' ),
			'NR'      => __( 'Nauru', 'advanced-ads-pro' ),
			'NP'      => __( 'Nepal', 'advanced-ads-pro' ),
			'NL'      => __( 'Netherlands', 'advanced-ads-pro' ),
			'AN'      => __( 'Netherlands Antilles', 'advanced-ads-pro' ),
			'NC'      => __( 'New Caledonia', 'advanced-ads-pro' ),
			'NZ'      => __( 'New Zealand', 'advanced-ads-pro' ),
			'NI'      => __( 'Nicaragua', 'advanced-ads-pro' ),
			'NE'      => __( 'Niger', 'advanced-ads-pro' ),
			'NG'      => __( 'Nigeria', 'advanced-ads-pro' ),
			'NU'      => __( 'Niue', 'advanced-ads-pro' ),
			'NF'      => __( 'Norfolk Island', 'advanced-ads-pro' ),
			'KR'      => __( 'North Korea', 'advanced-ads-pro' ),
			'MP'      => __( 'Northern Mariana Islands', 'advanced-ads-pro' ),
			'NO'      => __( 'Norway', 'advanced-ads-pro' ),
			'OM'      => __( 'Oman', 'advanced-ads-pro' ),
			'PK'      => __( 'Pakistan', 'advanced-ads-pro' ),
			'PW'      => __( 'Palau', 'advanced-ads-pro' ),
			'PS'      => __( 'Palestinian Territories', 'advanced-ads-pro' ),
			'PA'      => __( 'Panama', 'advanced-ads-pro' ),
			'PG'      => __( 'Papua New Guinea', 'advanced-ads-pro' ),
			'PY'      => __( 'Paraguay', 'advanced-ads-pro' ),
			'PE'      => __( 'Peru', 'advanced-ads-pro' ),
			'PH'      => __( 'Phillipines', 'advanced-ads-pro' ),
			'PN'      => __( 'Pitcairn Island', 'advanced-ads-pro' ),
			'PL'      => __( 'Poland', 'advanced-ads-pro' ),
			'PT'      => __( 'Portugal', 'advanced-ads-pro' ),
			'PR'      => __( 'Puerto Rico', 'advanced-ads-pro' ),
			'QA'      => __( 'Qatar', 'advanced-ads-pro' ),
			'XK'      => __( 'Republic of Kosovo', 'advanced-ads-pro' ),
			'RE'      => __( 'Reunion Island', 'advanced-ads-pro' ),
			'RO'      => __( 'Romania', 'advanced-ads-pro' ),
			'RU'      => __( 'Russian Federation', 'advanced-ads-pro' ),
			'RW'      => __( 'Rwanda', 'advanced-ads-pro' ),
			'BL'      => __( 'Saint Barth&eacute;lemy', 'advanced-ads-pro' ),
			'SH'      => __( 'Saint Helena', 'advanced-ads-pro' ),
			'KN'      => __( 'Saint Kitts and Nevis', 'advanced-ads-pro' ),
			'LC'      => __( 'Saint Lucia', 'advanced-ads-pro' ),
			'MF'      => __( 'Saint Martin (French)', 'advanced-ads-pro' ),
			'SX'      => __( 'Saint Martin (Dutch)', 'advanced-ads-pro' ),
			'PM'      => __( 'Saint Pierre and Miquelon', 'advanced-ads-pro' ),
			'VC'      => __( 'Saint Vincent and the Grenadines', 'advanced-ads-pro' ),
			'SM'      => __( 'San Marino', 'advanced-ads-pro' ),
			'ST'      => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'advanced-ads-pro' ),
			'SA'      => __( 'Saudi Arabia', 'advanced-ads-pro' ),
			'SN'      => __( 'Senegal', 'advanced-ads-pro' ),
			'RS'      => __( 'Serbia', 'advanced-ads-pro' ),
			'SC'      => __( 'Seychelles', 'advanced-ads-pro' ),
			'SL'      => __( 'Sierra Leone', 'advanced-ads-pro' ),
			'SG'      => __( 'Singapore', 'advanced-ads-pro' ),
			'SK'      => __( 'Slovak Republic', 'advanced-ads-pro' ),
			'SI'      => __( 'Slovenia', 'advanced-ads-pro' ),
			'SB'      => __( 'Solomon Islands', 'advanced-ads-pro' ),
			'SO'      => __( 'Somalia', 'advanced-ads-pro' ),
			'ZA'      => __( 'South Africa', 'advanced-ads-pro' ),
			'GS'      => __( 'South Georgia', 'advanced-ads-pro' ),
			'KP'      => __( 'South Korea', 'advanced-ads-pro' ),
			'SS'      => __( 'South Sudan', 'advanced-ads-pro' ),
			'ES'      => __( 'Spain', 'advanced-ads-pro' ),
			'LK'      => __( 'Sri Lanka', 'advanced-ads-pro' ),
			'SD'      => __( 'Sudan', 'advanced-ads-pro' ),
			'SR'      => __( 'Suriname', 'advanced-ads-pro' ),
			'SJ'      => __( 'Svalbard and Jan Mayen Islands', 'advanced-ads-pro' ),
			'SZ'      => __( 'Swaziland', 'advanced-ads-pro' ),
			'SE'      => __( 'Sweden', 'advanced-ads-pro' ),
			'CH'      => __( 'Switzerland', 'advanced-ads-pro' ),
			'SY'      => __( 'Syrian Arab Republic', 'advanced-ads-pro' ),
			'TW'      => __( 'Taiwan', 'advanced-ads-pro' ),
			'TJ'      => __( 'Tajikistan', 'advanced-ads-pro' ),
			'TZ'      => __( 'Tanzania', 'advanced-ads-pro' ),
			'TH'      => __( 'Thailand', 'advanced-ads-pro' ),
			'TL'      => __( 'Timor-Leste', 'advanced-ads-pro' ),
			'TG'      => __( 'Togo', 'advanced-ads-pro' ),
			'TK'      => __( 'Tokelau', 'advanced-ads-pro' ),
			'TO'      => __( 'Tonga', 'advanced-ads-pro' ),
			'TT'      => __( 'Trinidad and Tobago', 'advanced-ads-pro' ),
			'TN'      => __( 'Tunisia', 'advanced-ads-pro' ),
			'TR'      => __( 'Turkey', 'advanced-ads-pro' ),
			'TM'      => __( 'Turkmenistan', 'advanced-ads-pro' ),
			'TC'      => __( 'Turks and Caicos Islands', 'advanced-ads-pro' ),
			'TV'      => __( 'Tuvalu', 'advanced-ads-pro' ),
			'UG'      => __( 'Uganda', 'advanced-ads-pro' ),
			'UA'      => __( 'Ukraine', 'advanced-ads-pro' ),
			'AE'      => __( 'United Arab Emirates', 'advanced-ads-pro' ),
			'UY'      => __( 'Uruguay', 'advanced-ads-pro' ),
			'UM'      => __( 'US Minor Outlying Islands', 'advanced-ads-pro' ),
			'UZ'      => __( 'Uzbekistan', 'advanced-ads-pro' ),
			'VU'      => __( 'Vanuatu', 'advanced-ads-pro' ),
			'VE'      => __( 'Venezuela', 'advanced-ads-pro' ),
			'VN'      => __( 'Vietnam', 'advanced-ads-pro' ),
			'VG'      => __( 'Virgin Islands (British)', 'advanced-ads-pro' ),
			'VI'      => __( 'Virgin Islands (USA)', 'advanced-ads-pro' ),
			'WF'      => __( 'Wallis and Futuna Islands', 'advanced-ads-pro' ),
			'EH'      => __( 'Western Sahara', 'advanced-ads-pro' ),
			'WS'      => __( 'Western Samoa', 'advanced-ads-pro' ),
			'YE'      => __( 'Yemen', 'advanced-ads-pro' ),
			'ZM'      => __( 'Zambia', 'advanced-ads-pro' ),
			'ZW'      => __( 'Zimbabwe', 'advanced-ads-pro' ),
		];

		// remove continents, if Sucuri method is used
		// todo: needs more dynamic approach
		if ( 'sucuri' === Advanced_Ads_Geo_Plugin::get_current_targeting_method() ) {
			unset( $countries['CONT_NA'], $countries['CONT_SA'], $countries['CONT_EU'], $countries['CONT_AF'], $countries['CONT_AS'], $countries['CONT_AU'] );
		}

		return apply_filters( 'advanced-ads-geo-countries', $countries );
	}

	/*
	 * To change this license header, choose License Headers in Project Properties.
	 * To change this template file, choose Tools | Templates
	 * and open the template in the editor.
	 */

	public function get_GeoLite_country_filename() {
		if ( ! $upload_full = Advanced_Ads_Geo_Plugin::get_instance()->get_upload_full() ) {
			return false;
		}

		// Check if prefixed file exists.
		$prefix   = Advanced_Ads_Geo_Plugin::get_maxmind_file_prefix();
		$filename = $upload_full . $prefix . '-GeoLite2-Country.mmdb';
		if ( file_exists( $filename ) ) {
			return $filename;
		}

		// Check if unprefixed file exists.
		$filename = $upload_full . 'GeoLite2-Country.mmdb';
		if ( file_exists( $filename ) ) {
			return $filename;
		}

		return false;
	}

	public function get_GeoLite_city_filename() {
		if ( ! $upload_full = Advanced_Ads_Geo_Plugin::get_instance()->get_upload_full() ) {
			return false;
		}

		// Check if prefixed file exists.
		$prefix   = Advanced_Ads_Geo_Plugin::get_maxmind_file_prefix();
		$filename = $upload_full . $prefix . '-GeoLite2-City.mmdb';
		if ( file_exists( $filename ) ) {
			return $filename;
		}

		// Check if unprefixed file exists.
		$filename = $upload_full . 'GeoLite2-City.mmdb';
		if ( file_exists( $filename ) ) {
			return $filename;
		}

		return false;
	}

	public function get_GeoIP2Country_code( $ip_address ) {
		// Now get the location information from the MaxMind database.
		try {
			$reader = $this->get_GeoIP2Country_reader();

			// todo: return default country

			// Look up the IP address
			$record = $reader->country( $ip_address );

			// Get the location.
			$location = $record->country->isoCode;

			// MaxMind returns a blank for location if it can't find it, but we want to use get shop country to replace it.
			// todo: return default country
			return $location;
		} catch ( \InvalidArgumentException $e ) {
			error_log( 'InvalidArgumentException: ' . $e->getMessage() );
			return false;
		} catch ( AddressNotFoundException $e ) {
			error_log( 'AddressNotFoundException: ' . $e->getMessage() );
			return false;
		} catch ( Exception $e ) {
			return false;
		}
	}

	public function get_GeoIP2_city_reader() {
		// Now get the location information from the MaxMind database.
		try {
			$filename = $this->get_GeoLite_city_filename();
			if ( $filename === false ) {
				return false;
			}

			// Create a new Reader and point it to the database.
			return new Reader( $filename );
		} catch ( Exception $e ) {
			return false;
		}
	}

	public function get_GeoIP2_country_reader() {
		// get the location information from the MaxMind database.
		try {
			$filename = $this->get_GeoLite_country_filename();
			if ( $filename === false ) {
				return false;
			}

				// Create a new Reader and point it to the database.
				return new Reader( $filename );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Try to find a valid IP address.
	 *
	 * If ADVANCED_ADS_GEO_TEST_IP is defined use this.
	 * Otherwise @see self::get_raw_IP_address().
	 *
	 * If there are multiple IP addresses found, split the string and remove potential ports from IPv4 addresses.
	 * Validate them using the built-in filter_var() function.
	 * Since each proxy probably appended their IP at the end, use the first IP address in the array.
	 * Allow the user to filter the IP address and assign it to a class-member, so it will be short-circuited on successive calls.
	 *
	 * @return string
	 */
	public function get_real_IP_address() {
		if ( isset( $this->current_ip ) ) {
			return $this->current_ip;
		}

		$ip = defined( 'ADVANCED_ADS_GEO_TEST_IP' ) ? ADVANCED_ADS_GEO_TEST_IP : $this->get_raw_IP_address();

		$ip_array = array_map( function( $ip ) {
			$ip = trim( $ip );
			if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
				return $ip;
			}

			return preg_replace( '/([^:]+):\d+/', "$1", $ip );
		}, explode( ',', $ip ) );

		$ip_array = array_filter( $ip_array, function( $ip ) {
			return filter_var( $ip, FILTER_VALIDATE_IP );
		} );

		$ip = reset( $ip_array );

		/**
		 * Filter the found IP address.
		 *
		 * @param string $ip The last found IP address.
		 */
		return $this->current_ip = (string) apply_filters( 'get-ip-address', $ip );
	}

	/**
	 * Get IP address from various sources
	 *
	 * @return string
	 */
	public function get_raw_IP_address() {
		if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {  // cloudflare proxy, see https://support.cloudflare.com/hc/en-us/articles/200170876-I-can-t-install-mod-cloudflare-and-there-s-no-plugin-to-restore-original-visitor-IP-What-should-I-do-
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {  // IP from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {   // IP from shared internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * Check if the given country code belongs to an EU state
	 *
	 * @param str $country two letter country code
	 * @return bool true if is EU member state
	 */
	public function is_eu_state( $country = '' ) {
		return in_array( $country, self::$eu_states );
	}

}
