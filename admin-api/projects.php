<?php

/* * *************************************************************************
 *
 *   PROJECT: BigWish App
 *   powerd by IT PLUS Team
 *   Copyright 2020 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once 'init.php';

// Start Functionality
$postdata = file_get_contents("php://input");

$token  = apache_request_headers()["Authorization"];
$verify = $hs256Obj->verifyJWT('sha256', $token);

if (isset($token) && isset($verify) && $verify) {

    if ($_GET['action'] == 'getSomeProjects') {

        $start = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';
        $pagename = $_GET['pagename'] ? $_GET['pagename'] : '';
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $shopStatus = $_GET['shopStatus'];
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        //        $category_type = $_GET['category_type'] ? $_GET['category_type'] : '';

        $categoryData = $projectObj->getSomeProjects($start, $aItemsPerPage, $sort, $type, $searchName, $pagename, $shopStatus);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchProjectsCount') {
        $pagename = $_GET['pagename'] ? $_GET['pagename'] : '';
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $shopStatus = $_GET['shopStatus'];
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $categorySearchData = $projectObj->getSearchProjectsCount($sort, $type, $searchName, $pagename, $shopStatus);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getProjectsCount') {
        $pagename = $_GET['pagename'] ? $_GET['pagename'] : '';
        $shopStatus = $_GET['shopStatus'];
        $categoryData = $projectObj->getProjectsCount($pagename, $shopStatus);
        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'addEditProject') {
        // print_r($_FILES);
        //             die();
        $operation = $_REQUEST['operation'];
        $category_id = '';

        $temp = array();

        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $allow_extFile = array("pdf", "txt", "doc");
        $maxsizeFile = 1024 * 20;
        $maxsize = 1024 * 2; //kb
        $uploadFolder = $Config['uploads_path'] . 'projects';
        $uploadFolderFiles = $Config['uploads_path'] . 'projects/files';

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

                //if uploagetCategoryByParentIDding successfully
                if ($upfile) {
                    $temp['file'] = $upfile['newname'];
                } else {
                    $error = true;
                    $showError[] = $upload->showErrors();
                    $top_msg['error'][] = $showError[0][0];
                }
            }
        } else {
            $temp['file'] = null;
        }

        if ($_REQUEST['pagename'] == 'stations') {

            $uploadFile = new Upload($allow_extFile, false, $maxsizeFile, $uploadFolderFiles, false);

            if (isset($_FILES['fileUpload']['name']) and $_FILES['fileUpload']['name'] != '') {
                //get file name
                $file['name'] = addslashes($_FILES["fileUpload"]["name"]);

                // get file type
                $file['type'] = $_FILES["fileUpload"]['type'];
                // get filesize in KB
                $file['size'] = $_FILES["fileUpload"]['size'] / 1024;
                // get file tmp path
                $file['tmp'] = $_FILES["fileUpload"]['tmp_name'];

                //get file ext [to get max uploades size]
                $file['ext'] = $uploadFile->GetExt($_FILES["fileUpload"]["name"]);

                //check if guest have selected file or not
                if ($file['name'] != '') {
                    // Start Uploading File
                    $upfile = $uploadFile->Upload_File($file, $maxsizeFile);
                    // echo  $upfile['newname'];
                    // die();
                    //if uploagetCategoryByParentIDding successfully
                    if ($upfile) {
                        $temp['fileUpload'] = $upfile['newname'];
                    } else {
                        $error = true;
                        $showError[] = $uploadFile->showErrors();
                        $top_msg['error'][] = $showError[0][0];
                    }
                }
            } else {
                $temp['fileUpload'] = null;
            }
        }

        if ($operation === 'edit') {
            $category_id = $_REQUEST['category_id'];
        }
        $categoryData = $projectObj->addEditProject($_REQUEST, $temp['file'], $temp['fileUpload'], $category_id);
        $Data = $categoryData ? $categoryData : '';
    }
} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneProject') {

            $oneCategoryData = $projectObj->getOneProject($Req);
            $Data        = $oneCategoryData ? $oneCategoryData : '';
        }
        if ($_GET['action'] == 'getCategoryByParentID') {

            $category_type = $_GET['category_type'] ? $_GET['category_type'] : 'news';
            $oneCategoryData = $projectObj->getCategoryByParentID($Req, $category_type);
            $Data        = $oneCategoryData ? $oneCategoryData : '';
        }

        if ($_GET['action'] == 'deleteProject') {

            $categoryData = $projectObj->deleteProject($Req);
            $Data     = $categoryData ? $categoryData : '';
        }

        if ($_GET['action'] == 'getStationByIDSAndStatus') {
            $shopStatus = $_GET['shopStatus'];
            $categoryData = $projectObj->getStationByIDSAndStatus($Req, $shopStatus);
            $Data     = $categoryData ? $categoryData : '';
        }
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}

echo json_encode($Data);
