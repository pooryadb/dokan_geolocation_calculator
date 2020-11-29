<?php


abstract class Constants_Dgcpdb {
	const main_menu_slug = 'dgcpdb_main_slug';

	const enable_api_option_key = 'dgcpdb_enable_api';
	const not_found_city_message_api_option_key = 'dgcpdb_not_found_city_message';

	const my_user_meta_key = 'dgcpdb_meta';
	const user_meta_defaults
		= array(
			'lat'      => 0,
			'lng'      => 0,
			'diameter' => 0.1,
			'enabled'  => 0
		);
}