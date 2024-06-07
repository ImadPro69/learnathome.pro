<?php

/**
 * Blocks Initializer.
 *
 * @link    https://plugins360.com
 * @since   1.5.6
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Blocks class.
 *
 * @since 1.5.6
 */
class AIOVG_Blocks {

	/**
	 * Register our custom Gutenberg block category.
	 *
	 * @since  1.5.6
	 * @param  array $categories Default Gutenberg block categories.
	 * @return array             Modified Gutenberg block categories.
	 */
	public function block_categories( $categories ) {		
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'all-in-one-video-gallery',
					'title' => __( 'All-in-One Video Gallery', 'all-in-one-video-gallery' ),
				)
			)
		);		
	}

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @since 1.5.6
	 */
	public function enqueue_block_editor_assets() {	
		$categories_settings = get_option( 'aiovg_categories_settings' );
		$videos_settings     = get_option( 'aiovg_videos_settings' );
		$player_settings     = get_option( 'aiovg_player_settings' );

		$fields = aiovg_get_shortcode_fields();
		$videos = $fields['videos']['sections'];

		foreach ( $videos as $key => $section ) {
			foreach ( $section['fields'] as $_key => $field ) {
				if ( in_array( $field['name'], array( 'title', 'include', 'exclude', 'ratio', 'title_length', 'excerpt_length' ) ) ) {
					unset( $videos[ $key ]['fields'][ $_key ] );
					continue;
				}

				if ( isset( $field['description'] ) ) {
					$videos[ $key ]['fields'][ $_key ]['description'] = strip_tags( $field['description'] );
				}
			}
		}

		$editor_properties = array(
			'i18n' => array(
				'block_categories_title'       => __( 'AIOVG - Video Categories', 'all-in-one-video-gallery' ),
				'block_categories_description' => __( 'Display a list of video categories.', 'all-in-one-video-gallery' ),
				'select_parent'                => __( 'Select Parent', 'all-in-one-video-gallery' ),
				'select_template'              => __( 'Select Template', 'all-in-one-video-gallery' ),
				'grid'                         => __( 'Grid', 'all-in-one-video-gallery' ),
				'list'                         => __( 'List', 'all-in-one-video-gallery' ),
				'dropdown'                     => __( 'Dropdown', 'all-in-one-video-gallery' ),
				'columns'                      => __( 'Columns', 'all-in-one-video-gallery' ),
				'limit'                        => __( 'Limit (per page)', 'all-in-one-video-gallery' ),
				'order_by'                     => __( 'Order By', 'all-in-one-video-gallery' ),
				'id'                           => __( 'ID', 'all-in-one-video-gallery' ),
				'count'                        => __( 'Count', 'all-in-one-video-gallery' ),
				'name'                         => __( 'Name', 'all-in-one-video-gallery' ),
				'slug'                         => __( 'Slug', 'all-in-one-video-gallery' ),
				'menu_order'                   => __( 'Menu Order', 'all-in-one-video-gallery' ),
				'order'                        => __( 'Order', 'all-in-one-video-gallery' ),
				'asc'                          => __( 'ASC', 'all-in-one-video-gallery' ),
				'desc'                         => __( 'DESC', 'all-in-one-video-gallery' ),
				'show_hierarchy'               => __( 'Show Hierarchy', 'all-in-one-video-gallery' ),
				'show_description'             => __( 'Show Description', 'all-in-one-video-gallery' ),
				'show_videos_count'            => __( 'Show Videos Count', 'all-in-one-video-gallery' ),
				'hide_empty_categories'        => __( 'Hide Empty Categories', 'all-in-one-video-gallery' ),
				'show_pagination'              => __( 'Show Pagination', 'all-in-one-video-gallery' ),
				'block_videos_title'           => __( 'AIOVG - Video Gallery', 'all-in-one-video-gallery' ),
				'block_videos_description'     => __( 'Display a video gallery.', 'all-in-one-video-gallery' ),
				'select_color'                 => __( 'Selected Color', 'all-in-one-video-gallery' ),
				'block_search_title'           => __( 'AIOVG - Search Form', 'all-in-one-video-gallery' ),
				'block_search_description'     => __( 'A videos search form for your site.', 'all-in-one-video-gallery' ),
				'vertical'                     => __( 'Vertical', 'all-in-one-video-gallery' ),
				'horizontal'                   => __( 'Horizontal', 'all-in-one-video-gallery' ),
				'search_by_categories'         => __( 'Search By Categories', 'all-in-one-video-gallery' ),
				'search_by_keywords'           => __( 'Search By Video Title, Description', 'all-in-one-video-gallery' ),
				'search_by_tags'               => __( 'Search By Tags', 'all-in-one-video-gallery' ),
				'block_video_title'            => __( 'AIOVG - Video Player', 'all-in-one-video-gallery' ),
				'block_video_description'      => __( 'Display a video player.', 'all-in-one-video-gallery' ),
				'media_placeholder_title'      => __( 'Supports: MP4, WebM, OGV, HLS, MPEG-DASH, YouTube, Vimeo, Dailymotion, Rumble, Facebook, etc.', 'all-in-one-video-gallery' ),
				'media_placeholder_name'       => __( 'a video', 'all-in-one-video-gallery' ),
				'edit_video'                   => __( 'Edit Video', 'all-in-one-video-gallery' ),
				'general_settings'             => __( 'General', 'all-in-one-video-gallery' ),
				'width'                        => __( 'Width', 'all-in-one-video-gallery' ),
				'width_help'                   => __( 'In pixels. Maximum width of the player. Leave this field empty to scale 100% of its enclosing container/html element.', 'all-in-one-video-gallery' ),
				'ratio'                        => __( 'Height (Ratio)', 'all-in-one-video-gallery' ),
				'ratio_help'                   => __( "In percentage. 1 to 100. Calculate player's height using the ratio value entered.", 'all-in-one-video-gallery' ),
				'autoplay'                     => __( 'Autoplay', 'all-in-one-video-gallery' ),
				'loop'                         => __( 'Loop', 'all-in-one-video-gallery' ),
				'muted'                        => __( 'Muted', 'all-in-one-video-gallery' ),
				'poster_image'                 => __( 'Poster Image', 'all-in-one-video-gallery' ),
				'select_image'                 => __( 'Select', 'all-in-one-video-gallery' ),				
				'remove_image'                 => __( 'Remove', 'all-in-one-video-gallery' ),
				'replace_image'                => __( 'Replace', 'all-in-one-video-gallery' ),
				'player_controls'              => __( 'Player Controls', 'all-in-one-video-gallery' ),
				'play_pause'                   => __( 'Play / Pause', 'all-in-one-video-gallery' ),
				'current_time'                 => __( 'Current Time', 'all-in-one-video-gallery' ),
				'progressbar'                  => __( 'Progressbar', 'all-in-one-video-gallery' ),
				'duration'                     => __( 'Duration', 'all-in-one-video-gallery' ),				
				'speed'                        => __( 'Speed Control', 'all-in-one-video-gallery' ),
				'quality'                      => __( 'Quality Selector', 'all-in-one-video-gallery' ),
				'volume'                       => __( 'Volume Button', 'all-in-one-video-gallery' ),
				'fullscreen'                   => __( 'Fullscreen Button', 'all-in-one-video-gallery' ),
				'share'                        => __( 'Share Buttons', 'all-in-one-video-gallery' ),
				'embed'                        => __( 'Embed Button', 'all-in-one-video-gallery' ),
				'download'                     => __( 'Download Button', 'all-in-one-video-gallery' )
			),
			'categories' => array(
				'template'         => $categories_settings['template'],
				'id'               => 0,				
				'columns'          => $categories_settings['columns'],
				'limit'            => $categories_settings['limit'],
				'orderby'          => $categories_settings['orderby'],
				'order'            => $categories_settings['order'],
				'hierarchical'     => $categories_settings['hierarchical'],
				'show_description' => $categories_settings['show_description'],
				'show_count'       => $categories_settings['show_count'],
				'hide_empty'       => $categories_settings['hide_empty'],
				'show_pagination'  => 1
			),			
			'video'	=> array(
				'src'              => '',
				'id'               => 0,
				'poster'           => '',
				'width'            => 0,
				'ratio'            => $player_settings['ratio'],
				'autoplay'         => $player_settings['autoplay'] ? true : false,
				'loop'             => $player_settings['loop'] ? true : false,
				'muted'            => $player_settings['muted'] ? true : false,
				'playpause'        => isset( $player_settings['controls']['playpause'] ),
				'current'          => isset( $player_settings['controls']['current'] ),
				'progress'         => isset( $player_settings['controls']['progress'] ),
				'duration'         => isset( $player_settings['controls']['duration'] ),				
				'speed'            => isset( $player_settings['controls']['speed'] ),
				'quality'          => isset( $player_settings['controls']['quality'] ),					
				'volume'           => isset( $player_settings['controls']['volume'] ),
				'fullscreen'       => isset( $player_settings['controls']['fullscreen'] ),
				'share'            => isset( $player_settings['controls']['share'] ),
				'embed'            => isset( $player_settings['controls']['embed'] ),
				'download'         => isset( $player_settings['controls']['download'] )
			),
			'videos' => $videos
		);

		wp_localize_script( 'wp-block-editor', 'aiovg_blocks', $editor_properties );	

		do_action( 'aiovg_enqueue_block_editor_assets' );
	}		

	/**
	 * Register our custom blocks.
	 * 
	 * @since 1.5.6
	 */
	public function register_block_types() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return false;
		}

		$this->register_categories_block();
		$this->register_search_block();
		$this->register_video_block();
		$this->register_videos_block();		
	}

	/**
	 * Register the categories block.
	 *
	 * @since 3.0.0
	 */
	private function register_categories_block() {
		$attributes = array(			
			'template' => array(
				'type' => 'string'
			),
			'id' => array(
				'type' => 'number'
			),
			'columns' => array(
				'type' => 'number'
			),
			'limit' => array(
				'type' => 'number'
			),
			'orderby' => array(
				'type' => 'string'
			),
			'order' => array(
				'type' => 'string'
			),
			'hierarchical' => array(
				'type' => 'boolean'
			),
			'show_description' => array(
				'type' => 'boolean'
			),
			'show_count' => array(
				'type' => 'boolean'
			),
			'hide_empty' => array(
				'type' => 'boolean'
			),
			'show_pagination' => array(
				'type' => 'boolean'
			)
		);

		register_block_type( __DIR__ . '/build/categories', array(
			'attributes' => $attributes,
			'render_callback' => array( $this, 'render_categories_block' )
		) );
	}	

	/**
	 * Register the search block.
	 *
	 * @since 3.0.0
	 */
	private function register_search_block() {
		register_block_type( __DIR__ . '/build/search', array(
			'render_callback' => array( $this, 'render_search_block' ),
		) );
	}	

	/**
	 * Register the video block.
	 *
	 * @since 3.0.0
	 */
	private function register_video_block() {
		$attributes = array(
			'src' => array(
				'type' => 'string'
			),
			'id' => array(
				'type' => 'number'
			),
			'poster' => array(
				'type' => 'string'
			),
			'width' => array(
				'type' => 'number'
			),
			'ratio' => array(
				'type' => 'number'
			),
			'autoplay' => array(
				'type' => 'boolean'
			),
			'loop' => array(
				'type' => 'boolean'
			),
			'muted' => array(
				'type' => 'boolean'
			),
			'playpause' => array(
				'type' => 'boolean'
			),
			'current' => array(
				'type' => 'boolean'
			),
			'progress' => array(
				'type' => 'boolean'
			),
			'duration' => array(
				'type' => 'boolean'
			),			
			'speed' => array(
				'type' => 'boolean'
			),
			'quality' => array(
				'type' => 'boolean'
			),				
			'volume' => array(
				'type' => 'boolean'
			),
			'fullscreen' => array(
				'type' => 'boolean'
			),
			'share' => array(
				'type' => 'boolean'
			),
			'embed' => array(
				'type' => 'boolean'
			),
			'download' => array(
				'type' => 'boolean'
			)
		);

		register_block_type( __DIR__ . '/build/video', array(
			'attributes' => $attributes,
			'render_callback' => array( $this, 'render_video_block' ),
		) );
	}	

	/**
	 * Register the videos block.
	 *
	 * @since 3.0.0
	 */
	private function register_videos_block() {
		$fields = aiovg_get_shortcode_fields();			
		$attributes = array();

		foreach ( $fields['videos']['sections'] as $key => $section ) {
			foreach ( $section['fields'] as $field ) {
				if ( in_array( $field['name'], array( 'title', 'exclude', 'ratio', 'title_length', 'excerpt_length' ) ) ) {
					continue;
				}

				if ( 'categories' == $field['type'] || 'tags' == $field['type'] ) {
					$attributes[ $field['name'] ] = array(
						'type'  => 'array',
						'items' => array(
							'type' => 'integer',
						)
					);
				} else {
					$type = 'string';

					if ( 'number' == $field['type'] ) {
						$type = 'number';
					} elseif ( 'checkbox' == $field['type'] ) {
						$type = 'boolean';
					}

					$attributes[ $field['name'] ] = array(
						'type' => $type
					);
				}
			}
		}

		register_block_type( __DIR__ . '/build/videos', array(
			'attributes' => $attributes,
			'render_callback' => array( $this, 'render_videos_block' ),
		) );
	}

	/**
	 * Render the categories block frontend.
	 *
	 * @since  1.5.6
	 * @param  array  $atts An associative array of attributes.
	 * @return string       HTML output.
	 */
	public function render_categories_block( $atts ) {
		$output  = '<div ' . get_block_wrapper_attributes() . '>';
		$output .= do_shortcode( '[aiovg_categories ' . $this->build_shortcode_attributes( $atts ) . ']' );
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render the search block frontend.
	 *
	 * @since  1.5.6
	 * @param  array  $atts An associative array of attributes.
	 * @return string       HTML output.
	 */
	public function render_search_block( $atts ) {
		$output  = '<div ' . get_block_wrapper_attributes() . '>';
		$output .= do_shortcode( '[aiovg_search_form ' . $this->build_shortcode_attributes( $atts ) . ']' );
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render the video player block frontend.
	 *
	 * @since  3.0.0
	 * @param  array  $atts An associative array of attributes.
	 * @return string       HTML output.
	 */
	public function render_video_block( $atts ) {
		if ( empty( $atts['src'] ) || false !== strpos( $atts['src'], 'blob:' ) ) {
			return '<p>&nbsp;</p>';
		}		

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$atts['player'] = 'iframe';
		}
		
		unset( $atts['id'] );

		// Output
		$output  = '<div ' . get_block_wrapper_attributes() . '>';
		$output .= do_shortcode( '[aiovg_video ' . $this->build_shortcode_attributes( $atts ) . ']' );
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render the videos block frontend.
	 *
	 * @since  1.5.6
	 * @param  array  $atts An associative array of attributes.
	 * @return string       HTML output.
	 */
	public function render_videos_block( $atts ) {	
		// Always get ratio from the global settings	
		if ( isset( $atts['ratio'] ) ) {
			unset( $atts['ratio'] );
		}	

		// Output
		$output  = '<div ' . get_block_wrapper_attributes() . '>';
		$output .= do_shortcode( '[aiovg_videos ' . $this->build_shortcode_attributes( $atts ) . ']' );
		$output .= '</div>';

		return $output;
	}	

	/**
	 * Build shortcode attributes string.
	 * 
	 * @since  1.5.6
	 * @access private
	 * @param  array   $atts Array of attributes.
	 * @return string        Shortcode attributes string.
	 */
	private function build_shortcode_attributes( $atts ) {
		$attributes = array();
		
		foreach ( $atts as $key => $value ) {
			if ( is_null( $value ) || is_object( $value ) ) {
				continue;
			}

			if ( is_bool( $value ) ) {
				$value = ( true === $value ) ? 1 : 0;
			}

			if ( is_array( $value ) ) {
				$value = implode( ',', $value );
			}

			$attributes[] = sprintf( '%s="%s"', $key, $value );
		}
		
		return implode( ' ', $attributes );
	}

}
