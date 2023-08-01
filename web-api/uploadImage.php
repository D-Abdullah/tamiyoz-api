<?php

require_once('init.php');

 
//uploads\/users
$uploadFolder =  'api/uploads/'. $_GET['uploadFolderName'] ; //localhost && liveServer
//$uploadFolder = 'uploads/users';
$lastImage = $_GET['lastImage'];

   // echo json_encode('sssss'.$uploadFolders);die();

$allow_ext = array("jpg", "gif", "png", "jpeg", "bmp","txt","pdf",'rtf', "docx", "doc", "accdb", "xls","xlsx", "pptx","mp3", "mp3");
 
$th_width = 250;
$th_height = 250;
 

$maxsize = 1024 * 25; //kb
$upload = new Upload($allow_ext, $th_width, $th_height, $maxsize, $uploadFolder, true);
 
if (isset($_FILES['file']['name']) AND $_FILES['file']['name'] != '') {
    // echo json_encode('fff'.$_FILES['file']);die();
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
    //echo json_encode('sssss'.$name);die();

    //check if guest have selected file or not
    if ($file['name'] != '') {
        // echo json_encode('sssss wwwww '.$file['name']);die();    
        // Start Uploading File
        $upfile = $upload->Upload_File($file, $maxsize);
        // echo json_encode($upfile);die();
        //if uploading successfully
        if ($upfile) {
            $old_img = $_SERVER['DOCUMENT_ROOT'] . "/" .$uploadFolder . "/" . $lastImage;
             if (file_exists($old_img)) {
                    @unlink($old_img);
              }
              $old_thu = $_SERVER['DOCUMENT_ROOT'] . "/" .$uploadFolder . "/thumbs"."/" . $lastImage;
              if (file_exists($old_thu)) {
                     @unlink($old_thu);
               }
 
               $info['newname'] = $upfile['newname'];
            // $_SERVER['DOCUMENT_ROOT']
            //   $error = true;
            // $info['error'] = $_SERVER['DOCUMENT_ROOT'] ;   
        }
        else {
            $error = true;
            $info['error'] = 'حدثت مشكلة أثناء العملية يرجى المحاولة لاحقا';
        }
    }
    else{
        $error = true;
        $info['error'] = 'صيغة الملف المرفوع غير مدعومة ';
    }
}


 
echo json_encode($info);
die();
header("Content-Type: application/json;charset=utf-8");

