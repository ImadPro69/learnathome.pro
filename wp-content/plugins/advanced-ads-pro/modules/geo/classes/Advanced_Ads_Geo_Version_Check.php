<?php

/**
 * Class to check whether Geo is active and in an old version before the deprecation.
 */
class Advanced_Ads_Geo_Version_Check {
	/**
	 * Whether a version of Advanced Ads Geo is active,
	 * that does not yet know it's been deprecated.
	 *
	 * @return bool
	 */
	public static function is_geo_active() {
		return defined( 'AAGT_VERSION' );
	}

	/**
	 * If the loading order of the plugins is not default,
	 * AAGT_VERSION could be undefined, even though the Geo Targeting add-on is still installed.
	 * This is an approximation to that issue.
	 * If a user has renamed the plugin file, this will also fail.
	 *
	 * @return bool
	 */
	public static function is_geo_installed() {
		return ! empty( array_filter(
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			static function( $plugin ) {
				$needle = 'advanced-ads-geo.php';
				$len    = strlen( $needle );

				return substr_compare( $plugin, $needle, -$len, $len ) === 0;
			}
		) );
	}

	/**
	 * Render the admin notice.
	 *
	 * @return void
	 */
	public static function show_deprecated_geo_notice() {
		add_action( 'advanced-ads-admin-notices', static function() {
			?>
			<div class="notice notice-error advads-admin-notice">
				<p>
					<?php
					echo wp_kses(
						sprintf(
						/* translators: 1 is the opening link to the plugins page, 2 the closing link */
							__(
								'The geo-targeting visitor condition moved into Advanced Ads Pro. You can remove Geo Targeting %1$shere%2$s.',
								'advanced-ads-pro'
							),
							'<a href="' . admin_url( 'plugins.php' ) . '">',
							'</a>'
						),
						[ 'a' => [ 'href' => true ] ]
					);
					?>
				</p>
			</div>
			<?php
		} );
	}
}
