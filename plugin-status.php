<?php
/**
 * Plugin Name: Plugin Status - Export and Import
 * Description: Export the JSON file of current plugin active and inactive list. And Import the same to to auto activate or deactivate the pluigns from the JSON file.
 * Plugin URI: https://github.com/maheshwaghmare/plugin-status/
 * Author: Mahesh M. Waghmare
 * Author URI: https://maheshwaghmare.com/
 * Version: 1.0.0
 * License: GNU General Public License v2.0
 * Text Domain: plugin-status
 *
 * @package Plugin Status
 */

// Set constants.
define( 'PLUGIN_STATUS_VER', '1.0.0' );
define( 'PLUGIN_STATUS_FILE', __FILE__ );
define( 'PLUGIN_STATUS_BASE', plugin_basename( PLUGIN_STATUS_FILE ) );
define( 'PLUGIN_STATUS_DIR', plugin_dir_path( PLUGIN_STATUS_FILE ) );
define( 'PLUGIN_STATUS_URI', plugins_url( '/', PLUGIN_STATUS_FILE ) );

require_once PLUGIN_STATUS_DIR . 'classes/class-plugin-status.php';
