<?php

$notifyObj = new notifications(); 

class notifications {

    var $mDb; 
    var $userObj; 
    var $mConfig; 
    var $lang; 
    var $dateTime;
    function notifications() {
        global $Config; 
        $this ->mDb = new iplus();
        $this ->userObj = new users(); 
        $this ->mConfig = $Config; 
        $this->pushNotify = new pushmessage();
        $this -> lang =  LANGUAGE; 
        $this -> dateTime = date('Y-m-d H:i:s'); 
    }
 

    function sendNewNotification($temp) {
           // echo $sql; die();

       if ( $temp['send_user_id'] != $temp['receive_user_id']  ) {
                        
        
         $sql = "INSERT INTO `{$this->mPrefix}notification` SET ";
          foreach ($temp as $k => $v) {


              $sql.=($k == 'cat_name' || $k == 'order_title' || $k == 'order_id'  || $k == 'img'|| $k == 'user_name')?' ' : "{$k}='{$v}',";
          }
          $sql .="date_added='{$this -> dateTime }'";
            // echo $sql; die();

          $res =   $this->mDb->query($sql) ;
          if($res){
             
              $id  =   $this->mDb->getLastInsertId() ;
              
              $res2 =  $this->getUserDevicesToken($temp['receive_user_id']);

              $unReadMessage =  $this->getUnReadNotificationNum($temp['receive_user_id']);
                // echo "<pre>";print_r($res2); die();
              if ($res2) {
                  foreach ($res2 as $value) {
                      $arr['data']['notification_id']   = $id;
                      $arr['data']['notification_type'] = $temp['page_type'];
                      $arr['data']['url']               = $temp['url'];
                      $arr['data']['cat_name']         = $temp['cat_name'];
                      $arr['data']['msgcnt']            = $unReadMessage;
                      $arr['data']['notifiy_message']   = $this->makeNotificationMessage($value['lang_code'],$temp);
                      $arr['device_token_id']           = $value['device_token_id'];
                      $arr['platform_type']             = $value['type'];
                       // print_r($arr); die();
                      $pushed = $this->pushNotify->sendMessage($arr);
                      //echo $pushed;die();
                 
                  }
              }
          }
        return $res;

      }
    
    }

    function getSenderUserName($aUserId) {
        $sql = "SELECT user_name FROM `{$this->mPrefix}users`";
        $sql .= "WHERE `id`='{$aUserId}' ";
        //echo $sql;die();
        return $this->mDb->getOne($sql);
    }

    function getUserDevicesToken($aUserId) {
        $sql = "SELECT  dt.`id` as deviceID, dt.`type`, dt.`device_token_id` , d.lang_code FROM `{$this->mPrefix}devices` d";
        $sql .= " LEFT JOIN `{$this->mPrefix}device_token` dt ON (dt.id = d.device_token_id) ";
        $sql .= "WHERE d.`user_id`='{$aUserId}' ";
        $sql .= "AND d.`login`='1' ";
        // echo $sql;die();
        return $this->mDb->getAll($sql);
    }

 

 
    function updateNotifyStatus($aId) {

        $sql  = "UPDATE `{$this->mPrefix}notification` SET ";
        $sql .= "`status`= '1' ";
        $sql .= "WHERE `id`='{$aId}' ";
        //echo $sql;die();
        return $this->mDb->query($sql);
    }

    function getUnReadNotificationNum($aUserId) {
        $sql = " SELECT count(id)  FROM `{$this->mPrefix}notification`";
        $sql.= " WHERE `receive_user_id` = '{$aUserId}'";
        $sql.= " AND `status` = '0'";
        //echo $sql; die();
        return $this->mDb->getOne($sql);
    }
  
 
    function getNotificationsList($aUserId, $aStart = 0 , $aLimit = 0) {

        $sql =  " SELECT n.*, u.`avatar`, u.`user_name` ,u.`id` as user_id     FROM `{$this->mPrefix}notification` n ";
        $sql .= " LEFT JOIN `{$this->mPrefix}users` u ON (u.id = n.send_user_id) ";
        // $sql .= " LEFT JOIN `{$this->mPrefix}orders` o ON (o.id = n.url) ";
        // $sql .= " LEFT JOIN `{$this->mPrefix}categories` cat ON (o.category_id = cat.id) ";
 
        $sql .= " WHERE n.`receive_user_id` = {$aUserId} ";
        $sql .= " ORDER BY n.date_added DESC ";
        $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
        // echo $sql; die();
        $res= $this->mDb->getAll($sql);
        if (count($res) > 0 ) {
            for ($i=0; $i < count($res) ; $i++) { 

                //moreDetails For Message notifications
            //    if ($res[$i]['page_type'] ==  'message') {
            //        $sql =  " SELECT o.id   FROM `{$this->mPrefix}orders` o   ";
            //        $sql .= " LEFT JOIN `{$this->mPrefix}conversation` C ON (o.id = C.order_id) ";

            //        $sql .= " WHERE C.`id` = {$res[$i]['url']} ";
            //        $res2= $this->mDb->getRow($sql);
            //        // echo "$sql ";die();
            //        $res[$i]['order_id'] =  $res2['id'] ; 
            //        // $res[$i]['order_title'] =  $res2['order_title'] ; 

            //    }
            //    else 
               if ($res[$i]['page_type'] ==  'admin_notification') {
                   $res[$i]['admin_notification'] =   $this->getNotificationAdmin_notificationContent($res[$i]['url']);

                   // unset($res[$i]['order_title']) ;
                    

               }
               // else if ($res[$i]['page_type'] ==  'activePromotion' || $res[$i]['page_type'] ==  'cancelPromotion' || $res[$i]['page_type'] ==  'finishPromotion') {
 
                     
               //     unset($res[$i]['order_title']) ;
                    

               // }
            }
            # code...
        }
         
        return $res;
    }

