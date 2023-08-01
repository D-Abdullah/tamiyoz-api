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

    if ($_GET['action'] == 'getSomeShops') {
        $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
        $stationID= $_GET['station_id'] ? $_GET['station_id'] :'';
        $start= $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchStation = $_GET['searchStation'] ? $_GET['searchStation'] : '';
        $categoryData = $ShopsObj1->getSomeShops($start, $aItemsPerPage, $sort, $type, $searchName,$code,$searchStation,$stationID);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchShopsCount') {
        $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchStation = $_GET['searchStation'] ? $_GET['searchStation'] : '';
//           echo  $searchStation;
//           die();
        $categorySearchData = $ShopsObj1->getSearchShopsCount($sort, $type, $searchName,$searchStation,$code);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getShopsCount') {

        $categoryData = $ShopsObj1->getShopsCount();
        $Data      = $categoryData ? $categoryData : '';
    }
    if ($_GET['action'] == 'getTrainingCategories') {

        $categoryData = $ShopsObj1->getTrainingCategories();
        $Data      = $categoryData ? $categoryData : '';
    }


    if ($_GET['action'] == 'getTrainingCategories') {

        $categoryData = $ShopsObj1->getTrainingCategories();
        $Data      = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getAllStations') {
        $dData = $ShopsObj1->getAllStations();
        $Data = $dData ? $dData : '';
    }


    if ($_GET['action'] == 'getbooksWithCatId') {
        $cat_id = $_GET['cat_id'] ? $_GET['cat_id'] :'';
        $dData = $ShopsObj1->getbooksWithCatId($cat_id);
        $Data = $dData ? $dData : '';
    }








//} else {
//    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//}

if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);
//     var_dump($Req);
//     die();
//    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneShop') {
            $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
            $oneCategoryData = $ShopsObj1->getOneShop($Req["id"],$code);
            $Data= $oneCategoryData ? $oneCategoryData : '';
             if($Data==null){
                 $Data=[];
             }
        }
        if ($_GET['action'] == 'getBookByParentID') {

            $oneCategoryData = $ShopsObj1->getBookByParentID($Req);
            $Data        = $oneCategoryData ? $oneCategoryData : '';
        }



    }
//    else {
//        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//    }

//}

if ($Data){
    echo json_encode(['status'=>200,'data'=>$Data]);
}else{
    echo json_encode(['status'=>403,'data'=>[]]);
}

