<?php

/* * *************************************************************************
 *
 *   PROJECT: app System
 *   powerd by IT PLUS Team
 *   Copyright 2016 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */

require_once('init.php');


    if ($_GET['action'] == 'addDevices') {
        $token_id = $devicesApi->checkDevicesToken($Req);
        if ($token_id) {
            $Req['token_id'] = $token_id;
        } else {
            $Req['token_id'] = $devicesApi->addDevicesToken($Req);
        }
        $id = $devicesApi->checkDevicesUser($Req);
        if ($id) {
            $Data['token_id'] = $devicesApi->updateDevices($Req['token_id'], $Req['user_id'],'1');
        } else {
            $Data['token_id'] = $devicesApi->addDevices($Req);
        }
    } else if ($_GET['action'] == 'getDevicesId') {
        $Req = sanitize($_GET);
        $Data['token_id'] = $devicesApi->getDevicesId($Req);
    }

echo json_encode($Data);


