<?php

/* * *************************************************************************
 *
 *   PROJECT: school managment system
 *   powerd by ashraf hamdy
 *   Copyright 2013 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */
$mailer = new Mailer();

class Mailer {

    var $mConfig;

    /**
     * Initialize class constructor
     */
    function Mailer() {
        
    }

    /**
     * Send email to recepient
     *
     * @param str $aEmail recepient email
     * @param str $aSubject email subject
     * @param str $aBody email body
     * @param str $aFrom email FROM
     * @param str $aReplyto email for REPLY
     */
    function sendEmail($aTo, $aSubject, $aBody, $aFrom, $aReplyto) {

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .="From: {$aFrom}\r\n" . "Reply-To: {$aReplyto}\r\n";
        //echo $aTo."<br>".$aSubject."<br>".$aBody."<br>".$aFrom."<br>".$aReplyto."<br>".$headers;die();
        return mail($aTo, $aSubject, $aBody, $headers);
    }

    /**
     * Sends email by the given action
     *
     * @param str $aAction action that happened
     * @param arr $aLink link info array
     * @param arr $aCategory class.category info
     *
     * @return bool
     */
    function sendMail($aAction, $aLink, $aCategory) {
        $subject = $this->mConfig["{$aAction}_subject"];
        $body = $this->mConfig["{$aAction}_body"];

        $subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

        $body = str_replace('{your_site_url}', $aLink['url'], $body);
        $body = str_replace('{your_site_title}', $aLink['title'], $body);
        $body = str_replace('{your_site_desc}', $aLink['description'], $body);
        $body = str_replace('{your_site_rank}', $aLink['rank'], $body);
        $body = str_replace('{your_site_status}', $aLink['status'], $body);
        $body = str_replace('{your_site_email}', $aLink['email'], $body);
        $body = str_replace('{own_site}', $this->mConfig['site'], $body);
        $body = str_replace('{own_url}', $this->mConfig['base'], $body);
        $body = str_replace('{own_email}', $this->mConfig['site_email'], $body);
        $body = str_replace('{own_dir_url}', $this->mConfig['base'] . $this->mConfig['dir'], $body);
        $body = str_replace('{dir_link}', "{$this->mConfig['base']}{$this->mConfig['dir']}{$aCategory['path']}/", $body);

        $body = stripslashes($body);

        return $this->sendEmail($aLink['email'], $subject, $body, $this->mConfig['site_email'], $this->mConfig['site_email']);
    }








    function sendMailWithAttachmentFiles($email,$filesInfo) {

        $body = 'محتوي الرساله';
        $subject="طلب حجز محل";


        //* Uniqid Session *//
        $strSid = md5(uniqid(time()));

        $strHeader = "";
        $strHeader .= "From: " . $this->mConfig['site_name'] . "<" . $this->mConfig['site_email'] . ">\nReply-To: " . $this->mConfig['site_email'] . "";

        $strHeader .= "MIME-Version: 1.0\n";
        $strHeader .= "Content-Type: multipart/mixed; boundary=\"" . $strSid . "\"\n\n";
        $strHeader .= "This is a multi-part message in MIME format.\n";

        $strHeader .= "--" . $strSid . "\n";
        $strHeader .= "Content-type: text/html; charset=utf-8\n";
        $strHeader .= "Content-Transfer-Encoding: 7bit\n\n";
        $strHeader .= $body . "\n\n";

//* Attachment *//
//        if ($filesInfo["file"]["name"] != "") {
//            $strFilesName = $filesInfo["file"]["name"];
//            $strContent = chunk_split(base64_encode(file_get_contents("../uploads/RentesPDF/")));
//            $strHeader .= "--" . $strSid . "\n";
//            $strHeader .= "Content-Type: application/octet-stream; name=\"" . $strFilesName . "\"\n";
//            $strHeader .= "Content-Transfer-Encoding: base64\n";
//            $strHeader .= "Content-Disposition: attachment; filename=\"" . $strFilesName . "\"\n\n";
//            $strHeader .= $strContent . "\n\n";
//        }


        return mail($email, $subject, null, $strHeader);
    }


function SendTestMail($email){
    $to = $email;
    $from = 'momenadel3030@gmail.com';
    $fromName = 'tamiyoz';

    $subject = "Send Text Email with PHP by CodexWorld";

    $message = "First line of text\nSecond line of text";

// Additional headers
    $headers = 'From: '.$fromName.'<'.$from.'>';

// Send email
    if(mail($to, $subject, $message, $headers)){
        echo 'Email has sent successfully.';
    }else{
        echo 'Email sending failed.';
    }
}















}

?>
