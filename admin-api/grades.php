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

    if ($_GET['action'] == 'getSomeGrades') {

        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchCountry = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $searchStage = $_GET['searchStage'] ? $_GET['searchStage'] : '';


        $categoryData = $gradesObj->getSomeGrades($start, $aItemsPerPage, $sort, $type, $searchName,$searchCountry,$searchStage);

        $Data = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'getSearchGradesCount') {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchCountry = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $searchStage = $_GET['searchStage'] ? $_GET['searchStage'] : '';
        $categorySearchData = $gradesObj->getSearchGradesCount($sort, $type, $searchName,$searchCountry,$searchStage);

        $Data = $categorySearchData ? $categorySearchData : '';
    }

    if ($_GET['action'] == 'getGradesCount') {

        $categoryData = $gradesObj->getGradesCount();
        $Data      = $categoryData ? $categoryData : '';
    }

    if ($_GET['action'] == 'addEditGrade') {

        $operation = $_REQUEST['operation'];
        $data_id = '';

        $temp = array();

        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = 1024 * 2; //kb
        // $uploadFolder = 'wethaq/api/uploads/banks';
        $uploadFolder = $Config['uploads_path'] . 'grades';

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

        if ($operation === 'edit') {
            $data_id = $_REQUEST['grade_id'];
        }

        $categoryData = $gradesObj->addEditGrade($_REQUEST, $temp['file'], $data_id);
        $Data = $categoryData?$categoryData:''; 
    }


    if ($_GET['action'] == 'getAllCountries') {
        $dData = $gradesObj->getAllCountries();
        $Data = $dData ? $dData : '';
    }

    if ($_GET['action'] == 'getStagesWithCountryId') {
        $country_id = $_GET['country_id'] ? $_GET['country_id'] :'';
        $dData = $gradesObj->getStagesWithCountryId($country_id);
        $Data = $dData ? $dData : '';
    }
 if ($_GET['action'] == 'getGradesWithStageId') {
        $stage_id = $_GET['stage_id'] ? $_GET['stage_id'] :'';
        $dData = $gradesObj->getGradesWithStageId($stage_id);
        $Data = $dData ? $dData : '';
    }

} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}


if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if (isset($token) && isset($verify) && $verify) {

        if ($_GET['action'] == 'getOneGrade') {

            $oneData = $gradesObj->getOneGrade($Req);
            $Data        = $oneData ? $oneData : '';
        }
 if ($_GET['action'] == 'getGradeByParentID') {

            $oneData = $gradesObj->getGradeByParentID($Req);
            $Data        = $oneData ? $oneData : '';
        }

        if ($_GET['action'] == 'deleteGrade') {

            $categoryData = $gradesObj->deleteGrade($Req);
            $Data     = $categoryData ? $categoryData : '';
        }


    } 
    else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

echo json_encode($Data);
