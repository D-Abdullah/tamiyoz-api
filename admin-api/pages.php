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

if ($_GET['action'] == 'getSomePages') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchTitle = $_GET['searchTitle'] ? $_GET['searchTitle'] : '';

        $pagesData = $pagesObj->getSomePages($start, $aItemsPerPage, $sort, $type, $searchTitle);

        $Data = $pagesData ? $pagesData : '';
        echo json_encode($Data);

    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getSearchPagesCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchTitle = $_GET['searchTitle'] ? $_GET['searchTitle'] : '';

        $pagesSearchData = $pagesObj->getSearchPagesCount($sort, $type, $searchTitle);

        $Data = $pagesSearchData ? $pagesSearchData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getPagesCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $pagesData = $pagesObj->getPagesCount();
        $Data      = $pagesData ? $pagesData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'addEditPage') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {
        $temp = array();
        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = 1024 * 2; //kb
        // $uploadFolder = 'wethaq/api/uploads/banks';
        $uploadFolder = $Config['uploads_path'] . 'pages';

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

                //if uploagetStageByParentIDding successfully
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

        $pageData = $pagesObj->addEditPage($_REQUEST,$temp['file']);
        $Data     = $pageData ? $pageData : '';
        echo json_encode($Data);

    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);


    if ($_GET['action'] == 'getOnePage') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $onepageData = $pagesObj->getOnePage($Req);
            $Data        = $onepageData ? $onepageData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

    if ($_GET['action'] == 'deletePage') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $pageData = $pagesObj->deletePage($Req);
            $Data     = $pageData ? $pageData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

}
