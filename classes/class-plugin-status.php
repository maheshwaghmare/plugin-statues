<?php
/**
 * Plugin Status
 *
 * @package Plugin Status
 * @since 1.0.0
 */

if ( ! class_exists( 'Plugin_Status' ) ) :

	/**
	 * Plugin Status
	 *
	 * @since 1.0.0
	 */
	class Plugin_Status {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class Instance.
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 * @return object initialized object of class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			if ( is_multisite() ) {
				add_action( 'admin_notices', array( $this, 'multisite_support' ) );
				return;
			}

			add_action( 'admin_menu', array( $this, 'add_to_menus' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'admin_init', array( $this, 'export_json' ) );
			add_action( 'admin_init', array( $this, 'import_json' ) );
			add_action( 'plugin_action_links_' . PLUGIN_STATUS_BASE, array( $this, 'action_links' ) );
		}

		/**
		 * Multisite Support Notice
		 *
		 * @since 1.0.0
		 */
		function multisite_support() {
			?>
			<div class="notice notice-error">
				<p><?php _e( 'The plugin <b>"Plugin Status - Export and Import"</b> is not supported on multisite. You\'ll get the multisite support for this plugin in future plugin release.', 'plugin-status' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Import our exported file
		 *
		 * @since 1.0.0
		 */
		public function import_json() {
			if ( empty( $_POST['plugin-status-action'] ) || 'import' !== $_POST['plugin-status-action'] ) {
				return;
			}

			if ( isset( $_POST['plugin-status-action-nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['plugin-status-action-nonce'] ) ), 'plugin-status-action-nonce' ) ) {
				return;
			}

			if ( ! current_user_can( 'activate_plugins' ) ) {
				add_action( 'admin_notices', array( $this, 'user_permissions' ) );
				return;
			}

			$filename  = $_FILES['file']['name'];
			$file_info = explode( '.', $filename );
			$extension = end( $file_info );

			if ( 'json' !== $extension ) {
				wp_die( esc_html__( 'Please upload a valid .json file', 'plugin-status' ) );
			}

			$file = $_FILES['file']['tmp_name'];

			if ( empty( $file ) ) {
				wp_die( esc_html__( 'Please upload a file to import', 'plugin-status' ) );
			}

			// Retrieve the settings from the file and convert the JSON object to an array.
			$all_plugins = json_decode( file_get_contents( $file ), true );

			foreach ( $all_plugins as $status => $plugins ) {
				if ( 'active' === $status ) {
					foreach ( $plugins as $plugin_init => $plugin ) {
						activate_plugin( $plugin_init, '', false, true );
					}
				} elseif ( 'inactive' === $status ) {
					foreach ( $plugins as $plugin_init => $plugin ) {
						deactivate_plugins( $plugin_init, true, null );
					}
				}
			}

			wp_redirect( admin_url( 'plugins.php' ) );

			add_action( 'admin_notices', array( $this, 'imported_successfully' ) );
		}

		/**
		 * User Permissions
		 *
		 * @since 1.0.0
		 */
		public function user_permissions() {
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'You have not activate permissions! Please contact administrator.', 'plugin-status' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Imported notice
		 *
		 * @since 1.0.0
		 */
		public function imported_successfully() {
			?>
			<div class="notice notice-success">
				<p><?php esc_html_e( 'Successfully updated all plugin status.', 'plugin-status' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Export flow
		 *
		 * @since 1.0.0
		 */
		public function export_json() {
			if ( empty( $_POST['plugin-status-action'] ) || 'export' !== $_POST['plugin-status-action'] ) {
				return;
			}

			if ( isset( $_POST['plugin-status-action-nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['plugin-status-action-nonce'] ) ), 'plugin-status-action-nonce' ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Export file name.
			$default_filename = 'plugin-status-' . gmdate( 'm-d-Y' );
			$filename         = isset( $_POST['filename'] ) ? sanitize_file_name( $_POST['filename'] ) : $default_filename;
			if ( empty( $filename ) ) {
				$filename = $default_filename;
			}

			$all_plugins    = get_plugins();
			$active_plugins = get_option( 'active_plugins', array() );
			$json           = array(
				'active'   => array(),
				'inactive' => array(),
			);

			// Categories active and inactive plugin status.
			foreach ( $all_plugins as $plugin_init => $plugin ) {
				if ( in_array( $plugin_init, $active_plugins, true ) ) {
					$json['active'][ $plugin_init ] = $plugin;
				} else {
					$json['inactive'][ $plugin_init ] = $plugin;
				}
			}

			// Perform exporting JSON file.
			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename . '.json' );
			header( 'Expires: 0' );

			echo wp_json_encode( $json );
			exit;
		}

		/**
		 * Add menus
		 *
		 * @since 1.0.0
		 */
		public function add_to_menus() {
			add_submenu_page( 'plugins.php', __( 'Export', 'plugin-status' ), __( 'Export', 'plugin-status' ), 'export', 'export', array( $this, 'export_markup' ) );
			add_submenu_page( 'plugins.php', __( 'Import', 'plugin-status' ), __( 'Import', 'plugin-status' ), 'import', 'import', array( $this, 'import_markup' ) );
		}

		/**
		 * Export flow markup
		 *
		 * @since 1.0.0
		 */
		public function export_markup() {
			include_once PLUGIN_STATUS_DIR . 'includes/export.php';
		}

		/**
		 * Import flow markup
		 *
		 * @since 1.0.0
		 */
		public function import_markup() {
			include_once PLUGIN_STATUS_DIR . 'includes/import.php';
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @since 1.0.0
		 * @param   mixed $links Plugin Action links.
		 * @return  array
		 */
		function action_links( $links ) {
			$action_links = array(
				'import' => '<a href="' . admin_url( 'plugins.php?page=import' ) . '" aria-label="' . esc_attr__( 'Import', 'plugin-status' ) . '">' . esc_html__( 'Import', 'plugin-status' ) . '</a>',
				'export' => '<a href="' . admin_url( 'plugins.php?page=export' ) . '" aria-label="' . esc_attr__( 'Export', 'plugin-status' ) . '">' . esc_html__( 'Export', 'plugin-status' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		}

		/**
		 * Enqueue Assets.
		 *
		 * @version 1.0.0
		 *
		 * @param  string $hook Current hook name.
		 * @return void
		 */
		function enqueue_assets( $hook ) {

			if ( 'plugins.php' !== $hook ) {
				return;
			}

			wp_enqueue_script( 'plugin-status', PLUGIN_STATUS_URI . 'assets/js/script.js', array( 'jquery' ), PLUGIN_STATUS_VER, true );

			$data = array(
				'import_url' => admin_url( 'plugins.php?page=import' ),
				'export_url' => admin_url( 'plugins.php?page=export' ),
			);
			wp_localize_script( 'plugin-status', 'pluginStatus', $data );
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Plugin_Status::get_instance();

endif;
