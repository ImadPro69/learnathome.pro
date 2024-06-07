<?php
/**
 * Template for Email Invoices
 *
 * @since 1.8.6
 * 
 * @var object $level
 * @var MemberOrder $order
 */
?>
<table style="width:600px;margin-left:auto;margin-right:auto;">
	<thead>
	<tr>
		<td rowspan="2" style="width:80%;">
			<h2><?php bloginfo( 'sitename' ); ?></h2>
		</td>
		<td><?php echo esc_html( __('Invoice #: ', 'paid-memberships-pro' ) . '&nbsp;' . $order->code ); ?></td>
	</tr>
	<tr>
		<td>
			<?php echo esc_html( __( 'Date:', 'paid-memberships-pro' ) . '&nbsp;' . date_i18n( get_option( 'date_format' ), $order->getTimestamp() ) ); ?>
		</td>
	</tr>
	<?php if(!empty($order->billing->name)): ?>
		<tr>
			<td style="padding-bottom:10px;">
				<strong><?php esc_html_e( 'Bill to:', 'paid-memberships-pro' ); ?></strong><br>
				<?php
					echo wp_kses_post( pmpro_formatAddress(
						$order->billing->name,
						$order->billing->street,
						"",
						$order->billing->city,
						$order->billing->state,
						$order->billing->zip,
						$order->billing->country,
						$order->billing->phone
					) ); 
				?>
				<?php endif; ?>
			</td>
		</tr>
	</thead>
	<tbody>
	<tr>
		<td colspan="2">
			<table style="width:100%;border-width:0px;border-collapse:collapse;">
				<tr style="border-width:1px;border-style:solid;border-collapse:collapse;">
					<th style="text-align:center;border-width:1px;border-style:solid;border-collapse:collapse;padding:4px;"><?php esc_html_e('ID', 'paid-memberships-pro' ); ?></th>
					<th style="border-width:1px;border-style:solid;border-collapse:collapse;padding:4px;"><?php esc_html_e('Item', 'paid-memberships-pro' ); ?></th>
					<th style="border-width:1px;border-style:solid;border-collapse:collapse;padding:4px;"><?php esc_html_e('Price', 'paid-memberships-pro' ); ?></th>
				</tr>
				<tr style="border-width:1px;border-style:solid;border-collapse:collapse;">
					<td style="text-align:center;border-width:1px;border-style:solid;border-collapse:collapse;padding:4px;"><?php echo esc_html( $level->id ); ?></td>
					<td style="border-width:1px;border-style:solid;border-collapse:collapse;padding:4px;"><?php echo esc_html( $level->name ); ?></td>
					<td style="border-width:1px;border-style:solid;border-collapse:collapse;text-align:right;padding:4px;"><?php echo pmpro_escape_price( pmpro_formatPrice( $order->subtotal ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
				<?php
					if ( (float)$order->total > 0 ) {
						$pmpro_price_parts = pmpro_get_price_parts( $order, 'array' );
						foreach ( $pmpro_price_parts as $pmpro_price_part ) { ?>
							<tr style="border-width:1px;border-style:solid;border-collapse:collapse;padding:4px;">
								<th colspan="2" style="text-align:right;border-width:1px;border-style:solid;border-collapse:collapse;padding:4px;">
									<?php echo esc_html( $pmpro_price_part['label'] ); ?>
								</th>
								<td style="text-align:right;border-width:1px;border-style:solid;border-collapse:collapse;padding:4px;">
									<?php echo esc_html( $pmpro_price_part['value'] ); ?>
								</td>
							</tr>
							<?php
						}
					}
				?>
			</table>
		</td>
	</tr>
	</tbody>
</table>
