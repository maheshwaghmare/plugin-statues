<?php
/**
 * Import
 *
 * @package Plugin Statues
 */

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Import', 'plugin-statues' ); ?></h1>
	<div class="plugin-statues-ie" style="margin-top: 1em;">
		<div class="postbox">
			<div class="inside">
				<h3><?php esc_html_e( 'Import - Plugin Statues', 'plugin-statues' ); ?></h3>
				<p class="description"><?php esc_html_e( 'This tool allows you to import the plugin statues from the JSON file.', 'plugin-statues' ); ?></p>
				<form method="post" enctype="multipart/form-data">
					<p>
						<input type="file" name="file"/>
						<input type="hidden" name="plugin-statues-action" value="import" />
					</p>
					<p style="margin-bottom:0">
						<?php wp_nonce_field( 'plugin-statues-action-nonce', 'plugin-statues-action-nonce' ); ?>
						<?php submit_button( __( 'Import', 'plugin-statues' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
