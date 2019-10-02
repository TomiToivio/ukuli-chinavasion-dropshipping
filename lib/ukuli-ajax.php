<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ajax callback for product import.
 */
function ukuli_chinavasion_action_callback() {
    check_ajax_referer( 'ukuli_chinavasion_action', 'security' );
    if(current_user_can('manage_woocommerce')) {
        $chinavasionid = sanitize_text_field($_POST['chinavasionid']);
        $ukuliChinavasionAPI = new ukuliChinavasionAPI();
        $result = $ukuliChinavasionAPI->chinavasionImport($chinavasionid);
        echo $result;
    }
    wp_die();
}
add_action( 'wp_ajax_ukuli_chinavasion_action', 'ukuli_chinavasion_action_callback' );

/**
 * Ajax callback for product import.
 */
function ukuli_chinavasion_product_action_callback() {
    check_ajax_referer( 'chinavasion_product_action', 'security' );
    if(current_user_can('manage_woocommerce')) {
        $product = sanitize_text_field($_POST['product']);
        $ukuliChinavasionAPI = new ukuliChinavasionAPI();
        $result = $ukuliChinavasionAPI->chinavasionImport($product);
        echo $result;
    }
    wp_die();
}
add_action('wp_ajax_ukuli_chinavasion_product_action', 'ukuli_chinavasion_product_action_callback');

/**
 * Ajax callback for category import.
 */
function ukuli_chinavasion_category_action_callback() {
    check_ajax_referer( 'chinavasion_category_action', 'security' );
    if(current_user_can('manage_woocommerce')) {
        $category = sanitize_text_field($_POST['category']);
        $ukuliChinavasionAPI = new ukuliChinavasionAPI();
        $result = $ukuliChinavasionAPI->chinavasionProducts($category);
        echo $result;
    }
    wp_die();
}
add_action( 'wp_ajax_ukuli_chinavasion_category_action', 'ukuli_chinavasion_category_action_callback' );

/**
 * Ajax callback for API key.
 */
function ukuli_chinavasion_api_action_callback() {
    check_ajax_referer( 'ukuli_chinavasion_api_action', 'security' );
    if(current_user_can('manage_woocommerce')) {
	   $chinavasionapi = sanitize_text_field($_POST['chinavasionapi']);
	   $result = update_option( 'ukuli_chinavasion_api_key', $chinavasionapi);
	   echo $result;
    }
    wp_die();
}
add_action( 'wp_ajax_ukuli_chinavasion_api_action', 'ukuli_chinavasion_api_action_callback' );

/**
 * Ajax callback for Chinavasion order.
 */
function ukuli_chinavasion_order_action_callback() {
    check_ajax_referer( 'ukuli_chinavasion_order_action', 'security' );
    if(current_user_can('manage_woocommerce')) {
        $order_id = sanitize_text_field($_POST['order']);
        $shipping = sanitize_text_field($_POST['shipping']);
        $payment = sanitize_text_field($_POST['payment']);
        $ukuliChinavasionAPI = new ukuliChinavasionAPI();
        $result = $ukuliChinavasionAPI->chinavasionAddOrder($order_id, $shipping, $payment);
        echo $result;
    }
    wp_die();
}
add_action( 'wp_ajax_ukuli_chinavasion_order_action', 'ukuli_chinavasion_order_action_callback' );