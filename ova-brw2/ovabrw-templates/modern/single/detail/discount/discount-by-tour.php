<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get discount form
$discount_form = $product->get_meta_value( 'discount_from' );
if ( !ovabrw_array_exists( $discount_form ) ) return;

// Get discount to
$discount_to = $product->get_meta_value( 'discount_to' );

// Get guest options
$guest_options = $product->get_guests();

// Discount applicable
$applicable = $product->get_meta_value( 'discount_applicable' );

?>
<div class="ovabrw-product-discount">
	<div class="ovabrw-label">
		<?php esc_html_e( 'Base Discounts', 'ova-brw' ); ?>
	</div>
	<table class="ovabrw-table">
		<thead>
			<tr>
				<th>
					<?php if ( 'only' === $applicable ) {
						$first_guest_label = isset( $guest_options[0]['label'] ) ? $guest_options[0]['label'] : 0;

						echo sprintf( esc_html__( 'From - To (No. of %s)', 'ova-brw' ), $first_guest_label );
					} else {
						esc_html_e( 'From - To (No. of guests)', 'ova-brw' );
					} ?>
				</th>
				<?php foreach ( $guest_options as $guest ): ?>
					<th><?php echo esc_html( $guest['label'] ); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $discount_form as $i => $from ):
				// Get discount to
				$to = ovabrw_get_meta_data( $i, $discount_to );
			?>
				<tr>
					<td><?php echo sprintf( esc_html__( '%1$s - %2$s', 'ova-brw' ), $from, $to ); ?></td>
					<?php foreach ( $guest_options as $guest ):
						// Get discount prices
						$discount_prices = $product->get_meta_value( 'discount_'.$guest['name'].'_price' );

						// Get guest price
						$guest_price = ovabrw_get_meta_data( $i, $discount_prices );
					?>
						<td><?php echo ovabrw_wc_price( $guest_price ); ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>