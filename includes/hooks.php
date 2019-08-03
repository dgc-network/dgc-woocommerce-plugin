<?php
/**
 * List of hooks in DGC WooCommerce Plus plugin.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

$dgc = new DGC_Woocommerce_Plus();

add_action('woocommerce_before_shop_loop', array('DGC_Woocommerce_Plus', 'beforeProductsHolder'), 0);
add_action('woocommerce_after_shop_loop', array('DGC_Woocommerce_Plus', 'afterProductsHolder'), 200);
add_action('woocommerce_before_template_part', array('DGC_Woocommerce_Plus', 'beforeNoProducts'), 0);
add_action('woocommerce_after_template_part', array('DGC_Woocommerce_Plus', 'afterNoProducts'), 200);

add_action('paginate_links', array('DGC_Woocommerce_Plus', 'paginateLinks'));

// frontend sctipts
add_action('wp_enqueue_scripts', array($dgc, 'frontendScripts'));

// filter products
add_action('woocommerce_product_query', array($dgc, 'setFilter'));

// clear old transients
add_action('create_term', 'dgc_clear_transients');
add_action('edit_term', 'dgc_clear_transients');
add_action('delete_term', 'dgc_clear_transients');

add_action('save_post', 'dgc_clear_transients');
add_action('delete_post', 'dgc_clear_transients');