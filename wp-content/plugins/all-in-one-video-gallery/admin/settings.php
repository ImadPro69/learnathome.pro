<?php

/**
 * Settings
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
 * AIOVG_Admin_Settings class.
 *
 * @since 1.0.0
 */
class AIOVG_Admin_Settings {

	/**
     * Settings tabs array.
     *
	 * @since  1.0.0
	 * @access protected
     * @var    array
     */
    protected $tabs = array();
	
	/**
     * Settings sections array.
     *
	 * @since  1.0.0
	 * @access protected
     * @var    array
     */
    protected $sections = array();
	
	/**
     * Settings fields array
     *
	 * @since  1.0.0
	 * @access protected
     * @var    array
     */
    protected $fields = array();
	
	/**
	 * Add a settings menu for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {	
		add_submenu_page(
			'all-in-one-video-gallery',
			__( 'All-in-One Video Gallery - Settings', 'all-in-one-video-gallery' ),
			__( 'Settings', 'all-in-one-video-gallery' ),
			'manage_aiovg_options',
			'aiovg_settings',
			array( $this, 'display_settings_form' )
		);	
	}
	
	/**
	 * Display settings form.
	 *
	 * @since 1.0.0
	 */
	public function display_settings_form() {
		require_once AIOVG_PLUGIN_DIR . 'admin/partials/settings.php';		
	}
	
	/**
	 * Initiate settings.
	 *
	 * @since 1.0.0
	 */
	public function admin_init() {	
		$this->tabs     = $this->get_tabs();
        $this->sections = $this->get_sections();
        $this->fields   = $this->get_fields();
		
        // Initialize settings
        $this->initialize_settings();		
	}
	
	/**
     * Get settings tabs.
     *
	 * @since  1.0.0
     * @return array $tabs Setting tabs array.
     */
    public function get_tabs() {	
		$tabs = array(
			'general'  => __( 'General', 'all-in-one-video-gallery' ),
            'seo'      => __( 'SEO', 'all-in-one-video-gallery' ),
            'privacy'  => __( 'GDPR Compliance', 'all-in-one-video-gallery' ),
			'advanced' => __( 'Advanced', 'all-in-one-video-gallery' )
		);
		
		return apply_filters( 'aiovg_settings_tabs', $tabs );	
	}
	
