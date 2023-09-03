<?php
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
//  $generatedUUID = gen_uuid(); //Variable
//  echo $generatedUUID;

$apiUserID = "b4c5d34a-4855-483e-89bf-004863d9c462"; //Variable
$OcpApimSubscriptionKey = "675bcf16da3845d3839bb3bcc5f2591d"; //Variable
$callBackHost = 'https//http://localhost/mtnapilead.php/:80'; //variable


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


$credentials = $apiUserID . ':' . $apiKey;
$authorisation = base64_encode($credentials);

echo $authorisation;

?>