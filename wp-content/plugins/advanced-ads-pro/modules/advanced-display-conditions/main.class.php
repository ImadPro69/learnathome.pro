<?php
/**
 * The Advanced Display Conditions module.
 *
 * TODO should use a constant for option key as it is shared at multiple positions.
 */
class Advanced_Ads_Pro_Module_Advanced_Display_Conditions {

	protected $options = [];
	protected $is_ajax;

	public function __construct() {

		add_filter( 'advanced-ads-display-conditions', [ $this, 'display_conditions' ] );

		$this->is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( ! $this->is_ajax ) {
			// attach more ad select values
			add_filter( 'advanced-ads-ad-select-args', [ $this, 'additional_ad_select_args' ] );
		}
	}

	/**
	 * Add visitor condition.
	 *
	 * @since 1.0.1
	 * @param array $conditions Display conditions of the main plugin.
	 * @return array $conditions New global visitor conditions.
	 */
	public function display_conditions( $conditions ){

		// current uri
		$conditions['request_uri'] = [
			'label' => __( 'url parameters', 'advanced-ads-pro' ),
			'description' => sprintf(__( 'Display ads based on the current URL parameters (everything following %s), except values following #.', 'advanced-ads-pro' ), ltrim( home_url(), '/' ) ),
			'metabox' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'metabox_string' ], // callback to generate the metabox
			'check' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'check_request_uri' ], // callback for frontend check
			'helplink'    => 'https://wpadvancedads.com/manual/ads-by-url-parameters/?utm_source=advanced-ads&utm_medium=link&utm_campaign=condition-url-parameter',
		];

		/** page template, see https://developer.wordpress.org/themes/template-files-section/page-template-files/page-templates/
		 * in WP 4.7, this logic was extended to also support templates
		 * for other post types, hence we now add a condition for all
		 * other post types with registered templates
		 *
		 */
		$conditions['page_template'] = [
			'label' => sprintf(__( '%s template', 'advanced-ads-pro' ), 'page' ),
			'description' => sprintf(__( 'Display ads based on the template of the %s post type.', 'advanced-ads-pro' ), 'page' ),
			'metabox' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'metabox_page_template' ], // callback to generate the metabox
			'check' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'check_page_template' ], // callback for frontend check
			'post-type' => 'page'
		];
		/**
		 * load post templates
		 * need to check, because only works with WP 4.7 and higher
		 */
		if( method_exists( 'WP_Theme', 'get_post_templates' ) ){
			$page_templates = wp_get_theme()->get_post_templates();
			if( is_array( $page_templates ) ){
				foreach( $page_templates as $_post_type => $_templates ){
					// skip page templates, because they are already registered and another index would cause old conditions to not work anymore
					if( 'page' === $_post_type ){
					    continue;
					}
					$conditions['page_template_' . $_post_type ] = [
						'label' => sprintf(__( '%s template', 'advanced-ads-pro' ), $_post_type ),
						'description' => sprintf(__( 'Display ads based on the template of the %s post type.', 'advanced-ads-pro' ), $_post_type ),
						'metabox' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'metabox_page_template' ], // callback to generate the metabox
						'check' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'check_page_template' ], // callback for frontend check
						'post-type' => $_post_type
					];
				}
			}
		}
		// language set with the WPML plugin
		if( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$conditions['wpml_language'] = [
				'label'       => __( 'WPML language', 'advanced-ads-pro' ),
				'description' => __( 'Display ads based on the page language set by the WPML plugin.', 'advanced-ads-pro' ),
				'metabox'     => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'metabox_wpml_language' ], // callback to generate the metabox
				'check'       => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'check_wpml_language' ] // callback for frontend check
			];
		}

		$conditions['sub_page'] = [
			'label' => __( 'parent page', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on parent page.', 'advanced-ads-pro' ),
			'metabox' => [ 'Advanced_Ads_Display_Conditions', 'metabox_post_ids' ], // callback to generate the metabox
			'check' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'check_parent_page' ] // callback for frontend check
		];

		$conditions['post_meta'] = [
			'label' => __( 'post meta', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on post meta.', 'advanced-ads-pro' ),
			'metabox' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'metabox_post_meta' ], // callback to generate the metabox
			'check' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'check_post_meta' ] // callback for frontend check
		];

		$conditions['paginated_post'] = [
			'label' => __( 'pagination', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the index of a split page', 'advanced-ads-pro' ),
			'metabox' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'metabox_paginated_post' ], // callback to generate the metabox
			'check' => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'check_paginated_post' ], // callback for frontend check
		];

		$conditions['post_content'] = [
			'label'       => __( 'post content', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on words and phrases within the post or page content. Dynamically added text might not be considered.', 'advanced-ads-pro' ),
			'metabox'     => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'metabox_string' ], // callback to generate the metabox.
			'check'       => [ 'Advanced_Ads_Pro_Module_Advanced_Display_Conditions', 'check_post_content' ], // callback for frontend check.
			'helplink'    => 'https://wpadvancedads.com/manual/ads-based-on-keywords/?utm_source=advanced-ads&utm_medium=link?utm_campaign=condition-post-content',
		];

		return $conditions;
	}

	/**
	 * add ad select vars that can later be used by ajax
	 *
	 * @since untagged
	 * @param array $args Arguments passed to ads and groups from top level placements/ads/groups.
	 * @return array
	 */
	public function additional_ad_select_args( $args ) {
		if ( isset( $args['url_parameter'] ) ) {
			return $args;
		}

		if ( wp_doing_ajax() && isset( $_SERVER['HTTP_REFERER'] ) ) {
			// An AJAX request to load content initiated by a third party plugin.
			$args['url_parameter'] = $_SERVER['HTTP_REFERER'];
		} else {
			$args['url_parameter'] = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
			// Only consider QUERY_STRING, if not already included in REQUEST_URI.
			if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) && false === strpos( $_SERVER[ 'REQUEST_URI' ], $_SERVER[ 'QUERY_STRING' ] ) ) {
				$args['url_parameter'] .= $_SERVER[ 'QUERY_STRING' ];
			}
		}

		/**
		 * Allow other developers to manipulate the checked string dynamically.
		 *
		 * @param string $args['url_parameter'] Current URL parameter.
		 * @return string
		 */
		$args['url_parameter'] = apply_filters( 'advanced-ads-pro-display-condition-url-string', $args['url_parameter'] );

	    return $args;
	}

	/**
	 * Check if ad can be displayed by request_uri condition in frontend.
	 *
	 * @param array           $options options of the condition.
	 * @param Advanced_Ads_Ad $ad      The ad object.
	 *
	 * @return bool
	 */
	public static function check_request_uri( $options, Advanced_Ads_Ad $ad ) {
		$uri_string = isset( $ad->args['url_parameter'] ) ? $ad->args['url_parameter'] : '';

		// todo: implement this method into display conditions
		return Advanced_Ads_Visitor_Conditions::helper_check_string( $uri_string, $options );
	}

	/**
	 * Check if ad can be displayed by page template condition in frontend.
	 *
	 * @param array           $options Options of the condition.
	 * @param Advanced_Ads_Ad $ad      The ad object.
	 *
	 * @return bool
	 */
	public static function check_page_template( $options, Advanced_Ads_Ad $ad ) {
		if (!isset($options['value']) || !is_array($options['value'])) {
		    return false;
		}

		if (isset($options['operator']) && $options['operator'] === 'is_not') {
		    $operator = 'is_not';
		} else {
		    $operator = 'is';
		}

		$ad_options = $ad->options();
		$post = isset( $ad_options['post'] ) ? $ad_options['post'] : null;
		$post_template = get_page_template_slug( $post['id'] );

		if (!Advanced_Ads_Display_Conditions::can_display_ids($post_template, $options['value'], $operator)) {
		    return false;
		}

		return true;

	}

	/**
	 * Check if ad can be displayed by WPML language condition in frontend.
	 *
	 * @param array           $options Options of the condition.
	 * @param Advanced_Ads_Ad $ad      Advanced_Ads_Ad.
	 *
	 * @return bool
	 */
	public static function check_wpml_language( $options, Advanced_Ads_Ad $ad ) {
	    if (!isset($options['value']) || !is_array($options['value'])) {
		return false;
	    }

	    if (isset($options['operator']) && $options['operator'] === 'is_not') {
		$operator = 'is_not';
	    } else {
		$operator = 'is';
	    }

		$lang = apply_filters( 'wpml_current_language', null );
		if ( ! Advanced_Ads_Display_Conditions::can_display_ids( $lang, $options['value'], $operator ) ) {
		return false;
	    }

	    return true;
	}

	/**
	 * Check if ad can be displayed by 'is sub-page' condition in frontend.
	 *
	 * @param array           $options Options of the condition.
	 * @param Advanced_Ads_Ad $ad      The ad object.
	 *
	 * @return bool
	 */
	public static function check_parent_page( $options, Advanced_Ads_Ad $ad ) {
		if ( ! isset($options['value']) || ! is_array( $options['value'] ) ) {
			return false;
		}

		if ( isset( $options['operator'] ) && $options['operator'] === 'is_not' ) {
			$operator = 'is_not';
		} else {
			$operator = 'is';
		}

		global $post;
		$post_parent = ! empty( $post->post_parent ) ? $post->post_parent : 0;

		if ( ! Advanced_Ads_Display_Conditions::can_display_ids( $post_parent, $options['value'], $operator ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if ad can be displayed by 'post meta' condition in frontend.
	 *
	 * @param array           $options Options of the condition.
	 * @param Advanced_Ads_Ad $ad      The ad object.
	 *
	 * @return bool
	 */
	public static function check_post_meta( $options, Advanced_Ads_Ad $ad ) {
		global $post;
		$mode = ( isset($options['mode']) && $options['mode'] === 'all' ) ? 'all' : 'any';
		$meta_field = isset( $options['meta_field'] ) ? $options['meta_field'] : '';

		if ( empty( $post->ID ) && empty( $meta_field ) ) {
			return true;
		}

		$meta_values = get_post_meta( $post->ID, $meta_field );

		if ( ! $meta_values ) {
			// allow *_not operators return true
			return Advanced_Ads_Visitor_Conditions::helper_check_string( '', $options );
		}

		foreach ( $meta_values as $_meta_value ) {
			$result = Advanced_Ads_Visitor_Conditions::helper_check_string( $_meta_value, $options );

			if ( ! $result && 'all' === $mode ) {
				return false;
			}

			if ( $result  && 'any' === $mode ) {
				return true;
			}
		}

		return 'all' === $mode;
	}

	/**
	 * Check if ad can be displayed by 'paginated post' condition in frontend.
	 *
	 * @param array           $options Options of the condition.
	 * @param Advanced_Ads_Ad $ad      The ad object.
	 *
	 * @return bool
	 */
	public static function check_paginated_post( $options, Advanced_Ads_Ad $ad ) {
		if ( ! isset( $options['first'] ) || ! isset( $options['last'] ) ) {
			return false;
		}

		$ad_options = $ad->options();

		if ( ! isset( $ad_options['wp_the_query']['page'] ) || ! isset( $ad_options['wp_the_query']['numpages'] ) ) {
			return false;
		}

		$first = ! empty( $options['first'] ) ? absint( $options['first'] ) : 1;
		$last = ! empty( $options['last'] ) ? absint( $options['last'] ) : 1;
		$page = $ad_options['wp_the_query']['page'];
		$numpages = $ad_options['wp_the_query']['numpages'];

		if ( ! empty( $options['count_from_end'] ) ) {
			$first = $numpages + 1 - $first;
			$last = $numpages + 1 - $last;
		}

		if ( $first > $last ) {
			$tmp = $first;
			$first = $last;
			$last = $tmp;
		}

		if ( $page < $first || $page > $last ) {
			return false;
		}

		return true;
	}

	/**
	 * Check "Post content" display condition in frontend.
	 *
	 * @param array           $options The options of the condition.
	 * @param Advanced_Ads_Ad $ad The ad object.
	 * @return bool true if ad can be displayed.
	 */
	public static function check_post_content( $options, Advanced_Ads_Ad $ad ) {
		$ad_options = $ad->options();

		if ( ! isset( $ad_options['post']['id'] ) ) {
			return true;
		}

		$content = get_the_content( null, false, (int) $ad_options['post']['id'] );

		// Since we want to remove only this function, we do not use `__return_false`.
		$return_false = function() {
			return false;
		};

		add_filter( 'advanced-ads-can-inject-into-content', $return_false );
		$content = apply_filters( 'the_content', $content );
		remove_filter( 'advanced-ads-can-inject-into-content', $return_false );

		$content = wp_strip_all_tags( $content );

		return Advanced_Ads_Visitor_Conditions::helper_check_string( $content, $options );
	}

	/**
	 * callback to display any condition based on a string
	 *
	 * @since 1.4
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_string( $options, $index = 0, $form_name = '' ) {

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Display_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

	    // options
	    $value = isset( $options['value'] ) ? $options['value'] : '';
	    $operator = isset( $options['operator'] ) ? $options['operator'] : 'contains';

	    include dirname( __FILE__ ) . '/views/metabox-string.php';
	}

	/**
	 * callback to display the page templates condition
	 *
	 * @since 1.4.1
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_page_template( $options, $index = 0, $form_name = '' ) {

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Display_Conditions::get_instance()->conditions;

	    if (!isset($type_options[$options['type']])) {
		return;
	    }

	    // form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

	    // options
	    $values = ( isset($options['value']) && is_array($options['value']) ) ? $options['value'] : [];
	    $operator = ( isset($options['operator']) && $options['operator'] === 'is_not' ) ? 'is_not' : 'is';

	    // get values and select operator based on previous settings

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
	    <select name="<?php echo $name; ?>[operator]">
		<option value="is" <?php selected('is', $operator); ?>><?php _e('is', 'advanced-ads-pro'); ?></option>
		<option value="is_not" <?php selected('is_not', $operator); ?>><?php _e('is not', 'advanced-ads-pro'); ?></option>
	    </select><?php
	    // get all page templates
	    $post_type = ( isset( $type_options[$options['type']]['post-type'] ) ) ? $type_options[$options['type']]['post-type'] : 'page';
	    $templates = get_page_templates( null, $post_type );
		$rand = md5( $form_name );

	    ?><div class="advads-conditions-single advads-buttonset"><?php
	    foreach( $templates as $_name => $_file ) {
		if (isset( $values ) && is_array( $values ) && in_array( $_file, $values ) ) {
		    $_val = 1;
		} else {
		    $_val = 0;
		}
		$field_id = 'advads-conditions-' . sanitize_title( $_name ) . md5( $name );
		?><label class="button ui-button" for="<?php echo $field_id;
		?>"><?php echo $_name; ?></label><input type="checkbox" id="<?php echo $field_id; ?>" name="<?php echo $name; ?>[value][]" <?php checked($_val, 1); ?> value="<?php echo $_file; ?>"><?php
	    }
		if ( file_exists( ADVADS_BASE_PATH . 'admin/views/conditions/not-selected.php' ) ) {
			include ADVADS_BASE_PATH . 'admin/views/conditions/not-selected.php';
		}
	    ?></div>

	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}

	/**
	 * callback to display the WPML language condition
	 *
	 * @since 1.8*
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_wpml_language( $options, $index = 0, $form_name = '' ) {

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Display_Conditions::get_instance()->conditions;

	    if (!isset($type_options[$options['type']])) {
		return;
	    }

	    // form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

	    // options
	    $values = ( isset($options['value']) && is_array($options['value']) ) ? $options['value'] : [];
	    $operator = ( isset($options['operator']) && $options['operator'] === 'is_not' ) ? 'is_not' : 'is';

	    // get values and select operator based on previous settings

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
	    <select name="<?php echo $name; ?>[operator]">
		<option value="is" <?php selected('is', $operator); ?>><?php _e('is', 'advanced-ads-pro'); ?></option>
		<option value="is_not" <?php selected('is_not', $operator); ?>><?php _e('is not', 'advanced-ads-pro'); ?></option>
	    </select><?php

	    // get all languages
	    $wpml_active_languages = apply_filters( 'wpml_active_languages', null, [] );
		$rand = md5( $form_name );

	    ?><div class="advads-conditions-single advads-buttonset"><?php
	    if( is_array( $wpml_active_languages ) && count( $wpml_active_languages ) ){
		foreach( $wpml_active_languages as $_language ) {
			$field_id = 'advads-conditions-' . $_language['code'] . md5( $name );
		    $value = ( $values === [] || in_array($_language['code'], $values) ) ? 1 : 0;
			?><label class="button ui-button" for="<?php echo $field_id;
			?>"><?php echo $_language['native_name']; ?></label><input type="checkbox" id="<?php echo $field_id; ?>" name="<?php echo $name; ?>[value][]" <?php checked($value, 1); ?> value="<?php echo $_language['code']; ?>"><?php
		}
	    } else {
		_e( 'no languages set up in WPML', 'advanced-ads-pro' );
	    }
	    ?></div>
		<p class="description">
			<?php echo esc_html( $type_options[ $options['type'] ]['description'] ); ?>
			<a href="https://wpadvancedads.com/translating-ads-wpml/?utm_source=advanced-ads&utm_medium=link&utm_campaign=condition-wpml" class="advads-manual-link" target="_blank">
				<?php esc_html_e( 'Manual', 'advanced-ads-pro' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * Callback to display the 'post meta' condition
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_post_meta( $options, $index = 0, $form_name = '' ) {
		if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

		$type_options = Advanced_Ads_Display_Conditions::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

		// options
		$mode = ( isset($options['mode']) && $options['mode'] === 'all' ) ? 'all' : 'any';
		$operator = isset( $options['operator'] ) ? $options['operator'] : 'contains';
		$meta_field = isset( $options['meta_field'] ) ? $options['meta_field'] : '';
		$value = isset( $options['value'] ) ? $options['value'] : '';
		?><select name="<?php echo $name; ?>[mode]">
		    <option value="any" <?php selected( 'any', $mode ); ?>><?php _e( 'any of', 'advanced-ads-pro'); ?></option>
		    <option value="all" <?php selected( 'all', $mode ); ?>><?php _e( 'all of', 'advanced-ads-pro' ); ?></option>
		</select><input type="text" name="<?php echo $name; ?>[meta_field]" value="<?php echo $meta_field; ?>" placeholder="<?php _e( 'meta key', 'advanced-ads-pro' ); ?>"/><?php
		include dirname( __FILE__ ) . '/views/metabox-string.php';
	}

	/**
	 * Callback to display the 'paginated post' condition
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_paginated_post( $options, $index = 0, $form_name = '' ) {
		if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

		$type_options = Advanced_Ads_Display_Conditions::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

		// options
		$first = ! empty( $options['first'] ) ? Advanced_Ads_Pro_Utils::absint( $options['first'], 1 ) : 1;
		$last = ! empty( $options['last'] ) ? Advanced_Ads_Pro_Utils::absint( $options['last'], 1 ) : 1;
		$count_from_end = ! empty( $options['count_from_end'] );

		$first_field = '<input type="number" required="required" min="1" name="' . $name . '[first]" value="' . $first . '"/>.';
		$last_field = '<input type="number" required="required" min="1" name="' . $name . '[last]" value="' . $last . '"/>.';

		printf( __( 'from %s to %s', 'advanced-ads-pro' ), $first_field, $last_field ); ?> <input id="advads-conditions-<?php
		echo $index; ?>-count-from-end" type="checkbox" value="1" name="<?php
		echo $name; ?>[count_from_end]" <?php checked( $count_from_end, 1 ); ?>><label for="advads-conditions-<?php
		echo $index; ?>-count-from-end"><?php _e( 'count from end', 'advanced-ads-pro' ); ?></label>
		<input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p>
		<?php

	}

	/**
	 * Helper function to the name of a form field.
	 * falls back to default
	 *
	 * @param string $form_name form name if submitted.
	 * @param int    $index index of the condition.
	 *
	 * @return string
	 */
	public static function get_form_name_with_index( $form_name = '', $index = 0 ) {
		// form name basis
		if ( method_exists( 'Advanced_Ads_Display_Conditions', 'get_form_name_with_index' ) ) {
			return Advanced_Ads_Display_Conditions::get_form_name_with_index( $form_name, $index );
		} else {
			return Advanced_Ads_Display_Conditions::FORM_NAME . '[' . $index . ']';
		}
	}

}
