<?php
/**
 * Plugin Name: Plugin Statues - Export and Import
 * Description: Export the JSON file of current plugin active and inactive statues. And Import the same to to auto activate or deactivate the pluigns from the JSON configuration file.
 * Plugin URI: https://github.com/maheshwaghmare/plugin-statues/
 * Author: Mahesh M. Waghmare
 * Author URI: https://maheshwaghmare.com/
 * Version: 1.0.0
 * License: GNU General Public License v2.0
 * Text Domain: plugin-statues
 *
 * @package Plugin Statues
 */

// Set constants.
define( 'PLUGIN_STATUES_VER', '1.0.0' );
define( 'PLUGIN_STATUES_FILE', __FILE__ );
define( 'PLUGIN_STATUES_BASE', plugin_basename( PLUGIN_STATUES_FILE ) );
define( 'PLUGIN_STATUES_DIR', plugin_dir_path( PLUGIN_STATUES_FILE ) );
define( 'PLUGIN_STATUES_URI', plugins_url( '/', PLUGIN_STATUES_FILE ) );

require_once PLUGIN_STATUES_DIR . 'classes/class-plugin-statues.php';
