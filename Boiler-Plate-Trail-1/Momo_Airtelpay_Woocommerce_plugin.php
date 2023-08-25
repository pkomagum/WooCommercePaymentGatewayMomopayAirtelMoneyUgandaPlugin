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

//================================================================================================================

function woompesa_payment_gateway_init() {

	if( !class_exists( 'WC_Payment_Gateway' )) return;
	
	class WC_Gateway_Mpesa extends WC_Payment_Gateway {

		public function __construct(){

			// session_start();

			// Basic settings

			$this->id                 = 'momo';

			$this->icon               = plugin_dir_url(__FILE__) . 'img/logo.png';

			$this->has_fields         = false;

			$this->method_title       = __( 'MTN-MoMo', 'woocommerce' );

			$this->method_description = __( 'Enable customers to make payments to your business' );

			// load the settings
			
			$this->init_form_fields();

			$this->init_settings();

			// Define variables set by the user in the admin section

			$this->title              = $this->get_option( 'title' );

			$this->description        = $this->get_option( 'description' );

			$this->instructions       = $this->get_option( 'instructions', $this->description );

			$this->mer                = $this->get_option( 'mer' );

			$_SESSION['application_domain'] = $this->get_option('application_domain');

			$_SESSION['api_endpoint'] = $this->get_option( 'api_endpoint' );

			$_SESSION['mer'] = $this->get_option( 'mer' );

			$_SESSION['public_key']   = $this->get_option( 'public_key' );

            $_SESSION['origin'] = $this->get_option('origin');

			$_SESSION['api_key']   	  = $this->get_option( 'api_key' );

			$_SESSION['service_provider_code'] = $this->get_option('service_provider');

			$_SESSION['initiator_identifier'] = $this->get_option('initiator_identifier');

			$_SESSION['security_credential'] = $this->get_option('security_credential');


			//Save the admin options
			if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
				add_action( 'woocommerce_update_options_payment_gateways_'.$this->id, array( $this, 'process_admin_options' ) );
			} else {
				add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );
			}

			add_action( 'woocommerce_receipt_momo', array( $this, 'receipt_page' ));

		}

/*
Variables
==========
momo
MTN-MoMo
woocommerce_receipt_momo
*/
//================================================================================================================

		/**
		 *Initialize form fields that will be displayed in the admin section.
		 */
		public function init_form_fields() {

			$this->form_fields = array(

				'enabled' => array(

					'title'   => __( 'Enable/Disable', 'woocommerce' ),

					'type'    => 'checkbox',

					'label'   => __( 'Enable Momo Payments Gateway', 'woocommerce' ),

					'default' => 'yes'

				),

				'title' => array(

					'title'       => __( 'Title', 'woocommerce' ),

					'type'        => 'text',

					'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),

					'default'     => __( 'MTN-Momo', 'woocommerce' ),

					'desc_tip'    => true,

				),

				'description' => array(

					'title'       => __( 'Description', 'woocommerce' ),

					'type'        => 'textarea',

					'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce' ),

					'default'     => __( 'Place order and pay using MTN-Momo.'),

					'desc_tip'    => true,

				),

				'application_domain' => array(

					'title'       => __( 'Your Application Domain', 'woocommerce' ),

					'type'        => 'text',

					'description' => __( 'Your Application Domain. EX: http://www.example.com or http://localhost/example', 'woocommerce' ),

					'default'     => get_home_url(),

					'desc_tip'    => true,

				),

				'mer' => array(

					'title'       => __( 'Merchant Name', 'woocommerce' ),

					'description' => __( 'Company name', 'woocommerce' ),

					'type'        => 'text',

					'default'     => __( 'Lavaggio', 'woocommerce'),

					'desc_tip'    => false,

				),


				'origin' => array(

					'title'       =>  __( 'Origin', 'woocommerce' ),

					'default'     => get_home_url(),

					'type'        => 'text',

				),


				'api_endpoint' => array(

					'title'       =>  __( 'API Endpoint', 'woocommerce' ),

                    'default'     => __( 'api.sandbox.vm.co.mz', 'woocommerce'),

                    'type'        => 'text',

				),


				'api_key' => array(

					'title'       =>  __( 'API Key', 'woocommerce' ),

					'default'     => __( '', 'woocommerce'),

					'type'        => 'text',

				),


				'public_key' => array(

					'title'       =>  __( 'Public Key', 'woocommerce' ),

					'default'     => __( '', 'woocommerce'),

					'type'        => 'text',

				),

				'service_provider' => array(

					'title'       =>  __( 'Service Provider Code', 'woocommerce' ),

					'default'     => __( '171717', 'woocommerce'),

					'type'        => 'text',

				),

                'initiator_identifier' => array(

                    'title'       =>  __( 'Initiator Identifier', 'woocommerce' ),

                    'default'     => __( '', 'woocommerce'),

                    'type'        => 'text',
                ),

                'security_credential' => array(

                    'title'       =>  __( 'Security Credential', 'woocommerce' ),

                    'default'     => __( '', 'woocommerce'),

                    'type'        => 'text',
                ),

			);
		}



/*
Variables
==========


*/
//================================================================================================================


		/**
		 * Generates the HTML for admin settings page
		 * The heading and paragraph below are the ones that appear on the backend M-PESA settings page
		 */
		public function admin_options(){
			echo '<h3>' . 'MTN-Momo Payments Gateway' . '</h3>';
			echo '<p>' . 'Payments Made Simple' . '</p>';
			echo '<table class="form-table">';
			$this->generate_settings_html( );
			echo '</table>';
		}


