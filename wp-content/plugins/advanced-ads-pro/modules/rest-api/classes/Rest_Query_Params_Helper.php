<?php

namespace Advanced_Ads_Pro\Rest_Api;

/**
 * Helper class to register and parse request parameters.
 */
class Rest_Query_Params_Helper {
	/**
	 * Parse and populate default parameters.
	 *
	 * @param array $query_params The user-supplied request parameters.
	 *
	 * @return array
	 */
	public static function setup_query_params( array $query_params ) {
		$parameters = [
			'paged'          => null,
			'offset'         => null,
			'order'          => null,
			'orderby'        => null,
			'posts_per_page' => (int) ( isset( $query_params['per_page'] ) ? $query_params['per_page'] : get_option( 'posts_per_page' ) ),
		];

		// get paged request
		if ( isset( $query_params['page'] ) && (int) $query_params['page'] > 1 ) {
			$parameters['paged'] = (int) $query_params['page'];
		}

		// get offset
		if ( isset( $query_params['offset'] ) ) {
			$parameters['offset'] = (int) $query_params['offset'];
		};

		// get order
		if ( isset( $query_params['order'] ) ) {
			$order = strtoupper( $query_params['order'] );
			if ( in_array( $order, [ 'ASC', 'DESC' ], true ) ) {
				$parameters['order'] = $order;
			}
		}

		// get order by
		if ( isset( $query_params['orderby'] ) ) {
			$parameters['orderby'] = $query_params['orderby'];
		}

		return $parameters;
	}

	/**
	 * Default arguments for list request. Isolated from WP core requests.
	 *
	 * phpcs:disable WordPress.WP.I18n.MissingArgDomain -- we're re-using core translations here.
	 *
	 * @return array[]
	 */
	public static function get_list_args() {
		return [
			'page'     => [
				'description'       => __( 'Current page of the collection.' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
			],
			'per_page' => [
				'description'       => __( 'Maximum number of items to be returned in result set.' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			],
			'offset'   => [
				'description'       => __( 'Offset the result set by a specific number of items.' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			],
			'order'    => [
				'description'       => __( 'Order sort attribute ascending or descending.' ),
				'type'              => 'string',
				'default'           => 'desc',
				'enum'              => [
					'asc',
					'desc',
				],
				'validate_callback' => 'rest_validate_request_arg',
			],
			'orderby'  => [
				'description'       => __( 'Sort collection by object attribute.' ),
				'type'              => 'string',
				'default'           => 'date',
				'enum'              => [
					'date',
					'title',
					'slug',
				],
				'validate_callback' => 'rest_validate_request_arg',
			],
		];
	}
}
