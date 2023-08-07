<?php

    session_start();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        global $wpdb;
        $order = new WC_Order ( $_SESSION['order_id'] );

        $complete_payment = $order->payment_complete($order->get_transaction_id());

        $data = json_decode(file_get_contents("php://input"), TRUE);

        $insert = $wpdb->insert($wpdb->prefix.'momo_trx', [
            'order_id' => $_SESSION['order_id'],
            'phone_number' => $_SESSION['tel'],
            'trx_time' => date('Y-m-d H:i:s'),
            'merchant_request_id' => $_SESSION['mer'],
            'resultcode' => $data['code'] ,
            'resultdesc' => $data['description'],
            'processing_status' => $order->get_status()
        ]);

        if($insert and $complete_payment){
            echo json_encode(["response" =>  $insert]);
        }else{
            throw new Error('Error while saving the data', '500');
        }
    }

    exit();

/*
Variables
==========
woomomo_add_gateway_class
WC_Gateway_Momo
woomomo_momotrx_install
momo_trx

*/
//================================================================================================================