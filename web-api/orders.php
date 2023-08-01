<?php

/* * *************************************************************************
 *
 *   PROJECT: my_health_first App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */

require_once 'init.php';

$Data = array();


if ($_GET['action'] == 'addOrders') {
    cheackUserToken($Req['user_id']);
    if (!$Req['total_quantity']) {
        $Data['error'] = 'Please enter the total quantity';
    } else if (!$Req['quantity'] && $Req['order_type'] == 'participation') {
        $Data['error'] = 'Please enter the quantity for each visit';
    } else {
        $Req['status'] = 'pending';
        $res = $ordersObj->addOrders($Req, $Req['user_id']);
        if ($res) {
            $Data['data'] = $res;
        } else {
            $Data['error'] = 'An error occurred during the operation, please try again';
        }
    }
} else if ($_GET['action'] == 'allOrders') {
    cheackUserToken($Req['user_id']);
    $Data['data'] = $ordersObj->getAllOrders($Req);
} else if ($_GET['action'] == 'getOrderById') {
//    cheackUserToken($Req['user_id']);
    $Data['data'] = $ordersObj->getOrderById($Req);
} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    exit();
}



echo json_encode($Data);


