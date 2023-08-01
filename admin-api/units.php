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

    if ($_GET['action'] == 'getSomeUnits') {
        $start = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchCountry = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $searchStage = $_GET['searchStage'] ? $_GET['searchStage'] : '';
        $searchGrade = $_GET['searchGrade'] ? $_GET['searchGrade'] : '';
        $searchSemester = $_GET['searchSemester'] ? $_GET['searchSemester'] : '';
        $searchSubject = $_GET['searchSubject'] ? $_GET['searchSubject'] : '';
        $langData = $unitsObj->getSomeUnits($start, $aItemsPerPage, $sort, $type, $searchName,$searchCountry,$searchStage,$searchGrade,$searchSemester,$searchSubject);
        $Data = $langData ? $langData : '';
    }

    if ($_GET['action'] == 'getSearchUnitsCount') {
        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';
        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';
        $searchCountry = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $searchStage = $_GET['searchStage'] ? $_GET['searchStage'] : '';
        $searchGrade = $_GET['searchGrade'] ? $_GET['searchGrade'] : '';
        $searchSemester = $_GET['searchSemester'] ? $_GET['searchSemester'] : '';
        $searchSubject = $_GET['searchSubject'] ? $_GET['searchSubject'] : '';
        $langSearchData = $unitsObj->getSearchUnitsCount($sort, $type, $searchName,$searchCountry,$searchStage,$searchGrade,$searchSemester,$searchSubject);
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
        $semester = $_GET['semester'] ? $_GET['semester'] : '';
        $dData = $gradesObj->getSubjectsWithGradeID($grade_id,$semester);
        $Data = $dData ? $dData : '';
    }

    if ($_GET['action'] == 'getUnitsWithSubjectID') {
        $subject_id = $_GET['subject_id'] ? $_GET['subject_id'] : '';

        $dData = $gradesObj->getUnitsWithSubjectID($subject_id);
        $Data = $dData ? $dData : '';
    }
    if ($_GET['action'] == 'getLessonsWithUnitID') {
        $unit_id = $_GET['unit_id'] ? $_GET['unit_id'] : '';

        $dData = $gradesObj->getLessonsWithUnitID($unit_id);
        $Data = $dData ? $dData : '';
    }


    if ($_GET['action'] == 'getUnitsCount') {

        $langData = $unitsObj->getUnitsCount();
        $Data = $langData ? $langData : '';
    }

    if ($_GET['action'] == 'getAllSubjects') {

        $langData = $unitsObj->getAllSubjects();
        $Data = $langData ? $langData : '';

    }

    if ($_GET['action'] == 'addEditUnit') {

        $operation = $_REQUEST['operation'];
        $lang_id = '';
        if ($operation === 'edit') {
            $lang_id = $_REQUEST['unit_id'];
        }
//         print_r($_REQUEST); die();
        $langData = $unitsObj->addEditUnit($_REQUEST, $lang_id);
        $Data = $langData ? $langData : '';

    }
}else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}


    if (isset($postdata) && !empty($postdata)) {

        $Req = json_decode($postdata, true);
        if (isset($token) && isset($verify) && $verify) {
            if ($_GET['action'] == 'getOneUnit') {

                $token = apache_request_headers()["Authorization"];
                $verify = $hs256Obj->verifyJWT('sha256', $token);

                if (isset($token) && isset($verify) && $verify) {

                    $onelangData = $unitsObj->getOneUnit($Req);
                    $Data = $onelangData ? $onelangData : '';

                } else {
                    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
                }

            }

            if ($_GET['action'] == 'deleteUnit') {

                $token = apache_request_headers()["Authorization"];
                $verify = $hs256Obj->verifyJWT('sha256', $token);

                if (isset($token) && isset($verify) && $verify) {

                    $langData = $unitsObj->deleteUnit($Req);
                    $Data = $langData ? $langData : '';

                } else {
                    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
                }

            }


            if ($_GET['action'] == 'deleteOneLesson') {

                $token = apache_request_headers()["Authorization"];
                $verify = $hs256Obj->verifyJWT('sha256', $token);

                if (isset($token) && isset($verify) && $verify) {

                    $langData = $unitsObj->deleteOneLesson($Req);
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

