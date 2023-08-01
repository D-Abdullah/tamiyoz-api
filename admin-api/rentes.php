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

    if ($_GET['action'] == 'getSomeRentes') {
//         echo "ddd";
//         die();
        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchNumber = $_GET['searchNumber'] ? $_GET['searchNumber'] : '';
        $searchStation = $_GET['searchStation'] ? $_GET['searchStation'] : '';
        $searchShop = $_GET['searchShop'] ? $_GET['searchShop'] : '';
        $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

        $categoryData = $rentObj->getSomeRentes($start, $aItemsPerPage, $sort, $type, $searchNumber,$searchStation,$searchShop);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchRentesCount') {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchNumber = $_GET['searchNumber'] ? $_GET['searchNumber'] : '';
        $searchStation = $_GET['searchStation'] ? $_GET['searchStation'] : '';
        $searchShop = $_GET['searchShop'] ? $_GET['searchShop'] : '';
        $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

        $categorySearchData = $rentObj->getSearchRentesCount($sort, $type, $searchNumber,$searchStation,$searchShop);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getRentesCount') {
        $category_type = $_GET['srvice_type'] ? $_GET['service_type'] : '';
        $categoryData = $rentObj->getRentesCount($category_type);
        $Data      = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getAllStations') {
        $dData = $rentObj->getAllStations();
        $Data = $dData ? $dData : '';
    }


    if ($_GET['action'] == 'getAllShops') {
        $dData = $rentObj->getAllShops();
        $Data = $dData ? $dData : '';
    }


} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneRente') {

            $oneCategoryData = $rentObj->getOneRente($Req);
            $Data        = $oneCategoryData ? $oneCategoryData : '';
        }

        if ($_GET['action'] == 'deleteRente') {

            $categoryData = $rentObj->deleteRente($Req);
            $Data     = $categoryData ? $categoryData : '';
        }


    } 
    else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

echo json_encode($Data);
