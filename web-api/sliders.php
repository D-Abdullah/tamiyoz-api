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

    if ($_GET['action'] == 'getSomeSliders') {
        $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';


        $categoryData = $slidersObj2->getSomeSliders($start, $aItemsPerPage, $sort, $type, $searchName,$code);

        $Data = $categoryData ? $categoryData : '';
    }







if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

        if ($_GET['action'] == 'getOneSlider') {
            $code= $_GET['lang_code'] ? $_GET['lang_code'] :'ar';
            $oneCountryData = $slidersObj2->getOneSlider($Req['id'],$code);
            $Data        = $oneCountryData ? $oneCountryData : '';
            if($Data==null){
                $Data=[];
            }




        }



}

if ($Data){
    echo json_encode(['status'=>200,'data'=>$Data]);
}else{
    echo json_encode(['status'=>403,'data'=>[]]);
}
