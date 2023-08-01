<?php

/* * *************************************************************************
 *
 *   PROJECT: itop App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */



require_once 'init.php';

// Start Functionality

if ($_GET['action'] == 'getAllHomeSectionItems') {
   $_GET['lang_code'] == "en" ? "en" : "ar";
   $res = $homeSectionObj->getAllHomeSectionItems($_GET['lang_code']);
   if ($res) {
      $Data=[
         "status"=> 200,
         "images_path"=> "/api/uploads/home_section/",
         "errors"=>"",
         "data"=>$res
      ];
   }else{
      $Data ='There is no content';
   }
}


echo json_encode($Data);





