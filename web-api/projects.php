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

//$token  = apache_request_headers()["Authorization"];
//$verify = $hs256Obj->verifyJWT('sha256', $token);

//if (isset($token) && isset($verify) && $verify) {

if ($_GET['action'] == 'getSomeProjects') {

    $code = $_GET['lang_code'] ? $_GET['lang_code'] : 'ar';
    $start = $_GET['start'] ? $_GET['start'] : 0;
    $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';
    $pagename = $_GET['pagename'] ? $_GET['pagename'] : '';
    $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
    $type = $_GET['type'] ? $_GET['type'] : 'DESC';

    $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
    $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

    $categoryData = $projectObj1->getSomeProjects($start, $aItemsPerPage, $sort, $type, $searchName, $code, $pagename);

    $Data = $categoryData ? $categoryData : '';
}



if ($_GET['action'] == 'getProjectsStations') {

    $code = $_GET['lang_code'] ? $_GET['lang_code'] : 'ar';
    $start = $_GET['start'] ? $_GET['start'] : 0;
    $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';
    $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
    $type = $_GET['type'] ? $_GET['type'] : 'DESC';
    $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
    $categoryData = $projectObj1->getProjectsStations($start, $aItemsPerPage, $sort, $type, $searchName, $code);

    $Data = $categoryData ? $categoryData : '';
}




if ($_GET['action'] == 'getstationWithAllRentedShop') {
    $code = $_GET['lang_code'] ? $_GET['lang_code'] : 'ar';
    $categoryData = $projectObj1->getstationWithAllRentedShop('0',$code);
    $Data     = $categoryData ? $categoryData : '';
}











if ($_GET['action'] == 'getSearchProjectsCount') {
    $code = $_GET['lang_code'] ? $_GET['lang_code'] : 'ar';
    $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
    $type = $_GET['type'] ? $_GET['type'] : 'DESC';
    $pagename = $_GET['pagename'] ? $_GET['pagename'] : '';
    $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
    $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

    $categorySearchData = $projectObj1->getSearchProjectsCount($sort, $type, $searchName, $code, $pagename);

    $Data = $categorySearchData ? $categorySearchData : '';
}

if ($_GET['action'] == 'getProjectsCount') {
    $pagename = $_GET['pagename'] ? $_GET['pagename'] : '';
    $categoryData = $projectObj1->getProjectsCount($pagename);
    $Data      = $categoryData ? $categoryData : '';
}



//} else {
//    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    //    if (isset($token) && isset($verify) && $verify) {

    if ($_GET['action'] == 'getOneProject') {
        $code = $_GET['lang_code'] ? $_GET['lang_code'] : 'ar';
        $pagename = $_GET['pagename'] ? $_GET['pagename'] : '';
        $oneCategoryData = $projectObj1->getOneProject($Req['id'], $code, $pagename);
        $Data = $oneCategoryData ? $oneCategoryData : '';
        if ($Data == null) {
            $Data = [];
        }
    }
    if ($_GET['action'] == 'getCategoryByParentID') {

        $category_type = $_GET['category_type'] ? $_GET['category_type'] : 'news';
        $oneCategoryData = $projectObj1->getCategoryByParentID($Req, $category_type);
        $Data        = $oneCategoryData ? $oneCategoryData : '';
    }
}
//    else {
//        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//    }

//}

if ($Data) {
    echo json_encode(['status' => 200, 'data' => $Data]);
} else {
    echo json_encode(['status' => 403, 'data' => []]);
}
