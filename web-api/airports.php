<?php

/* * *************************************************************************
 *
 *   PROJECT: template_admin_area App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

 
require_once 'init.php';

// Start Functionality
// $postdata = file_get_contents("php://input");
// $token  = apache_request_headers()["Authorization"];
// $verify = $hs256Obj->verifyJWT('sha256', $token);
// $Req = json_decode($postdata, true);

// echo "die";die();

if ($_GET['action'] == 'getAirportsByParams') {
 

    $data = $airportsObj->getAirportsByParams($Req);
    if ($data) {
       $Data['data']=$data;
    }else{
       $Data['error'] = 'لا يوجد محتوى'; 
    }  


}  


elseif ($_GET['action'] == 'getSearchAirportsCount') {

   

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';

        $airportsSearchData = $airportsObj->getSearchAirportsCount($sort, $type, $searchName,$place_id);

        $Data = $airportsSearchData ? $airportsSearchData : '';
       
        
        $data = $airportsObj->getSearchAirportsCount($Req);
        if ($data) {
           $Data['data']=$data;
        }else{
           $Data['error'] = 'لا يوجد محتوى'; 
        }  
    

}

 
 echo json_encode($Data);
