<?php
/**
 * Class Pimp_My_Site_Admin
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package pimp-my-site/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Pimp_My_Site_Admin
 *
 * @author themosaurus
 * @package pimp-my-site/admin
 */
class Pimp_My_Site_Admin {

	/**
	 * The class managing the plugin settings
	 *
	 * @var Pimp_My_Site_Settings
	 */
	public $settings;

	/**
	 * Pimp_My_Site_Admin constructor.
	 */
	public function __construct() {
		$this->settings = require 'settings/class-pimp-my-site-settings.php';
	}
}
