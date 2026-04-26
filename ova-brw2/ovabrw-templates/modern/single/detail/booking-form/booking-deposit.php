<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Enable deposit
$enable_deposit = $product->get_meta_value( 'enable_deposit' );

if ( 'yes' === $enable_deposit ):
	$pay_full 	= $product->get_meta_value( 'force_deposit' );
	$default 	= $product->get_meta_value( 'default_deposit', 'full' );
	$type 		= $product->get_meta_value( 'type_deposit' );
	$value 		= $product->get_meta_value( 'amount_deposit' );

	if ( 'value' === $type ) {
		$value = ovabrw_wc_price( $value );
	} elseif ( 'percent' === $type ) {
		$value .= '%';
	}
?>
	<div class="ovabrw-modern-deposit">
		<?php if ( 'yes' === $pay_full ): ?>
			<div class="deposit-label pay-full <?php echo 'full' === $default ? 'active' : ''; ?>">
				<?php esc_html_e( 'Pay 100%', 'ova-brw' ); ?>
			</div>
			<div class="deposit-label pay-deposit <?php echo 'deposit' === $default ? 'active' : ''; ?>">
				<?php echo sprintf( esc_html__( 'Deposit Option %s Per item', 'ova-brw' ), wc_format_localized_price( $value ) ); ?>
			</div>
		<?php else: ?>
			<div class="deposit-label pay-deposit active">
				<?php echo sprintf( esc_html__( 'Deposit Option %s Per item', 'ova-brw' ), wc_format_localized_price( $value ) ); ?>
			</div>
		<?php endif; ?>
		<div class="deposit-type">
			<?php if ( 'yes' === $pay_full ):
				$pay_full_id 	= ovabrw_unique_id( 'pay_full' );
				$pay_deposit_id = ovabrw_unique_id( 'pay_deposit' );
			?>
				<label for="<?php echo esc_attr( $pay_full_id ); ?>" class="ovabrw-label-field">
					<?php ovabrw_text_input([
						'type' 		=> 'radio',
						'id' 		=> $pay_full_id,
						'class' 	=> 'pay-full',
						'name' 		=> $product->get_meta_key( 'type_deposit' ),
						'value' 	=> 'full',
						'checked' 	=> 'full' === $default ? true : false
					]); ?>
					<span class="checkmark">
						<?php esc_html_e( 'Full Payment', 'ova-brw' ); ?>
					</span>
				</label>
				<label for="<?php echo esc_attr( $pay_deposit_id ); ?>" class="ovabrw-label-field">
					<?php ovabrw_text_input([
						'type' 		=> 'radio',
						'id' 		=> $pay_deposit_id,
						'class' 	=> 'pay-deposit',
						'name' 		=> $product->get_meta_key( 'type_deposit' ),
						'value' 	=> 'deposit',
						'checked' 	=> 'deposit' === $default ? true : false
					]); ?>
					<span class="checkmark">
						<?php esc_html_e( 'Deposit Payment', 'ova-brw' ); ?>
					</span>
				</label>
			<?php else:
				$pay_deposit_id = ovabrw_unique_id( 'pay_deposit' );
			?>
				<label for="<?php echo esc_attr( $pay_deposit_id ); ?>" class="ovabrw-label-field">
					<?php ovabrw_text_input([
						'type' 		=> 'radio',
						'id' 		=> $pay_deposit_id,
						'class' 	=> 'pay-deposit',
						'name' 		=> $product->get_meta_key( 'type_deposit' ),
						'value' 	=> 'deposit',
						'checked' 	=> true
					]); ?>
					<span class="checkmark">
						<?php esc_html_e( 'Deposit Payment', 'ova-brw' ); ?>
					</span>
				</label>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>