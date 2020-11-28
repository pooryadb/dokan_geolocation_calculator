<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://romroid.ir
 * @since      1.0.0
 *
 * @package    Dgcpdb
 * @subpackage Dgcpdb/admin/partials
 */

// check user capabilities
if (!current_user_can('manage_options')) {
	return;
}

$dokan_user_id_list = array_column(
	get_users(
		array(
			'fields'       => array('ID'),
			'meta_key'     => 'dokan_enable_selling',
			'meta_value'   => 'yes',
			'meta_compare' => 'EXISTS'
		)
	),
	'ID'
);

if (empty($dokan_user_id_list)) {
	$msg = __('No vendor registered!', 'dgcpdb');
}

?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<?php if (isset($msg)) { ?>
        <div class='dgcpdb_message'>
            <p><?php echo $msg; ?></p>
        </div>
	<?php } ?>

    <form action="" method="post">
        <h3 id="stores_table"><a href="#stores_table"><?php _e('Stores Table', 'dgcpdb'); ?></a></h3>

        <table class="table dgcpdb_zebra_style">
            <thead>
            <tr>
                <th><?php _e('id', 'dgcpdb'); ?></th>
                <th><?php _e('name', 'dgcpdb'); ?></th>
                <th><?php _e('store name', 'dgcpdb'); ?></th>
                <th><?php _e('city', 'dgcpdb'); ?></th>
                <th><?php _e('latitude', 'dgcpdb'); ?></th>
                <th><?php _e('longitude', 'dgcpdb'); ?></th>
                <th><?php _e('diameter (KM)', 'dgcpdb'); ?></th>
                <th><?php _e('enabled', 'dgcpdb'); ?></th>
            </tr>
            </thead>
            <tbody class="list">
			<?php foreach ($dokan_user_id_list as $index => $user_id) {
				$user_data        = get_user_meta($user_id);
				$user_dokan_data  = get_user_meta($user_id, 'dokan_profile_settings')[0];
				$user_dgcpdb_data = get_user_meta($user_id, Constants_Dgcpdb::my_user_meta_key)[0];

				$user_dokan_data  = wp_parse_args(
					$user_dokan_data,
					array(
						'store_name' => __('Store-Name isn\'t set !!', 'dgcpdb'),
						'address'    => array(
							'city' => __('City isn\'t set !!', 'dgcpdb'),
						)

					)
				);
				$user_dgcpdb_data = wp_parse_args($user_dgcpdb_data, Constants_Dgcpdb::user_meta_defaults);
				?>
                <tr>
                    <td contenteditable="false"
                        id="id:<?php echo $user_id; ?>"><?php echo $user_id; ?></td>
                    <td contenteditable="false"
                        id="name:<?php echo $user_id; ?>"><?php echo $user_data["first_name"][0] . ' ' . $user_data["last_name"][0]; ?></td>
                    <td contenteditable="false"
                        id="storeName:<?php echo $user_id; ?>"><?php echo $user_dokan_data["store_name"]; ?></td>
                    <td contenteditable="false"
                        id="city:<?php echo $user_id; ?>"><?php echo $user_dokan_data["address"]["city"]; ?></td>
                    <td contenteditable="false"
                        id="lat:<?php echo $user_id; ?>">
                        <input type="number" class="dgcpdb_number_input" name="lat[]" step="0.000001" min="0" max="90"
                               value="<?php echo $user_dgcpdb_data['lat'] ?>">
                    </td>
                    <td contenteditable="false"
                        id="lng:<?php echo $user_id; ?>">
                        <input type="number" class="dgcpdb_number_input" name="lng[]" step="0.000001" min="0" max="180"
                               value="<?php echo $user_dgcpdb_data['lng'] ?>">
                    </td>
                    <td contenteditable="false"
                        id="diameter:<?php echo $user_id; ?>">
                        <input type="number" class="dgcpdb_number_input" name="diameter[]" step="0.1" min="0.1"
                               value="<?php echo $user_dgcpdb_data['diameter'] ?>">
                    </td>
                    <td contenteditable="false"
                        id="enabled:<?php echo $user_id; ?>">
                        <label class="dgcpdb_switch">
                            <input type="checkbox" class="dgcpdb_checkbox" name="enabled[<?php echo $index; ?>]" <?php
							checked($user_dgcpdb_data['enabled'], 'yes') ?>>
                            <span class="dgcpdb_switch_slider round"></span>
                        </label>
                    </td>

                    <input type="hidden" name="user_id[]" value="<?php echo $user_id; ?>">

                </tr>

			<?php } ?>
            </tbody>
        </table>

        <div class="dgcpdb_form">
            <h3 id="api_section"><a href="#api_section"><?php _e('API section', 'dgcpdb'); ?></a></h3>
            <div class="form-check">
                <label class="dgcpdb_switch">
                    <input type="checkbox" class="form-check-input" name="enable_api"
						<?php checked(get_option(Constants_Dgcpdb::enable_api_option_key, 'yes'), 'yes'); ?>>
                    <span class="dgcpdb_switch_slider round"></span>
                </label>
                <label class="form-check-label"><?php _e('Enable WP-API ?', 'dgcpdb'); ?></label>
            </div>
        </div>

        <input type="submit" class="btn btn-primary px-5 my-5" value="<?php _e('Save', 'dgcpdb'); ?>">

    </form>
</div>
