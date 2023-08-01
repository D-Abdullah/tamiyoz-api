<?php

/* * *************************************************************************
 *
 *   PROJECT: itop App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

 

require_once 'init.php';

// Start Functionality
 
if ($_GET['action'] == 'getPageById') {
//    print_r($_REQUEST);
//    die();
    $res = $pagesObj2->getOnePage($_REQUEST);


     if ($res) {
        $Data=$res;
     }else{
        $Data ='There is no content';
     }
}

elseif ($_GET['action'] == 'contact_us') {
   $Req['subject'] = 'من تطبيق تحلية  . اسم المرسل : '.$Req['name'] ;

     $msg = '<div style="text-align: justify;direction:rtl;line-height:1.5;"> ';

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
if ($Data){
    echo json_encode(['status'=>200,'data'=>$Data]);
}else{
    echo json_encode(['status'=>403,'data'=>[]]);
}

   
    
 

 
 