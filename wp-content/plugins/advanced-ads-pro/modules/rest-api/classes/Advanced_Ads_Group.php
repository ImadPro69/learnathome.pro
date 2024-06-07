<?php

namespace Advanced_Ads_Pro\Rest_Api;

/**
 * REST API extension for the base \Advanced_Ads_Group.
 */
class Advanced_Ads_Group extends \Advanced_Ads_Group {
	/**
	 * Constructor
	 *
	 * @param int $group The group term id.
	 *
	 * @throws Rest_Exception Throw an exception if the provided id is not a group.
	 */
	public function __construct( $group ) {
		parent::__construct( $group, [] );

		if ( $this->id === 0 ) {
			throw new Rest_Exception( serialize( new \WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid group ID.', 'advanced-ads-pro' ),
				[ 'status' => 404 ]
			) ) );
		}
	}

	/**
	 * Return group details for API response.
	 *
	 * @return array
	 */
	public function get_rest_response() {
		$ad_ids = $this->get_ordered_ad_ids();

		return [
			'ID'         => $this->id,
			'name'       => $this->name,
			'type'       => $this->type,
			'ads'        => $ad_ids,
			'ad_weights' => $this->get_ad_weights( $ad_ids ),
		];
	}
}
