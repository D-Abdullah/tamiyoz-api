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

    if ($_GET['action'] == 'getSomePartners') {
        $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
        $start= $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 100;
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';

        $categoryData = $partnersObj1->getSomePartners($start, $aItemsPerPage, $sort, $type, $searchName,$code);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchPartnersCount') {
        $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $categorySearchData = $partnersObj1->getSearchPartnersCount($sort, $type, $searchName,$code);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getPartnersCount') {

        $categoryData = $partnersObj1->getPartnersCount();
        $Data      = $categoryData ? $categoryData : '';
    }




//} else {
//    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

//    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOnePartner') {
            $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
            $oneCountryData = $partnersObj1->getOnePartner($Req['id'],$code);
            $Data        = $oneCountryData ? $oneCountryData : '';
            if($Data==null){
                $Data=[];
            }
        }
 if ($_GET['action'] == 'getPartnerByParentID') {

            $oneCountryData = $partnersObj1->getPartnerByParentID($Req);
            $Data        = $oneCountryData ? $oneCountryData : '';
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


