<?php

/* * *************************************************************************
 *
 *   PROJECT: Big Wish App
 *   powerd by IT PLUS Team
 *   Copyright 2017 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'); 


header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=utf-8");
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'); 
 
require_once('init.php');

// Start Functionality  
$postdata = file_get_contents("php://input") ;





if($_GET['action'] == 'getAllLastestNews') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $lang_code = $_GET['lang_code'] ? $_GET['lang_code'] : "ar";
        $data = $NewsWebObj->getAllLastestNews($lang_code);
        $Data = [
            'errors' => $data ? '' : "no data",
            'data' => $data ? $data : null,
        ];
    }
}

if($_GET['action'] == 'getOneNewsDetails') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $lang_code = $_GET['lang_code'] ? $_GET['lang_code'] : "ar";
        $id = $_GET['id'] ? $_GET['id'] : "";
        $data = $NewsWebObj->getOneNewsDetails($id,$lang_code);
        $Data = [
            'errors' => $data ? '' : "no data",
            'data' => $data ? $data : null,
        ];
    }
}



if($_GET['action'] == 'getAllNewsCount') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $lang_code = $_GET['lang_code'] ? $_GET['lang_code'] : "ar";

        $data = $NewsWebObj->getAllNewsCount($lang_code);
        $Data = [
            'errors' => $data ? '' : "no data",
            'data' => $data ? $data : null,
        ];
    }
}

if($_GET['action'] == 'getAllNews') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $lang_code = $_GET['lang_code'] ? $_GET['lang_code'] : "ar";
        $start = $_GET['start'] ? $_GET['start'] : "0";
        $perpage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : "1000";

        $data = $NewsWebObj->getAllNews($start,$perpage,$lang_code);
        $Data = [
            'errors' => $data ? '' : "no data",
            'data' => $data ? $data : null,
        ];
    }
}




if(isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata,TRUE);
    if (isset($Req['user_id']) && !empty($Req['user_id'])) {
        $userInfo = $userObj->getUserInfo($Req['user_id']);
    }
    
    
    if($_GET['action'] == 'getIncreaseProductViews') {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
               
        
                $data = $NewsWebObj->getIncreaseProductViews($Req);
                $Data = [
                    'errors' => $data ? "" : "no data",
                    'data' => $data ? $data : null,
                ];
            }
   }
   if($_GET['action'] == 'sendProductRating') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $NewsWebObj->sendProductRating($Req);
                $Data = [
                    'errors' => $data ? "" : "no data",
                    'data' => $data ? $data : null,
                ];
            }
   }
    
    
    

    if($_GET['action'] == 'checkCatProductsExist') {

        $data = $NewsWebObj->checkCatProductsExist($Req);
        $Data = [
            'errors'  => '',
            'data' => $data? $data : '',
        ]; 
    }



}

echo json_encode($Data);
