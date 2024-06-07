<?php

/**
 * Video Player Widget.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Widget_Video class.
 *
 * @since 1.0.0
 */
class AIOVG_Widget_Video extends WP_Widget {
	
	/**
     * Unique identifier for the widget.
     *
     * @since  1.0.0
	 * @access protected
     * @var    string
     */
    protected $widget_slug;
	
	/**
	 * Get things started.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {		
		$this->widget_slug = 'aiovg-widget-video';
		
		$options = array( 
			'classname'   => $this->widget_slug,
			'description' => __( 'Display a video player.', 'all-in-one-video-gallery' ),
		);
		
		parent::__construct( $this->widget_slug, __( 'AIOVG - Video Player', 'all-in-one-video-gallery' ), $options );
		
		add_action( 'wp_ajax_aiovg_autocomplete_get_videos', array( $this, 'ajax_callback_autocomplete_get_videos' ) );	
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.0.0
	 * @param array	$args	  The array of form elements.
	 * @param array $instance The current instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// Vars
		$post_id = 0;
				
		if ( ! empty( $instance['id'] ) ) {
			$post_id = (int) $instance['id'];
		} else {
			$query_args = array(				
				'post_type' => 'aiovg_videos',			
				'post_status' => 'publish',
				'posts_per_page' => 1,
				'fields' => 'ids',
				'no_found_rows' => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false
			);
	
			$aiovg_query = new WP_Query( $query_args );
			
			if ( $aiovg_query->have_posts() ) {
				$posts = $aiovg_query->posts;
				$post_id = (int) $posts[0];
			}
		}

		// Process output
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		the_aiovg_player( $post_id, $instance );
	
		echo $args['after_widget'];
	}
	
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since 1.0.0
	 * @param array $new_instance The new instance of values to be generated via the update.
	 * @param array $old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title']      = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['id']         = isset( $new_instance['id'] ) ? (int) $new_instance['id'] : '';
		$instance['width']      = isset( $new_instance['width'] ) ? sanitize_text_field( $new_instance['width'] ) : '';
		$instance['ratio']      = isset( $new_instance['ratio'] ) ? (float) $new_instance['ratio'] : 0;		
		$instance['autoplay']   = isset( $new_instance['autoplay'] ) ? (int) $new_instance['autoplay'] : 0;	
		$instance['loop']       = isset( $new_instance['loop'] ) ? (int) $new_instance['loop'] : 0;
		$instance['muted']      = isset( $new_instance['muted'] ) ? (int) $new_instance['muted'] : 0;	
		$instance['playpause']  = isset( $new_instance['playpause'] ) ? (int) $new_instance['playpause'] : 0;
		$instance['current']    = isset( $new_instance['current'] ) ? (int) $new_instance['current'] : 0;
		$instance['progress']   = isset( $new_instance['progress'] ) ? (int) $new_instance['progress'] : 0;	
		$instance['duration']   = isset( $new_instance['duration'] ) ? (int) $new_instance['duration'] : 0;
		$instance['tracks']     = isset( $new_instance['tracks'] ) ? (int) $new_instance['tracks'] : 0;	
		$instance['chapters']   = isset( $new_instance['chapters'] ) ? (int) $new_instance['chapters'] : 0;		
		$instance['speed']      = isset( $new_instance['speed'] ) ? (int) $new_instance['speed'] : 0;
		$instance['quality']    = isset( $new_instance['quality'] ) ? (int) $new_instance['quality'] : 0;
		$instance['volume']     = isset( $new_instance['volume'] ) ? (int) $new_instance['volume'] : 0;
		$instance['fullscreen'] = isset( $new_instance['fullscreen'] ) ? (int) $new_instance['fullscreen'] : 0;
		$instance['share']      = isset( $new_instance['share'] ) ? (int) $new_instance['share'] : 0;
		$instance['embed']      = isset( $new_instance['embed'] ) ? (int) $new_instance['embed'] : 0;
		$instance['download']   = isset( $new_instance['download'] ) ? (int) $new_instance['download'] : 0;
		
		return $instance;
	}
	
	/**
	 * Generates the administration form for the widget.
	 *
	 * @since 1.0.0
	 * @param array $instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {
		$player_settings = get_option( 'aiovg_player_settings' );
		
		// Define the array of defaults
		$defaults = array(
			'title'      => '',
			'id'         => 0,
			'width'      => $player_settings['width'],
			'ratio'      => $player_settings['ratio'],
			'autoplay'   => $player_settings['autoplay'],
			'loop'       => $player_settings['loop'],
			'muted'      => $player_settings['muted'],
			'playpause'  => isset( $player_settings['controls']['playpause'] ) ? 1 : 0,
			'current'    => isset( $player_settings['controls']['current'] ) ? 1 : 0,
			'progress'   => isset( $player_settings['controls']['progress'] ) ? 1 : 0,
			'duration'   => isset( $player_settings['controls']['duration'] ) ? 1 : 0,
			'tracks'     => isset( $player_settings['controls']['tracks'] ) ? 1 : 0,
			'chapters'   => isset( $player_settings['controls']['chapters'] ) ? 1 : 0,			
			'speed'      => isset( $player_settings['controls']['speed'] ) ? 1 : 0,
			'quality'    => isset( $player_settings['controls']['quality'] ) ? 1 : 0,
			'volume'     => isset( $player_settings['controls']['volume'] ) ? 1 : 0,
			'fullscreen' => isset( $player_settings['controls']['fullscreen'] ) ? 1 : 0,
			'share'      => isset( $player_settings['controls']['share'] ) ? 1 : 0,
			'embed'      => isset( $player_settings['controls']['embed'] ) ? 1 : 0,
			'download'   => isset( $player_settings['controls']['download'] ) ? 1 : 0
		);
		
		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array) $instance,
			$defaults
		);
			
		// Display the admin form
		include AIOVG_PLUGIN_DIR . 'widgets/forms/video.php';
	}

	/**
	 * Autocomplete UI: Get videos.
	 *
	 * @since 2.5.5
	 */
	public function ajax_callback_autocomplete_get_videos() {	
		// Security check
		check_ajax_referer( 'aiovg_ajax_nonce', 'security' );

		// Proceed safe		
		$args = array(
			'post_type'              => 'aiovg_videos',
			'post_status'            => 'publish',
			'numberposts'            => 100,
			'cache_results'          => false,  
    		'update_post_meta_cache' => false, 
    		'update_post_term_cache' => false
		);

		if ( isset( $_POST['term'] ) && ! empty( $_POST['term'] ) ) {
			$args['s'] = sanitize_text_field( $_POST['term'] );
		} else {
			$args['orderby']     = 'title';
			$args['order']       = 'ASC';
			$args['numberposts'] = 10;
		}

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			$posts = array(
				array(
					'ID'         => 0,
					'post_title' => __( 'No videos found. Click this to display the last added video.', 'all-in-one-video-gallery' )
				)
			);
		}

		echo wp_json_encode( $posts );
		wp_die();			
	}
	
}