<?php

/* * *************************************************************************
 *
 *   PROJECT: Tahalya app
 *   powerd by IT PLUS Team
 *   Copyright 2020 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'); 

require_once('init.php');

// Start Functionality  
$postdata = file_get_contents("php://input") ;

  // Authorization

if ($_GET['action'] == 'getSomeUsers') {

    $token = apache_request_headers()["Authorization"];
   
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {  
 $dateTime = date('Y-m-d H:i:s');
        $start = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $userType = $_GET['userType'] ? $_GET['userType'] : '';
        
        $searchUserPhone = $_GET['searchUserPhone'] ? $_GET['searchUserPhone'] : '';
        $searchDateFrom = $_GET['searchDateFrom'] ? $_GET['searchDateFrom'] : '';
        $searchDateTo = $_GET['searchDateTo'] ? $_GET['searchDateTo'] :   $dateTime ;
        $searchUserStatus = $_GET['searchUserStatus'] !='' ? $_GET['searchUserStatus'] : '';
        $searchUserType = $_GET['searchUserType'] ? $_GET['searchUserType'] : '';

        $usersData = $userAdmObj->getSomeUsers($userType, $start, $aItemsPerPage, $sort, $type, $searchName,$searchUserPhone,$searchDateFrom,$searchDateTo,$searchUserStatus,$searchUserType);

        $Data = $usersData?$usersData:''; 
        echo json_encode($Data);

    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'chickEmail') {

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);
    $req=$_GET['email'];
    if (isset($token) && isset($verify) && $verify) {


        $userLevelData = $userAdmObj->chickEmail($req);
        $Data = $userLevelData?$userLevelData:'';
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}
if ($_GET['action'] == 'getAllCarTypes') {

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $userLevelData = $userAdmObj->getAllCarTypes();
        $Data = $userLevelData?$userLevelData:'';
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}
if ($_GET['action'] == 'getAllCities') {
    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $citiesDat = $userAdmObj->getAllCities();
        $Data      = $citiesDat ? $citiesDat : '';
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}
if ($_GET['action'] == 'getDistrictsByCityId') {
    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $id=$_GET['city_id'];
        $citiesDat = $userAdmObj->getDistrictsByCityId($id);
        $Data      = $citiesDat ? $citiesDat : '';
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}


if ($_GET['action'] == 'chickMobile') {

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);
    $req=$_GET['mobile'];
    if (isset($token) && isset($verify) && $verify) {


        $userLevelData = $userAdmObj->chickMobile($req);
        $Data = $userLevelData?$userLevelData:'';
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getSearchUsersCount') {  
 $dateTime = date('Y-m-d H:i:s');
    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $userType = $_GET['userType'] ? $_GET['userType'] : '';
        $searchUserPhone = $_GET['searchUserPhone'] ? $_GET['searchUserPhone'] : '';
        $searchDateFrom = $_GET['searchDateFrom'] ? $_GET['searchDateFrom'] : '';
        $searchDateTo = $_GET['searchDateTo'] ? $_GET['searchDateTo'] :   $dateTime ;
        $searchUserStatus = $_GET['searchUserStatus'] !='' ? $_GET['searchUserStatus'] : '';
        $searchUserType = $_GET['searchUserType'] ? $_GET['searchUserType'] : '';


        $usersSearchData = $userAdmObj->getSearchUsersCount($userType, $sort, $type, $searchName,$searchUserPhone,$searchDateFrom,$searchDateTo,$searchUserStatus,$searchUserType);

        $Data =
        $usersSearchData?$usersSearchData:'';  
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getUsersCount') {  

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $userType = $_GET['userType'] ? $_GET['userType'] : '';
        $usersData = $userAdmObj->getUsersCount($userType);
        $Data = $usersData?$usersData:''; 
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}

if ($_GET['action'] == 'getUsersLevels') {  

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {
        $leveltype=$_GET['level_type'] ?$_GET['level_type'] :"admin";
        $provider_id=$_GET['provider_id'] ?$_GET['provider_id'] :"";
        $usersLevelsData = $userAdmObj->getUsersLevels($leveltype,$provider_id);
        $Data = $usersLevelsData?$usersLevelsData:''; 
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getUserDetailsInfo') {  

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $id = $_GET['id'] ? $_GET['id'] : '';
        $userData = $userAdmObj->getUserDetailsInfo($id);
        $Data = $userData?$userData:''; 
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}


if ($_GET['action'] == 'addEditUser') {

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $operation = $_REQUEST['operation'];
        $user_id = '';
        $temp = array();
        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = 1024 * 10; //kb
        // $uploadFolder = 'link/api/uploads/users';
        $uploadFolder = $Config['uploads_path'] . 'users';
      
        $thumbsConfig = array(
            array(
                'name' => 'medium',
                'width' => '240',
                'hight' => '250'
            ),
            array(
                'name' => 'small',
                'width' => '80',
                'hight' => '80'
            ),
        );

        $upload = new Upload($allow_ext, $thumbsConfig, $maxsize, $uploadFolder, false);

        if (isset($_FILES['file']['name']) AND $_FILES['file']['name'] != '') {

        //get file name
            $file['name'] = addslashes($_FILES["file"]["name"]);
        // get file type
            $file['type'] = $_FILES["file"]['type'];
        // get filesize in KB
            $file['size'] = $_FILES["file"]['size'] / 1024;
        // get file tmp path
            $file['tmp'] = $_FILES["file"]['tmp_name'];
        //get file ext [to get max uploades size]
            $file['ext'] = $upload->GetExt($_FILES["file"]["name"]);
        //check if guest have selected file or not
            if ($file['name'] != '') {
        // Start Uploading File
                $upfile = $upload->Upload_File($file, $maxsize);

                //if uploading successfully
                if ($upfile) {
                    $temp['file'] = $upfile['newname'];
                } else {
                    $error = true;
                    $showError[] = $upload->showErrors();
                    $top_msg['error'][] = $showError[0][0];
                }
            }
        }
        else
        {
            $temp['file'] = null;
        }
       
 
        if ($operation === 'edit') {
            $user_id = $_REQUEST['user_id'];
        }

        
            // print_r($_REQUEST); die();
            $usersData = $userAdmObj->addEditUser($_REQUEST, $temp['file'], $user_id);
            $Data = $usersData?$usersData:'';
            echo json_encode($Data);

        



    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getSearchUsersByType') {


    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $query = $_GET['query'] ? $_GET['query'] : '';
        $user_type = $_GET['user_type'] ? $_GET['user_type'] : 'parent';
        $oneUsersData = $userAdmObj->getSearchUsersByType($query,$user_type);
        $Data = $oneUsersData?$oneUsersData:'';
        echo json_encode($Data);
    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if(isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata,TRUE);

    if($_GET['action'] == 'signIn') {

    	$email = $Req['email'];
    	$password = $Req['password'];
        $signInData = $userAdmObj->signIn($email, $password);
        // JWT Header
        $header = [
            "alg"   => "HS256",
            "typ"   => "JWT"
        ];
        // JWT Payload data
        $payload = [
            "user_id"   => $signInData['id']
        ];
        $token = $hs256Obj->generateJWT('sha256', $header, $payload);
        $returnedData = ['token'=> $token, 'userData'=> $signInData];
        $Data = $returnedData? $returnedData :'';
        echo json_encode($Data);

    }

    if ($_GET['action'] == 'getUserLevel') {

        $token = apache_request_headers()["Authorization"];
       
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $userLevelData = $userAdmObj->getUserLevel($Req);
            $Data = $userLevelData?$userLevelData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

    if ($_GET['action'] == 'getOneUser') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $userType = $_GET['userType'] ? $_GET['userType'] : '';

            $oneUsersData = $userAdmObj->getOneUser($userType, $Req);
            $Data = $oneUsersData?$oneUsersData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }
    if ($_GET['action'] == 'getAllProviderDrivers') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $providerdriversData = $userAdmObj->getAllProviderDrivers($Req);
            $Data = $providerdriversData?$providerdriversData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }
    if ($_GET['action'] == 'addingDriverToOrder') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $driverData = $userAdmObj->addingDriverToOrder($Req);
            $Data = $driverData?$driverData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }
    if ($_GET['action'] == 'addFinancialOperation') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);
        if (isset($token) && isset($verify) && $verify) {

            $addopoData = $userAdmObj->addFinancialOperation($Req);
            $Data = $addopoData?$addopoData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }
    }

    if ($_GET['action'] == 'deleteUser') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $userType = $_GET['userType'] ? $_GET['userType'] : '';

            $usersData = $userAdmObj->deleteUser($userType, $Req);
            $Data = $usersData?$usersData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }
    if ($_GET['action'] == 'deleteStudentsParents') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {
            $usersData = $userAdmObj->deleteStudentsParents($Req);
            $Data = $usersData?$usersData:'';
            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }
     if ($_GET['action'] == 'deleteOneUserCar') {

        $token = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $userDeleteCarData = $userAdmObj->deleteOneUserCar($Req['user_id'], $Req['car_id']);
            
            $Data =$userDeleteCarData ? $userDeleteCarData : "no car deleted";

            echo json_encode($Data);
        }
        else{
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////// APP ////////////////////////////////////////////

    if($_GET['action'] == 'forgetPassword') {

        $email = $Req['email'];
        $userForgetPassData = $userAdmObj->userForgetPassword($email);

        if ($userForgetPassData['error'] === 'email_mobile_already_exists')
        {
            // generate new user password
            // Send new password to user by this email

            $infoUser = $userAdmObj->getUserInfoByEmail($email);

            $new_pass = newPassword();

            $ch = $userAdmObj->changePassword($infoUser['id'], md5($new_pass));
     

            if ($ch) {

                $subject = 'Your password has been changed based on your request - IPlus';

                $msg = "<div style='direction:ltr;line-height:1.5;'> Welcome";
                $msg.= "<span style='color:green;display:block'>" . $infoUser['full_name'] . "</span>";
                $msg.= "Your password has been changed based on your request, and your new password is ";
                $msg.= "<span style='color:green;display:block'>" . $new_pass . "</span></div>";
                $site_email = $Config['site_email'];

                $send = $mailer->sendEmail($infoUser['email'], $subject, $msg, 'IPlus' . '<' . $site_email . '>', $site_email);

                if ($send) {
                    $Data['success'] = 'successSendPassByEmail';
                } else {
                    $Data['error'] = 'problemToSendPassword';
                }

            } else {
                $Data['error'] = 'problemWhenChangePassword';
            }

            echo json_encode($Data);
        }
        else
        {
            $Data = $userForgetPassData? $userForgetPassData :'';
            echo json_encode($Data);
        }
    }
}