/*
Variables
==========


*/
//================================================================================================================

/**
		 * Receipt Page
		 **/
		public function receipt_page( $order_id ) {
			echo $this->woomomo_generate_iframe( $order_id );
		}


/*
Variables
==========
woomomo_generate_iframe

*/
//================================================================================================================


/**
 * Function that posts the params to M-PESA and generates the HTML for the page
 */
public function woomomo_generate_iframe( $order_id ) {

    $order = new WC_Order( $order_id );
    $_SESSION['total'] = (int) $order->order_total;
    $_SESSION['order_id'] = $order->id;

    $tel = $order->billing_phone;
    $tel = str_replace("-", "", $tel);
    $tel = str_replace( array(' ', '<', '>', '&', '{', '}', '*', "+", '!', '@', '#', "$", '%', '^', '&'), "", $tel );
    $_SESSION['tel'] = $tel;

    if ($_GET['transactionType'] == 'checkout') {

        ?>

        <div class="container instructions">
            <div class="row">
                <div class="col-md-4">
                    <div class="icon">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <div class="description">
                        <h3>Click on <span>Pay</span></h3>
                        <p>Click the pay button to initiate the MTN-MoMo payment</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="icon">
                        <i class="fas fa-mobile"></i>
                    </div>
                    <div class="description">
                        <h3>Enter the <span>PIN</span></h3>
                        <p>Check the notification on your mobile phone and enter your PIN</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="description">
                        <h3>Finalize <span>Order</span></h3>
                        <p>Click the Complete Order button to finalize your order</p>
                    </div>
                </div>
            </div>
        </div>

        <?php

        $object_request = [
            "tel" => $_SESSION['tel'],
            "total" => $_SESSION['total'],
            "public_key" => $_SESSION['public_key'],
            "api_key" => $_SESSION['api_key'],
            "origin" => $_SESSION['origin'],
            "api_endpoint" => $_SESSION['api_endpoint'],
            "third_part_reference" => '',
            "service_provider_code" => $_SESSION['service_provider_code'],
            "initiator_identifier" => $_SESSION['initiator_identifier'],
            "security_credential" => $_SESSION['security_credential']
        ];

        $data = json_encode(base64_encode(json_encode($object_request)), JSON_PRETTY_PRINT);
        $links = json_encode(
            [
                'application_domain' => $_SESSION['application_domain'],
                'order_page' => wc_get_account_endpoint_url('orders')
            ], JSON_PRETTY_PRINT);

        ?>

        <div class="clear">
            <button onClick='initializePayment(<?php echo $data ?>, <?php echo json_encode($links) ?>)' style="color: white" id="pay_btn">Pay</button>
        </div>
        <?php

        echo "<br/>";
    }
}



/*
Variables
==========
woomomo_generate_iframe

*/
//================================================================================================================


		/**
		 * Process the payment field and redirect to checkout/pay page.
		 */
		public function process_payment( $order_id ) {

			$order = new WC_Order( $order_id );

			$_SESSION["orderID"] = $order->id;

			// Redirect to checkout/pay page

			$checkout_url = $order->get_checkout_payment_url(true);

			$checkout_edited_url = $checkout_url."&transactionType=checkout";

			return array(
				'result' => 'success',
				'redirect' => add_query_arg('order', $order->id,
				add_query_arg('key', $order->order_key, $checkout_edited_url))
			);
		}
	}
}



/*
Variables
==========


*/
//================================================================================================================




/**
 * Telling woocommerce that mpesa payments gateway class exists
 * Filtering woocommerce_payment_gateways
 * Add the Gateway to WooCommerce
 **/
function woomomo_add_gateway_class( $methods ) {
	$methods[] = 'WC_Gateway_Momo';
	return $methods;
}

if(!add_filter( 'woocommerce_payment_gateways', 'woompesa_add_gateway_class' )){
	die;
}

//Create Table for M-PESA Transactions
function woomomo_momotrx_install() {

	global $wpdb;
	global $trx_db_version;
	$trx_db_version = '1.0';
	
	$table_name = $wpdb->prefix .'momo_trx';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (

		id mediumint(9) NOT NULL AUTO_INCREMENT,

		order_id varchar(150) DEFAULT '' NULL,

		phone_number varchar(150) DEFAULT '' NULL,

		trx_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,

		merchant_request_id varchar(150) DEFAULT '' NULL,

		checkout_request_id varchar(150) DEFAULT '' NULL,

		resultcode varchar(150) DEFAULT '' NULL,

		resultdesc varchar(150) DEFAULT '' NULL,

		processing_status varchar(20) DEFAULT '0' NULL,

		PRIMARY KEY  (id)

	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option( 'trx_db_version', $trx_db_version );
}



/*
Variables
==========
woomomo_add_gateway_class
WC_Gateway_Momo
woomomo_momotrx_install
momo_trx

*/
//================================================================================================================


/**
 * Initialize the payment by making a request to the M-PESA API
 */
function initialize_payment(){

    require_once 'initialize-payment.php';
}


/**
 * Complete the payment and create an order by persisting the data
 * into the database
 */
function complete_payment(){

    require_once 'complete-payment.php';
}





/*
Variables
==========


*/
//================================================================================================================












/*
Variables
==========


*/
//================================================================================================================

/*
Variables
==========


*/