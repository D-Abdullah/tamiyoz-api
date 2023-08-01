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
    function sendEmail($aEmail, $aSubject, $aBody, $aFrom, $aReplyto) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .="From: {$aFrom}\r\n" . "Reply-To: {$aReplyto}\r\n";
        //echo "$headers";die();
        return mail($aEmail, $aSubject, $aBody, $headers);
    }

    /**
     * Sends email by the given action
     *
     * @param str $aAction action that happened
     * @param arr $aLink link info array
     * @param arr $aCategory category info
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

   
}

?>
