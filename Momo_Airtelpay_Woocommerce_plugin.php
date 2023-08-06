<?php

defined('ABSPATH') or die("No access please!");

/*
Plugin Name: Woocommerce Mobile Money Payment Gateway (Uganda)
Description: Mobile Money Payment Gateway for WooCommerce for Ugandan service providers MTN and Airtel Uganda.
Version: 1.0.0
Author: Peter Komagum
Author URI: [Your Website or Profile URL]
Licence: MIT
WC requires at least: 2.2
WC tested up to: 5.7.2
*/
/*
Variables
==========


*/

//================================================================================================================

add_action('plugins_loaded', 'woomomo_payment_gateway_init'); // Create class WC_Gateway_Mpesa
add_action( 'wp_enqueue_scripts', 'woomomo_adds_to_the_head' ); // Add Styles and Scripts

/*
Variables
==========
woomomo_payment_gateway_init - Gateway class
woomomo_adds_to_the_head - Styles and scripts
*/

//================================================================================================================

function woomomo_adds_to_the_head() {
    wp_enqueue_script('axios', plugin_dir_url(__FILE__) . '/script/axios.js',  array('jquery'));
    wp_enqueue_script('sweetalert', plugin_dir_url(__FILE__) . '/script/sweetalert2.all.min.js',  array('jquery'));
	wp_enqueue_script('Callbacks', plugin_dir_url(__FILE__) . '/script/process_payment.js', array('jquery'));
	wp_enqueue_style( 'Responses', plugin_dir_url(__FILE__) . '/style/display.css',false,'1.1','all');
}
/*
Variables
==========
woomomo_adds_to_the_head
axios
sweetalert
Callbacks
Responses
*/
//================================================================================================================


//Calls the woompesa_mpesatrx_install function during plugin activation which creates table that records transactions.
register_activation_hook(__FILE__,'woomomo_momopaytrx_install');

/*
Variables
==========
woomomo_momopaytrx_install

*/

//================================================================================================================

//Request payment function start//
add_action( 'init', function() {
	/** Add a custom path and set a custom query argument. */
	add_rewrite_rule( '^/payment/?([^/]*)/?', 'index.php?payment_action=1', 'top' );
} );
/*
Variables
==========


*/
//================================================================================================================


add_filter( 'query_vars', function( $query_vars ) {

	/** Make sure WordPress knows about this custom action. */
	$query_vars []= 'initialize_payment';
	$query_vars []= 'payment_action';

	return $query_vars;

} );


/*
Variables
==========


*/
//================================================================================================================


add_action('wp', function(){
    if(get_query_var('initialize_payment')){
        initialize_payment();
    }
 });
 
 /*
Variables
==========


*/
//================================================================================================================

 add_action( 'wp', function() {
     /** This is an call for our custom action. */
     if ( get_query_var( 'payment_action' ) ) {
         complete_payment();
     }
 } );
 
 /*
Variables
==========


*/















/*
Variables
==========


*/