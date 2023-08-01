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

//searchCountry,searchStage,searchGrade,searchSemester
$token  = apache_request_headers()["Authorization"];
$verify = $hs256Obj->verifyJWT('sha256', $token);

if (isset($token) && isset($verify) && $verify) {

    if ($_GET['action'] == 'getSomeSubjects') {
        $start = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchCountry = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $searchStage = $_GET['searchStage'] ? $_GET['searchStage'] : '';
        $searchGrade = $_GET['searchGrade'] ? $_GET['searchGrade'] : '';
        $searchSemester = $_GET['searchSemester'] ? $_GET['searchSemester'] : '';
        $langData = $subjectObj->getSomeSubjects($start, $aItemsPerPage, $sort, $type, $searchName,$searchCountry,$searchStage,$searchGrade,$searchSemester);
        $Data = $langData ? $langData : '';
    }

    if ($_GET['action'] == 'getSearchSubjectsCount') {
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchCountry = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $searchStage = $_GET['searchStage'] ? $_GET['searchStage'] : '';
        $searchGrade = $_GET['searchGrade'] ? $_GET['searchGrade'] : '';
        $searchSemester = $_GET['searchSemester'] ? $_GET['searchSemester'] : '';
        $langSearchData = $subjectObj->getSearchSubjectsCount($sort, $type, $searchName,$searchCountry,$searchStage,$searchGrade,$searchSemester);
//       echo $langSearchData;die();
        $Data = $langSearchData >0 ? $langSearchData : '0';

    }

    if ($_GET['action'] == 'getAllCountries') {
        $dData = $gradesObj->getAllCountries();
        $Data = $dData ? $dData : '';
    }

    if ($_GET['action'] == 'getStagesWithCountryId') {
        $country_id = $_GET['country_id'] ? $_GET['country_id'] : '';
        $dData = $gradesObj->getStagesWithCountryId($country_id);
        $Data = $dData ? $dData : '';
    } if ($_GET['action'] == 'getGradesWithStageID') {
        $stage_id = $_GET['stage_id'] ? $_GET['stage_id'] : '';
        $dData = $gradesObj->getGradesWithStageID($stage_id);
        $Data = $dData ? $dData : '';
    }


    if ($_GET['action'] == 'getSubjectsWithGradeID') {
        $grade_id = $_GET['grade_id'] ? $_GET['grade_id'] : '';
        $semester = $_GET['semester'] ? $_GET['semester'] : 'both';
        $dData = $gradesObj->getSubjectsWithGradeID($grade_id,$semester);
        $Data = $dData ? $dData : '';
    }


    if ($_GET['action'] == 'getSubjectsCount') {

        $langData = $subjectObj->getSubjectsCount();
        $Data = $langData ? $langData : '';
    }

    if ($_GET['action'] == 'getAllSubjects') {

        $langData = $subjectObj->getAllSubjects();
        $Data = $langData ? $langData : '';

    }

    if ($_GET['action'] == 'addEditSubject') {

        $operation = $_REQUEST['operation'];
        $lang_id = '';
        $temp = array();
        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = 1024 * 2; //kb
        // $uploadFolder = '_wethaq/api/uploads/languages';
        $uploadFolder = $Config['uploads_path'] . 'subjects';

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
        } else {
            $temp = null;
        }

        if ($operation === 'edit') {
            $lang_id = $_REQUEST['subject_id'];
        }

//         print_r($_REQUEST); die();

        $langData = $subjectObj->addEditSubject($_REQUEST, $temp['file'], $lang_id);
        $Data = $langData ? $langData : '';

    }
}else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}


    if (isset($postdata) && !empty($postdata)) {

        $Req = json_decode($postdata, true);
        if (isset($token) && isset($verify) && $verify) {
            if ($_GET['action'] == 'getOneSubject') {

                $token = apache_request_headers()["Authorization"];
                $verify = $hs256Obj->verifyJWT('sha256', $token);

                if (isset($token) && isset($verify) && $verify) {

                    $onelangData = $subjectObj->getOneSubject($Req);
                    $Data = $onelangData ? $onelangData : '';

                } else {
                    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
                }

            }

            if ($_GET['action'] == 'deleteSubject') {

                $token = apache_request_headers()["Authorization"];
                $verify = $hs256Obj->verifyJWT('sha256', $token);

                if (isset($token) && isset($verify) && $verify) {

                    $langData = $subjectObj->deleteSubject($Req);
                    $Data = $langData ? $langData : '';

                } else {
                    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
                }

            }

        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }
    }

    echo json_encode($Data);

