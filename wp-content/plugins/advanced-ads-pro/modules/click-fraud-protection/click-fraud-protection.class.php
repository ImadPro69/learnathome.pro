<?php
/**
 * Click Fraud Protection class.
 */
class Advanced_Ads_Pro_Module_CFP
{

	public $module_enabled = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		$this->module_enabled = isset( $options['cfp']['enabled'] ) && $options['cfp']['enabled'];

		add_filter( 'advanced-ads-output-wrapper-options', [ $this, 'add_wrapper' ], 25, 2 );
		add_filter( 'advanced-ads-ad-output', [ $this, 'ad_output' ], 99, 2 );

		add_action( 'wp_enqueue_scripts', [ $this, 'add_frontend_script_data' ], 11 );

		add_filter( 'advanced-ads-can-display', [ $this, 'can_display' ], 10, 3 );

		// add visitor conditions
		add_filter( 'advanced-ads-visitor-conditions', [ $this, 'add_visitor_conditions' ] );
		add_filter( 'advanced-ads-js-visitor-conditions', [ $this, 'add_to_passive_cache_busting' ] );

		//Add new node to 'Ad health' if the user is banned.
		add_filter( 'advanced-ads-ad-health-nodes', [ $this, 'add_ad_health_node' ] );

		// delete ban cookie before any output
		add_action( 'init', [ $this, 'delete_ban_cookie' ], 50 );

