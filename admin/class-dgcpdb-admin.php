<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://romroid.ir
 * @since      1.0.0
 *
 * @package    Dgcpdb
 * @subpackage Dgcpdb/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dgcpdb
 * @subpackage Dgcpdb/admin
 * @author     poorya dehghan berenji <dev.poorya.db@gmail.com>
 */
class Dgcpdb_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dgcpdb_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dgcpdb_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/dgcpdb-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dgcpdb_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dgcpdb_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/dgcpdb-admin.js', array('jquery'), $this->version, false);

	}

	public function check_dokan_state() {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');

		if (!is_plugin_active('dokan-lite/dokan.php')) {
			$message = sprintf(__('please enable dokan-lite plugin or %s plugin will not work correctly', 'dgcpdb'), DGCPDB_PLUGIN_NAME);
			echo "<div class='notice notice-error'><p>$message</p></div>";
		}

		if (!is_plugin_active('dokan-pro/dokan-pro.php')) {
			$message = sprintf(__('please enable dokan-pro plugin or %s plugin will not work correctly', 'dgcpdb'), DGCPDB_PLUGIN_NAME);
			echo "<div class='notice notice-error'><p>$message</p></div>";
		}
	}

	/**
	 * Register the admin menus
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menus() {
		$position   = 16;
		$capability = 'manage_options';
		$main_slug  = Constants_Dgcpdb::main_menu_slug;


		$page_title = __('Dokan stores geolocation', 'dgcpdb');
		$menu_title = __('Dokan stores PDB', 'dgcpdb');
		$function   = 'dgcpdb_main_slug_callback';

		add_menu_page(
			$page_title,
			$menu_title,
			$capability,
			$main_slug,
			$function,
			'dashicons-location',
			$position
		);

		//---------------------------------

		$page_title = __("Coordinates", "sr_pdb");
		$menu_title = __("Coordinates", "sr_pdb");
		$slug       = $main_slug;
		$function   = 'dgcpdb_main_slug_callback';

		function dgcpdb_main_slug_callback() {
			require_once plugin_dir_path(__FILE__) . 'partials/dgcpdb-admin-coordinate.php';
		}

		add_submenu_page(
			$main_slug,
			$page_title,
			$menu_title,
			$capability,
			$slug,
			$function
		);
	}

}
