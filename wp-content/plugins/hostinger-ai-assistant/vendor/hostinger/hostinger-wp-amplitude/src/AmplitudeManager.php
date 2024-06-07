<?php

namespace Hostinger\Amplitude;

use Hostinger\WpHelper\Config;
use Hostinger\WpHelper\Requests\Client;
use Hostinger\WpHelper\Utils as Helper;

class AmplitudeManager {
	public const AMPLITUDE_ENDPOINT = '/v3/wordpress/plugin/trigger-event';
	private const CACHE_ONE_DAY = 86400;

	private Config $configHandler;
	private Client $client;
	private Helper $helper;

	public function __construct(
		Helper $helper,
		Config $configHandler,
		Client $client
	) {
		$this->helper        = $helper;
		$this->configHandler = $configHandler;
		$this->client        = $client;
	}

	public function sendRequest( string $endpoint, array $params ): array {
		try {
			if ( isset( $params['action'] ) && isset( $params['location'] ) && ! $this->shouldSendAmplitudeEvent( $params['action'], $params['location'] ) ) {
				return array();
			}

			$response = $this->client->post( $endpoint, array( 'params' => $params ) );
			return $response;
		} catch ( \Exception $exception ) {
			$this->helper->errorLog( 'Error sending request: ' . $exception->getMessage() );
			return ['status' => 'error', 'message' => $exception->getMessage()];
		}
	}

	// Events which firing once per day
	public static function getSingleAmplitudeEvents(): array {
		return apply_filters( 'hostinger_onece_per_day_events', [] );
	}

	public function shouldSendAmplitudeEvent( string $eventAction, string $location ): bool {
		$oneTimePerDay = self::getSingleAmplitudeEvents();

		$eventAction = sanitize_text_field( $eventAction );

		if ( in_array( $eventAction, $oneTimePerDay ) && get_transient( $eventAction . '-' . $location ) ) {
			return false;
		}

		if ( in_array( $eventAction, $oneTimePerDay ) ) {
			set_transient( $eventAction . '-' . $location, true, self::CACHE_ONE_DAY );
		}

		return true;
	}
}
