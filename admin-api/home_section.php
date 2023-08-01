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

if ($_GET['action'] == 'getSomeSection') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchTitle = $_GET['searchTitle'] ? $_GET['searchTitle'] : '';

        $homeSectionData = $homeSectionObj->getSomeSection($start, $aItemsPerPage, $sort, $type, $searchTitle);

        $Data = $homeSectionData ? $homeSectionData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}

if ($_GET['action'] == 'getSearchSectionCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchTitle = $_GET['searchTitle'] ? $_GET['searchTitle'] : '';

        $pagesSearchData = $homeSectionObj->getSearchSectionCount($sort, $type, $searchTitle);

        $Data = $pagesSearchData ? $pagesSearchData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}

if ($_GET['action'] == 'getSectionCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $homeSectionData = $homeSectionObj->getSectionCount();
        $Data      = $homeSectionData ? $homeSectionData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}

if ($_GET['action'] == 'addEditSection') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {
        $temp = array();
        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = 1024 * 2; //kb
        // $uploadFolder = 'wethaq/api/uploads/banks';
        $uploadFolder = $Config['uploads_path'] . 'home_section';

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

        if (isset($_FILES['file']['name']) and $_FILES['file']['name'] != '') {

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
        } else {
            $temp = null;
        }
        /*********************************************************************** */
        $tempIco = array();
        $allow_extIco = array("jpg", "gif", "png", "jpeg", "bmp", "svg");
        $maxsizeIco = 1024 * 2; //kb
        // $uploadFolder = 'wethaq/api/uploads/banks';
        $uploadFolderIco = $Config['uploads_path'] . 'home_section';

        $thumbsConfigIco = array(
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

        $uploadIco = new Upload($allow_extIco, $thumbsConfigIco, $maxsizeIco, $uploadFolderIco, false);

        if (isset($_FILES['ico']['name']) and $_FILES['ico']['name'] != '') {

            //get file name
            $ico['name'] = addslashes($_FILES["ico"]["name"]);
            // get file type
            $ico['type'] = $_FILES["ico"]['type'];
            // get filesize in KB
            $ico['size'] = $_FILES["ico"]['size'] / 1024;
            // get file tmp path
            $ico['tmp'] = $_FILES["ico"]['tmp_name'];
            //get file ext [to get max uploades size]
            $ico['ext'] = $uploadIco->GetExt($_FILES["ico"]["name"]);
            //check if guest have selected file or not
            if ($ico['name'] != '') {
                // Start Uploading File
                $upfileIco = $uploadIco->Upload_File($ico, $maxsizeIco);

                //if uploagetStageByParentIDding successfully
                if ($upfileIco) {
                    $tempIco['ico'] = $upfileIco['newname'];
                } else {
                    $error = true;
                    $showError[] = $uploadIco->showErrors();
                    $top_msg['error'][] = $showError[0][0];
                }
            }
        } else {
            $tempIco = null;
        }
        $pageData = $homeSectionObj->addEditSection($_REQUEST, $temp['ico'], $tempIco['ico']);
        $Data     = $pageData ? $pageData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);


    if ($_GET['action'] == 'getOneSection') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $onepageData = $homeSectionObj->getOneSection($Req);
            $Data        = $onepageData ? $onepageData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }
    }
}
