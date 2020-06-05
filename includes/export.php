<?php
/**
 * Export
 *
 * @package Plugin Statues
 */

$filename = 'plugin-statues-' . gmdate( 'm-d-Y' );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Export', 'plugin-statues' ); ?></h1>
	<div class="plugin-statues-ie" style="margin-top: 1em;">
		<div class="postbox">
			<div class="inside">
				<h3><?php esc_html_e( 'Export - Plugin Statues', 'plugin-statues' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Export the all the plugins current plugin activate and inactivate statues in .JSON file.', 'plugin-statues' ); ?></p>					
				<form method="post">
					<?php if ( isset( $_GET['extra'] ) ) { ?>
						<p><input type="text" name="filename" value="<?php echo esc_attr( $filename ); ?>" class="regular-text" /> <i><?php esc_html__( '(Optional) Name of the exported file.', 'plugin-statues' ); ?></i></p>
					<?php } ?>
					<p><input type="hidden" name="plugin-statues-action" value="export" /></p>
					<p style="margin-bottom:0">
						<?php wp_nonce_field( 'plugin-statues-action-nonce', 'plugin-statues-action-nonce' ); ?>
						<?php submit_button( __( 'Export', 'plugin-statues' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
