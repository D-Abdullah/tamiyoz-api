<?php

/* * *************************************************************************
 *
 *   PROJECT: my_health_first App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */

require_once 'init.php';

$Data = array();
//require_once './../twilio-php-master/src/Twilio/autoload.php';
//use Twilio\Rest\Client;
//$service_sid = 'MGba64f5950e52ee71e52034f3302844df';
//$sid = "AC1edc0afe402d3650e2d02d256061de3f";
//$token = "158e2722bd0cfa14e041d1c844339b91";
// Start Functionality

if ($_GET['action'] == "cheackEmailAndMobile") {
    $Req['mobile'] = $Req['callKey'] . $Req['mobile'];
    $Data['data'] = $userObj->cheackByParam('mobile', $Req['mobile'], $Req['user_id']);
} elseif ($_GET['action'] == 'addEditUser') {
    if ($Req['mode'] == 'edit') {
        cheackUserToken($Req['user_id']);
    }
    if ($Req['mobile']) {
        $Req['mobile'] = $Req['callKey'] . $Req['mobile'];
    }
    if (!$Req['full_name']) {
        $Data['error'] = 'Please enter your username';
    } else if (!$Req['city_id']) {
        $Data['error'] = 'Please select a city';
    } else if (!$Req['district_id']) {
        $Data['error'] = 'Please select a neighborhood';
    } else {
        $usersData = $userObj->addEditUser($Req, $Req['user_id']);
        if ($usersData) {
            $Data['data'] = $usersData;
        } else {
            $Data['error'] = 'An error occurred during the operation, please try again';
        }
    }
} elseif ($_GET['action'] == 'login') {
    $Req['mobile'] = $Req['callKey'] . $Req['mobile'];
    $userInfo = $userObj->checkUserLogin(trim($Req['mobile']));
    if (!empty($userInfo)) {
        $correctPass = password_verify($Req['password'], $userInfo['password']);
        if ($correctPass) {
            $userInfo['lang'] = $Req['lang'];
            $signInData = $userObj->getUserDetailsInfo($userInfo);
            if ($signInData['status'] == '1') {
                $Data['data'] = $signInData;
            } else if ($signInData['status'] == '0') {
                $Data['error'] = 'Sorry, your account is currently disabled, please review management';
            } else {
                $Data['error'] = 'Sorry, there was a problem fetching the data, try again';
            }
        } else {
            $Data['error'] = 'The password is incorrect';
        }
    } else {
        $Data['error'] = 'There is no account associated with the mobile number';
    }
} elseif ($_GET['action'] == 'forgetPassword') {
    $Req['mobile'] = $Req['callKey'] . $Req['mobile'];
    //  mobile sms masir
    $infoUser = $userObj->getForgateDateByParams($Req);
    if ($infoUser) {
        $new_pass = newPassword();
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT, array('cost' => 12));
        $ch = $userObj->changePassword($infoUser['id'], $hashed_pass);
        if ($ch) {
            $Data['success'] = 'تم إنشاء كلمة مرور جديدة لحسابك الخاص وهى : ' . $new_pass;
        } else {
            $Data['error'] = "There was a problem during the password change process, please try again.";
        }
    } else {
        $Data['error'] = "There is no account associated with the mobile number";
    }
} elseif ($_GET['action'] == 'logOut') {
    cheackUserToken($Req['user_id']);
    $res = $devicesApi->updateDevices($Req['device_token_id'], $Req['user_id'], '0');
//    if ($res) {
    $Data['success'] = true;
//    } else {
//        $Data['error'] = 'There was a problem during the process, please try again later';
//    }
} elseif ($_GET['action'] == 'all-users-type') {
    $Data['data'] = $userObj->getMembersByTypeApi($Req);
} elseif ($_GET['action'] == 'addEditProviderInfo') {
    cheackUserToken($Req['user_id']);
    $Data['data'] = $userObj->addEditProviderInfo($Req);
} elseif ($_GET['action'] == 'addRepresentative') {
    cheackUserToken($Req['user_id']);
    $Req['mobile'] = $Req['callKey'] . $Req['mobile'];
    $res = $userObj->cheackByParam('mobile', $Req['mobile']);
    if ($res) {
        $Data['error'] = "There is an account associated with the phone number";
    } else {
        if (!$Req['full_name']) {
            $Data['error'] = 'Please enter your username';
        } else if (!$Req['city_id']) {
            $Data['error'] = 'Please select a city';
        } else if (!$Req['district_id']) {
            $Data['error'] = 'Please select a neighborhood';
        } else {
            $Data['data'] = $userObj->addRepresentative($Req);
        }
    }
} elseif ($_GET['action'] == 'DeleteUser') {
    cheackUserToken($Req['user_id']);
    $res = $userObj->DeleteUser($Req);
    if ($res) {
        $Data['data'] = $res;
        $Data['success'] = 'The delivery representative was deleted successfully';
    } else {
        $Data['error'] = 'There was a problem during the process, please try again later';
    }
} elseif ($_GET['action'] == 'updateMobileInUsers') {
    cheackUserToken($Req['user_id']);
    $Req['id'] = $Req['user_id'];
    $Req['mobile'] = $Req['callKey'] . $Req['mobile'];
    $res = $userObj->updateMobileInUsers($Req);
    if ($res) {
        $Data['data'] = $userObj->getUserDetailsInfo($Req);
    } else {
        $Data['error'] = 'There was a problem during the process, please try again later';
    }
} elseif ($_GET['action'] == 'sendSms') {
    $Req['message'] = urlencode($Req['message']);
    $get_ = file_get_contents("https://www.hisms.ws/api.php?send_sms&username=0558085562&password=asasas&numbers={$Req['numbers']}&sender=TAHLIA&message={$Req['message']}");
    $r = explode('-', $get_);
    $Data['status'] = $r[0];
} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    exit();
}

function sendPostPHP($url) {

    $fields = [
        '__VIEWSTATE ' => "",
        '__EVENTVALIDATION' => '',
        'btnSubmit' => 'Submit',
    ];

    //url-ify the data for the POST
    $fields_string = http_build_query($fields);

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //execute post
    $obj = json_decode(curl_exec($ch));
    return (array) $obj;
}

echo json_encode($Data);


