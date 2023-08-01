<?php

/* * *************************************************************************
/* * *************************************************************************
 *
 *   PROJECT: template_admin_area App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/ *  */
 
require_once 'init.php';

 

if ($_GET['action'] == 'getAllPlaces') {
        $data = $placesObj->getAllPlaces($Req);
        if ($data) {
           $Data['data']=$data;
        }else{
           $Data['error'] = 'لا يوجد محتوى'; 
        }  
 
}  


elseif ($_GET['action'] == 'getSearchPlacesCount') {


        $data = $placesObj->getSearchPlacesCount($Req);
        if ($data) {
           $Data['data']=$data;
        }else{
           $Data['error'] = 'لا يوجد محتوى'; 
        }  

   
        
    

}

 

 echo json_encode($Data);
