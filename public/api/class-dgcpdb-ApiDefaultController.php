<?php

class ApiDefaultController extends ApiBaseController {
	public $method;
	public $response;
	public $response_status;

	/**
	 * @param string $lat float format - min: 0, max: 90, step: 0.000001
	 */
	private $lat_param;
	/**
	 * @param string $lng float format - min: 0, max: 180, step: 0.000001
	 */
	private $lng_param;

	public function __construct($method) {
		$this->method          = $method;
		$this->response_status = 404;
		$this->response        = array(
			'message' => '',
		);
	}

	public function init(WP_REST_Request $request) {
		try {
			if (!$this->is_api_enabled()) {
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

		$this->lat_param = $request->get_param('lat');
		$this->lng_param = $request->get_param('lng');
		if (isset($this->lat_param) && isset($this->lng_param)) {
			$result_city = $this->get_nearest_store_city_name();
			if (is_null($result_city)) {
				$this->response = array(
					'message' => get_option(Constants_Dgcpdb::not_found_city_message_api_option_key, __("There is no store near you!", 'dgcpdb')),
					'city'    => NULL,
				);
			} else {
				$this->response = array(
					'message' => '',
					'city'    => $result_city,
				);
			}
			$this->response_status = 200;
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
	 * @return string|NULL city name or null if not found
	 */
	private function get_nearest_store_city_name() {
		$dokan_user_id_list = array_column(
			get_users(
				array(
					'fields'       => array('ID'),
					'role__in'     => array('administrator', 'seller', 'shop_manager'),
					'meta_key'     => 'dokan_enable_selling',
					'meta_value'   => 'yes',
					'meta_compare' => 'EXISTS'
				)
			),
			'ID'
		);

		if (empty($dokan_user_id_list)) {
			$this->response = array(
				'message' => 'No vendor registered!',
				'city'    => NULL,
			);
		}

		return $this->find_nearest_store($dokan_user_id_list);
	}

	/**
	 * @param int[] $user_id_array
	 *
	 * @return string|NULL city name or null if not found
	 */
	private function find_nearest_store($user_id_array) {
		$nearest_user_id       = -1;
		$nearest_user_distance = INF;
		foreach ($user_id_array as $index => $user_id) {
			$user_dgcpdb_data = wp_parse_args(
				get_user_meta($user_id, Constants_Dgcpdb::my_user_meta_key)[0],
				Constants_Dgcpdb::user_meta_defaults
			);

			if ($user_dgcpdb_data['enabled'] == 'no') {
				continue;
			}

			$distance = $this->calc_distance($user_dgcpdb_data['lat'], $user_dgcpdb_data['lng']);
			if (bccomp($user_dgcpdb_data['diameter'], $distance, 1) >= 0) {
				if ($distance < $nearest_user_distance) {
					$nearest_user_id       = $user_id;
					$nearest_user_distance = $distance;
				}
			}
		}

		if ($nearest_user_id == -1) {
			return NULL;
		} else {
			$user_dokan_data = wp_parse_args(
				get_user_meta($nearest_user_id, 'dokan_profile_settings')[0],
				array(
					'address' => array(
						'city' => 'emp',
					)
				)
			);
			return $user_dokan_data['address']['city'];
		}
	}

	/**
	 * @param string $store_lat float format
	 * @param string $store_lng float format
	 *
	 * @return string distance in (#.# KM) float format
	 */
	private function calc_distance($store_lat, $store_lng) {
		bcscale(6);
		$radius = "6371"; // earth radius
		$rad    = bcdiv(pi() . "", "180");

		$lat_rad1        = bcmul($this->lat_param, $rad);
		$lng_rad1        = bcmul($this->lng_param, $rad);
		$lat_rad2        = bcmul($store_lat, $rad);
		$lng_rad2        = bcmul($store_lng, $rad);
		$lng_rad_1_2_sub = bcsub($lng_rad1, $lng_rad2);

		$sin_lat1    = sin(floatval($lat_rad1)) . "";
		$sin_lat2    = sin(floatval($lat_rad2)) . "";
		$cos_lat1    = cos(floatval($lat_rad1)) . "";
		$cos_lat2    = cos(floatval($lat_rad2)) . "";
		$cos_lng_sub = cos(floatval($lng_rad_1_2_sub)) . "";

		$sin_lat1_2_mul             = bcmul($sin_lat1, $sin_lat2);
		$cos_lat1_2_mul_cos_lng_sub = bcmul(bcmul($cos_lat1, $cos_lat2), $cos_lng_sub);

		$sin_cos_add = bcadd($sin_lat1_2_mul, $cos_lat1_2_mul_cos_lng_sub);
		return bcmul(acos(floatval($sin_cos_add)) . "", $radius, 1);
	}
}