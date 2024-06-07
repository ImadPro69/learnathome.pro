<?php
	/**
	 * Template: Levels
	 *
	 * See documentation for how to override the PMPro templates.
	 * @link https://www.paidmembershipspro.com/documentation/templates/
	 *
	 * @version 2.0
	 *
	 * @author Paid Memberships Pro
	 */
	global $wpdb, $pmpro_msg, $pmpro_msgt, $current_user;

	$pmpro_levels = pmpro_sort_levels_by_order( pmpro_getAllLevels(false, true) );
	$pmpro_levels = apply_filters( 'pmpro_levels_array', $pmpro_levels );

	if($pmpro_msg)
	{
		?>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo $pmpro_msg?></div>
		<?php
	}
?>

<div id="pmpro_levels_table" class="<?php echo pmpro_get_element_class( 'pmpro_table pmpro_checkout', 'pmpro_levels_table' ); ?> bg-transparent">

	<div class="row justify-content-center">

	<?php
		$count = 0;
		$has_any_level = false;
		foreach($pmpro_levels as $level)
		{
			$user_level = pmpro_getSpecificMembershipLevelForUser( $current_user->ID, $level->id );
			$has_level = ! empty( $user_level );
			$has_any_level = $has_level ?: $has_any_level;
			?>

			<div class="pmpro-levels-col col-md-6 col-lg <?php if($count++ % 2 == 0) { ?>odd<?php } ?><?php if( $has_level ) { ?> active<?php } ?>">

				<div class="card h-100 <?php if ( $user_level === $level ) { ?>border-primary<?php } ?>">

					<div class="card-body text-center pb-3 d-flex flex-column">

						<h2 class="text-center h3 mb-0 pb-0">
							<?php if ( $user_level ) : ?>
								<strong>
									<?php echo esc_html( $level->name ); ?>
								</strong>
							<?php else : ?>
								<?php echo esc_html( $level->name ); ?>
							<?php endif; ?>
						</h2>

						<?php
						$expiration_text = pmpro_getLevelExpiration( $level );
						if ( pmpro_isLevelFree( $level ) ) :
							$cost_text = '<strong>' . esc_html__( 'Free', 'paid-memberships-pro' ) . '</strong>';
						else :
							$cost_text = pmpro_getLevelCost( $level, true, true );
						endif;

						if ( ! empty( $cost_text ) ) : ?>

							<div class="level-short-price text-center py-3">
								<?php echo wp_kses_post( $cost_text ); ?>
							</div>

							<?php if ( ! empty( $level->description ) ) : ?>
								<div class="level-description mb-auto">
									<?php echo wp_kses_post( $level->description ); ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $expiration_text ) ) : ?>
								<div class="level-cost-expiration text-muted text-center small pt-3">
									<?php echo wp_kses_post( $expiration_text ); ?>
								</div>
							<?php endif; ?>

						<?php endif;?>

					</div>

					<div class="card-footer">

						<?php if ( ! $has_level ) { ?>
							<a class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-select', 'pmpro_btn-select' ); ?>" href="<?php echo esc_url( pmpro_url( "checkout", "?level=" . $level->id, "https" ) ) ?>"><?php esc_html_e('Select', 'paid-memberships-pro' );?></a>
						<?php } else { ?>
							<?php
							//if it's a one-time-payment level, offer a link to renew
							if( pmpro_isLevelExpiringSoon( $user_level ) && $level->allow_signups ) {
								?>
								<a class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-select', 'pmpro_btn-select' ); ?>" href="<?php echo esc_url( pmpro_url( "checkout", "?level=" . $level->id, "https" ) ) ?>"><?php esc_html_e('Renew', 'paid-memberships-pro' );?></a>
								<?php
							} else {
								?>
								<a class="<?php echo pmpro_get_element_class( 'pmpro_btn disabled', 'pmpro_btn' ); ?> disabled" href="<?php echo esc_url( pmpro_url( "account" ) ) ?>"><?php esc_html_e('Your&nbsp;Level', 'paid-memberships-pro' );?></a>
								<?php
							}
							?>
						<?php } ?>

					</div>

				</div>

			</div>
			<?php
		}
	?>
	</div>
</div>

<p class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?> d-none">
	<?php if( $has_any_level ) { ?>
		<a href="<?php echo esc_url( pmpro_url("account" ) ) ?>" id="pmpro_levels-return-account">&larr; <?php esc_html_e('Return to Your Account', 'paid-memberships-pro' );?></a>
	<?php } else { ?>
		<a href="<?php echo esc_url( home_url() ) ?>" id="pmpro_levels-return-home">&larr; <?php esc_html_e('Return to Home', 'paid-memberships-pro' );?></a>
	<?php } ?>
</p> <!-- end pmpro_actions_nav -->
