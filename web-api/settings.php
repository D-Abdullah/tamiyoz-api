<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    require_once('init.php');
    // Start Functionality

    if ($_GET['action'] == 'getAllSettings')
    {


        $settingsData = $settingsweb->getAutoLoadSettings();
        $Data = $settingsData?$settingsData:'';
//        echo json_encode($Data);
        
    }



if ($Data){
    echo json_encode(['status'=>200,'data'=>$Data]);
}else{
    echo json_encode(['status'=>403,'data'=>[]]);
}