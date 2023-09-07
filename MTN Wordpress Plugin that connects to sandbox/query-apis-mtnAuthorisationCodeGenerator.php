<?php
/**
 * Plugin name: Query APIs - MTN lead - A
 * Plugin URI: https://omukiguy.com
 * Description: Exchange information with external APIs in WordPress
 * Author: Peter Komagum
 * Author URI: https://omukiguy.com
 * text-domain: query-apis
 */

// If this file is access directly, abort!!!

defined( 'ABSPATH' ) or die( 'Unauthorized Access' );

function get_send_data() {

	function gen_uuid() {
		$uuid = array(
			'time_low' => 0,
			'time_mid' => 0,
			'time_hi' => 0,
			'clock_seq_hi' => 0,
			'clock_seq_low' => 0,
			'node' => array()
		);
	
		$uuid['time_low'] = mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16);
		$uuid['time_mid'] = mt_rand(0, 0xffff);
		$uuid['time_hi'] = (4 << 12) | (mt_rand(0, 0x1000));
		$uuid['clock_seq_hi'] = (1 << 7) | (mt_rand(0, 128));
		$uuid['clock_seq_low'] = mt_rand(0, 255);
	
		for ($i = 0; $i < 6; $i++) {
			$uuid['node'][$i] = mt_rand(0, 255);
		}
	
		$uuid = sprintf('%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
			$uuid['time_low'],
			$uuid['time_mid'],
			$uuid['time_hi'],
			$uuid['clock_seq_hi'],
			$uuid['clock_seq_low'],
			$uuid['node'][0],
			$uuid['node'][1],
			$uuid['node'][2],
			$uuid['node'][3],
			$uuid['node'][4],
			$uuid['node'][5]
		);
	
		return $uuid;
	}
	
	$apiUserID = gen_uuid(); //Variable

	$OcpApimSubscriptionKey = "f16088dbf1ec4e1bb853604cd001f81a"; //Variable
	$callBackHost = 'http://mysite.local//:80'; //variable
	echo "<hr>";
	echo "This is the Business UUID :=        ".$apiUserID;
	echo "<hr>";

	echo "These are the Variables being passed to the fuction:";
	echo "<hr>";
	echo $callBackHost;
	echo "<hr>";
	echo $apiUserID;
	echo "<hr>";
	echo $OcpApimSubscriptionKey;
	echo "<hr>";

	function postGeneratingSanboxProvisioning ($callBackHost ,$apiUserID , $OcpApimSubscriptionKey) {

		$curl = curl_init();
	
		$postData = json_encode([
			"providerCallbackHost" => $callBackHost
		]);
	
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$postData,
		  CURLOPT_HTTPHEADER => array(
			'X-Reference-Id: ' . $apiUserID,
			'Content-Type: application/json',
			'Ocp-Apim-Subscription-Key: '. $OcpApimSubscriptionKey
		  ),
		));
		
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	
	}
	
	
	echo "Sandbox Post has been sent: ". postGeneratingSanboxProvisioning ($callBackHost ,$apiUserID , $OcpApimSubscriptionKey);
	echo "<hr>";
	sleep(20);  // This will make the program wait for 60 seconds
	echo "20 seconds have passed!";
	echo "<hr>";
	echo "Then we check if the sandbox provisioning is available?";
	echo "<hr>";

	echo "These are the Variables being passed to the fuction:";
	echo "<hr>";
	echo $apiUserID;
	echo "<hr>";
	echo $OcpApimSubscriptionKey;
	echo "<hr>";

	function getCheckIfSanboxUserIsAvailable($apiUserID, $OcpApimSubscriptionKey) {

		$curl = curl_init();
	
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/' . $apiUserID,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Ocp-Apim-Subscription-Key: ' . $OcpApimSubscriptionKey
			),
		));
	
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	

	echo "Checking if the provisioning has been made using a GET:      ". getCheckIfSanboxUserIsAvailable($apiUserID, $OcpApimSubscriptionKey);
	echo "<hr>";
	sleep(20);  // This will make the program wait for 60 seconds
	echo "20 seconds have passed!";
	echo "<hr>";
	echo "Variables passed to the fucntion to get the Sandbox API";
	echo "<hr>";
	echo $apiUserID;
	echo "<hr>";
	echo $OcpApimSubscriptionKey;
	echo "<hr>";
	echo "Then we send a post that should bring back the API Key";
	echo "<hr>";

	function postGetSanboxApiKey ($apiUserID , $OcpApimSubscriptionKey) {
 
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/'. $apiUserID .'/apikey',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: ' . $OcpApimSubscriptionKey,
    'Content-Length: 0' 
  ),
));

$response = curl_exec($curl);

curl_close($curl);

    // Decode the JSON response to get the apiKey
    $decodedResponse = json_decode($response, true);
    if (isset($decodedResponse['apiKey'])) {
        return $decodedResponse['apiKey'];
    }

    return "API key not found";

}
	
	$apiKey = postGetSanboxApiKey ($apiUserID , $OcpApimSubscriptionKey);
	
	echo "This is the API Key Generated: ". $apiKey;
	echo "<hr>";


	$credentials = $apiUserID . ':' . $apiKey;
	$authorisation = base64_encode($credentials);
	
	echo "This is the Authorisation Code generated: ". $authorisation;


////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////Other important Variables//////

