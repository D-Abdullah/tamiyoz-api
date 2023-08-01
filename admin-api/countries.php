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

    if ($_GET['action'] == 'getSomeCountries') {

        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';


        $categoryData = $countriesObj->getSomeCountries($start, $aItemsPerPage, $sort, $type, $searchName);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchCountriesCount') {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $categorySearchData = $countriesObj->getSearchCountriesCount($sort, $type, $searchName);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getCountriesCount') {

        $categoryData = $countriesObj->getCountriesCount();
        $Data      = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'addEditCountry') {

        $operation = $_REQUEST['operation'];
        $category_id = '';

        $temp = array();

        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = 1024 * 2; //kb
        // $uploadFolder = 'wethaq/api/uploads/banks';
        $uploadFolder = $Config['uploads_path'] . 'countries';

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

                //if uploagetCountryByParentIDding successfully
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
            $category_id = $_REQUEST['country_id'];
        }

        $categoryData = $countriesObj->addEditCountry($_REQUEST, $temp['file'], $category_id);
        $Data = $categoryData?$categoryData:''; 
    }


} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneCountry') {

            $oneCountryData = $countriesObj->getOneCountry($Req);
            $Data        = $oneCountryData ? $oneCountryData : '';
        }
 if ($_GET['action'] == 'getCountryByParentID') {

            $oneCountryData = $countriesObj->getCountryByParentID($Req);
            $Data        = $oneCountryData ? $oneCountryData : '';
        }

        if ($_GET['action'] == 'deleteCountry') {

            $categoryData = $countriesObj->deleteCountry($Req);
            $Data     = $categoryData ? $categoryData : '';
        }


    } 
    else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

echo json_encode($Data);
