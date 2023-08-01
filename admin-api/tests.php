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

if ($_GET['action'] == 'getSomeTests') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchTitle = $_GET['searchTitle'] ? $_GET['searchTitle'] : '';
        $searchCountry = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $searchStage = $_GET['searchStage'] ? $_GET['searchStage'] : '';
        $searchGrade = $_GET['searchGrade'] ? $_GET['searchGrade'] : '';
        $searchSemester = $_GET['searchSemester'] ? $_GET['searchSemester'] : '';
        $searchSubject = $_GET['searchSubject'] ? $_GET['searchSubject'] : '';
        $pagesData = $TestsObj->getSomeTests($start, $aItemsPerPage, $sort, $type, $searchTitle,$searchCountry,$searchStage,$searchGrade,$searchSemester,$searchSubject);

        $Data = $pagesData ? $pagesData : '';
        echo json_encode($Data);

    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getSearchTestsCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchTitle = $_GET['searchTitle'] ? $_GET['searchTitle'] : '';
        $searchCountry = $_GET['searchCountry'] ? $_GET['searchCountry'] : '';
        $searchStage = $_GET['searchStage'] ? $_GET['searchStage'] : '';
        $searchGrade = $_GET['searchGrade'] ? $_GET['searchGrade'] : '';
        $searchSemester = $_GET['searchSemester'] ? $_GET['searchSemester'] : '';
        $searchSubject = $_GET['searchSubject'] ? $_GET['searchSubject'] : '';
        $pagesSearchData = $TestsObj->getSearchTestsCount($sort, $type, $searchTitle,$searchCountry,$searchStage,$searchGrade,$searchSemester,$searchSubject);

        $Data = $pagesSearchData ? $pagesSearchData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getTestsCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $pagesData = $TestsObj->getTestsCount();
        $Data      = $pagesData ? $pagesData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'addEditTest' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {
        $pageData = $TestsObj->addEditTest($_REQUEST);
        $Data     = $pageData ? $pageData : '';
        echo json_encode($Data);

    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);



    if ($_GET['action'] == 'getOneTest') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $onepageData = $TestsObj->getOneTest($Req);
            $Data        = $onepageData ? $onepageData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

    if ($_GET['action'] == 'deleteTest') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $pageData = $TestsObj->deleteTest($Req);
            $Data     = $pageData ? $pageData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }
    if ($_GET['action'] == 'deleteQuestion') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $pageData = $TestsObj->deleteQuestion($Req);
            $Data     = $pageData ? $pageData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    } if ($_GET['action'] == 'deleteQuestionOption') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $pageData = $TestsObj->deleteQuestionOption($Req);
            $Data     = $pageData ? $pageData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

}
