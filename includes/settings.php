<?php
/**
 * HTML markup for Settings page.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="wrap">
	<h1><?php _e('WC Ajax Product Filter', 'textdomain'); ?></h1>
	<form method="post" action="options.php">
		<?php
		settings_fields('dgc_settings');
		do_settings_sections('dgc_settings');

		// check if filter is applied
		$settings = apply_filters('dgc_settings', get_option('dgc_settings'));
		?>

		<?php if (has_filter('dgc_settings')): ?>
			<p><span class="dashicons dashicons-info"></span> <?php _e('Filter has been applied and that may modify the settings below.', 'textdomain'); ?></p>
		<?php endif ?>

		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('Shop loop container', 'textdomain'); ?></th>
				<td>
					<input type="text" name="dgc_settings[shop_loop_container]" size="50" value="<?php echo esc_attr($settings['shop_loop_container']); ?>">
					<br />
					<span><?php _e('Selector for tag that is holding the shop loop. Most of cases you won\'t need to change this.', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('No products container', 'textdomain'); ?></th>
				<td>
					<input type="text" name="dgc_settings[not_found_container]" size="50" value="<?php echo esc_attr($settings['not_found_container']); ?>">
					<br />
					<span><?php _e('Selector for tag that is holding no products found message. Most of cases you won\'t need to change this.', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Pagination container', 'textdomain'); ?></th>
				<td>
					<input type="text" name="dgc_settings[pagination_container]" size="50" value="<?php echo esc_attr($settings['pagination_container']); ?>">
					<br />
					<span><?php _e('Selector for tag that is holding the pagination. Most of cases you won\'t need to change this.', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Overlay background color', 'textdomain'); ?></th>
				<td>
					<input type="text" name="dgc_settings[overlay_bg_color]" size="50" value="<?php echo esc_attr($settings['overlay_bg_color']); ?>">
					<br />
					<span><?php _e('Change this color according to your theme, eg: #fff', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Product sorting', 'textdomain'); ?></th>
				<td>
					<input type="checkbox" name="dgc_settings[sorting_control]" value="1" <?php (!empty($settings['sorting_control'])) ? checked(1, $settings['sorting_control'], true) : ''; ?>>
					<span><?php _e('Check if you want to sort your products via ajax.', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Scroll to top', 'textdomain'); ?></th>
				<td>
					<input type="checkbox" name="dgc_settings[scroll_to_top]" value="1" <?php (!empty($settings['scroll_to_top'])) ? checked(1, $settings['scroll_to_top'], true) : ''; ?>>
					<span><?php _e('Check if to enable scroll to top after updating shop loop.', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Scroll to top offset', 'textdomain'); ?></th>
				<td>
					<input type="text" name="dgc_settings[scroll_to_top_offset]" size="50" value="<?php echo esc_attr($settings['scroll_to_top_offset']); ?>">
					<br />
					<span><?php _e('You need to change this value to match with your theme, eg: 100', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Enable Font Awesome', 'textdomain'); ?></th>
				<td>
					<input type="checkbox" name="dgc_settings[enable_font_awesome]" value="1" <?php (!empty($settings['enable_font_awesome'])) ? checked(1, $settings['enable_font_awesome'], true) : ''; ?>>
					<span><?php _e('If your theme/plugins load font awesome then you can disable by unchecking it.', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Custom JavaScript after update', 'textdomain'); ?></th>
				<td>
					<textarea name="dgc_settings[custom_scripts]" rows="5" cols="70"><?php echo esc_attr($settings['custom_scripts']); ?></textarea>
					<br />
					<span><?php _e('If you want to add custom scripts that would be loaded after updating shop loop, eg: alert("hello");', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Disable Transients', 'textdomain'); ?></th>
				<td>
					<input type="checkbox" name="dgc_settings[disable_transients]" value="1" <?php (!empty($settings['disable_transients'])) ? checked(1, $settings['disable_transients'], true) : ''; ?>>
					<span><?php _e('To disable transients check this checkbox.', 'textdomain'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Clear Transients', 'textdomain'); ?></th>
				<td>
					<input type="checkbox" name="dgc_settings[clear_transients]" value="1">
					<span><?php _e('To clean transients check this checkbox and then press \'Save Changes\' button.', 'textdomain'); ?></span>
				</td>
			</tr>
		</table>
		<?php submit_button() ?>
	</form>
</div>
