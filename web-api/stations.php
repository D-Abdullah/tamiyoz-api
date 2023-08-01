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

//if (isset($token) && isset($verify) && $verify) {

    if ($_GET['action'] == 'getSomeStations') {
        $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

        $categoryData = $stationObj1->getSomeStations($start, $aItemsPerPage, $sort, $type, $searchName,$code);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchStationsCount') {
//        echo "ddd";
//        die();
        $code=$_GET['lang_code']?$_GET['lang_code']:'ar';
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

        $categorySearchData = $stationObj1->getSearchStationsCount($sort, $type, $searchName,$code);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getStationsCount') {
        $category_type = $_GET['srvice_type'] ? $_GET['service_type'] : '';
        $categoryData = $stationObj1->getStationsCount($category_type);
        $Data      = $categoryData ? $categoryData : '';
    }



//} else {
//    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneStation') {

            $oneCategoryData = $stationObj1->getOneStation($Req['id']);
            $Data= $oneCategoryData ? $oneCategoryData : '';
        }
 if ($_GET['action'] == 'getCategoryByParentID') {
            $category_type = $_GET['category_type'] ? $_GET['category_type'] : 'news';
            $oneCategoryData = $stationObj1->getCategoryByParentID($Req,$category_type);
            $Data        = $oneCategoryData ? $oneCategoryData : '';
        }



    } 
    else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

echo json_encode($Data);
