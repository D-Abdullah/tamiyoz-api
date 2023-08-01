<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once 'init.php';
// Start Functionality


$postdata = file_get_contents("php://input");
if ($_GET['action'] == 'getHomeStatistics') {


    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);


    if (isset($token) && isset($verify) && $verify) {
        $statisticsData = $statisticsObj->getHomeStatistics();
        $Data           = $statisticsData ? $statisticsData : '';


        echo json_encode($Data);   
        } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}

if ($_GET['action'] == 'getHomeDriversLocations') {
    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);
    if (isset($token) && isset($verify) && $verify) {
        $statisticsDat = $statisticsObj->getHomeDriversLocations();
        $Data           = $statisticsDat ? $statisticsDat : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}


    if (isset($postdata) && !empty($postdata)) {
    $Req = json_decode($postdata, true);
    }
