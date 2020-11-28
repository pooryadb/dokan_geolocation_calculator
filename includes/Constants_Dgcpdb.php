<?php


abstract class Constants_Dgcpdb {
	const main_menu_slug = 'dgcpdb_main_slug';

	const my_user_meta_key = 'dgcpdb_meta';
	const user_meta_defaults
		= array(
			'lat'      => 0,
			'lng'      => 0,
			'diameter' => 0.1,
			'enabled'  => 0
		);
}