<?php
/**
 * Plugin Statues
 *
 * @package Plugin Statues
 * @since 1.0.0
 */

if ( ! class_exists( 'Plugin_Statues' ) ) :

	/**
	 * Plugin Statues
	 *
	 * @since 1.0.0
	 */
	class Plugin_Statues {

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
			add_action( 'plugin_action_links_' . PLUGIN_STATUES_BASE, array( $this, 'action_links' ) );
		}

		/**
		 * Multisite Support Notice
		 *
		 * @since 1.0.0
		 */
		function multisite_support() {
			?>
			<div class="notice notice-error">
				<p><?php _e( 'The plugin <b>"Plugin Statues - Export and Import"</b> is not supported on multisite. You\'ll get the multisite support for this plugin in future plugin release.', 'plugin-statues' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Import our exported file
		 *
		 * @since 1.0.0
		 */
		public function import_json() {
			if ( empty( $_POST['plugin-statues-action'] ) || 'import' !== $_POST['plugin-statues-action'] ) {
				return;
			}

			if ( isset( $_POST['plugin-statues-action-nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['plugin-statues-action-nonce'] ) ), 'plugin-statues-action-nonce' ) ) {
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
				wp_die( esc_html__( 'Please upload a valid .json file', 'plugin-statues' ) );
			}

			$file = $_FILES['file']['tmp_name'];

			if ( empty( $file ) ) {
				wp_die( esc_html__( 'Please upload a file to import', 'plugin-statues' ) );
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
				<p><?php esc_html_e( 'You have not activate permissions! Please contact administrator.', 'plugin-statues' ); ?></p>
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
				<p><?php esc_html_e( 'Successfully updated all plugin statues.', 'plugin-statues' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Export flow
		 *
		 * @since 1.0.0
		 */
		public function export_json() {
			if ( empty( $_POST['plugin-statues-action'] ) || 'export' !== $_POST['plugin-statues-action'] ) {
				return;
			}

			if ( isset( $_POST['plugin-statues-action-nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['plugin-statues-action-nonce'] ) ), 'plugin-statues-action-nonce' ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Export file name.
			$default_filename = 'plugin-statues-' . gmdate( 'm-d-Y' );
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

			// Categories active and inactive plugin statues.
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
			add_submenu_page( 'plugins.php', __( 'Export', 'plugin-statues' ), __( 'Export', 'plugin-statues' ), 'export', 'export', array( $this, 'export_markup' ) );
			add_submenu_page( 'plugins.php', __( 'Import', 'plugin-statues' ), __( 'Import', 'plugin-statues' ), 'import', 'import', array( $this, 'import_markup' ) );
		}

		/**
		 * Export flow markup
		 *
		 * @since 1.0.0
		 */
		public function export_markup() {
			include_once PLUGIN_STATUES_DIR . 'includes/export.php';
		}

		/**
		 * Import flow markup
		 *
		 * @since 1.0.0
		 */
		public function import_markup() {
			include_once PLUGIN_STATUES_DIR . 'includes/import.php';
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
				'import' => '<a href="' . admin_url( 'plugins.php?page=import' ) . '" aria-label="' . esc_attr__( 'Import', 'plugin-statues' ) . '">' . esc_html__( 'Import', 'plugin-statues' ) . '</a>',
				'export' => '<a href="' . admin_url( 'plugins.php?page=export' ) . '" aria-label="' . esc_attr__( 'Export', 'plugin-statues' ) . '">' . esc_html__( 'Export', 'plugin-statues' ) . '</a>',
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

			wp_enqueue_script( 'plugin-statues', PLUGIN_STATUES_URI . 'assets/js/script.js', array( 'jquery' ), PLUGIN_STATUES_VER, true );

			$data = array(
				'import_url' => admin_url( 'plugins.php?page=import' ),
				'export_url' => admin_url( 'plugins.php?page=export' ),
			);
			wp_localize_script( 'plugin-statues', 'pluginStatues', $data );
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Plugin_Statues::get_instance();

endif;
