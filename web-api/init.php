<?php

/* * *************************************************************************
 *
 *   PROJECT: my_health_first App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);




header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,User-Token ,Authorization,X-Requested-With,access-control-allow-origin');



//date_default_timezone_set('Asia/Riyadh');


$postdata = file_get_contents("php://input");
$Req = json_decode($postdata, TRUE);


//echo "<pre>";
//print_r($Req);
//echo "</pre>";



define('LANGUAGE', 'ar');
require_once('../vendor/autoload.php');
require_once('../includes/config.inc.php');
require_once('../utils/util.php');
require_once('classes/plusMailer.php');
require_once('classes/iplus.php');
require_once('classes/upload-class.php');
require_once('classes/class.hs256.php');
// Main Components
require_once('classes/class.api-push-notifications.php');
require_once('classes/class.users.php');
require_once('classes/class.notifications.php');
require_once('classes/class.devices.php');

require_once('classes/class.pages.php');
require_once('classes/class.payment_ways.php');


require_once 'classes/class.places.php';
require_once 'classes/class.orders.php';
require_once 'classes/class.common.php';

require_once 'classes/class.services.php';
require_once 'classes/class.projects.php';
require_once 'classes/class.stations.php';
require_once 'classes/class.shops.php';
require_once 'classes/class.chances.php';
require_once 'classes/class.rentes.php';
require_once 'classes/class.settings.php';
require_once 'classes/class.partners.php';
require_once 'classes/class.news.php';
require_once 'classes/class.training_courses.php';
require_once 'classes/class.sliders.php';
require_once 'classes/class.shipments.php';
require_once 'classes/class.airports.php';
require_once 'classes/class.home_section.php';


// Sections
if (!function_exists('apache_request_headers')) {

    ///
    function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach ($_SERVER as $key => $val) {
            if (preg_match($rx_http, $key)) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', strtolower($arh_key));
                if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                    foreach ($rx_matches as $ak_key => $ak_val)
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    $arh_key = implode('-', $rx_matches);
                }
                $arh[$arh_key] = $val;
            }
        }
        if (isset($_SERVER['CONTENT_TYPE']))
            $arh['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        if (isset($_SERVER['CONTENT_LENGTH']))
            $arh['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        return( $arh );
    }

    ///
}

/// Added by Ali Hamdy
function cheackUserToken($user_id) {
    global $userObj;
    if ($user_id) {
        $UserToken = apache_request_headers()["User-Token"];
        $AuthorizationUserCode = $userObj->getAuthorizationCodeByUserId($user_id);
        if (($AuthorizationUserCode != $UserToken)) {
            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
            exit();
        }
    } else {
        echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
        exit();
    }
}

///// Start Authorization users By Ahmadklsany
//if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
//
//    if ($Req['user_id']) {
//        $UserToken = apache_request_headers()["Usertoken"];
//        $AuthorizationUserCode = $userObj->getAuthorizationCodeByUserId($Req['user_id']);
//        // echo  $UserToken  ."<br>" .$AuthorizationUserCode  ;die();
//        if ($AuthorizationUserCode != $UserToken) {
//            echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//            exit();
//        }
//    }
//} else {
//    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
//    exit();
//}
?>