$price = "4000000";
$paymentCurrency = "EUR";
$customerPhoneNumber = "46733123453";
$messageToCustomerForPayment = "Paying for good";
$noteForPayment = "Please pay now";
$targetEnvironment = "sandbox";

$customerUUID = gen_uuid();

echo "<hr>";
echo "This is the Customer UUID := ". $customerUUID;
echo "<hr>";

// sleep(10);  
// echo "10 seconds have passed!";
echo "<hr>";
echo "These are the variables sent to the post creating the access token:";
echo "<hr>";
echo $authorisation;
echo "<hr>";
echo $OcpApimSubscriptionKey;
echo "<hr>";

function postCreatingAccessToken ($authorisation ,$OcpApimSubscriptionKey) {

	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://sandbox.momodeveloper.mtn.com/collection/token/',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Basic '. $authorisation,
		'Ocp-Apim-Subscription-Key: '. $OcpApimSubscriptionKey,
		'Content-Length: 0' 
	  ),
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	
	// return $response;
	
	$decodedResponse = json_decode($response, true);
		if (isset($decodedResponse['access_token'])) {
			return $decodedResponse['access_token'];
		}
	
		return "Acess tocken not found";
	
	
}

$AccessToken = postCreatingAccessToken ($authorisation ,$OcpApimSubscriptionKey);
echo "This is the Access token generated: ".$AccessToken;
echo "<hr>";





function generateTrackingNumber($filename = "last_number.txt") {
    // Check if the file exists
    if (!file_exists($filename)) {
        // If not, create the file and initialize with 1
        file_put_contents($filename, '1');
        return '000000001'; // Return the first number with leading zeros
    }

    // Read the last number from the file
    $lastNumber = (int)file_get_contents($filename);

    // Increment the number
    $newNumber = $lastNumber + 1;

    // Save the new number back to the file
    file_put_contents($filename, $newNumber);

    // Return the new number, formatted to 9 digits with leading zeros
    return str_pad($newNumber, 9, '0', STR_PAD_LEFT);
}
echo "There are the variables needed to send out the request to pay: ";
echo "<hr>";
echo $price;
echo "<hr>";
echo $paymentCurrency;
echo "<hr>";
echo $customerPhoneNumber;
echo "<hr>";
echo $messageToCustomerForPayment;
echo "<hr>";
echo $noteForPayment;
echo "<hr>";
echo $AccessToken;
echo "<hr>";
echo $customerUUID;
echo "<hr>";
echo $OcpApimSubscriptionKey;
echo "<hr>";
echo $targetEnvironment;
echo "<hr>";

function postSendingOutRequestToPay ($price, $paymentCurrency, $customerPhoneNumber,$messageToCustomerForPayment,$noteForPayment, $AccessToken, $customerUUID, $OcpApimSubscriptionKey,$targetEnvironment) {
    
    $orderTrackingNumber = strval(generateTrackingNumber());

    $curl = curl_init();

    $postData = json_encode([
        "amount" => $price,
        "currency" => $paymentCurrency,
        "externalId" => $orderTrackingNumber,
        "payer" => [
            "partyIdType" => "MSISDN",
            "partyId" => $customerPhoneNumber,
        ],
        "payerMessage" => $messageToCustomerForPayment,
        "payeeNote" => $noteForPayment
    ]);
    
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer '. $AccessToken,
            'X-Reference-Id: ' . $customerUUID,
            'X-Target-Environment: sandbox',
            'Content-Type: application/json',
            'Ocp-Apim-Subscription-Key: ' . $OcpApimSubscriptionKey
        ],
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        return "cURL Error: " . $error_msg;
    }

    curl_close($curl);
    return $response;
}

echo "This is the Post sent out creating a Request to Pay: ". postSendingOutRequestToPay ($price, $paymentCurrency, $customerPhoneNumber,$messageToCustomerForPayment,$noteForPayment, $AccessToken, $customerUUID, $OcpApimSubscriptionKey, $targetEnvironment);
echo "<hr>";

sleep(30); 
echo "30 seconds have passed!";
echo "<hr>";
echo "Then We need to check if the user has completed the payment then follow up with a check after 30 seconds";
echo "<hr>";
echo "These are the variables that were sent to the function: ";
echo "<hr>";
echo $customerUUID;
echo "<hr>";
echo $AccessToken;
echo "<hr>";
echo $OcpApimSubscriptionKey;
echo "<hr>";
echo $targetEnvironment;
echo "<hr>";


function getCheckingIfPaymentIsComplete($customerUUID,$AccessToken,$OcpApimSubscriptionKey, $targetEnvironment  ) {


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/'.$customerUUID,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '. $AccessToken,
    'X-Target-Environment: '. $targetEnvironment,
    'Ocp-Apim-Subscription-Key: '. $OcpApimSubscriptionKey
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return $response;



}

echo "This is the Status of the Payment: ". getCheckingIfPaymentIsComplete($customerUUID,$AccessToken,$OcpApimSubscriptionKey, $targetEnvironment);
echo "<hr>";

}
/**
 * Register a custom menu page
 */
function wpdocs_register_my_custom_menu_page() {
	add_menu_page(
		__( 'API Test Settings', 'textdomain' ),
		'API Test - MTN Lead',
		'manage_options',
		'api-test.php',
		'get_send_data',
		'dashicons-testimonial',
		85
	);
}

add_action( 'admin_menu', 'wpdocs_register_my_custom_menu_page' );
