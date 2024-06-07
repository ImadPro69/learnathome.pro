<?php
/**
 * PGHB Connect metabox template
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

if ( false === $pubguru_config ) : ?>

<div class="pubguru-not-connected space-y-4">
	<label for="m2-connect-consent" class="flex items-center w-full">
		<span class="self-start">
			<input type="checkbox" class="!mt-1 !mr-2" id="m2-connect-consent">
		</span>
		<span class="text-base">
			<?php
			printf(
				wp_kses_post(
					/* translators: %s link to privacy policy */
					__( 'This form is designed exclusively for MonetizeMore customers who wish to integrate Advanced Ads with the PubGuru Dashboard. By selecting "Connect PGHB," you agree to share your domain name to facilitate the connection with your PubGuru account, in alignment with our <a href="%s">Privacy Policy</a>. Rest assured, no additional information is exchanged, and Advanced Ads does not engage in any tracking activities.', 'advanced-ads' )
				),
				'https://wpadvancedads.com/privacy-policy/'
			);
			?>
		</span>
	</label>

	<div class="flex items-center gap-x-4">
		<button class="button button-primary js-pubguru-connect" disabled type="button"><?php esc_html_e( 'Connect PGHB', 'advanced-ads' ); ?></button>
		<div class="lds-ripple aa-spinner"><div></div><div></div></div>
	</div>
</div>
<?php endif; ?>

<div class="pubguru-connected<?php echo false === $pubguru_config ? ' hidden' : ''; ?>">
	<form id="pubguru-modules" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="POST" data-security="<?php echo esc_attr( wp_create_nonce( 'pubguru_module_changed' ) ); ?>">

		<p class="text-base mb-6">
			<?php
				echo wp_kses_post(
					__( 'To activate any of the settings with your PubGuru account, please select from the options below. Should you have any inquiries, feel free to <a href="https://www.monetizemore.com/contact/">click here<a/> to reach out to us or send an email to <a href="mailto:support@monetizemore.com">support@monetizemore.com</a>', 'advanced-ads' )
				);
				?>
		</p>
		<div class="advads-ui-switch-list">
			<div>
				<label for="pubguru-header-bidding" class="advads-ui-switch">
					<input type="checkbox" name="header_bidding" id="pubguru-header-bidding" class="sr-only peer" <?php checked( boolval( get_option( 'pubguru_module_header_bidding', false ) ) ); ?>>
					<div></div>
					<span>
						<?php esc_html_e( 'Install PubGuru Header Bidding', 'advanced-ads' ); ?>
					</span>
				</label>
			</div>

			<div>
				<label for="pubguru-tag-conversion" class="advads-ui-switch">
					<input type="checkbox" name="tag_conversion" id="pubguru-tag-conversion" <?php checked( boolval( get_option( 'pubguru_module_tag_conversion', false ) ) ); ?> />
					<div></div>
					<span>
						<?php esc_html_e( 'Activate Tag Conversion', 'advanced-ads' ); ?>
					</span>
				</label>
			</div>

			<div>
				<label for="pubguru-adstxt" class="advads-ui-switch">
					<input type="checkbox" name="ads_txt" id="pubguru-adstxt" <?php checked( boolval( get_option( 'pubguru_module_ads_txt', false ) ) ); ?> />
					<div></div>
					<span>
						<?php
						echo wp_kses(
							__( 'Enable ads.txt settings from the <a href="https://app.pubguru.com/ads-txt" target="_blank">PubGuru platform</a>', 'advanced-ads' ),
							[
								'a' => [
									'href'   => [],
									'title'  => [],
									'target' => [],
								],
							]
						);
						?>
					</span>
				</label>
			</div>

			<div>
				<label for="pubguru-traffic-cop" class="advads-ui-switch">
					<input type="checkbox" name="traffic_cop" id="pubguru-traffic-cop" <?php checked( boolval( get_option( 'pubguru_module_traffic_cop', false ) ) ); ?> />
					<div></div>
					<span class="pg-tc-trail <?php echo $has_traffic_cop ? 'hidden' : ''; ?>">
						<?php esc_html_e( 'Install Traffic Cop. 7 days free trial', 'advanced-ads' ); ?>
					</span>
					<span class="pg-tc-install <?php echo $has_traffic_cop ? '' : 'hidden'; ?>">
						<?php esc_html_e( 'Install Traffic Cop.', 'advanced-ads' ); ?>
					</span>
				</label>
			</div>

		</div>

	</form>

</div>
