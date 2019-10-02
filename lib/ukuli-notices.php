<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Some admin notices for currencies, API key etc.
 */
function ukuli_chinavasion_admin_notice_error() {
if(get_option("woocommerce_currency") != ("EUR" || "USD" || "EUR" || "GBP" || "AUD" || "CAD" || "CHF" || "HKD" || "CNY" || "NZD")) {
	$class = 'notice notice-error';
	$message = __( 'Chinavasion does not support your currency.', 'ukuli' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}
if(empty(get_option("ukuli_chinavasion_api_key"))) {
	$class = 'notice notice-error';
	$message = __( 'Chinavasion API key is not set.', 'ukuli' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}
if (!class_exists( 'WooCommerce')) {
   $class = 'notice notice-error';
	$message = __( 'Chinavasion plugin requires WooCommerce.', 'ukuli' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}
}
add_action( 'admin_notices', 'ukuli_chinavasion_admin_notice_error' );
