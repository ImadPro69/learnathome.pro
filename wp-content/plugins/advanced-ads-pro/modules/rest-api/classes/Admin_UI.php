<?php

namespace Advanced_Ads_Pro\Rest_Api;

/**
 * Admin UI for the REST API.
 */
class Admin_UI {
	/**
	 * The Rest_Api object.
	 *
	 * @var Rest_Api
	 */
	private $rest_api;

	/**
	 * Constructor.
	 *
	 * @param Rest_Api $rest_api The Rest_Api object.
	 */
	public function __construct( Rest_Api $rest_api ) {
		$this->rest_api = $rest_api;
	}

	/**
	 * Register the settings field for the REST API module.
	 *
	 * @return void
	 */
	public function settings_init() {
		add_settings_field(
			'module-rest-api',
			__( 'REST API', 'advanced-ads-pro' ),
			[ $this, 'render_settings' ],
			\Advanced_Ads_Pro::OPTION_KEY . '-settings',
			\Advanced_Ads_Pro::OPTION_KEY . '_modules-enable'
		);
	}

	/**
	 * Setup and render the REST API module setting.
	 *
	 * @return void
	 */
	public function render_settings() {
		$module_enabled = $this->rest_api->is_enabled();
		$option_key     = \Advanced_Ads_Pro::OPTION_KEY . '[rest-api]';

		require_once __DIR__ . '/../views/module-enable.php';
	}
}
