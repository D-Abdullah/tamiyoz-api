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

    private function sendMessageAndroidAndIos($device_token_ids, $params) {


        // new  link app
        define('API_ACCESS_KEY', 'AAAAs3MgzaQ:APA91bFMO9PLs7z7iqs195IBtPF3aLnXfXa0dHc8xQdI7jsAL9yUHTnSskMwO7473jWKDIe0W9JnByDZFHEzHiM3fjm7JEjmhIiJNY09yFdgWJ1qGpfoCmrxRuy4x5STeOgxMO9J8b6n'); //AIzaSyDeXZKdbc1uz1YDwcKDfFV3b1negz6KGGE
        // $registrationIds = array('ePs8JSBR0bg:APA91bHbOvDHnDJdXmHexE-ZhFltfQtQLt_kWtAbx6hZM9wOKqcpzoknB2sWHbQbd8NtwUOwG1tGAlANO1xn-WfA8VT8x3h5e0f9w94GbPkbdRUvXp16jmD0-2iliGFKwie4UXeyk-6v' );

    //   print_r($device_token_ids);
        $fields = array(
            'registration_ids'    => $device_token_ids,
            'data'  => array(
                'push'  => true,
                'type'  => $params['data']['notification_type'],
                'id'    => $params['data']['notification_id'],
                'order_id'   => $params['data']['url'],
                'details'   => $params['data']['details'],
                'title'   => $params['data']['title'],
                "body"  => $params['data']['notifiy_message']
            ),
            'notification'  => array(
                "title"        => $params['data']['title'],  //Any value
                "body"         => $params['data']['notifiy_message'],  //Any value
                'badge'        => $params['data']['msgcnt'],
                "color"        => "#1FDDE9",
                "sound"        => "default", //If you want notification sound
                // "click_action" => "FCM_PLUGIN_ACTIVITY",  // Must be present for Android
                "icon"         => "fcm_push_icon"  // White icon Android resource
            )
        );

        $headers = array(
            'Authorization: key='.API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        //  curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
//   echo  "result is ".$result ."<br>";
        return $result;

    }

    /**
     * Send message to SmartPhone
     * $params [platform_type, msg, device_token_id]
     */

    public function sendMessage($params) {
        

        $rtn =  $this->sendMessageAndroidAndIos($params["device_token_ids"], $params);
        return $rtn;
    }


}