	/**
     * Get settings sections.
     *
	 * @since  1.0.0
     * @return array $sections Setting sections array.
     */
    public function get_sections() {		
		$sections = array(			
            array(
                'id'          => 'aiovg_player_settings',
                'title'       => __( 'Player Settings', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_player_settings'
            ),             		
            array(
                'id'          => 'aiovg_videos_settings',
                'title'       => __( 'Videos Layout', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_videos_settings'
            ),
			array(
                'id'          => 'aiovg_categories_settings',
                'title'       => __( 'Categories Layout', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_categories_settings'
            ),            			
			array(
                'id'          => 'aiovg_video_settings',
                'title'       => __( 'Single Video Page', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_video_settings'
            ),            
            array(
                'id'          => 'aiovg_related_videos_settings',
                'title'       => __( 'Related Videos', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_video_settings'
            ),
            array(
                'id'          => 'aiovg_images_settings',
                'title'       => __( 'Images Settings', 'all-in-one-video-gallery' ),
				'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_images_settings'
            ),
            array(
                'id'          => 'aiovg_featured_images_settings',
                'title'       => __( 'Featured Images', 'all-in-one-video-gallery' ),
				'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_images_settings'
            ),
            array(
                'id'          => 'aiovg_likes_settings',
                'title'       => __( 'Likes / Dislikes', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_likes_settings'
            ),
            array(
                'id'          => 'aiovg_pagination_settings',
                'title'       => __( 'Pagination Settings', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_pagination_settings'
            ),
            array(
                'id'          => 'aiovg_socialshare_settings',
                'title'       => __( 'Share Buttons', 'all-in-one-video-gallery' ),
				'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_socialshare_settings'
            ),                       
			array(
                'id'          => 'aiovg_permalink_settings',
                'title'       => __( 'Custom Permalink Slugs', 'all-in-one-video-gallery' ),
				'description' => __( 'NOTE: Just make sure that, after updating the fields in this section, you flush the rewrite rules by visiting "Settings > Permalinks". Otherwise you\'ll still see the old links.', 'all-in-one-video-gallery' ),
                'tab'         => 'seo',
                'page'        => 'aiovg_permalink_settings'
            ),
            array(
                'id'          => 'aiovg_privacy_settings',
                'title'       => __( 'GDPR Compliance', 'all-in-one-video-gallery' ),
				'description' => __( 'These options will help with privacy restrictions such as GDPR and the EU Cookie Law.', 'all-in-one-video-gallery' ),
                'tab'         => 'privacy',
                'page'        => 'aiovg_privacy_settings'
            ),			
            array(
                'id'          => 'aiovg_general_settings',
                'title'       => __( 'Misc Settings', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'advanced',
                'page'        => 'aiovg_general_settings'
            ),
            array(
                'id'          => 'aiovg_api_settings',
                'title'       => __( 'API Keys', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'advanced',
                'page'        => 'aiovg_api_settings'
            ),
            array(
                'id'          => 'aiovg_page_settings',
                'title'       => __( 'Page Settings', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'advanced',
                'page'        => 'aiovg_page_settings'
            )           			
        );

        if ( false !== get_option( 'aiovg_brand_settings' ) ) {
            $sections[] = array(
                'id'          => 'aiovg_brand_settings',
                'title'       => __( 'Logo & Branding', 'all-in-one-video-gallery' ),
                'description' => '',
                'tab'         => 'general',
                'page'        => 'aiovg_brand_settings'
            );
        }
		
		return apply_filters( 'aiovg_settings_sections', $sections );		
	}
	
	/**
     * Get settings fields.
     *
	 * @since  1.0.0
     * @return array $fields Setting fields array.
     */
    public function get_fields() {
        $video_templates = aiovg_get_video_templates();

		$fields = array(			
			'aiovg_player_settings' => array(
                array(
                    'name'              => 'player',
                    'label'             => __( 'Player Library', 'all-in-one-video-gallery' ),
                    'description'       => sprintf(
                        '<a href="https://www.vidstack.io/" target="_blank">%s</a>: %s',
                        __( 'Vidstack Player', 'all-in-one-video-gallery' ),
                        __( 'We are very much impressed by this player library which will be used as a default player when their stable release comes out. We confirm that you are safe to use this player in a production environment at the moment but <strong>the current player skin is subject to change</strong>.', 'all-in-one-video-gallery' )
                    ),
                    'type'              => 'radio',
                    'options'           => array(
                        'videojs'  => __( 'Video.js', 'all-in-one-video-gallery' ),
                        'vidstack' => __( 'Vidstack (Plyr)', 'all-in-one-video-gallery' )
                    ),
                    'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'width',
                    'label'             => __( 'Width', 'all-in-one-video-gallery' ),
                    'description'       => __( 'In pixels. Maximum width of the player. Leave this field empty to scale 100% of its enclosing container/html element.', 'all-in-one-video-gallery' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'aiovg_sanitize_int'
                ),
				array(
                    'name'              => 'ratio',
                    'label'             => __( 'Height (Ratio)', 'all-in-one-video-gallery' ),
                    'description'       => sprintf(
						'%s<br /><br /><strong>%s:</strong><br />"56.25" - %s<br />"62.5" - %s<br />"75" - %s<br />"67" - %s<br />"100" - %s<br />"41.7" - %s', 
						__( "In percentage. 1 to 100. Calculate player's height using the ratio value entered.", 'all-in-one-video-gallery' ),
						__( 'Examples', 'all-in-one-video-gallery' ),
						__( 'Wide Screen TV', 'all-in-one-video-gallery' ),
						__( 'Monitor Screens', 'all-in-one-video-gallery' ),
						__( 'Classic TV', 'all-in-one-video-gallery' ),
						__( 'Photo Camera', 'all-in-one-video-gallery' ),
						__( 'Square', 'all-in-one-video-gallery' ),
						__( 'Cinemascope', 'all-in-one-video-gallery' )
					),
                    'type'              => 'text',
                    'sanitize_callback' => 'floatval'
                ),
				array(
                    'name'              => 'autoplay',
                    'label'             => __( 'Autoplay', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this to start playing the video as soon as it is ready', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'loop',
                    'label'             => __( 'Loop', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this, so that the video will start over again, every time it is finished', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'muted',
                    'label'             => __( 'Muted', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this to turn OFF the audio output of the video by default', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'preload',
                    'label'             => __( 'Preload', 'all-in-one-video-gallery' ),
                    'description'       => sprintf(
						'%s<br /><br />%s<br />%s<br />%s',
						__( 'Specifies if and how the video should be loaded when the page loads.', 'all-in-one-video-gallery' ),
						__( '"Auto" - The video should be loaded entirely when the page loads', 'all-in-one-video-gallery' ),
						__( '"Metadata" - Only metadata should be loaded when the page loads', 'all-in-one-video-gallery' ),
						__( '"None" - The video should not be loaded when the page loads', 'all-in-one-video-gallery' )
					),
                    'type'              => 'select',
					'options'           => array(
						'auto'     => __( 'Auto', 'all-in-one-video-gallery' ),
						'metadata' => __( 'Metadata', 'all-in-one-video-gallery' ),
						'none'     => __( 'None', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
                array(
                    'name'              => 'playsinline',
                    'label'             => __( 'Playsinline', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this to play videos inline on mobile devices instead of automatically going into fullscreen mode', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'controls',
                    'label'             => __( 'Player Controls', 'all-in-one-video-gallery' ),
                    'description'       => sprintf( 
                        __( '<a href="%s">Click here</a> to configure your share buttons.', 'all-in-one-video-gallery' ), 
                        esc_url( admin_url( 'admin.php?page=aiovg_settings&tab=general&section=aiovg_socialshare_settings' ) ) 
                    ),
                    'type'              => 'multicheck',
					'options'           => array(
						'playpause'  => __( 'Play / Pause', 'all-in-one-video-gallery' ),
						'current'    => __( 'Current Time', 'all-in-one-video-gallery' ),
						'progress'   => __( 'Progressbar', 'all-in-one-video-gallery' ),
						'duration'   => __( 'Duration', 'all-in-one-video-gallery' ),
                        'tracks'     => __( 'Subtitles', 'all-in-one-video-gallery' ),
                        'chapters'   => __( 'Chapters', 'all-in-one-video-gallery' ),                        
                        'speed'      => __( 'Speed Control', 'all-in-one-video-gallery' ),
                        'quality'    => __( 'Quality Selector', 'all-in-one-video-gallery' ),
						'volume'     => __( 'Volume Button', 'all-in-one-video-gallery' ),
						'fullscreen' => __( 'Fullscreen Button', 'all-in-one-video-gallery' ),
                        'share'      => __( 'Share Buttons', 'all-in-one-video-gallery' ),
                        'embed'      => __( 'Embed Button', 'all-in-one-video-gallery' ),
                        'download'   => __( 'Download Button', 'all-in-one-video-gallery' )						
					),
					'sanitize_callback' => 'aiovg_sanitize_array'
                ),
                array(
                    'name'              => 'hotkeys',
                    'label'             => __( 'Keyboard Hotkeys', 'all-in-one-video-gallery' ),
                    'description'       => sprintf(
						'%s<br /><br />%s<br />%s<br />%s<br />%s<br />%s<br />%s<br />%s',
                        __( 'Check this option to enable keyboard shortcuts to control the player.', 'all-in-one-video-gallery' ),
						__( '"Spacebar" - Toggles between Play and Pause.', 'all-in-one-video-gallery' ),
						__( '"Left Arrow" - Rewinds the video.', 'all-in-one-video-gallery' ),
                        __( '"Right Arrow" - Forwards the video.', 'all-in-one-video-gallery' ),
						__( '"Up Arrow" - Increases the volume.', 'all-in-one-video-gallery' ),
						__( '"Down Arrow" - Lowers the volume.', 'all-in-one-video-gallery' ),
                        __( '"F Key" - Toggles fullscreen mode.', 'all-in-one-video-gallery' ),
                        __( '"M Key" - Toggles audio mute.', 'all-in-one-video-gallery' )
					),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'cc_load_policy',
                    'label'             => __( 'Automatically Show Subtitles', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this option to automatically show subtitles on the player if available.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'quality_levels',
                    'label'             => __( 'Quality Levels', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Enter the video quality levels, one per line.<br />Valid options are "4320p", "2880p", "2160p", "1440p", "1080p", "720p", "576p", "480p", "360p", and "240p".', 'all-in-one-video-gallery' ),
					'type'              => 'textarea',
					'sanitize_callback' => 'sanitize_textarea_field'
				),
                array(
                    'name'              => 'use_native_controls',
                    'label'             => __( 'Use Native Controls', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Enables native player controls on the selected source types. For example, uses YouTube Player for playing YouTube videos & Vimeo Player for playing Vimeo videos. Note that none of our custom player features will work on the selected sources.', 'all-in-one-video-gallery' ),
                    'type'              => 'multicheck',
					'options'           => array(
						'youtube'     => __( 'YouTube', 'all-in-one-video-gallery' ),
						'vimeo'       => __( 'Vimeo', 'all-in-one-video-gallery' ),
						'dailymotion' => __( 'Dailymotion', 'all-in-one-video-gallery' )					
					),
					'sanitize_callback' => 'aiovg_sanitize_array'
                ),
                array(
                    'name'              => 'force_js_initialization',
                    'label'             => __( 'Force JavaScript Based Initialization', 'all-in-one-video-gallery' ),
                    'description'       => __( 'By default, the plugin adds the player as an iframe to avoid conflicts with other javascript-based libraries on your website. Check this option to force the standard javascript-based player initialization if you are not a fan of the iframes.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'lazyloading',
                    'label'             => __( 'Lazyload Videos', 'all-in-one-video-gallery' ),
                    'description'       => __( 'This is an experimental feature and currently works only with the javascript-based initialization for the Vidstack or the embeddable players.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                )
			),
            'aiovg_images_settings' => array(
                array(
                    'name'              => 'width',
                    'label'             => __( 'Image Width', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Always 100% of its enclosing container/html element.', 'all-in-one-video-gallery' ),
                    'type'              => 'html',
					'sanitize_callback' => 'aiovg_sanitize_int'
                ),
				array(
                    'name'              => 'ratio',
                    'label'             => __( 'Image Height (Ratio)', 'all-in-one-video-gallery' ),
                    'description'       => __( "In percentage. 1 to 100. Calculate images's height using the ratio value entered.", 'all-in-one-video-gallery' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'floatval'
                ),
                array(
                    'name'              => 'size',
                    'label'             => __( 'Image File Size', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Those previous options control how the images are displayed on the front-end gallery pages. Whereas this option controls which image file size to load.', 'all-in-one-video-gallery' ) . '<br /><br />' . 
                        __( 'Whenever you upload an image, WordPress automatically creates 4 different image sizes. WordPress does this, so you don\'t have to keep resizing images manually and to ensure the best image size is selected for different locations on your website. Select "Thumbnail" for the small file size, so your gallery loads fast. Select "Large" if the image quality is important to your website.', 'all-in-one-video-gallery' ),
                    'type'              => 'select',
					'options'           => array(
						'thumbnail' => __( 'Thumbnail', 'all-in-one-video-gallery' ),
						'medium'    => __( 'Medium', 'all-in-one-video-gallery' ),
						'large'     => __( 'Large', 'all-in-one-video-gallery' ),
						'full'      => __( 'Full Size', 'all-in-one-video-gallery' )	
					),
					'sanitize_callback' => 'sanitize_key'
                )
            ),
            'aiovg_featured_images_settings' => array(
                array(
                    'name'              => 'enabled',
                    'label'             => __( 'Enable / Disable', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this option to enable featured images.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'download_external_images',
                    'label'             => __( 'Download External Images', 'all-in-one-video-gallery' ),
                    'description'       => __( 'WordPress requires featured images to be stored locally as attachments. But, when you add videos from YouTube, Vimeo, Dailymotion & Rumble websites, we simply link the image URLs from their server. Check this option to download the images from such external websites to your server and to link them as featured.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'hide_on_single_video_pages',
                    'label'             => __( 'Hide on Single Video Pages', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Some themes display the featured image above the player on our single video pages. Check this option if you want to hide this.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                )
            ),
            'aiovg_likes_settings' => array(   
                array(
                    'name'              => 'like_button',
                    'label'             => __( 'Like Button', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this option to enable the like button', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'dislike_button',
                    'label'             => __( 'Dislike Button', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this option to enable the dislike button', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'login_required_to_vote',
                    'label'             => __( 'Login Required to Vote', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this option to require login to like or dislike', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                )
            ),
            'aiovg_socialshare_settings' => array(
				array(
                    'name'              => 'services',
                    'label'             => __( 'Enable / Disable', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'multicheck',
					'options'           => array(
						'facebook'  => __( 'Facebook', 'all-in-one-video-gallery' ),
						'twitter'   => __( 'Twitter', 'all-in-one-video-gallery' ),						
						'linkedin'  => __( 'Linkedin', 'all-in-one-video-gallery' ),
                        'pinterest' => __( 'Pinterest', 'all-in-one-video-gallery' ),
                        'tumblr'    => __( 'Tumblr', 'all-in-one-video-gallery' ),
                        'whatsapp'  => __( 'WhatsApp', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'aiovg_sanitize_array'
                ),
                array(
                    'name'              => 'open_graph_tags',
                    'label'             => __( 'Open Graph Tags', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this option to enable Facebook Open Graph meta tags and Twitter cards on the single video pages', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'twitter_username',
                    'label'             => __( 'Twitter Username', 'all-in-one-video-gallery' ),
                    'description'       => __( 'The Twitter @username the player card should be attributed to. Required for sharing videos in Twitter.', 'all-in-one-video-gallery' ),
                    'placeholder'       => '@username',
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                )
            ),  			
            'aiovg_videos_settings' => array(
                array(
                    'name'              => 'template',
                    'label'             => __( 'Select Template', 'all-in-one-video-gallery' ),
                    'description'       => ( aiovg_fs()->is_not_paying() ? sprintf( __( '<a href="%s" target="_blank">Upgrade Pro</a> for more templates (Popup, Slider, Playlist, Compact, etc.)', 'all-in-one-video-gallery' ), esc_url( aiovg_fs()->get_upgrade_url() ) ) : '' ),
                    'type'              => 'select',
					'options'           => $video_templates,
					'sanitize_callback' => 'sanitize_key'
                ),                                
				array(
                    'name'              => 'columns',
                    'label'             => __( 'Columns', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Enter the number of columns you like to have in the gallery view.', 'all-in-one-video-gallery' ),
                    'type'              => 'number',
                    'min'               => 1,
                    'max'               => 12,
                    'step'              => 1,                    
                    'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'limit',
                    'label'             => __( 'Limit (per page)', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Number of videos to show per page. Use a value of "0" to show all videos.', 'all-in-one-video-gallery' ),
                    'type'              => 'number',
                    'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'orderby',
                    'label'             => __( 'Order By', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'select',
					'options'           => array(
						'title'      => __( 'Title', 'all-in-one-video-gallery' ),
						'date'       => __( 'Date Added', 'all-in-one-video-gallery' ),                        
						'views'      => __( 'Views Count', 'all-in-one-video-gallery' ),
                        'likes'      => __( 'Likes Count', 'all-in-one-video-gallery' ),
                        'dislikes'   => __( 'Dislikes Count', 'all-in-one-video-gallery' ),
						'rand'       => __( 'Random', 'all-in-one-video-gallery' ),
                        'menu_order' => __( 'Menu Order', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'order',
                    'label'             => __( 'Order', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'select',
					'options'           => array(
						'asc'  => __( 'Ascending', 'all-in-one-video-gallery' ),
						'desc' => __( 'Descending', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
                array(
                    'name'              => 'thumbnail_style',
                    'label'             => __( 'Image Position', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'select',
					'options'           => array(
						'standard'   => __( 'Top', 'all-in-one-video-gallery' ),
						'image-left' => __( 'Left', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'display',
                    'label'             => __( 'Show / Hide (Thumbnails)', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'multicheck',
					'options'           => array(
						'count'    => __( 'Videos Count', 'all-in-one-video-gallery' ),
                        'title'    => __( 'Video Title', 'all-in-one-video-gallery' ),
                        'category' => __( 'Category Name(s)', 'all-in-one-video-gallery' ),
                        'tag'      => __( 'Tag Name(s)', 'all-in-one-video-gallery' ),
						'date'     => __( 'Date Added', 'all-in-one-video-gallery' ),					
						'user'     => __( 'Author Name', 'all-in-one-video-gallery' ),                        
						'views'    => __( 'Views Count', 'all-in-one-video-gallery' ),
                        'likes'    => __( 'Likes Count', 'all-in-one-video-gallery' ),
                        'dislikes' => __( 'Dislikes Count', 'all-in-one-video-gallery' ),
						'duration' => __( 'Video Duration', 'all-in-one-video-gallery' ),
						'excerpt'  => __( 'Video Excerpt (Short Description)', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'aiovg_sanitize_array'
                ),
                array(
                    'name'              => 'title_length',
                    'label'             => __( 'Title Length', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Number of characters.', 'all-in-one-video-gallery' ),
                    'type'              => 'number',
                    'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'excerpt_length',
                    'label'             => __( 'Excerpt Length', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Number of characters.', 'all-in-one-video-gallery' ),
                    'type'              => 'number',
                    'sanitize_callback' => 'intval'
                )
			),
			'aiovg_categories_settings' => array(
                array(
                    'name'              => 'template',
                    'label'             => __( 'Select Template', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'select',
					'options'           => array(
						'grid'     => __( 'Grid', 'all-in-one-video-gallery' ),
						'list'     => __( 'List', 'all-in-one-video-gallery' ),
                        'dropdown' => __( 'Dropdown', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'columns',
                    'label'             => __( 'Columns', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Enter the number of columns you like to have in your categories page.', 'all-in-one-video-gallery' ),
                    'type'              => 'number',
                    'min'               => 1,
                    'max'               => 12,
                    'step'              => 1,                    
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'limit',
                    'label'             => __( 'Limit (per page)', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Number of categories to show per page. Use a value of "0" to show all categories.', 'all-in-one-video-gallery' ),
                    'type'              => 'number',
                    'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'orderby',
                    'label'             => __( 'Order By', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'select',
					'options'           => array(
						'id'         => __( 'ID', 'all-in-one-video-gallery' ),
						'count'      => __( 'Count', 'all-in-one-video-gallery' ),
						'name'       => __( 'Name', 'all-in-one-video-gallery' ),
						'slug'       => __( 'Slug', 'all-in-one-video-gallery' ),
                        'menu_order' => __( 'Menu Order', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'order',
                    'label'             => __( 'Order', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'select',
					'options'           => array(
						'asc'  => __( 'Ascending', 'all-in-one-video-gallery' ),
						'desc' => __( 'Descending', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
                array(
                    'name'              => 'hierarchical',
                    'label'             => __( 'Show Hierarchy', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this to show the child categories', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'show_description',
                    'label'             => __( 'Show Description', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this to show the categories description', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'show_count',
                    'label'             => __( 'Show Videos Count', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this to show the videos count next to the category name', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'hide_empty',
                    'label'             => __( 'Hide Empty Categories', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this to hide categories with no videos', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'back_button',
                    'label'             => __( 'Link to Parent Category', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Add a link to the parent category page on the single category pages. When there is no parent, links to the "All Categories" page.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                )
			),
            'aiovg_pagination_settings' => array(
				array(
                    'name'              => 'ajax',
                    'label'             => __( 'Ajax', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this to enable Pagination with Ajax', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'mid_size',
                    'label'             => __( 'Page Range', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Enter how many page numbers to show either side of the current page in the pagination links. Default 2.', 'all-in-one-video-gallery' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'aiovg_sanitize_int'
                )
            ), 			
			'aiovg_video_settings' => array(
				array(
                    'name'              => 'display',
                    'label'             => __( 'Show / Hide', 'all-in-one-video-gallery' ),
                    'description'       => sprintf( 
                        __( '<a href="%s">Click here</a> to configure your share buttons.', 'all-in-one-video-gallery' ), 
                        esc_url( admin_url( 'admin.php?page=aiovg_settings&tab=general&section=aiovg_socialshare_settings' ) ) 
                    ),
                    'type'              => 'multicheck',
					'options'           => array(
                        'category' => __( 'Category Name(s)', 'all-in-one-video-gallery' ),
                        'tag'      => __( 'Tag Name(s)', 'all-in-one-video-gallery' ),
						'date'     => __( 'Date Added', 'all-in-one-video-gallery' ),					
						'user'     => __( 'Author Name', 'all-in-one-video-gallery' ),
						'views'    => __( 'Views Count', 'all-in-one-video-gallery' ),
						'related'  => __( 'Related Videos', 'all-in-one-video-gallery' ),
                        'share'    => __( 'Share Buttons', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'aiovg_sanitize_array'
                ),
                array(
                    'name'              => 'has_comments',
                    'label'             => __( 'Comments', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'radio',
                    'options'           => array(
                        1  => __( 'Enable comments (can be overridden per video)', 'all-in-one-video-gallery' ),
                        2  => __( 'Forcefully enable comments on all the video pages', 'all-in-one-video-gallery' ),
                        -1 => __( 'Disable comments (can be overridden per video)', 'all-in-one-video-gallery' ),                        
                        -2 => __( 'Forcefully disable comments on all the video pages', 'all-in-one-video-gallery' )
                    ),
					'sanitize_callback' => 'intval'
                )
            ),            
            'aiovg_related_videos_settings' => array(             
				array(
                    'name'              => 'columns',
                    'label'             => __( 'Columns', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Enter the number of columns you like to have in the related videos section.', 'all-in-one-video-gallery' ),
                    'type'              => 'number',
                    'min'               => 1,
                    'max'               => 12,
                    'step'              => 1,                    
                    'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'limit',
                    'label'             => __( 'Limit (per page)', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Number of videos to show per page. Use a value of "0" to show all videos.', 'all-in-one-video-gallery' ),
                    'type'              => 'number',
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'orderby',
                    'label'             => __( 'Order By', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'select',
					'options'           => array(
						'title'      => __( 'Title', 'all-in-one-video-gallery' ),
						'date'       => __( 'Date Added', 'all-in-one-video-gallery' ),                        
						'views'      => __( 'Views Count', 'all-in-one-video-gallery' ),
                        'likes'      => __( 'Likes Count', 'all-in-one-video-gallery' ),
                        'dislikes'   => __( 'Dislikes Count', 'all-in-one-video-gallery' ),
						'rand'       => __( 'Random', 'all-in-one-video-gallery' ),
                        'menu_order' => __( 'Menu Order', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'order',
                    'label'             => __( 'Order', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'select',
					'options'           => array(
						'asc'  => __( 'Ascending', 'all-in-one-video-gallery' ),
						'desc' => __( 'Descending', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'display',
                    'label'             => __( 'Show / Hide', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'multicheck',
					'options'           => array(
						'pagination' => __( 'Pagination', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'aiovg_sanitize_array'
                ),
			),                                 
			'aiovg_permalink_settings' => array(
				array(
                    'name'              => 'video',
                    'label'             => __( 'Video Detail Page', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Replaces the SLUG value used by custom post type "aiovg_videos".', 'all-in-one-video-gallery' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                )
			),
            'aiovg_privacy_settings' => array(
				array(
                    'name'              => 'show_consent',
                    'label'             => __( 'Cookie Consent', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Ask for viewer consent before loading YouTube, Vimeo, or embedded videos from any third-party websites.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
                    'sanitize_callback' => 'intval'
               	),
				array(
                    'name'              => 'consent_message',
                    'label'             => __( 'Consent Message', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'wysiwyg',
                    'sanitize_callback' => 'wp_kses_post'
               	),
				array(
                    'name'              => 'consent_button_label',
                    'label'             => __( 'Consent Button Label', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'disable_cookies',
                    'label'             => __( 'Disable Cookies from our Plugin', 'all-in-one-video-gallery' ),
                    'description'       => '',
                    'type'              => 'multicheck',
					'options'           => array(
						'aiovg_videos_views' => __( '<strong>aiovg_videos_views</strong>: Required for unique views calculation. Disabling this option would count each view by the user. So, when a user watches the same video 10 times, the views count will also increase by 10 for that video. But, under the unique views, the views count is calculated only once per user for a video.', 'all-in-one-video-gallery' ),
						'aiovg_rand_seed'    => __( '<strong>aiovg_rand_seed</strong>: Required if you show videos in a random order with the pagination on any of your pages.', 'all-in-one-video-gallery' )
					),
					'sanitize_callback' => 'aiovg_sanitize_array'
                ),
			),			
            'aiovg_general_settings' => array(
                array(
                    'name'              => 'datetime_format',
                    'label'             => __( 'DateTime Format', 'all-in-one-video-gallery' ),
                    'description'       => sprintf(
						'%s <a href="https://wordpress.org/documentation/article/customize-date-and-time-format/" target="_blank">%s</a><br />%s', 
						__( "Enter the PHP DateTime format that the plugin should use when displaying the dates on the site's front end.", 'all-in-one-video-gallery' ),
						__( 'Documentation on date and time formatting.', 'all-in-one-video-gallery' ),
						__( 'When left empty, the plugin will display a human-readable format such as "1 hour ago", "5 mins ago", and "2 days ago".', 'all-in-one-video-gallery' )
					),
                    'placeholder'       => 'F j, Y',
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'maybe_flush_rewrite_rules',
                    'label'             => __( 'Auto Flush Rewrite Rules', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this box to automatically detect and insert the missing permalink rules. Rarely, this option can cause issues in some WordPress environments. Kindly disable this option If you find a frequent 404 or 500 error on your website.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
				array(
                    'name'              => 'delete_plugin_data',
                    'label'             => __( 'Remove data on uninstall?', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this box to delete all of the plugin data (database stored content) when uninstalled', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'delete_media_files',
                    'label'             => __( 'Delete media files?', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this box to also delete the associated media files when a video post or a video category is deleted', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
					'sanitize_callback' => 'intval'
                )
            ),
            'aiovg_api_settings' => array(
                array(
                    'name'              => 'youtube_api_key',
                    'label'             => __( 'YouTube API Key', 'all-in-one-video-gallery' ),
                    'description'       => sprintf( __( 'Follow <a href="%s" target="_blank">this guide</a> to get your own API key.', 'all-in-one-video-gallery' ), 'https://plugins360.com/all-in-one-video-gallery/how-to-get-youtube-api-key/' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'vimeo_access_token',
                    'label'             => __( 'Vimeo Access Token', 'all-in-one-video-gallery' ),
                    'description'       => sprintf( __( 'Follow <a href="%s" target="_blank">this guide</a> to get your own access token.', 'all-in-one-video-gallery' ), 'https://plugins360.com/all-in-one-video-gallery/how-to-get-vimeo-access-token/' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                )
            ),	
            'aiovg_page_settings' => array(
				array(
                    'name'              => 'category',
                    'label'             => __( 'Single Category Page', 'all-in-one-video-gallery' ),
                    'description'       => __( 'This is the page where the videos from a particular category is displayed. The [aiovg_category] short code must be on this page.', 'all-in-one-video-gallery' ),
                    'type'              => 'pages',
					'sanitize_callback' => 'sanitize_key'
                ),
                array(
                    'name'              => 'tag',
                    'label'             => __( 'Single Tag Page', 'all-in-one-video-gallery' ),
                    'description'       => __( 'This is the page where the videos from a particular tag is displayed. The [aiovg_tag] short code must be on this page.', 'all-in-one-video-gallery' ),
                    'type'              => 'pages',
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'search',
                    'label'             => __( 'Search Page', 'all-in-one-video-gallery' ),
                    'description'       => __( 'This is the page where the search results are displayed. The [aiovg_search] short code must be on this page.', 'all-in-one-video-gallery' ),
                    'type'              => 'pages',
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'user_videos',
                    'label'             => __( 'User Videos Page', 'all-in-one-video-gallery' ),
                    'description'       => __( 'This is the page where the videos from an user is displayed. The [aiovg_user_videos] short code must be on this page.', 'all-in-one-video-gallery' ),
                    'type'              => 'pages',
					'sanitize_callback' => 'sanitize_key'
                ),
				array(
                    'name'              => 'player',
                    'label'             => __( 'Player Page', 'all-in-one-video-gallery' ),
                    'description'       => __( 'This is the page used to show the video player.', 'all-in-one-video-gallery' ),
                    'type'              => 'pages',
					'sanitize_callback' => 'sanitize_key'
                )
            )            		
        );
        
        if ( false !== get_option( 'aiovg_brand_settings' ) ) {
            $fields['aiovg_brand_settings'] = array(
				array(
                    'name'              => 'show_logo',
                    'label'             => __( 'Show Logo', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Check this option to show the watermark on the video.', 'all-in-one-video-gallery' ),
                    'type'              => 'checkbox',
                    'sanitize_callback' => 'intval'
               	),
				array(
                    'name'              => 'logo_image',
                    'label'             => __( 'Logo Image', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Upload the image file of your logo. We recommend using the transparent PNG format with width below 100 pixels. If you do not enter any image, no logo will displayed.', 'all-in-one-video-gallery' ),
                    'type'              => 'file',
                    'sanitize_callback' => 'aiovg_sanitize_url'
               	),
				array(
                    'name'              => 'logo_link',
                    'label'             => __( 'Logo Link', 'all-in-one-video-gallery' ),
                    'description'       => __( 'The URL to visit when the watermark image is clicked. Clicking a logo will have no affect unless this is configured.', 'all-in-one-video-gallery' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'esc_url_raw'
               	),
				array(
                    'name'              => 'logo_position',
                    'label'             => __( 'Logo Position', 'all-in-one-video-gallery' ),
                    'description'       => __( 'This sets the corner in which to display the watermark.', 'all-in-one-video-gallery' ),
                    'type'              => 'select',
					'options'           => array(
						'topleft'     => __( 'Top Left', 'all-in-one-video-gallery' ),
						'topright'    => __( 'Top Right', 'all-in-one-video-gallery' ),
						'bottomleft'  => __( 'Bottom Left', 'all-in-one-video-gallery' ),
						'bottomright' => __( 'Bottom Right', 'all-in-one-video-gallery' )
					),
                    'sanitize_callback' => 'sanitize_key'
               	),
				array(
                    'name'              => 'logo_margin',
                    'label'             => __( 'Logo Margin', 'all-in-one-video-gallery' ),
                    'description'       => __( 'The distance, in pixels, of the logo from the edges of the display.', 'all-in-one-video-gallery' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'floatval'
               	),
				array(
                    'name'              => 'copyright_text',
                    'label'             => __( 'Copyright Text', 'all-in-one-video-gallery' ),
                    'description'       => __( 'Text that is shown when a user right-clicks the player with the mouse.', 'all-in-one-video-gallery' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
               	)
			);
        }
		
		return apply_filters( 'aiovg_settings_fields', $fields );		
	}
	
	/**
     * Initialize and registers the settings sections and fields to WordPress.
     *
     * @since 1.0.0
     */
    public function initialize_settings() {	
        // Register settings sections & fields
        foreach ( $this->sections as $section ) {		
			$page_hook = isset( $section['page'] ) ? $section['page'] : $section['id'];
			
			// Sections
            if ( false == get_option( $section['id'] ) ) {
                add_option( $section['id'] );
            }
			
            if ( isset( $section['description'] ) && ! empty( $section['description'] ) ) {
                $callback = array( $this, 'settings_section_callback' );
            } elseif ( isset( $section['callback'] ) ) {
                $callback = $section['callback'];
            } else {
                $callback = null;
            }
			
            add_settings_section( $section['id'], $section['title'], $callback, $page_hook );
			
			// Fields			
			$fields = $this->fields[ $section['id'] ];
			
			foreach ( $fields as $option ) {			
                $name     = $option['name'];
                $type     = isset( $option['type'] ) ? $option['type'] : 'text';
                $label    = isset( $option['label'] ) ? $option['label'] : '';
                $callback = isset( $option['callback'] ) ? $option['callback'] : array( $this, 'callback_' . $type );				
                $args     = array(
                    'id'                => $name,
                    'class'             => isset( $option['class'] ) ? $option['class'] : $name,
                    'label_for'         => "{$section['id']}[{$name}]",
                    'description'       => isset( $option['description'] ) ? $option['description'] : '',
                    'name'              => $label,
                    'section'           => $section['id'],
                    'size'              => isset( $option['size'] ) ? $option['size'] : null,
                    'options'           => isset( $option['options'] ) ? $option['options'] : '',
                    'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
                    'type'              => $type,
                    'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
                    'min'               => isset( $option['min'] ) ? $option['min'] : '',
                    'max'               => isset( $option['max'] ) ? $option['max'] : '',
                    'step'              => isset( $option['step'] ) ? $option['step'] : ''					
                );
				
                add_settings_field( "{$section['id']}[{$name}]", $label, $callback, $page_hook, $section['id'], $args );
            }
			
			// Creates our settings in the options table
        	register_setting( $page_hook, $section['id'], array( $this, 'sanitize_options' ) );			
        }		
    }

    /**
 	 * Displays a section description.
 	 *
	 * @since 1.0.0
	 * @param array $args Settings section args.
 	 */
	public function settings_section_callback( $args ) {
        foreach ( $this->sections as $section ) {
            if ( $section['id'] == $args['id'] ) {
                printf( '<div class="inside">%s</div>', $section['description'] ); 
                break;
            }
        }
    }

	/**
     * Displays a text field for a settings field.
     *
	 * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_text( $args ) {	
        $value       = esc_attr( $this->get_option( $args['id'], $args['section'], '' ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'text';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
		
        $html        = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
        $html       .= $this->get_field_description( $args );
		
        echo $html;		
    }
	
	/**
     * Displays a url field for a settings field.
     *
	 * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_url( $args ) {
        $this->callback_text( $args );
    }
	
	/**
     * Displays a number field for a settings field.
     *
	 * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_number( $args ) {	
        $value       = esc_attr( $this->get_option( $args['id'], $args['section'], 0 ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'number';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
        $min         = empty( $args['min'] ) ? '' : ' min="' . $args['min'] . '"';
        $max         = empty( $args['max'] ) ? '' : ' max="' . $args['max'] . '"';
        $step        = empty( $args['max'] ) ? '' : ' step="' . $args['step'] . '"';
		
        $html        = sprintf( '<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder, $min, $max, $step );
        $html       .= $this->get_field_description( $args );
		
        echo $html;		
    }
	
	/**
     * Displays a checkbox for a settings field.
     *
	 * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_checkbox( $args ) {	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], 0 ) );
		
        $html  = '<fieldset>';
        $html  .= sprintf( '<label for="%1$s[%2$s]">', $args['section'], $args['id'] );
        $html  .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="0" />', $args['section'], $args['id'] );
        $html  .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="1" %3$s />', $args['section'], $args['id'], checked( $value, 1, false ) );
        $html  .= sprintf( '%1$s</label>', $args['description'] );
        $html  .= '</fieldset>';
		
        echo $html;		
    }
	
	/**
     * Displays a multicheckbox for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_multicheck( $args ) {	
        $value = $this->get_option( $args['id'], $args['section'], array() );
		
        $html  = '<fieldset>';
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="" />', $args['section'], $args['id'] );
        foreach ( $args['options'] as $key => $label ) {
            $checked  = in_array( $key, $value ) ? 'checked="checked"' : '';
            $html    .= sprintf( '<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
            $html    .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, $checked );
            $html    .= sprintf( '%1$s</label><br>',  $label );
        }
        $html .= $this->get_field_description( $args );
        $html .= '</fieldset>';
		
        echo $html;		
    }
	
	/**
     * Displays a radio button for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_radio( $args ) {	
        $value = $this->get_option( $args['id'], $args['section'], '' );
		
        $html  = '<fieldset>';
        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<label for="%1$s[%2$s][%3$s]">',  $args['section'], $args['id'], $key );
            $html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
            $html .= sprintf( '%1$s</label><br>', $label );
        }
        $html .= $this->get_field_description( $args );
        $html .= '</fieldset>';
		
        echo $html;		
    }
	
	/**
     * Displays a selectbox for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_select( $args ) {	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], '' ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		
        $html  = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
        }
        $html .= sprintf( '</select>' );
        $html .= $this->get_field_description( $args );
		
        echo $html;		
    }
	
	/**
     * Displays a textarea for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_textarea( $args ) {	
        $value       = esc_textarea( $this->get_option( $args['id'], $args['section'], '' ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="'.$args['placeholder'].'"';
		
        $html        = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>', $size, $args['section'], $args['id'], $placeholder, $value );
        $html       .= $this->get_field_description( $args );
		
        echo $html;		
    }
	
	/**
     * Displays the html for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_html( $args ) {
        echo $this->get_field_description( $args );
    }
	
	 /**
     * Displays a rich text textarea for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_wysiwyg( $args ) {	
        $value = $this->get_option( $args['id'], $args['section'], '' );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';
		
        echo '<div style="max-width: ' . $size . ';">';
        $editor_settings = array(
            'teeny'         => true,
            'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
            'textarea_rows' => 10
        );
        if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
            $editor_settings = array_merge( $editor_settings, $args['options'] );
        }
        wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );
        echo '</div>';
        echo $this->get_field_description( $args );		
    }
	
	/**
     * Displays a file upload field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_file( $args ) {	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], '' ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $id    = $args['section'] . '[' . $args['id'] . ']';
        $label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File', 'all-in-one-video-gallery' );
		
        $html  = '<div class="aiovg-media-uploader"> ';
        $html .= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
        $html .= '<input type="button" class="aiovg-upload-media button" value="' . $label . '" />';
        $html .= '</div> ';
        $html .= $this->get_field_description( $args );
		
        echo $html;		
    }
	
	/**
     * Displays a password field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_password( $args ) {	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], '' ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		
        $html  = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
        $html .= $this->get_field_description( $args );
		
        echo $html;		
    }
	
	/**
     * Displays a color picker field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_color( $args ) {	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], '#ffffff' ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		
        $html  = sprintf( '<input type="text" class="%1$s-text aiovg-color-picker" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, '#ffffff' );
        $html .= $this->get_field_description( $args );
		
        echo $html;		
    }
	
	/**
     * Displays a select box for creating the pages select box.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_pages( $args ) {	
        $dropdown_args = array(
			'show_option_none'  => '— ' . __( 'Select a page', 'all-in-one-video-gallery' ) . ' —',
			'option_none_value' => -1,
            'selected'          => esc_attr( $this->get_option( $args['id'], $args['section'], -1 ) ),
            'name'              => $args['section'] . '[' . $args['id'] . ']',
            'id'                => $args['section'] . '[' . $args['id'] . ']',
            'echo'              => 0			
        );
		
        $html  = wp_dropdown_pages( $dropdown_args );
		$html .= $this->get_field_description( $args );
		
        echo $html;		
    }
	
	/**
     * Get field description for display.
     *
	 * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function get_field_description( $args ) {	
        if ( ! empty( $args['description'] ) ) {
            if ( 'wysiwyg' == $args['type'] ) {
                $description = sprintf( '<pre>%s</pre>', $args['description'] );
            } else {
                $description = sprintf( '<p class="description">%s</p>', $args['description'] );
            }
        } else {
            $description = '';
        }
		
        return $description;		
    }
	
	/**
     * Sanitize callback for Settings API.
     *
	 * @since  1.0.0
     * @param  array $options The unsanitized collection of options.
     * @return                The collection of sanitized values.
     */
    public function sanitize_options( $options ) {	
        if ( ! $options ) {
            return $options;
        }
		
        foreach ( $options as $option_slug => $option_value ) {		
            $sanitize_callback = $this->get_sanitize_callback( $option_slug );
			
            // If callback is set, call it
            if ( $sanitize_callback ) {
                $options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
                continue;
            }			
        }
		
        return $options;		
    }
	
	/**
     * Get sanitization callback for given option slug.
     *
	 * @since  1.0.0
     * @param  string $slug Option slug.
     * @return mixed        String or bool false.
     */
    public function get_sanitize_callback( $slug = '' ) {	
        if ( empty( $slug ) ) {
            return false;
        }
		
        // Iterate over registered fields and see if we can find proper callback
        foreach ( $this->fields as $section => $options ) {
            foreach ( $options as $option ) {
                if ( $option['name'] != $slug ) {
                    continue;
                }
				
                // Return the callback name
                return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
            }
        }
		
        return false;		
    }
	
	/**
     * Get the value of a settings field.
     *
	 * @since  1.0.0
     * @param  string $option  Settings field name.
     * @param  string $section The section name this field belongs to.
     * @param  string $default Default text if it's not found.
     * @return string
     */
    public function get_option( $option, $section, $default = '' ) {	
        $options = get_option( $section );
		
        if ( ! empty( $options[ $option ] ) ) {
            return $options[ $option ];
        }
		
        return $default;		
    }
	
}
