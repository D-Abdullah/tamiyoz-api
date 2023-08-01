<?php
//require("./vendor/phpmailer/phpmailer/src/PHPMailer.php");
//require("./vendor/phpmailer/phpmailer/src/Exception.php");
//require("./vendor/phpmailer/phpmailer/src/SMTP.php");


//
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;


$RanteObj1 = new Rante();

class Rante {

var $mDb; 
var $mConfig; 

function Rante() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config;
    $this->mail=new Mailer();
}

function convert_object_to_array($data) {

    if (is_object($data)) {
        $data = get_object_vars($data);
    }

    if (is_array($data)) {
        return array_map(__METHOD__, $data);
    }
    else {
        return $data;
    }
}

function addRente($Req){
//    var_dump($Req);
   $name=$Req['name']?$Req['name']:'';
    $phone=$Req['phone']?$Req['phone']:'';
    $email=$Req['email']?$Req['email']:'';
    $notes=$Req['notes']?$Req['notes']:'';
    $rante_time=$Req['rante_time']?$Req['rante_time']:'';
    $shop_id=$Req['shop_id']?$Req['shop_id']:'';
    $activity_type=$Req['activity_type']?$Req['activity_type']:'';
    // $activity_name=$Req['activity_name']?$Req['activity_name']:'';
    $commercial_registration_no=$Req['commercial_registration_no']?$Req['commercial_registration_no']:'';

    $dateTime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO `rents_requests` SET ";
    $sql .= " `name` = '{$name}', ";
    $sql .= " `phone` = '{$phone}', ";
    $sql .= " `email` = '{$email}', ";
    $sql .= " `activity_type` = '{$activity_type}', ";
    //   $sql .= " `activity_name` = '{$activity_name}', ";
        $sql .= " `commercial_registration_no` = '{$commercial_registration_no}', ";
    
    $sql .= " `notes` = '{$notes}', ";
    $sql .= " `rante_time` = '{$rante_time}', ";
     $sql .= " `shop_id` = '{$shop_id}',";
    $sql .= " `date_added` = '{$dateTime}' ";
    
    //     echo $sql;
    // die();
   $id=$this -> mDb -> queryreturnlastid($sql);

//   echo $res;
//     die();
   $this->generateRenteRequestPdf($Req,$id);





  return $id;
}







function generateRenteRequestPdf($data,$id){
    $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new \Mpdf\Mpdf([
        'fontDir' => array_merge($fontDirs, [
            _DIR_ . './font',
        ]),
        'fontdata' => $fontData + [
                'Cairo' => [
                    'R' => 'Cairo-VariableFont_wght.ttf',
                    'I' => 'Cairo-VariableFont_wght.ttf',
                ]
            ],
        'default_font' => 'Cairo'
    ]);








    $dateTime = date('d-m-Y');
    $statonID=$this->getOneShop($data['shop_id']);
    $numofRent=$id;
//    $mpdf = new \Mpdf\Mpdf(['autoArabic' => true,'autoScriptToLang' => true]);
    ob_start();
    include "./RentRequest.php";
    $template = ob_get_contents();
    ob_end_clean();

    $mpdf->WriteHtml($template);
    $mpdf->output("../uploads/RentesPDF/RentRequest_$id.pdf", \Mpdf\Output\Destination::FILE);

    $sql = "UPDATE `rents_requests` SET ";
    $sql .= " `rent_file` = 'RentRequest_{$id}.pdf' ";
    $sql .= " WHERE  `id` = '{$id}'";
    $this -> mDb -> query($sql);




//     $this->sendMail($id);


}


    function getOneShop($id,$code='ar') {
        $sql = "SELECT  c.`station_id` FROM `shops` c";
        $sql .= " WHERE c.`id` = '{$id}'";

        $result= $this -> mDb -> getOne($sql);

        return $result;

    }






    function  sendMail($id,$senderName){




        $mail = new PHPMailer\PHPMailer\PHPMailer();

        //     $mail = new PHPMailer();
        //    $mail->IsSMTP();  // telling the class to use SMTP
        //    $mail->SMTPDebug = 2;
        //    $mail->Mailer = "smtp";
        //    $mail->Host = "ssl://smtp.gmail.com";
        //    $mail->Port = 587;
        //    $mail->SMTPAuth = true; // turn on SMTP authentication
        //    $mail->Username = "myemail@example.com"; // SMTP username
        //    $mail->Password = "mypasswword"; // SMTP password
        //    $Mail->Priority = 1;




        $mail->IsSMTP();
        //  $mail->SMTPDebug = 2;
        $mail->Port = 465 ; //465 or 587

          $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->IsHTML(true);
        $mail->CharSet="UTF-8";
        $mail->Host = "tamiyoz.com";
      
      
        //Authentication
        $mail->Username = "norepley@tamiyoz.com";
        $mail->Password = "5=%E{(?9^hMK";
        // $mail->Password = "#t)7{bj.Yq(P";
            
        $mail->addAttachment("../uploads/RentesPDF/RentRequest_$id.pdf", "RentRequest_$id.pdf");
        //Set Params
        $mail->SetFrom("norepley@tamiyoz.com",'tamiyoz');

        $mail->Subject = "تم اضافة طلب حجز جديد ";
        $mail->Body= " قام $senderName  بإضافة طلب حجز جديد رقم $id 
ويمكنك الإطلاع على تفاصيل طلب الحجز من خلال الضغط على هذا الرابط         
        ";
        $mail->Body.="<br>";
        $mail->Body.="<a href='https://tamiyoz.com/admin-area/rentes/rente/$id'>عرض الطلب</a>";

        $sql = "SELECT `email` FROM `correspondence_mails` WHERE  `status`='1'";
        $res = $this->mDb->getAll($sql);
        for($i=0; $i<count($res);$i++){
            $mail->AddAddress($res[$i]['email']);
        }
//        $mail->Send();






        if(!$mail->Send()) {
            return "Message not sent please try again.....";
        } else {
            return "Message has been sent";
        }









//        $this->mail->sendMailWithAttachmentFiles('momenadel3030@gmail.com','');
    }






}?>