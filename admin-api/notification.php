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

require_once('init.php'); 

// Start Functionality
$postdata = file_get_contents("php://input");

$token  = apache_request_headers()["Authorization"];
$verify = $hs256Obj->verifyJWT('sha256', $token);

if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if($_GET['action'] == 'changeOrderStatusNotification') {

        if (isset($token) && isset($verify) && $verify) {

            $notifyData = $notificationObj->changeOrderStatusNotification($Req);
            $Data = $notifyData? $notifyData :'';
            echo json_encode($Data);

        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }


}
