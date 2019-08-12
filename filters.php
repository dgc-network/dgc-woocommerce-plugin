<?php
/*
List of available filters in dgc WooCommerce Plus plugin.
You can use these filterns in your theme in funtions.php file
and set different default settings.
*/

// Set default settings
function dgc_default_settings($settings) {
	$settings['shop_loop_container'] = '.another-container';
	$settings['custom_scripts'] = 'alert("hello");';
	$settings['scroll_to_top'] = null;
	return $settings;
}
add_filter('dgc_settings', 'dgc_default_settings');