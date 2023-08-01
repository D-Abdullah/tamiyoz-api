<?php

/* * *************************************************************************
 *
 *   PROJECT: itop App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */
 

require_once('init.php');

// Start Functionality  
 

if($_GET['action'] == 'readNotification') {
      
      $data = $notifyObj->updateNotifyStatus($Req['notification_id']);
      if ($data) {
        $Data['data']=$Req['notification_id'];
       }else{
        $Data['error'] = true; 
       }   
}
elseif ($_GET['action'] == 'getNotificationsList') {

         $data = $notifyObj->getNotificationsList($Req['user_id'],$Req['start'],$Req['limit']);
         if ($data) {
          $Data['data']=$data;
         }else{
          $Data['error'] = 'لا يوجد محتوى'; 
         }
}

elseif ($_GET['action'] == 'getNotificationDetails') {  

    $notifyData = $notifyObj->getNotificationDetails($Req['notification_id'],$Req['user_id']); 

       if ($notifyData) {
          $Data['data']=$notifyData;
       }else{
          $Data['error'] = 'لا يوجد محتوى'; 
       }
}
elseif ($_GET['action'] == 'getUnReadNotificationNum') {  


    $UnReadNotificationNum = $notifyObj->getUnReadNotificationNum($Req['user_id']); 

     if ($UnReadNotificationNum) {
        $Data['data'] = $UnReadNotificationNum;
     } 
}
elseif ($_GET['action'] == 'setDeviceToken') {
    if ($Req['device_token_id']) {

        $id = $devices->checkDeviceToken($Req['device_token_id']);
        // id from table device_token
        if ($id) {
            // id from table devices
            $deviceId = $devices->checkDevices($id, $Req['user_id']);
            if ($deviceId) {
                // update devices where id == $deviceId
                $devices->updateDevicesApi($deviceId, '1');
            }
            else{
                $temp['device_token_id'] = $id;
                $temp['user_id']         = $Req['user_id'];
                $temp['lang_code']         = LANGUAGE;
                $temp['login'] = 1;
                
                $devices->addDevicesApi($temp);
            }

            // return id of device_token table to store in app storage to use it later
            $data = $id;
            
        }else{
            $temp['device_token_id'] = $Req['device_token_id'];
            $temp['type']            = $Req['type'];
            $lastID = $devices->addDeviceTokenApi($temp);
            if ($lastID) {
                $devic['device_token_id'] = $lastID;
                $devic['user_id']         = $Req['user_id'];
                $temp['lang_code']         = LANGUAGE;
                $devic['login']  = 1;

                $devices->addDevicesApi($devic);
            }
            
            // return id of device_token table to store in app storage to use it later
            $data = $lastID;
        }
    }


    $Data = $data ? $data : '';
} 



elseif ($_GET['action'] == 'sendNotificationByType') {
         if ($Req['notificationType']) {

            $sendNotification = $notifyObj->sendNotificationByType($Req); 
        }
       if ($sendNotification) {
          $Data['data'] = $sendNotification;
       } else{
          $Data['error'] = true;
       }
   

} 


echo json_encode($Data);
 
//echo $Data;die();
