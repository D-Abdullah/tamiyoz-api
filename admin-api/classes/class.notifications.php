<?php

$notificationObj = new Notification(); 

class Notification {

var $mDb; 
var $mConfig; 
var $mMailer;

function Notification() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config;
	$this->pushNotify = new pushmessage();
    $this->mMailer = new Mailer();
}

function changeOrderStatusNotification($temp) {

    // print_r($temp); die();

    $dateTime = date('Y-m-d H:i:s');

    $sql = "INSERT INTO `notification` SET ";

    if ($temp['send_user_id'] === null) {
        $sql .="`receive_user_id`='{$temp['receive_user_id']}', ";
        $sql .="`page_type`='{$temp['page_type']}', ";
        $sql .="`url`='{$temp['url']}', ";

        $sql .="`date_added`='{$dateTime}'";
        //echo $sql; die();
        $res = $this->mDb->query($sql);
    }
    else{
        foreach ($temp as $k => $v) {
            $sql.="{$k}='{$v}',";
        }
        $sql .="`date_added`='{$dateTime}'";
        //echo $sql; die();
        $res =  $this->mDb->query($sql);
    }

    if($res){

        $id   =  $this->mDb->getLastInsertId();
        $res2 =  $this->getUserDevicesToken($temp['receive_user_id']);

        if ($res2) {
            foreach ($res2 as $value) {
                $arr['data']['notification_id']   = $id;
                $arr['data']['notification_type'] = $temp['page_type'];
                $arr['data']['url']               = $temp['url'];
                $arr['data']['msgcnt']            = 1;
                $arr['data']['app_language']   = $value['lang_code'];

                if ($value['lang_code'] === "ar") {
                    
                    if ($temp['page_type'] === "changeOrderStatus") {
                        $arr['data']['notifiy_message']  = "قامت الإدارة بتغيير حالة الطلب رقم : " . $temp['url'];
                    }

                }
                elseif($value['lang_code'] === "en"){

                    if ($temp['page_type'] === "changeOrderStatus" ) {
                        $arr['data']['notifiy_message']  = "The management has changed the status of the order " .  $temp['url'];;
                    }
                }
                else{

                    // Set Default Language == Arabic
                    if ($temp['page_type'] === "changeOrderStatus") {
                        $arr['data']['notifiy_message']  = "قامت الإدارة بتغيير حالة الطلب رقم : " . $temp['url'];
                    }
                }

                $arr['device_token_id']           = $value['device_token_id'];
                $arr['platform_type']             = $value['type'];
                //print_r($arr); die();
                $this->pushNotify->sendMessage($arr);

            }
        }

    }
    return $res;

}

function getUserDevicesToken($aUserId) {
    $sql = "SELECT D.`lang_code`, DT.`id` as deviceID, DT.`type`, DT.`device_token_id` FROM `devices` D";
    $sql .= " LEFT JOIN `device_token` DT ON (DT.id = D.device_token_id) ";
    $sql .= "WHERE D.`user_id`='{$aUserId}' ";
    $sql .= "AND D.`login`='1' ";
    // echo $sql;die();
    return $this->mDb->getAll($sql);
}



}?>