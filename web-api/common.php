<?php

/* * *************************************************************************
 *
 *   PROJECT: Big Wish App
 *   powerd by IT PLUS Team
 *   Copyright 2017 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'); 

require_once('init.php');

// Start Functionality  
$postdata = file_get_contents("php://input") ;


if($_GET['action'] == 'getAllSlides') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $data = $CommonObj->getAllSlides();
        $Data = [
            'errors' => $data ? '' : "no data",
            'data' => $data ? $data : null,
        ];
    }
}




if($_GET['action'] == 'getAllLanguages') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $data = $CommonObj->getAllLanguages();
        $Data = [
            'errors' => $data ? "" : 'no data',
            'data' => $data ? $data : null,
        ];

    }
}


if ($_GET['action'] == 'getHeaderAdvertisements') {


    $postsData = $CommonObj->getHeaderAdvertisements();

    $Data = $postsData ? $postsData : false;

}


if ($_GET['action'] == 'getCenterAdvertisements') {



    $postsData = $CommonObj->getCenterAdvertisements();

    $Data = $postsData ? $postsData : false;

}


if ($_GET['action'] == 'getSideAdvertisements') {

    $postsData = $CommonObj->getSideAdvertisements();

    $Data = $postsData ? $postsData : false;

}

if ($_GET['action'] == 'getBottomAdvertisements') {


    $postsData = $CommonObj->getBottomAdvertisements();

    $Data = $postsData ? $postsData : false;

}


if($_GET['action'] == 'getAllSocialMedia') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $data = $CommonObj->getAllSocialMedia();
        $Data = [
            'errors' =>$data ? "": 'no data',
            'data' => $data ? $data : null,
        ];
    }
}

if($_GET['action'] == 'getOnePageDetails') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $lang_code = $_GET['lang_code'] ? $_GET['lang_code'] : "ar";
        $id=$_GET['id'] ? $_GET['id'] : "";
        $data = $CommonObj->getOnePageDetails($id,$lang_code);
        $Data = [
            'errors' =>$data ? "": 'no data',
            'data' => $data ? $data : null,
        ];
    }
}
if($_GET['action'] == 'sendMailContactUs') {


    $Req['subject'] = 'من موقع الاكاديمية   . اسم المرسل : '.$Req['name'] ;
    $msg ='<div style="text-align: justify;direction:rtl;line-height:1.5;">Phone : '.$Req['phone'].'</div><br><br>';
    $msg .= '<div style="text-align: justify;direction:rtl;line-height:1.5;"> ';

    # code...

    $msg .= stripslashes($Req['message']) . '</div>';


//   echo  $msg  . '<br>';die();
    // echo " $msg" . '<br>';

    $info = $mailer->sendEmail($Config['site_email'], $Req['subject'], $msg, $Config['site_name'] . ' ' . '<' . $Req['email'] . '>', $Req['email']);

    if ($info) {
        $Data['data']='تمت عمليه الارسال بنجاح ,شكرا على ثقتكم بنا';
    }else{
        $Data['error'] =  'حدثت مشكلة أثناء العملية يرجى المحاولة لاحقا';
    }


}

if($_GET['action'] == 'getAllPages') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $lang_code = $_GET['lang_code'] ? $_GET['lang_code'] : "ar";
        $data = $CommonObj->getAllPages($lang_code);
        $Data = [
            'errors' =>$data ? "": 'no data',
            'data' => $data ? $data : null,
        ];
    }
}



if(isset($postdata) && !empty($postdata)) {

    $Req = json_decode($postdata,TRUE);

if($_GET['action'] == 'seandOrderToAdmin') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userInfo = $userObj->getUserInfo($Req['user']['user_id']);
        if(trim($Req['user']['token']) == trim($userInfo['authentication_code']))
           {
                $data = $CommonObj->seandOrderToAdmin($Req);
               
            }
         $Data = [
            'errors' =>$data ? "": 'Order Not Send',
            'data' => $data ? $data : null,
        ];
    }
}


if($_GET['action'] == 'addSubscriber') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         
        $data = $CommonObj->addSubscriber($Req);
        $Data = [
            'errors' =>$data ? "": 'email not added',
            'data' => $data ? $data : null,
        ];
    }
}


if($_GET['action'] == 'addRegistrationAcceptance') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $data = $CommonObj->addRegistrationAcceptance($Req);
        $Data = [
            'errors' =>$data ? "": 'request not added',
            'data' => $data ? $data : null,
        ];
    }
}

    
    

    
    // if (isset($Req['user_id']) && !empty($Req['user_id'])) {
    //     $userInfo = $userObj->getUserInfo($Req['user_id']);
    // }


    // if(trim($Req['authentication_code']) == trim($userInfo['authentication_code']))
    // {

    //     if($_GET['action'] == 'getOnePageDetails') {

    //         $data = $catsObj->getOnePageDetails($Req);
    //         $Data = [
    //             'errors'  => '',
    //             'data' => $data? $data : '',
    //         ]; 
    //     }

    // }else{
    //     $Data = [
    //         'errors'  => 'Something went wrong you dont have permission to use this',
    //         'data' => '',
    //     ];
    // }
}

echo json_encode($Data);
