<?php
/**
 * Import
 *
 * @package Plugin Status
 */

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Import', 'plugin-status' ); ?></h1>
	<div class="plugin-status-ie" style="margin-top: 1em;">
		<div class="postbox">
			<div class="inside">
				<h3><?php esc_html_e( 'Import - Plugin Status', 'plugin-status' ); ?></h3>
				<p class="description"><?php esc_html_e( 'This tool allows you to import the plugin status from the JSON file.', 'plugin-status' ); ?></p>
				<form method="post" enctype="multipart/form-data">
					<p>
						<input type="file" name="file"/>
						<input type="hidden" name="plugin-status-action" value="import" />
					</p>
					<p style="margin-bottom:0">
						<?php wp_nonce_field( 'plugin-status-action-nonce', 'plugin-status-action-nonce' ); ?>
						<?php submit_button( __( 'Import', 'plugin-status' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
