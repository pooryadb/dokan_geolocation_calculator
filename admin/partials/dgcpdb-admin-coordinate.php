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

wp_enqueue_style(DGCPDB_PLUGIN_SLUG);
wp_enqueue_style(DGCPDB_PLUGIN_SLUG . '_admin_bootstrap_css');
wp_enqueue_script(DGCPDB_PLUGIN_SLUG . '_admin_bootstrap_js');

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
	$msg = __('No vendor registered!', 'dgcpdb');
}

//-------- paginate table:
$results_per_page = get_option(Constants_Dgcpdb::coordinate_item_per_page_option_key, 10);
$current_page     = isset($_GET['paged']) ? $_GET['paged'] : 1;

$args = array(
	'base'     => @add_query_arg('paged', '%#%'),
	'format'   => '',
	'total'    => ceil(sizeof($dokan_user_id_list) / $results_per_page),
	'current'  => $current_page,
	'show_all' => false,
	'type'     => 'plain',
);

$start_index  = ($current_page - 1) * $results_per_page;
$end_index_p1 = $start_index + $results_per_page;
$end_index_p1 = (sizeof($dokan_user_id_list) < $end_index_p1) ? sizeof($dokan_user_id_list) : $end_index_p1;
//-------- paginate table:
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<?php if (isset($msg)) { ?>
        <div class='dgcpdb_message'>
            <p><?php echo $msg; ?></p>
        </div>
	<?php } ?>

    <h3 id="stores_table"><a href="#stores_table"><?php _e('Stores Table', 'dgcpdb'); ?></a></h3>
    <small><?php echo sprintf(
			__('this table only shows Enabled-vendors. In order to enable other vendors <a href="%s" target="_blank">click here</a>', 'dgcpdb'),
			admin_url('?page=dokan#/vendors?status=pending')
		); ?></small>
    <div class="page-link d-flex flex-row justify-content-between">
        <form action="<?php echo admin_url('?page=' . Constants_Dgcpdb::main_menu_slug) . '&paged=1'; ?>" method="post" class="form-inline small">
            <input type="number" step="1" min="1" name="store_item_per_page" placeholder="<?php _e('Item per page', 'dgcpdb'); ?>"
                   value="<?php echo get_option(Constants_Dgcpdb::coordinate_item_per_page_option_key, 10); ?>">

            <input type="submit" class="btn btn-secondary btn-sm" value="<?php _e('Save', 'dgcpdb'); ?>">
        </form>
        <span class="alert-link">
			<?php echo paginate_links($args); ?>
        </span>
    </div>
    <form action="" method="post">
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
			<?php
			$i = 0;
			for ($index = $start_index; $index < $end_index_p1; $index++, $i++) {
				$user_id = $dokan_user_id_list[$index];

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
                        <input type="number" class="dgcpdb_number_input" name="lat[<?php echo $i; ?>]" step="0.000001" min="0" max="90"
                               value="<?php echo $user_dgcpdb_data['lat'] ?>">
                    </td>
                    <td contenteditable="false"
                        id="lng:<?php echo $user_id; ?>">
                        <input type="number" class="dgcpdb_number_input" name="lng[<?php echo $i; ?>]" step="0.000001" min="0" max="180"
                               value="<?php echo $user_dgcpdb_data['lng'] ?>">
                    </td>
                    <td contenteditable="false"
                        id="diameter:<?php echo $user_id; ?>">
                        <input type="number" class="dgcpdb_number_input" name="diameter[<?php echo $i; ?>]" step="0.1" min="0.1"
                               value="<?php echo $user_dgcpdb_data['diameter'] ?>">
                    </td>
                    <td contenteditable="false"
                        id="enabled:<?php echo $user_id; ?>">
                        <label class="dgcpdb_switch">
                            <input type="checkbox" class="dgcpdb_checkbox" name="enabled[<?php echo $i; ?>]" <?php
							checked($user_dgcpdb_data['enabled'], 'yes') ?>>
                            <span class="dgcpdb_switch_slider round"></span>
                        </label>
                    </td>

                    <input type="hidden" name="user_id[<?php echo $i; ?>]" value="<?php echo $user_id; ?>">

                </tr>

			<?php } ?>
            </tbody>
        </table>
        <div class="page-link alert-link text-center">
			<?php echo paginate_links($args); ?>
        </div>
        <input type="submit" class="btn btn-primary px-5 my-5" value="<?php _e('Save', 'dgcpdb'); ?>">

    </form>
</div>
