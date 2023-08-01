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

if ($_GET['action'] == 'getSomeFrequentlyQuestions') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $start         = $_GET['start'] ? $_GET['start'] : 0;
        $aItemsPerPage = $_GET['aItemsPerPage'] ? $_GET['aItemsPerPage'] : 5;

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';

        $langData = $frequently_questionsObj->getSomeFrequentlyQuestions($start, $aItemsPerPage, $sort, $type, $searchName);

        $Data = $langData ? $langData : '';
        echo json_encode($Data);

    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getSearchFrequentlyQuestionsCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $sort = $_GET['sort'] ? $_GET['sort'] : 'id';
        $type = $_GET['type'] ? $_GET['type'] : 'DESC';

        $searchName = $_GET['searchName'] ? $_GET['searchName'] : '';

        $langSearchData = $frequently_questionsObj->getSearchFrequentlyQuestionsCount($sort, $type, $searchName);

        $Data = $langSearchData ? $langSearchData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getFrequentlyQuestionsCount') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $langData = $frequently_questionsObj->getFrequentlyQuestionsCount();
        $Data      = $langData ? $langData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'getAllFrequentlyQuestions') {

    $token  = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $langData = $frequently_questionsObj->getAllFrequentlyQuestions();
        $Data      = $langData ? $langData : '';
        echo json_encode($Data);
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if ($_GET['action'] == 'addEditFrequentlyQuestion') {

    $token = apache_request_headers()["Authorization"];
    $verify = $hs256Obj->verifyJWT('sha256', $token);

    if (isset($token) && isset($verify) && $verify) {

        $operation = $_REQUEST['operation'];
        $question_id = '';

        if ($operation === 'edit') {
            $question_id = $_REQUEST['question_id'];
        }

        // print_r($_REQUEST); die();

        $langData = $frequently_questionsObj->addEditFrequentlyQuestion($_REQUEST, $question_id);
        $Data = $langData?$langData:''; 
        echo json_encode($Data);

    }
    else{
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
    }

}

if (isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata, true);

    if ($_GET['action'] == 'getOneQuestion') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $onelangData = $frequently_questionsObj->getOneQuestion($Req);
            $Data        = $onelangData ? $onelangData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

    if ($_GET['action'] == 'deleteFrequentlyQuestion') {

        $token  = apache_request_headers()["Authorization"];
        $verify = $hs256Obj->verifyJWT('sha256', $token);

        if (isset($token) && isset($verify) && $verify) {

            $langData = $frequently_questionsObj->deleteFrequentlyQuestion($Req);
            $Data     = $langData ? $langData : '';
            echo json_encode($Data);
        } else {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        }

    }

}
