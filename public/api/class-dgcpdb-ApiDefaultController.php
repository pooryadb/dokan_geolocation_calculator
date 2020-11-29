<?php

class ApiDefaultController extends ApiBaseController {
	public $method;
	public $response;
	public $response_status;

	public function __construct($method) {
		$this->method          = $method;
		$this->response_status = 404;
		$this->response        = array(
			'message' => '',
		);
	}

	public function init(WP_REST_Request $request) {
		try {
			if ($this->is_api_enabled()) {
				throw new Exception('API is disabled.', 204);
			}
			if (!method_exists($this, $this->method)) {
				throw new Exception('No method exists.', 502);
			}
			$this->{$this->method}($request);
		} catch (Exception $e) {
			$this->response_status     = $e->getCode();
			$this->response['message'] = $e->getMessage();
		}

		return new WP_REST_Response($this->response, $this->response_status);
	}


	/**
	 * @return bool
	 */
	private function is_api_enabled() {
		$enable_opt = get_option(Constants_Dgcpdb::enable_api_option_key, 'yes');
		return $enable_opt == 'yes';
	}

	public function find_store(WP_REST_Request $request) {

		$lat_param = $request->get_param('lat');
		$lng_param = $request->get_param('lng');
		if (isset($lat_param) && isset($lng_param)) {
			$cityName               = $this->find_nearest_store_city_name($lat_param, $lng_param);
			$this->response['city'] = $cityName;
			$this->response_status  = 200;
		} else {
			$this->response        = array(
				'message' => '"lat" or "lng" not provided!',
				'city'    => NULL,
			);
			$this->response_status = 400;
		}

		return new WP_REST_Response($this->response, $this->response_status);
	}

	/**
	 * @param string $lat float format - min: 0, max: 90, step: 0.000001
	 * @param string $lng float format - min: 0, max: 180, step: 0.000001
	 *
	 * @return string|NULL city name or null if not found
	 */
	private function find_nearest_store_city_name($lat, $lng) {
		return NULL;
	}
}