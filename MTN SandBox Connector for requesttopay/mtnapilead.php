<?php

$authorisation = 'YmUxNzI5NjYtYThlNS00OGNlLThmMDgtZjBiOTU1ZWMzZGM3OjUyNTJhNmUxMzAzODQ4Njc5YjUyYzBkNWY1MjI1Y2Fj'; //Variable
$apiUserID = ""; //Variable
$OcpApimSubscriptionKey = "675bcf16da3845d3839bb3bcc5f2591d"; //Variable

//////Other important Variables//////

$price = "4000000";
$paymentCurrency = "EUR";
$customerPhoneNumber = "46733123453";
$messageToCustomerForPayment = "Paying for good";
$noteForPayment = "Please pay now";
$targetEnvironment = "sandbox";

echo "<hr>";
echo $apiUserID;

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
//   $generatedUUID = gen_uuid(); //Variable
//   echo $generatedUUID;

// $apiUserID = '879bb47b-e363-4e4b-d437-06a2b54f342c';

$apiUserID = gen_uuid();

echo "<hr>";
echo $apiUserID;
echo "<hr>";

sleep(10);  // This will make the program wait for 60 seconds
echo "10 seconds have passed!";
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
// $AccessToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSMjU2In0.eyJjbGllbnRJZCI6IjI0NTE5YjgyLTQxMzAtNDYwYS04MWIxLWViYmE2MzdjYjVlZCIsImV4cGlyZXMiOiIyMDIzLTA5LTAzVDEzOjI2OjE0LjEyNCIsInNlc3Npb25JZCI6ImUyMGFmZDZmLWEzOTMtNDIzMC04NGU5LTJiOGU5NWJjNWQzMSJ9.ltj0cVjfI-jF_h7rzY7kySETmQrdTclyFzY6Q02SKoCQNcWISNMM1nP_-5WqbltPwrOQOI_vASZyj_DfhQ9Jrn3TRR9-GQfeAe-Y6yM3x00hEAgt06xAKTZwQDOmsumfa9L736Usb9EhCnY-tawyriCD69R4LVrZvIRUplusPUz-1LKeB2VACwLE6dkkhsy05AA-jhr_G0SPDtCmwHQ1etWMyNk1UX6Efv147b95pA17lcNoHmoBGmmH4qRoRYC5tpFZrLs3Fxw0NJfLeze7mgees0dHS7sYjyKIDGOlmDDFBJPjlShnFoITMVp8-IOjmwNGbBmEVNPuC44-uXaJBA";
echo $AccessToken;
echo "<hr>";

sleep(5);  // This will make the program wait for 60 seconds
echo "5 seconds have passed!";
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


function postSendingOutRequestToPay ($price, $paymentCurrency, $customerPhoneNumber,$messageToCustomerForPayment,$noteForPayment, $AccessToken, $apiUserID, $OcpApimSubscriptionKey) {
    
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
            'Authorization: Bearer ' . $AccessToken,
            'X-Reference-Id: ' . $apiUserID,
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

echo postSendingOutRequestToPay ($price, $paymentCurrency, $customerPhoneNumber,$messageToCustomerForPayment,$noteForPayment, $AccessToken, $apiUserID, $OcpApimSubscriptionKey);
echo "<hr>";

sleep(30);  // This will make the program wait for 60 seconds
echo "30 seconds have passed!";

echo "<hr>";

echo $apiUserID;
echo "<hr>";
echo $AccessToken;
echo "<hr>";
echo $OcpApimSubscriptionKey;
echo "<hr>";

function getCheckingIfPaymentIsComplete($apiUserID,$AccessToken,$OcpApimSubscriptionKey  ) {


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/'.$apiUserID,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '. $AccessToken,
    'X-Target-Environment: sandbox',
    'Ocp-Apim-Subscription-Key: '. $OcpApimSubscriptionKey
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return $response;



}

echo getCheckingIfPaymentIsComplete($apiUserID,$AccessToken,$OcpApimSubscriptionKey);
echo "<hr>";

?>