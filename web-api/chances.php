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

    if ($_GET['action'] == 'getSomeChances') {
        $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

        $categoryData = $ChanceObj1->getSomeChances($start, $aItemsPerPage, $sort, $type, $searchName,$code);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchChancesCount') {
        $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

        $categorySearchData = $ChanceObj1->getSearchChancesCount($sort, $type, $searchName,$code);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getChancesCount') {
        
        $category_type = $_GET['srvice_type'] ? $_GET['service_type'] : '';
        $categoryData = $ChanceObj1->getChancesCount($category_type);
        $Data      = $categoryData ? $categoryData : '';
    }



//} else {
//    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

//    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneChance') {
            $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
            $oneCategoryData = $ChanceObj1->getOneChance($Req['id'],$code);
            $Data= $oneCategoryData ? $oneCategoryData : '';
        }
 if ($_GET['action'] == 'getCategoryByParentID') {

            $category_type = $_GET['category_type'] ? $_GET['category_type'] : 'news';
            $oneCategoryData = $ChanceObj1->getCategoryByParentID($Req,$category_type);
            $Data        = $oneCategoryData ? $oneCategoryData : '';
        }


//    }
//    else {
//        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//    }

}

if ($Data){
    echo json_encode(['status'=>200,'data'=>$Data]);
}else{
    echo json_encode(['status'=>403,'data'=>[]]);
}


