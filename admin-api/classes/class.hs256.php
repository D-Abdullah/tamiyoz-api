<?php

$hs256Obj = new hs256();

class hs256 {

    var $mDb; 
    var $mConfig; 

    function hs256() {
        global $Config; 
        $this -> mDb = new iplus(); 
        $this -> mConfig = $Config; 
    } 

    function base64UrlEncode($data)
    {
        $urlSafeData = strtr(base64_encode($data), '+/', '-_');

        return rtrim($urlSafeData, '='); 
    } 

    function base64UrlDecode($data)
    {
        $urlUnsafeData = strtr($data, '-_', '+/');

        $paddedData = str_pad($urlUnsafeData, strlen($data) % 4, '=', STR_PAD_RIGHT);

        return base64_decode($paddedData);
    }

    function generateJWT($algo, array $header, array $payload) {

        $secret = 'IT-PLUS.co@147';

        $headerEncoded = $this->base64UrlEncode(json_encode($header));

        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        // Delimit with period (.)
        $dataEncoded = "$headerEncoded.$payloadEncoded";

        $rawSignature = hash_hmac($algo, $dataEncoded, $secret, true);

        $signatureEncoded = $this->base64UrlEncode($rawSignature);

        // Delimit with second period (.)
        $jwt = "$dataEncoded.$signatureEncoded";

        return $jwt;
    }

    function verifyJWT($algo, $jwt)
    {
        $secret = 'IT-PLUS.co@147';
        $newjwt = substr($jwt,7);

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $newjwt);

        $dataEncoded = "$headerEncoded.$payloadEncoded";

        $signature = $this->base64UrlDecode($signatureEncoded);

        $rawSignature = hash_hmac($algo, $dataEncoded, $secret, true);

        return hash_equals($rawSignature, $signature);
    }

} ?>