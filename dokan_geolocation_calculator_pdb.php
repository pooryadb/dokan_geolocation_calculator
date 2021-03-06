<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://romroid.ir
 * @since             1.0.0
 * @package           Dgcpdb
 *
 * @wordpress-plugin
 * Plugin Name:       Dokan Geolocation Calculator
 * Plugin URI:        Helps you to find nearest shop using user latitude & longitude.
 * Description:       Admin sets coordinates for each store and plugin find nearest store from user location in a API request.
 * Version:           1.0.6
 * Author:            poorya dehghan berenji
 * Author URI:        http://romroid.ir
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dgcpdb
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('DGCPDB_VERSION', '1.0.6');
define('DGCPDB_PLUGIN_SLUG', 'dgcpdb');
define('DGCPDB_PLUGIN_NAME', __('Dokan Geolocation Calculator', 'dgcpdb'));

require plugin_dir_path(__FILE__) . 'includes/Constants_Dgcpdb.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dgcpdb-activator.php
 */
function activate_dgcpdb() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-dgcpdb-activator.php';
	Dgcpdb_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-dgcpdb-deactivator.php
 */
function deactivate_dgcpdb() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-dgcpdb-deactivator.php';
	Dgcpdb_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_dgcpdb');
register_deactivation_hook(__FILE__, 'deactivate_dgcpdb');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-dgcpdb.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_dgcpdb() {

	$plugin = new Dgcpdb();
	$plugin->run();

}

run_dgcpdb();
