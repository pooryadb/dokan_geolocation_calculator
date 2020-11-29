<?php

/**
 * The WP-api functionality of the plugin.
 *
 * @link       http://romroid.ir
 * @since      1.0.0
 *
 * @package    Dgcpdb
 * @subpackage Dgcpdb/public
 */

/**
 * The  WP-api functionality of the plugin.
 *
 * Defines the plugin name, version, api endpoints
 *
 * @package    Dgcpdb
 * @subpackage Dgcpdb/public/api
 * @author     poorya dehghan berenji <dev.poorya.db@gmail.com>
 */
class ApiBaseController extends WP_REST_Controller {

	/**
	 * The MAIN-ROUTE of API.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private $API_ROUTE   = 'dgcpdb/v';

	/**
	 * The VERSION of API.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private $API_VERSION = '1';
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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		require plugin_dir_path(dirname(__FILE__)) . 'api/class-dgcpdb-ApiDefaultController.php';
	}

	/**
	 * Register the Endpoints of API.
	 *
	 * @since    1.0.0
	 */
	public function register_routes() {
		$namespace = $this->API_ROUTE . $this->API_VERSION;

		register_rest_route(
			$namespace,
			'/find_store',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array(new ApiDefaultController('find_store'), 'init'),
				)
			)
		);

		//add_action('rest_api_init', 'customize_rest_cors', 15);

	}

	public function customize_rest_cors() {
		remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
		remove_filter('rest_post_dispatch', 'rest_send_allow_header');
	}
}