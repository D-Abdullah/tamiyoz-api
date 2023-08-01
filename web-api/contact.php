<?php
/* * *************************************************************************
 *
 *   PROJECT: tamiyoz.com App
 *   powered by IT PLUS Team
 *   Copyright 2023 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */

require_once 'init.php';

$errors = [];
$Data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = file_get_contents("php://input");
    $request = json_decode($post, true);

    $name = trim($request['name'] ?? '');
    $email = trim($request['email'] ?? '');
    $phone = trim($request['phone'] ?? '');
    $message = trim($request['message'] ?? '');

    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (!preg_match('/^[\p{Arabic}\s\p{Latin}]+$/u', $name)) {
        $errors['name'] = 'Name should only contain letters';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    // if (empty($phone)) {
    //     $errors['phone'] = 'Phone is required';
    // } elseif (!preg_match('/^[0-9]{11}$/', $phone)) {
    //     $errors['phone'] = 'Invalid phone number format';
    // }

    if (empty($message)) {
        $errors['message'] = 'Message is required';
    } elseif (strlen($message) > 1000) {
        $errors['message'] = 'Message should be no more than 1000 characters';
    }

    if (empty($errors)) {
        $to = $Config['site_email'];
        $subject = "tamiyoz: Contact user email";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: tamiyoz.com <$to>\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        if (mail($to, $subject, $message, $headers)) {
            $Data = [
                "success" => true,
            ];
        } else {
            $Data = [
                "success" => false,
                "message" => "Email sending failed"
            ];
        }
    } else {
        http_response_code(422);
    }
} else {
    http_response_code(405);
}

header('Content-Type: application/json');
echo json_encode($Data ?: $errors);