		if ( $this->module_enabled ) {
			add_filter( 'advanced-ads-output-wrapper-options', [ $this, 'add_wrapper_options' ], 10, 2 );
			add_filter( 'advanced-ads-output-wrapper-options-group', [ $this, 'add_wrapper_options_group' ], 10, 2 );
			add_filter( 'advanced-ads-pro-ad-needs-backend-request', [ $this, 'ad_needs_backend_request' ], 10, 1 );
		}
	}

	/**
	 * determine cookies parameter (domain or path)
	 */
	private function get_path_and_domain() {
		$path = '';
		$site_url = site_url();
		$domain = '';

		// get host name form server first before getting client side value
		$host_name = !empty( $_SERVER['SERVER_NAME'] )? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];

		$expl_url = explode( '.', $site_url );

		if ( 2 < count( $expl_url ) ) {
			// using subdomain
			$domain = $host_name;
		}
		$path = explode( $host_name, $site_url );
		if ( isset( $path[1] ) ) {
			$path = $path[1];
		} else {
			$path = $path[0];
		}
		return [
			'path' => $path,
			'domain' => $domain,
		];
	}

	private function get_conditions_options( $ad ) {
		$options = $ad->options();
		if ( !isset( $options['visitors'] ) ) return [];
		$results = [];
		foreach( $options['visitors'] as $cond ) {
			if ( isset( $cond['type'] ) && 'ad_clicks' == $cond['type'] ) {
				$results[] = $cond;
			}
		}
		return $results;
	}

	/**
	 *  Notify passive cache-busting that this condition can be checked using JavaScript.
	 */
	public function add_to_passive_cache_busting( $conditions ) {
		$conditions[] = 'ad_clicks';
		return $conditions;
	}

	/**
	 * add the visitor condition
	 */
	public function add_visitor_conditions( $conditions ) {
		$conditions['ad_clicks'] = [
			'label' => __( 'max. ad clicks', 'advanced-ads-pro' ),
			'description' => __( 'Display ad only if a click limit has not been reached.', 'advanced-ads-pro' ),
			'metabox' => [ $this, 'visitor_conditions_metabox' ], // callback to generate the metabox
			'check' => [ $this, 'visitor_conditions_check' ] // callback for frontend check
		];
		return $conditions;
	}

	/**
	 * render the markup for visitor condition
	 *
	 * @param array  $options Options of the condition.
	 * @param int    $index Index of the condition.
	 * @param string $form_name Name of the form, falls back to class constant.
	 */
	public function visitor_conditions_metabox( $options, $index = 0, $form_name = '' ) {
		if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

		$type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;
		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis
		$name = Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions::get_form_name_with_index( $form_name, $index );

		// options
		$limit      = isset( $options['limit'] ) ? Advanced_Ads_Pro_Utils::absint( $options['limit'], 1 ) : 1;
		$expiration = isset( $options['expiration'] ) ? Advanced_Ads_Pro_Utils::absint( $options['expiration'], 1 ) : 1;

		?><input type="number" name="<?php echo esc_attr( $name ); ?>[limit]" value="<?php echo esc_attr( $limit ); ?>" required="required" min="1" step="1" />
		<?php _e( 'within', 'advanced-ads-pro' ); ?>
		<input type="number" name="<?php echo $name; ?>[expiration]" value="<?php echo $expiration; ?>" required="required" min="1" step="1" />
		<?php _e( 'hours', 'advanced-ads-pro' ); ?>
        <input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<p class="description">
			<?php echo esc_html( $type_options[ $options['type'] ]['description'] ); ?>
			<a href="https://wpadvancedads.com/manual/max-ad-clicks/?utm_source=advanced-ads&utm_medium=link&utm_campaign=condition-max-ad-clicks" class="advads-manual-link" target="_blank">
				<?php esc_html_e( 'Manual', 'advanced-ads-pro' ); ?>
			</a>
		</p>

		<label class="advads-conditions-table-width-100">
			<input type="checkbox" name="<?php echo esc_attr( $name ); ?>[exclude-from-global]" value="1" <?php checked( ! empty( $options['exclude-from-global'] ) ); ?> />
			<?php esc_html_e( 'Ignore global Click Fraud Protection', 'advanced-ads-pro' ); ?>
			<a href="<?php echo esc_url( ADVADS_URL ) . 'manual/click-fraud-protection/#utm_source=advanced-ads&utm_medium=link&utm_campaign=pro-cfp-manual'; ?>" target="_blank" class="advads-manual-link">
				<?php esc_html_e( 'Manual', 'advanced-ads-pro' ); ?>
			</a>
		</label>
        <?php
	}

	/**
	 *  delete ban cookie
	 */
	public function delete_ban_cookie() {
		if ( !$this->module_enabled && isset( $_COOKIE['advads_pro_cfp_ban'] ) && !headers_sent() ) {
			$pnd = $this->get_path_and_domain();
			$path = '' != $pnd['path'] ? $pnd['path'] : '/';
			if ( '' != $pnd['domain'] ) {
				setcookie( 'advads_pro_cfp_ban', '0', time() - 3600, $path, $pnd['domain'] );
			} else {
				setcookie( 'advads_pro_cfp_ban', '0', time() - 3600, $path );
			}
		}
	}

	/**
	 * front end check for the visitor condition
	 *
	 * @param array           $options Options of the condition.
	 * @param Advanced_Ads_Ad $ad      Ad object.
	 */
	public function visitor_conditions_check( $options = [], $ad = false ) {
		if ( isset( $_COOKIE['advanced_ads_ad_clicks_' . $ad->id] ) ) {
			$cval = json_decode( stripslashes( $_COOKIE['advanced_ads_ad_clicks_' . $ad->id] ), true );

			$update_cookie = false;

			$now = time();

			foreach( $cval as $key => $value ) {
				// check against the right value in case of multiple ad clicks conditions
				if ( '_' . $options['expiration'] == $key ) {
					if ( $now >= absint( $value['ttl'] ) ) {
						// expired TTL - just skip, cookie will be updated client side
						return true;
					} else {
						if ( absint( $value['count'] ) >= absint( $options['limit'] ) ) {
							return false;
						}
					}
				}
			}

		}

		return true;
	}

	/**
	 *  determine if ads should be hidden
	 *
	 * @param bool            $can_display Whether the current ad can be displayed.
	 * @param Advanced_Ads_Ad $ad The ad object.
	 * @param array           $check_options Check options.
	 * @return bool
	 */
	public function can_display( $can_display, $ad, $check_options ) {
		if ( ! $can_display || ! $this->module_enabled || ! empty( $check_options['passive_cache_busting'] ) ) {
			return $can_display;
		}

		// Check if the global setting is ignored.
		foreach ( $this->get_conditions_options( $ad ) as $condition ) {
			if ( ! empty( $condition['exclude-from-global'] ) ) {
				return true;
			}
		}

		// Check if the user is banned
		return empty( $_COOKIE['advads_pro_cfp_ban'] );
	}

	/**
	 * Enqueue front end script.
	 */
	public function add_frontend_script_data() {
		// Do not enqueue on AMP pages.
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return;
		}

		$options = Advanced_Ads_Pro::get_instance()->get_options();
		$pnd     = $this->get_path_and_domain();

		wp_localize_script( Advanced_Ads_Pro::FRONTEND_SCRIPT_HANDLE, 'advadsCfpInfo', [
			'cfpExpHours'   => isset( $options['cfp']['cookie_expiration'] ) ? $options['cfp']['cookie_expiration'] : 3,
			'cfpClickLimit' => isset( $options['cfp']['click_limit'] ) ? absint( $options['cfp']['click_limit'] ) : 3,
			'cfpBan'        => isset( $options['cfp']['ban_duration'] ) ? $options['cfp']['ban_duration'] : 7,
			'cfpPath'       => $pnd['path'],
			'cfpDomain'     => $pnd['domain'],
		] );
	}

	/**
	 * Add the JS that adds this ad to the list of CFP.
	 *
	 * @param string          $output Ad output.
	 * @param Advanced_Ads_Ad $ad Ad object.
	 * @return string
	 */
	public function ad_output( $output, $ad ) {
		// Do not enqueue on AMP pages.
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return $output;
		}
		$cond = $this->get_conditions_options( $ad );
		if ( !empty( $cond ) || $this->module_enabled ) {
			$output .= '<script type="text/javascript">;new advadsCfpAd( ' . $ad->id . ' );</script>';
		}
		return $output;
	}

	/**
	 * add the HTML attribute for front end JS
	 *
	 * @param array           $wrapper Wrapper options.
	 * @param Advanced_Ads_Ad $ad      The ad object.
	 */
	public function add_wrapper( $wrapper, $ad ) {
		$cond = $this->get_conditions_options( $ad );
		if ( !empty( $cond ) || $this->module_enabled ) {
			// Iframe or `a` tag of the ad
			$wrapper['data-cfpa'] = $ad->id;
			// Ad wrapper.
			$wrapper['data-cfpw'] = $ad->id;

			// print all expiration hours
			if ( !empty( $cond ) ) {
				$hours = [];
				foreach( $cond as $_c ) {
					$hours[ '_' . (float) $_c['expiration'] ] = (int) $_c['limit'];

					if ( isset( $_c['exclude-from-global'] ) ) {
						$wrapper['data-cfp-exclude'] = true;
					}
				}
				// Click limits.
				$wrapper['data-cfph'] = wp_json_encode( $hours );
			}
		}
		return $wrapper;
	}

	/**
	 * Add new node to 'Ad health' if the user is banned.
	 *
	 * @param array $nodes.
	 * @return bool array $nodes.
	 */
	public function add_ad_health_node( $nodes ) {
		if ( isset( $_COOKIE['advads_pro_cfp_ban'] ) ) {
			$nodes[] = [ 'type' => 1, 'data' => [
				'parent' => 'advanced_ads_ad_health',
				'id'    => 'advanced_ads_ad_health_cfp_enabled',
				'title' => __( 'Click Fraud Protection enabled', 'advanced-ads-pro' ),
				'href'  => 'https://wpadvancedads.com/manual/click-fraud-protection',
				'meta'   => [
					'class' => 'advanced_ads_ad_health_warning',
					'target' => '_blank'
				]
			] ];
		}
		return $nodes;
	}

	/**
	 * Add the data attribute to the top level wrapper when it is an ad wrapper.
	 *
	 * @param array           $options Wrapper options.
	 * @param Advanced_Ads_Ad $ad Ad object.
	 *
	 * @return array
	 */
	public function add_wrapper_options( $options, Advanced_Ads_Ad $ad ) {
		if ( ! empty( $ad->args['is_top_level'] ) ) {
			$options['data-cfptl'] = true;
		}
		return $options;
	}

	/**
	 * Add the data attribute to the top level wrapper when it is a group wrapper.
	 *
	 * @param array              $options Wrapper options.
	 * @param Advanced_Ads_Group $group   group object.
	 *
	 * @return array
	 */
	public function add_wrapper_options_group( $options, Advanced_Ads_Group $group ) {
		if ( ! empty( $group->ad_args['is_top_level'] ) ) {
			$options['data-cfptl'] = true;
		}
		return $options;
	}

	/**
	 * Enable cache-busting if the module is enabled.
	 *
	 * @param string  $return What cache-busting type is needed.
	 * @return string $return What cache-busting type is needed.
	 */
	public function ad_needs_backend_request( $return ) {
		if ( 'static' === $return ) {
			return 'passive';
		}

		return $return;
	}
}
