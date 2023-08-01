<?php
/* * *************************************************************************
 *
 *   PROJECT: BigWish App
 *   powerd by IT PLUS Team
 *   Copyright 2020 IT Plus Inc
 *   http://it-plus.co/ *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once 'init.php';

// Start Functionality
$postdata = file_get_contents("php://input");

$token = apache_request_headers()["Authorization"];
$verify = $hs256Obj->verifyJWT('sha256', $token);

if (isset($token) && isset($verify) && $verify) {


    if ($_GET['action'] == 'uploadFile') {
        // print_r($_FILES);
        //             die();
        $operation = $_REQUEST['operation'];
        $category_id = '';
        $folderName=$_GET['folderName'];
        $temp = array();

        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $allow_extFile = array("pdf", "txt", "doc");
        $maxsizeFile = 1024 * 20;
        $maxsize = 1024 * 2; //kb
        $uploadFolder = $Config['uploads_path'] . $folderName;
//        $uploadFolderFiles = $Config['uploads_path'] . 'projects/files';

        $thumbsConfig = array(
            array(
                'name' => 'medium',
                'width' => '240',
                'hight' => '250'
            ),
            array(
                'name' => 'small',
                'width' => '80',
                'hight' => '80'
            ),
        );

        $upload = new Upload($allow_ext, $thumbsConfig, $maxsize, $uploadFolder, false);

        if (isset($_FILES['file']['name']) and $_FILES['file']['name'] != '') {

            //get file name
            $file['name'] = addslashes($_FILES["file"]["name"]);
            // get file type
            $file['type'] = $_FILES["file"]['type'];
            // get filesize in KB
            $file['size'] = $_FILES["file"]['size'] / 1024;
            // get file tmp path
            $file['tmp'] = $_FILES["file"]['tmp_name'];
            //get file ext [to get max uploades size]
            $file['ext'] = $upload->GetExt($_FILES["file"]["name"]);
            //check if guest have selected file or not
            if ($file['name'] != '') {
                // Start Uploading File
                $upfile = $upload->Upload_File($file, $maxsize);

                //if uploagetCategoryByParentIDding successfully
                if ($upfile) {
                    $temp['file'] = $upfile['newname'];
                } else {
                    $error = true;
                    $showError[] = $upload->showErrors();
                    $top_msg['error'][] = $showError[0][0];
                }
            }
        } else {
            $temp['file'] = null;
        }

        $categoryData = $temp['file'];
        $Data = $categoryData ? $categoryData : '';
    }


} else {
    echo json_encode(["status" => 401, "message" => "Unauthorized Request"]);
}




echo json_encode( ["status" =>200, "ImageName" => $Data] );
