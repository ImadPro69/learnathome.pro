<?php

/**
 * Video Player.
 *
 * @link    https://plugins360.com
 * @since   2.4.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Player class.
 *
 * @since 2.4.0
 */
class AIOVG_Player {

	/**
	 * The only instance of the class.
	 *
	 * @since  2.4.0
	 * @static
	 * @var    AIOVG_Player	 
	 */
	public static $instance;

	/**
	 * Current player reference ID.
	 *
	 * @since  3.5.0
	 * @access private
	 * @var    int	 
	 */
	private $reference_id;

	/**
	 * Create a new instance of the main class.
	 *
	 * @since  2.4.0
	 * @static
	 * @return AIOVG_Player
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
            self::$instance = new self();
        }

		return self::$instance;
	}

	/**
	 * Get things started.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		$this->reference_id = 0;
	}

	/**
	 * Get the player HTML.
	 *
	 * @since  3.5.0
	 * @param  int    $post_id Post ID.
 	 * @param  array  $args    Player options.
 	 * @return string $html    Player HTML.
	 */
	public function create( $post_id, $args ) {	
		++$this->reference_id;
		$post_id = (int) $post_id;			
		
		$player_type = $this->get_player_type( $args );

		require_once AIOVG_PLUGIN_DIR . 'includes/player/base.php';

		switch ( $player_type ) {
			case 'videojs':
				require_once AIOVG_PLUGIN_DIR . 'includes/player/videojs.php';

				$player = new AIOVG_Player_VideoJS( $post_id, $args, $this->reference_id );
				$html = $player->get_player();
				break;

			case 'vidstack':
				require_once AIOVG_PLUGIN_DIR . 'includes/player/vidstack.php';

				$player = new AIOVG_Player_Vidstack( $post_id, $args, $this->reference_id );
				$html = $player->get_player();
				break;

			case 'amp':
				require_once AIOVG_PLUGIN_DIR . 'includes/player/amp.php';

				$player = new AIOVG_Player_AMP( $post_id, $args, $this->reference_id );
				$html = $player->get_player();
				break;

			case 'popup':
				require_once AIOVG_PLUGIN_DIR . 'includes/player/popup.php';

				$player = new AIOVG_Player_Popup( $post_id, $args, $this->reference_id );
				$html = $player->get_player();
				break;

			default:
				require_once AIOVG_PLUGIN_DIR . 'includes/player/iframe.php';

				$player = new AIOVG_Player_Iframe( $post_id, $args, $this->reference_id );
				$html = $player->get_player();
		}

		// Output
		$params = $player->get_params();
		return apply_filters( 'aiovg_player_html', $html, $params ); 
	}

	/**
	 * Get the player type.
	 *
	 * @since  3.5.0
	 * @access private
 	 * @param  array   $args Player options.
 	 * @return string        Player type.
	 */
	private function get_player_type( $args ) {
		if ( isset( $args['player'] ) && ! empty( $args['player'] ) ) {
			return $args['player'];
		}

		$player_settings = get_option( 'aiovg_player_settings' );

		$player_type = ( 'vidstack' == $player_settings['player'] ? 'vidstack' : 'videojs' );

		if ( empty( $player_settings['force_js_initialization'] ) ) {
			$player_type = 'iframe';
		}

		if ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) {
			$player_type = 'amp';
		}

		if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
			$player_type = 'amp';
		}

		// Output
		return $player_type;
	}
		
}
