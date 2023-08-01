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

    if ($_GET['action'] == 'getSomeShops') {

        $start = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : '';

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchCatagory = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $categoryData = $ShopsObj->getSomeShops($start, $aItemsPerPage, $sort, $type, $searchName, $searchCatagory);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchShopsCount') {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchCatagory = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        //           echo  $searchCatagory;
        //           die();
        $categorySearchData = $ShopsObj->getSearchShopsCount($sort, $type, $searchName, $searchCatagory);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getShopsCount') {

        $categoryData = $ShopsObj->getShopsCount();
        $Data      = $categoryData ? $categoryData : '';
    }
    if ($_GET['action'] == 'getTrainingCategories') {

        $categoryData = $ShopsObj->getTrainingCategories();
        $Data      = $categoryData ? $categoryData : '';
    }


    if ($_GET['action'] == 'getTrainingCategories') {

        $categoryData = $ShopsObj->getTrainingCategories();
        $Data      = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getAllStations') {
        $dData = $ShopsObj->getAllStations();
        $Data = $dData ? $dData : '';
    }
    if ($_GET['action'] == 'AllRentsCount') {
        $shop_id = $_GET['shop'] ? $_GET['shop'] : '';
        $dData = $ShopsObj->AllRentsCount($shop_id);
        $Data = $dData ? $dData : '';
    }


    if ($_GET['action'] == 'getbooksWithCatId') {
        $cat_id = $_GET['cat_id'] ? $_GET['cat_id'] : '';
        $dData = $ShopsObj->getbooksWithCatId($cat_id);
        $Data = $dData ? $dData : '';
    }





    if ($_GET['action'] == 'addEditShop') {

        $operation = $_REQUEST['operation'];
        $category_id = '';

        $temp = array();

        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = 1024 * 2; //kb

        $allow_extFile = array("pdf", "txt", "doc");
        $maxsizeFile = 1024 * 20;
        $maxsize = 1024 * 2; //kb
        // $uploadFolder = 'wethaq/api/uploads/banks';
        $uploadFolder = $Config['uploads_path'] . 'shops';
        $uploadFolderFiles = $Config['uploads_path'] . 'shops/files';


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

                //if uploagetCountryByParentIDding successfully
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













        if ($operation === 'edit') {
            $category_id = $_REQUEST['book_id'];
        }

        $categoryData = $ShopsObj->addEditShop($_REQUEST, $temp['file'], $temp['fileUpload']);
        $Data = $categoryData ? $categoryData : '';
    }
} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}

if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneShop') {

            $oneCategoryData = $ShopsObj->getOneShop($Req);
            $Data        = $oneCategoryData ? $oneCategoryData : '';
        }
        if ($_GET['action'] == 'getBookByParentID') {

            $oneCategoryData = $ShopsObj->getBookByParentID($Req);
            $Data        = $oneCategoryData ? $oneCategoryData : '';
        }

        if ($_GET['action'] == 'deleteShop') {

            $categoryData = $ShopsObj->deleteShop($Req);
            $Data     = $categoryData ? $categoryData : '';
        }
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }
}

echo json_encode($Data);
