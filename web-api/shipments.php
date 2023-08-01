<?php

/* * *************************************************************************
 *
 *   PROJECT: template_admin_area App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

require_once 'init.php';

// Start Functionality
// $postdata = file_get_contents("php://input");
// $token  = apache_request_headers()["Authorization"];
// $verify = $hs256Obj->verifyJWT('sha256', $token);
// $Req = json_decode($postdata, true);

// echo "die";die();

if ($_GET['action'] == 'getShipmentsByParams') {
    // echo  $_GET['place_id'] ;die();
    $data = $shipmentsObj->getShipmentsByParams($Req);
    if ($data) {
        $Data['data'] = $data;
        $Data['count'] = $shipmentsObj->getSearchShipmentsCount($Req);

    } else {
        $Data['error'] = 'لا يوجد محتوى';
    }

} elseif ($_GET['action'] == 'getShipmentDetails') {

    $data = $shipmentsObj->getShipmentDetails($Req);
    if ($data) {
        $Data['data'] = $data;
    } else {
        $Data['error'] = 'لا يوجد محتوى';
    }

} elseif ($_GET['action'] == 'addEditOffer') {

    $id = $offersObj->addEditOffer($Req);
    if ($id) {
        $Data['data'] = $offersObj->getOfferDetails($id) ;
    } else {
        $Data['error'] = 'لا يوجد محتوى';
    }

} elseif ($_GET['action'] == 'confirmCode') {
    $id = $shipmentsObj->checkCode($Req);
    // echo  $id  ;die();
    if ($id) {
        $data = $shipmentsObj->confirmCode($Req);
        if ($data) {
            $Data['data'] = 'تم تاكيد الكود بنجاح';
        } else {
            $Data['error'] = 'حصلت مشكلة أثناء تأكيد الكود';
        }

    } else {
        $Data['error'] = 'الكود المدخل غير صحيح يرجى التأكد منه';
    }

}

// code: "654984984"
// type: "given_code"
// user_id: "41"

echo json_encode($Data);
