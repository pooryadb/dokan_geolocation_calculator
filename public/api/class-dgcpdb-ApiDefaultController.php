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
			$meta = get_user_meta($user_id, Constants_Dgcpdb::my_user_meta_key);
			if (!isset($meta[0])) {
				continue;
			}
			$user_dgcpdb_data = wp_parse_args(
				$meta[0],
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
	 * This uses the ‘haversine’ formula to calculate the great-circle distance between two points – that is, the shortest distance over the
	 * earth’s surface – giving an ‘as-the-crow-flies’ distance between the points (ignoring any hills they fly over, of course!)
	 * =======================================
	 * a = sin²(Δφ/2) + cos φ1 ⋅ cos φ2 ⋅ sin²(Δλ/2)
	 * c = 2 ⋅ atan2( √a, √(1−a) )
	 * result = R ⋅ c
	 * =======================================
	 * φ is latitude, λ is longitude, R is earth’s radius (mean radius = 6,371km);
	 * note that angles need to be in radians to pass to trig functions!
	 *
	 * @field $this->lat_param
	 * @field $this->lng_param
	 * @param string $lat float format
	 * @param string $lng float format
	 *
	 * @return string distance in (#.# KM) float format
	 */
	private function calc_distance($lat, $lng) {
		bcscale(10); //necessary for precision
		$radius = "6371"; // earth radius (KM)
		$radian = bcdiv(pi() . "", "180");

		$phi_1       = bcmul($this->lat_param, $radian);
		$phi_2       = bcmul($lat, $radian);
		$delta_phi   = bcmul(bcsub($this->lat_param, $lat), $radian);
		$delta_landa = bcmul(bcsub($this->lng_param, $lng), $radian);

		$sin__delta_phi_div2__pow2   = bcpow(sin(floatval($delta_phi) / 2) . "", "2");
		$mul___cos__phi_1_2          = bcmul(cos(floatval($phi_1)) . "", cos(floatval($phi_2)) . "");
		$sin__delta_landa_div2__pow2 = bcpow(sin(floatval($delta_landa) / 2) . "", "2");

		$formula_a = bcadd($sin__delta_phi_div2__pow2, bcmul($mul___cos__phi_1_2, $sin__delta_landa_div2__pow2));

		$sqrt_1_minus_a = bcsqrt(bcsub("1", $formula_a));
		$atan2_a        = atan2(floatval(bcsqrt($formula_a)), floatval($sqrt_1_minus_a));
		$formula_c      = bcmul("2", $atan2_a . "");

		return bcmul($radius, $formula_c, 1);
	}
}