   function getNotificationAdmin_notificationContent($admin_notification_id){
         $sql =  " SELECT   nl.notification FROM `{$this->mPrefix}admin_notification_langs` nl   ";

         $sql .= " WHERE nl.admin_notification_id = '{$admin_notification_id}'  AND nl.lang_code = '{$this->lang}' ";
                   // echo "$sql ";die();
         return $this->mDb->getOne($sql);
    }
    function getNotificationDetails($id ,$user_id) {

            $sql  = " SELECT  n.id , nl.notification as admin_notification  FROM `notification` n ";

            $sql .= " LEFT JOIN `users` u ON u.`id` = n.`receive_user_id`";
            $sql .= " LEFT JOIN `admin_notification_langs` nl ON nl.`admin_notification_id` = n.`url`";

            $sql .= " WHERE n.`id` = '{$id}' AND  n.`receive_user_id` = '{$user_id}'   AND nl.lang_code = '{$this->lang}'  ";
           // echo "$sql";die();
           return $this -> mDb -> getRow($sql);
 

    }
    function  sendNotificationByType($req){
           // echo $sql; die();

             if ($req['notificationType'] == 'addOrder') {
                    $addNotSend['send_user_id'] = $req['user_id'];
                    $addNotSend['url']       = $req['id'];
                    $addNotSend['page_type'] = $req['notificationType'];
                    $addNotSend['cat_name'] = $req['cat_name'];

                    $prov_ids =   $this ->userObj ->  getProviderNearByOrderLocation($req['lat'],$req['lon'] , $req['user_type_require'], $req['user_id'] );
                     // echo "<pre>";print_r($prov_ids); die();

                    for ($i=0; $i < count($prov_ids) ; $i++) { 
                        if ( intval( $prov_ids[$i]['distance'] )  <= 600 ) {
                              $addNotSend['receive_user_id'] = $prov_ids[$i]['id'];
                               $this->sendNewNotification($addNotSend);
                        }
                    }
     
             }
             elseif ($req['notificationType'] == 'acceptOrder') {
                    $addNotSend['send_user_id'] = $req['user_id'];
                    $addNotSend['url']       = $req['order_id'];
                    $addNotSend['page_type'] = $req['notificationType'];
                    $addNotSend['receive_user_id'] = $req['ownerOrder_user_id'];
                    $addNotSend['cat_name'] = $req['cat_name'];

                     $this->sendNewNotification($addNotSend);
                    
             }
             elseif ($req['notificationType'] == 'completedOrder'){
                    $addNotSend['send_user_id'] = $req['send_user_id'];
                    $addNotSend['url']       = $req['order_id'];
                    $addNotSend['page_type'] = $req['notificationType'];
                    $addNotSend['receive_user_id'] = $req['receive_user_id'];
                    $addNotSend['cat_name'] = $req['cat_name'];

                    $this->sendNewNotification($addNotSend);
             }
            
         


 
     }

    function makeNotificationMessage($lang_code,$temp){

        $sender_name =  $this->getSenderUserName($temp['send_user_id']);
        if ($temp['page_type'] == 'addOrder'){
            $message  = $lang_code == 'ar' ? 'قام  ' . $sender_name . ' باضافة طلب جديد   : '.$temp['cat_name']  : $sender_name . ' add new order : '  ;   
        }
       elseif ($temp['page_type'] == 'acceptOrder'){
            $message  = $lang_code == 'ar' ? 'قام  ' . $sender_name . 'بقبول طلبك  : '.$temp['cat_name']   : $sender_name . ' add new offer on your order   ' ;   
        }
         elseif ($temp['page_type'] == 'completeOrder'){
            $message  = $lang_code == 'ar' ? 'قام  ' . $sender_name . 'بانهاء الطلب  : '.$temp['cat_name']   : $sender_name . ' add new offer on your order   ' ;   
        }
       
        return $message ; 
     }

}?>