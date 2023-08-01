<?php

/* * *************************************************************************
 *
 *   PROJECT: homra App
 *   powerd by IT PLUS Team
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */


require_once('init.php');





if ($_GET['action'] == 'deleteImage') {
    if ($token[0] == $Config['token']) {
        $old_file = "../uploads/{$temp['uploadFolder']}/{$temp['name']}";
        if (file_exists($old_file)) {
            $aData = @unlink($old_file);
        }
        $old_file = "../uploads/{$temp['uploadFolder']}/thumbs/{$temp['name']}";
        if (file_exists($old_file)) {
            @unlink($old_file);
        }
    } else {
        $Data = ["status" => 401, "message" => "Unauthorized Request" . json_encode(apache_request_headers())];
    }
} else {
    if ($_GET['type'] == 'chatFile') {
        $allow_ext = array("pdf", "rtf", "xlsx", "docx");
        $maxsize = (100 * 1024 * 1024); //100 mb
        $uploadFolder = "../uploads/{$_GET['path']}";
        $upload = new Upload($allow_ext, false, false, $maxsize, $uploadFolder, false);
    } else {
        $allow_ext = array("jpg", "gif", "png", "jpeg", "bmp");
        $maxsize = (10 * 1024); //5 mb 
        $uploadFolder = "../uploads/{$_GET['path']}";
//        echo $uploadFolder."<hr>";
        $upload = new Upload($allow_ext, 250, 250, $maxsize, $uploadFolder, true);
    }

    /** check img * */
    if (isset($_FILES['file']['name']) AND $_FILES['file']['name'] != '') {
        //get file name
        $file[name] = addslashes($_FILES["file"]["name"]);
        //get file type
        $file[type] = $_FILES["file"]['type'];
        //get filesize in KB
        $file[size] = $_FILES["file"]['size'] / (1024); // 100 mb
        //get file tmp path
        $file[tmp] = $_FILES["file"]['tmp_name'];
        //get file ext [to get max uploades size]
        $file[ext] = $upload->GetExt($_FILES["file"]["name"]);
        //check if guest have selected file or not
        if ($file[name] != '') {
            //Start Uploading File
            $upfile = $upload->Upload_File($file, $maxsize);

            //if uploading successfully

            if ($upfile) {
                if ($_GET['type'] == 'avatar') {

                    $temp_['img'] = $upfile['newname'];
                    $temp_['id'] = $_GET['user_id'];
               
                    $img = $userObj->getAvatarById($temp_['id']);
                    $res = $userObj->updatePhotoInfo($temp_);
                    if ($res && $img) {
                        $old_file = "../uploads/users/" . $img;
                        if (file_exists($old_file)) {
                            @unlink($old_file);
                        }
                        $old_thumbs = "../uploads/users/thumbs/" . $img;
                        if (file_exists($old_thumbs)) {
                            @unlink($old_thumbs);
                        }
                    }
                } else if ($_GET['type'] == 'chatFile' || $_GET['type'] == 'chatImg') {
                    $message->updateMessagesId($_GET['imgId'], $upfile['newname']);
                }
                $aData['name'] = $upfile['newname'];
                $aData['error'] = '';
                $aData['success'] = 'Profile photo modified successfully';
                //echo json_encode($aData);
//            }
            } else {
                $error = true;
                $showError[] = $upload->showErrors();
                $temp['file'] = '0';
                $temp['error'] = $showError[0][0];
                $aData['name'] = '';
                $aData['success'] = '';
                $aData['error'] = $temp['error'];
            }
        }
    }
}

echo json_encode($aData);
?>