<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get products
$products = OVABRW()->options->get_product_from_search([
	'posts_per_page' 	=> ovabrw_get_meta_data( 'posts_per_page', $args, 6 ),
	'orderby' 			=> ovabrw_get_meta_data( 'orderby', $args, 'ID' ),
	'order' 			=> ovabrw_get_meta_data( 'order', $args, 'DESC' ),
	'category_ids' 		=> ovabrw_get_meta_data( 'categories', $args )
]);

// Slide options
$slide_options = ovabrw_get_meta_data( 'slide_options', $args, [] );

if ( $products->have_posts() ): ?>
	<div class="ovabrw-product-filter" data-options="<?php echo esc_attr( json_encode( $slide_options ) ); ?>">
		<div class="swiper swiper-loading">
			<div class="swiper-wrapper">
				<?php while ( $products->have_posts() ): $products->the_post(); ?>
					<div class="swiper-slide">
						<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.sanitize_file_name( $args['template'] ).'.php', [
							'thumbnail_type' => 'image'
						]); ?>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
		<?php if ( ovabrw_get_meta_data( 'nav', $slide_options ) ): ?>
			<div class="button-nav button-prev">
				<i class="brwicon-left" aria-hidden="true"></i>
			</div>
			<div class="button-nav button-next">
				<i class="brwicon-right-1" aria-hidden="true"></i>
			</div>
		<?php endif; ?>
		<?php if ( ovabrw_get_meta_data( 'dots', $slide_options ) ): ?>
			<div class="button-dots"></div>
		<?php endif; ?>
	</div>
<?php else: ?>
	<div class="not-found">
		<?php esc_html_e( 'No product found.', 'ova-brw' ); ?>
	</div>
<?php endif; wp_reset_postdata(); ?>