<?php
/**
 * Export
 *
 * @package Plugin Status
 */

$filename = 'plugin-status-' . gmdate( 'm-d-Y' );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Export', 'plugin-status' ); ?></h1>
	<div class="plugin-status-ie" style="margin-top: 1em;">
		<div class="postbox">
			<div class="inside">
				<h3><?php esc_html_e( 'Export - Plugin Status', 'plugin-status' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Export the all the plugins current plugin activate and inactivate status in .JSON file.', 'plugin-status' ); ?></p>					
				<form method="post">
					<?php if ( isset( $_GET['extra'] ) ) { ?>
						<p><input type="text" name="filename" value="<?php echo esc_attr( $filename ); ?>" class="regular-text" /> <i><?php esc_html__( '(Optional) Name of the exported file.', 'plugin-status' ); ?></i></p>
					<?php } ?>
					<p><input type="hidden" name="plugin-status-action" value="export" /></p>
					<p style="margin-bottom:0">
						<?php wp_nonce_field( 'plugin-status-action-nonce', 'plugin-status-action-nonce' ); ?>
						<?php submit_button( __( 'Export', 'plugin-status' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
