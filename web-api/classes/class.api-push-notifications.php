<?php

/**
 * Copyright 2014 Shop-Wiz.Com.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

$pushmessage = new pushmessage();

class pushmessage {
    
    private function sendMessageAndroidAndIos($device_token_id, $params) {
        
        // new 
        define('API_ACCESS_KEY', 'AAAAKRbOZz4:APA91bHa7reS1K1ptiBZBg8VMzoiQ4RcxSM19_2rMZCq7ub-WoX0m3agFx2CjvdMaZz4DP-dGW5b6A4M0h1qlK83xTX2IjiE6GiskmhaTecmxa70w4idIi9KFsivbsTCBi6VI1rwMm27'); 
        // define('API_ACCESS_KEY', 'asdjalkjsljdl'); 
        //$deviceTokenIds = array($device_token_id);
        $fields = array(
            'to'    => $device_token_id,
            'data'  => array(
                        'push'         => true,
                        'type'         => $params['data']['notification_type'],
                        'id'           => $params['data']['notification_id'],
                        'url'          => $params['data']['url'],
                        "body"         => strip_tags($params['data']['notifiy_message']),
                        "order_id"     => $params['data']['order_id'],
                        "order_title"  => $params['data']['order_title'],
                        "avatar"       => $params['data']['avatar'],
                        "user_name"    => $params['data']['user_name'],
                        "user_id"    => $params['data']['user_id'],
                        
                                         
                    ),

            'notification'  => array(
                                "title"        => strip_tags($params['data']['title']),  //Any value 
                                "body"         => strip_tags($params['data']['notifiy_message']),  //Any value 
                                'badge'        => $params['data']['msgcnt'],
                                "color"        => "#666666",
                                "sound"        => "default", //If you want notification sound 
                                "click_action" => "FCM_PLUGIN_ACTIVITY",  // Must be present for Android 
                                "icon"         => "fcm_push_icon"  // White icon Android resource 
                             )
        );


        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        //~ curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
       // echo "$result";die();
        curl_close( $ch );
        return $result;

    }

    /**
    * Send message to SmartPhone
    * $params [platform_type, msg, device_token_id]
    */

    public function sendMessage($params) {

        $rtn =  $this->sendMessageAndroidAndIos($params["device_token_id"], $params);
        return $rtn;
        // break;
    }
}
