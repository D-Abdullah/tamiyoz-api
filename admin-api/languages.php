<?php

/* * *************************************************************************
 *
 *   PROJECT: Tahalya app
 *   powerd by IT PLUS Team
 *   Copyright 2020 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once 'init.php';

// Start Functionality
$postdata = file_get_contents("php://input");

if ($_GET['action'] == 'getSomeLanguages') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';

        $langData = $langObj->getSomeLanguages($start, $aItemsPerPage, $sort, $type, $searchName);

        $Data = $langData ? $langData : '';
        echo json_encode($Data);

    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getSearchLanguagesCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';

        $langSearchData = $langObj->getSearchLanguagesCount($sort, $type, $searchName);

        $Data = $langSearchData ? $langSearchData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getLanguagesCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $langData = $langObj->getLanguagesCount();
        $Data      = $langData ? $langData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getAllLanguages') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $langData = $langObj->getAllLanguages();
        $Data      = $langData ? $langData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'addEditLanguage') {

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $operation = $_REQUEST['operation'];
        $lang_id = '';

        $temp = array();

        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = 1024 * 2; //kb
        // $uploadFolder = '_wethaq/api/uploads/languages';
        $uploadFolder = $Config['uploads_path'] . 'languages';
        
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
            $temp = null;
        }

        if ($operation === 'edit') {
            $lang_id = $_REQUEST['lang_id'];
        }

        // print_r($_REQUEST); die();

        $langData = $langObj->addEditLanguage($_REQUEST, $temp['file'], $lang_id); 
        $Data = $langData?$langData:''; 
        echo json_encode($Data);

    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if ($_GET['action'] == 'getOneLanguage') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $onelangData = $langObj->getOneLanguage($Req);
            $Data        = $onelangData ? $onelangData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

    if ($_GET['action'] == 'deleteLanguage') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $langData = $langObj->deleteLanguage($Req);
            $Data     = $langData ? $langData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

}
