<?php

namespace Advanced_Ads_Pro\Rest_Api;

/**
 * REST API extension of the \Advanced_Ads_Ad class.
 */
class Advanced_Ads_Ad extends \Advanced_Ads_Ad {
	/**
	 * Constructor.
	 *
	 * @param int $id The Post ID.
	 *
	 * @throws Rest_Exception Throw an exception if the provided id is not an ad.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, [] );

		add_filter( 'advanced-ads-tracking-link-attributes', [ $this, 'filter_tracking_attributes' ], 10, 2 );

		// throw an exception if this is not an ad. I.e., if an ID of another or inexistent object is passed.
		if ( ! $this->is_ad ) {
			throw new Rest_Exception( serialize( new \WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid ad ID.', 'advanced-ads-pro' ),
				[ 'status' => 404 ]
			) ) );
		}
	}

	/**
	 * Return ad details for API response.
	 *
	 * @return array
	 */
	public function get_rest_response() {
		return [
			'ID'              => $this->id,
			'title'           => $this->title,
			'type'            => $this->type,
			'start_date'      => get_post_datetime( $this->id )->getTimestamp(),
			'expiration_date' => $this->expiry_date,
			'content'         => $this->prepare_rest_output(),
		];
	}

	/**
	 * Parse the ad content according to ad type, but without adding any wrappers.
	 *
	 * @return string
	 */
	private function prepare_rest_output() {
		$user_supplied_content = $this->options( 'change-ad.content', false );
		if ( $user_supplied_content ) {
			// output was provided by the user.
			return $user_supplied_content;
		}

		// load ad type specific content filter.
		$output = $this->type_obj->prepare_output( $this );

		// remove superfluous whitespace
		$output = str_replace( [ "\n", "\r", "\t" ], ' ', $output );
		$output = preg_replace( '/\s+/', ' ', $output );

		/**
		 * Allow filtering of the API ad markup.
		 *
		 * @var string           $output The ad content.
		 * @var \Advanced_Ads_Ad $this   The current ad object.
		 */
		$output = (string) apply_filters( 'advanced-ads-rest-ad-content', $output, $this );

		return $output;
	}

	/**
	 * If tracking is active, filter the attributes to remove tracking-specific frontend attributes.
	 *
	 * @param array            $attributes Keys are attribute names, values their respective values.
	 * @param \Advanced_Ads_Ad $ad         The ad object. Check if it's the same as the current one.
	 *
	 * @return array
	 */
	public function filter_tracking_attributes( array $attributes, \Advanced_Ads_Ad $ad ) {
		if ( $this->id !== $ad->id ) {
			return $attributes;
		}

		unset(
			$attributes['data-bid'],
			$attributes['data-id'],
			$attributes['class']
		);

		return $attributes;
	}
}
