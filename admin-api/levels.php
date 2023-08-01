<?php

/* * *************************************************************************
 *
 *   PROJECT: Tahalya app
 *   powerd by IT PLUS Team
 *   Copyright 2020 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'); 

require_once('init.php'); 

// Start Functionality  
$postdata = file_get_contents("php://input") ;

if ($_GET['action'] == 'getSomeLevels') {

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {  

        $start = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerlevel = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';
//         echo $_GET['aItemsPerPage'];
//         die();
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $level_type = $_GET['level_type'] ? $_GET['level_type'] : 'admin';
        $provider_id=$_GET['provider_id'] ?$_GET['provider_id'] :"";
        $levelsData = $levelsObj->getSomeLevels($start, $aItemsPerlevel, $sort, $type, $searchName);

        $Data = $levelsData?$levelsData:''; 
        echo json_encode($Data);

    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getSearchLevelsCount') {  

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $level_type = $_GET['level_type'] ? $_GET['level_type'] : 'admin';
        $provider_id=$_GET['provider_id'] ?$_GET['provider_id'] :"";
        $levelsSearchData = $levelsObj->getSearchLevelsCount($sort, $type, $searchName,$level_type,$provider_id); 

        $Data = $levelsSearchData?$levelsSearchData:'';  
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getLevelsCount') {  

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {
        $leveltype=$_GET['level_type'] ?$_GET['level_type'] :"admin";
        $provider_id=$_GET['provider_id'] ?$_GET['provider_id'] :"";
        $levelsData = $levelsObj->getLevelsCount();
        $Data = $levelsData?$levelsData:''; 
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if(isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata,TRUE);
    
    if ($_GET['action'] == 'addEditLevel') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $levelData = $levelsObj->addEditLevel($Req); 
            $Data = $levelData?$levelData:''; 
            echo json_encode($Data);

        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

    if ($_GET['action'] == 'getOneLevel') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {
            $leveltype=$_GET['level_type'] ?$_GET['level_type'] :"admin";
            $provider_id=$_GET['provider_id'] ?$_GET['provider_id'] :"";
            $onelevelData = $levelsObj->getOneLevel($Req,$leveltype,$provider_id); 
            $Data = $onelevelData?$onelevelData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

    if ($_GET['action'] == 'deleteLevel') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $levelData = $levelsObj->deleteLevel($Req); 
            $Data = $levelData?$levelData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

}

