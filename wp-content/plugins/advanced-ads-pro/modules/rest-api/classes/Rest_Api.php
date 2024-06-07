<?php

namespace Advanced_Ads_Pro\Rest_Api;

/**
 * Advanced Ads Rest API.
 */
class Rest_Api {
	/**
	 * Injected instance of the current Advanced_Ads_Pro instance.
	 *
	 * @var \Advanced_Ads_Pro
	 */
	private $advanced_ads_pro;

	/**
	 * Set the namespace for the endpoints.
	 *
	 * @const string
	 */
	const NS = 'advanced-ads/v1';

	/**
	 * Constructor.
	 *
	 * @param \Advanced_Ads_Pro $advanced_ads_pro The current Advanced_Ads_Pro instance.
	 */
	public function __construct( \Advanced_Ads_Pro $advanced_ads_pro ) {
		$this->advanced_ads_pro = $advanced_ads_pro;
	}

	/**
	 * Check if the REST API module is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return ! empty( $this->advanced_ads_pro->get_options()['rest-api']['enabled'] );
	}

	/**
	 * Register all REST routes.
	 * Ad list, single ad. Group list, single group.
	 *
	 * @return void
	 */
	public function register_rest_routes() {

		// register the main ad list
		register_rest_route(
			self::NS,
			'/ads',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_ad_list' ],
				'permission_callback' => '__return_true',
				'args'                => Rest_Query_Params_Helper::get_list_args(),
			]
		);

		// register the endpoint to get an individual ad
		register_rest_route(
			self::NS,
			'/ads/(?<id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_ad' ],
				'args'                => [
					'id' => [
						'validate_callback' => function( $param ) {
							return is_numeric( $param );
						},
					],
				],
				'permission_callback' => '__return_true',
			]
		);

		// register the endpoint to get ad groups
		register_rest_route(
			self::NS,
			'/groups',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_group_list' ],
				'permission_callback' => '__return_true',
				'args'                => Rest_Query_Params_Helper::get_list_args(),
			]
		);

		// register the endpoint to get a single ad group
		register_rest_route(
			self::NS,
			'/groups/(?<id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_group' ],
				'args'                => [
					'id' => [
						'validate_callback' => function( $param ) {
							return is_numeric( $param );
						},
					],
				],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Get single ad callback.
	 * If the passed id is not an Advanced_Ads_Ad, return a WP_Error.
	 *
	 * @param \WP_REST_Request $request The current request object.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_ad( \WP_REST_Request $request ) {
		$id = (int) $request->get_param( 'id' );

		try {
			$ad = new Advanced_Ads_Ad( $id );
		} catch ( Rest_Exception $e ) {
			return unserialize( $e->getMessage() );
		}

		/**
		 * Trigger an action when a single ad is requested via the API.
		 *
		 * @var Advanced_Ads_Ad $ad The requested ad.
		 */
		do_action( 'advanced-ads-rest-ad-request', $ad );

		return new \WP_REST_Response( $ad->get_rest_response() );
	}

	/**
	 * Get ad list callback.
	 * Return WP_Error if paging is incorrect.
	 * Set paging headers on successful request.
	 *
	 * @param \WP_REST_Request $request The current request object.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_ad_list( \WP_REST_Request $request ) {
		try {
			$ads = new Rest_Ads_Query( $request->get_query_params() );
		} catch ( Rest_Exception $e ) {
			return unserialize( $e->getMessage() );
		}

		return new \WP_REST_Response( $ads->get_ads(), 200, [
			'X-WP-Total'      => $ads->found_posts,
			'X-WP-TotalPages' => $ads->max_num_pages,
		] );
	}

	/**
	 * Get group list callback.
	 * Return WP_Error if paging is incorrect.
	 * Set paging headers on successful request.
	 *
	 * @param \WP_REST_Request $request The current request object.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_group_list( \WP_REST_Request $request ) {
		try {
			$groups = new Rest_Groups_Query( $request->get_query_params() );
		} catch ( Rest_Exception $e ) {
			return unserialize( $e->getMessage() );
		}

		return new \WP_REST_Response( $groups->get_groups(), 200, [
			'X-WP-Total'      => $groups->found_terms(),
			'X-WP-TotalPages' => $groups->max_num_pages(),
		] );
	}

	/**
	 * Get single group callback.
	 * Return WP_Error if passed id is not an Advanced_Ads_Group.
	 *
	 * @param \WP_REST_Request $request The current request object.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_group( \WP_REST_Request $request ) {
		try {
			return new \WP_REST_Response(
				( new Advanced_Ads_Group( $request->get_param( 'id' ) ) )->get_rest_response()
			);
		} catch ( Rest_Exception $e ) {
			return unserialize( $e->getMessage() );
		}
	}
}
