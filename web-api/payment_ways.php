<?php

/* * *************************************************************************
 *
 *   PROJECT: itop App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

 
require_once 'init.php';

// Start Functionality
 

 
if ($_GET['action'] == 'getPayment_ways') { 

           $data = $payment_waysObj->getPayment_ways();
 
           if ($data) {
              $Data['data']=$data;
           }else{
              $Data['error'] = 'لا يوجد محتوى'; 
           }  
         
  }
echo json_encode($Data);





