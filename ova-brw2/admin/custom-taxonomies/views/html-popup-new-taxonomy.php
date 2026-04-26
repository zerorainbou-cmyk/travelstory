<?php defined( 'ABSPATH' ) || exit(); ?>

<form action="" method="post" id="ovabrw-popup-taxonomy-form" enctype="multipart/form-data">
	<?php ovabrw_wp_text_input([
		'type' 	=> 'hidden',
		'name' 	=> 'ovabrw-taxonomy-action',
		'value'	=> $action
	]); ?>
	<button class="popup-taxonomy-close">X</button>
	<table width="100%">
		<tbody>
			<?php add_action( OVABRW_PREFIX.'popup_add_taxonomy_before', $action, $slug ); ?>
			<tr class="slug">
				<td class="label ovabrw-required">
					<?php esc_html_e( 'Slug', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'class' 		=> 'ovabrw-input-required',
						'name' 			=> 'slug',
						'value' 		=> $slug,
						'placeholder' 	=> esc_html__( 'taxonomy_1', 'ova-brw' ),
						'attrs' 		=> [
							'autocomplete' => 'off'
						]
					]); ?>
					<span>
						<em>
							<?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
						</em><br>
						<em>
							<?php esc_html_e( 'Taxonomy key, must not exceed 32 characters', 'ova-brw' ); ?>
						</em>
					</span>
				</td>
			</tr>
			<tr class="name">
				<td class="label ovabrw-required">
					<?php esc_html_e( 'Name', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'class' 		=> 'ovabrw-input-required',
						'name' 			=> 'name',
						'placeholder' 	=> esc_html__( 'taxonomy_1', 'ova-brw' ),
						'attrs' 		=> [
							'autocomplete' => 'off'
						]
					]); ?>
				</td>
			</tr>
			<tr class="name">
				<td class="label ovabrw-required">
					<?php esc_html_e( 'Singular name', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'class' 		=> 'ovabrw-input-required',
						'name' 			=> 'singular_name',
						'placeholder' 	=> esc_html__( 'Taxonomys 1', 'ova-brw' ),
						'attrs' 		=> [
							'autocomplete' => 'off'
						]
					]); ?>
				</td>
			</tr>
			<tr class="label-frontend">
				<td class="label">
					<?php esc_html_e( 'Label frontend', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'name' 			=> 'label_frontend',
						'placeholder' 	=> esc_html__( 'enter label', 'ova-brw' ),
						'attrs' 		=> [
							'autocomplete' => 'off'
						]
					]); ?>
				</td>
			</tr>
			<?php add_action( OVABRW_PREFIX.'popup_add_taxonomy_after', $action, $slug ); ?>
			<tr class="status">
				<td class="label"></td>
				<td>
					<label>
						<?php ovabrw_wp_text_input([
							'type' 		=> 'checkbox',
							'name' 		=> 'enabled',
							'value' 	=> 'on',
							'checked' 	=> true
						]);

						esc_html_e( 'Enable', 'ova-brw' ); ?>
					</label>
					<label>
						<?php ovabrw_wp_text_input([
		            		'type' 		=> 'checkbox',
							'name' 		=> 'show_listing',
							'value' 	=> 'on',
							'checked' 	=> true
						]);

						esc_html_e( 'Show in Listing', 'ova-brw' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="ovabrw-taxonomy-submit">
		<button type="submit" class="button button-primary">
			<?php esc_html_e( 'Save', 'ova-brw' ); ?>
		</button>
		<div class="ovabrw-loading">
			<span class="dashicons dashicons-update-alt"></span>
		</div>
	</div>
</form>