<?php
/**
 * Plugin Name: Airtel Money Payments Gateway
 * Plugin URI: https://lavaggio.biz
 * Author: Peter Komagum
 * Author URI: https://lavaggio.biz
 * Description: Ugandan Local MTN Momo Payments Gateway for mobile.
 * Version: 0.1.0
 * License: GPL2
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: airtelmoney-payments-woo
 * 
 * Class WC_Gateway_Airtel_Money file.
 *
 * @package WooCommerce\Airtelmoney
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;


add_action( 'plugins_loaded', 'airtelmoney_payment_init', 11 );
add_filter( 'woocommerce_currencies', 'lavaggio_airtel_add_ugx_currencies' );
add_filter( 'woocommerce_currency_symbol', 'lavaggio_airtel_add_ugx_currencies_symbol', 10, 2 );
add_filter( 'woocommerce_payment_gateways', 'add_to_woo_airtelmoney_payment_gateway');

function airtelmoney_payment_init() {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-wc-payment-gateway-airtelmoney.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/airtelmoney-order-statuses.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/airtelmoney-checkout-description-fields.php';
	}
}

function add_to_woo_airtelmoney_payment_gateway( $gateways ) {
    $gateways[] = 'WC_Gateway_Airtel_Money';
    return $gateways;
}

function lavaggio_airtel_add_ugx_currencies( $currencies ) {
	$currencies['UGX'] = __( 'Ugandan Shillings', 'airtelmoney-payments-woo' );
	return $currencies;
}

function lavaggio_airtel_add_ugx_currencies_symbol( $currency_symbol, $currency ) {
	switch ( $currency ) {
		case 'UGX': 
			$currency_symbol = 'UGX'; 
		break;
	}
	return $currency_symbol;
}