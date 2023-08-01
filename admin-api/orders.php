<?php

/* * *************************************************************************
 *
 *   PROJECT: BigWish App
 *   powerd by IT PLUS Team
 *   Copyright 2020 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once 'init.php';

// Start Functionality
$postdata = file_get_contents("php://input");

$token  = apache_request_headers()["Authorization"];
$verify = $hs256Obj->verifyJWT('sha256', $token);


if (isset($token) && isset($verify) && $verify) {
    

    if ($_GET['action'] == 'getSomeOrders') {
        $dateTime = date('Y-m-d H:i:s');

        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchId = $_GET['searchId'] ? $_GET['searchId'] : '';
        $searchUserName = $_GET['searchUserName'] ? $_GET['searchUserName'] : '';
        $searchUserPhone = $_GET['searchUserPhone'] ? $_GET['searchUserPhone'] : '';
        $searchDateFrom = $_GET['searchDateFrom'] ? $_GET['searchDateFrom'] : '';
        $searchDateTo = $_GET['searchDateTo'] ? $_GET['searchDateTo'] :   $dateTime ;
        $searchStatus = $_GET['searchStatus'] ? $_GET['searchStatus'] : '';
        $searchProviderName = $_GET['searchProviderName'] ? $_GET['searchProviderName'] : '';
       
        $orderData = $orderObj->getSomeOrders($start, $aItemsPerPage, $sort, $type, $searchId, $searchUserName, $searchDateFrom, $searchDateTo,$searchStatus);

        $Data = $orderData ? $orderData : '';
    }

    if ($_GET['action'] == 'getSearchOrdersCount') {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchId = $_GET['searchId'] ? $_GET['searchId'] : '';
        $searchUserName = $_GET['searchUserName'] ? $_GET['searchUserName'] : '';
        $searchDateFrom = $_GET['searchDateFrom'] ? $_GET['searchDateFrom'] : '';
        $searchDateTo = $_GET['searchDateTo'] ? $_GET['searchDateTo'] : '';
        $searchStatus = $_GET['searchStatus'] ? $_GET['searchStatus'] : '';
         
        $orderSearchData = $orderObj->getSearchOrdersCount($sort, $type, $searchId, $searchUserName, $searchDateFrom, $searchDateTo,$searchStatus);

        $Data = $orderSearchData ? $orderSearchData : '';
    }

    if ($_GET['action'] == 'getOrdersCount') {

        $orderData = $orderObj->getOrdersCount();
        $Data      = $orderData ? $orderData : '';
    }
    
      if ($_GET['action'] == 'getCarTypes') {
       

        $orderData = $orderObj->getCarTypes();

        $Data = $orderData ? $orderData : '';
    }

} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneOrder') {

            $oneOrderData = $orderObj->getOneOrder($Req);
            $Data        = $oneOrderData ? $oneOrderData : '';
        }
        if ($_GET['action'] == 'getChatForThisOrder') {

            $oneOrderchat = $orderObj->getChatForThisOrder($Req);
            $Data        = $oneOrderchat ? $oneOrderchat : '';
        }

        if ($_GET['action'] == 'deleteOrder') {

            $orderData = $orderObj->deleteOrder($Req);
            $Data     = $orderData ? $orderData : '';
        }

        if ($_GET['action'] == 'changeOrderStatus') {

            $orderData = $orderObj->changeOrderStatus($Req);
            $Data     = $orderData ? $orderData : '';
        }


    } 
    else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

echo json_encode($Data);
