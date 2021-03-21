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
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<?php if (isset($msg)) { ?>
        <div class='dgcpdb_message'>
            <p><?php echo $msg; ?></p>
        </div>
	<?php } ?>

    <form action="" method="post" class="dgcpdb_form">
        <div class="form-check">
            <label class="dgcpdb_switch">
                <input type="checkbox" class="form-check-input" name="enable_api"
					<?php checked(get_option(Constants_Dgcpdb::enable_api_option_key, 'yes'), 'yes'); ?>>
                <span class="dgcpdb_switch_slider round"></span>
            </label>
            <label class="form-check-label"><?php _e('Enable WP-API ?', 'dgcpdb'); ?></label>
            <div class="dgcpdb_code_box">
                <table>
                    <tr>
                        <td>API URL:</td>
                        <td><code><?php echo sprintf("%s/wp-json/dgcpdb/v1/find_store", site_url()) ?></code></td>
                    </tr>
                    <tr>
                        <td>method:</td>
                        <td><code>Post</code></td>
                    </tr>
                    <tr>
                        <td>params:</td>
                        <td><code>lat=[float] & lng=[float]</code></td>
                    </tr>
                    <tr>
                        <td>json response:</td>
                        <td>
                            <code>
                                {
                                "message" => [HTML message when city not-found],
                                "city" => [string city-name | null]
                                }
                            </code>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <label for="api_not_found_message">
            <h5><?php _e('Message when no store found:', 'dgcpdb'); ?></h5>
			<?php _e('Please keep message short and only use simple HTML tags.', 'dgcpdb'); ?>
        </label>
		<?php
		$content = get_option(Constants_Dgcpdb::not_found_city_message_api_option_key, __("There is no store near you!", 'dgcpdb'));
		wp_editor(
			$content,
			'api_not_found_message',
			array(
				'textarea_name' => 'api_not_found_message',
				'textarea_rows' => 3,
				'quicktags'     => array('buttons' => 'strong,em,link'),
				'teeny'         => true,
				'media_buttons' => false,
			)
		); ?>

        <input type="submit" class="btn btn-primary px-5 my-5" value="<?php _e('Save', 'dgcpdb'); ?>">

    </form>
</div>
