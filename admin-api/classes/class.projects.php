<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$projectObj = new Project();

class Project
{

    var $mDb;
    var $mConfig;

    function Project()
    {
        global $Config;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
    }

    function convert_object_to_array($data)
    {

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map(__METHOD__, $data);
        } else {
            return $data;
        }
    }

    function getSomeProjects($aStart, $aLimit, $sort, $type, $searchName, $pagename, $shopStatus)
    {


        if ($pagename == 'stations') {

            $sql = "SELECT  c.`id`,";
            $sql .= " cl.`name`,";
            $sql .= " c.`date_added`,";
            $sql .= " c.`sort`,";
            $sql .= " c.`status`,";
            $sql .= " c.`img`,";
            $sql .= " u.`full_name` AS 'user_full_name'";
            $sql .= " FROM `projects` c ";
            $sql .= " LEFT JOIN `projects_langs` cl ON (c.`id` = cl.`project_id` AND cl.`lang_code` = 'ar') ";
            $sql .= $shopStatus >= 0 && $shopStatus != null && $shopStatus != '' ? "INNER JOIN `shops` sh ON (sh.`station_id` = c.`id`  AND  sh.`rented`='{$shopStatus}')" :
                "";
            $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
            $sql .= " WHERE  c.`project_type`='stations' ";
            $sql .= $searchName ? ' AND cl.`name` like "%' . $searchName . '%"' : '';
            $sql .= $shopStatus >= 0 && $shopStatus != null && $shopStatus != '' ? "GROUP BY c.`id`":"";

            $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit} " : '';
            // echo $sql;
            // die();
            $res = $this->mDb->getAll($sql);
        } else {


            $sql = "SELECT  c.`id`,";
            $sql .= " cl.`name`,";
            // $sql .= " clp.`name` as 'parent_name',";
            $sql .= " c.`date_added`,";
            $sql .= " c.`sort`,";
            $sql .= " c.`parent_id`,";
            $sql .= " c.`status`,";
            $sql .= " c.`img`,";
            $sql .= " u.`full_name` AS 'user_full_name'";
            $sql .= " FROM `projects` c ";
            $sql .= " LEFT JOIN `projects_langs` cl ON c.`id` = cl.`project_id` ";
            $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
            $sql .= " WHERE c.`id` > 0   AND cl.`lang_code` = 'ar'";

            $sql .= $pagename == 'projects' ? "AND  c.`project_type`='projects'" : '';
            $sql .= $pagename == 'companies' ? "AND  c.`project_type`='companies'" : '';
            $sql .= $searchName ? ' AND cl.`name` like "%' . $searchName . '%"' : '';
            $sql .= "ORDER BY c.`sort`";

            $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
            $res = $this->mDb->getAll($sql);
        }



        for ($i = 0; $i < count($res); $i++) {
            if ($pagename == 'stations' || $pagename == 'companies') {
                $stmt = "SELECT  cl.`img` FROM `projects_images` cl";
                $stmt .= " WHERE cl.`project_id` = '{$res[$i]['id']}'";
                $res[$i]['img'] = $this->mDb->getOne($stmt);
            }
        }









        return $res;
    }




    function getStationByIDSAndStatus($ids, $shopStatus)
    {

        $tempids = '(' . implode(',', $ids) . ')';
        $sql = "SELECT c.`id`,";
        $sql .= " cl.`name`";
        $sql .= " FROM `projects` c ";
        $sql .= " LEFT JOIN `projects_langs` cl ON (c.`id` = cl.`project_id` AND cl.`lang_code` = 'ar') ";
        $sql .= " WHERE  c.`id` IN " . $tempids;
        $projects = $this->mDb->getAll($sql);
        // print_r($projects);
        // die();
        if (count($projects) > 0) {
            for ($i = 0; $i < count($projects); $i++) {
                $sql = "SELECT ";
                $sql .= " shL.`name` as shopName,";
                $sql .= " sh.`rented` as 'shopStutas'";
                 $sql .= " FROM `shops` sh";

                $sql .= " LEFT JOIN `shops_langs` shL ON (shL.`shop_id` = sh.`id` AND shL.`lang_code` = 'ar' ) ";
                $sql .= "WHERE";
                 $sql .= $shopStatus >= 0 && $shopStatus != null && $shopStatus != '' ? "(sh.`station_id` = {$projects[$i]['id']}  AND  sh.`rented`='{$shopStatus}')" :
                    "(sh.`station_id` = {$projects[$i]['id']}) ";
                // echo $sql;
                // die();
                $projects[$i]['shops'] = $this->mDb->getAll($sql);

             $qrcode = (new QRCode(new QROptions(
                    [
                        'eccLevel' => QRCode::ECC_L,
                        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                        'version' => 5,
                    ]
                )))->render("https://tamiyoz.com/project-page/" . $projects[$i]['id']);
                //       $qrcode = (new QRCode)->render("station/".$res[$i]['id']);
                $projects[$i]['qrcode'] = $qrcode;









            }

        }
        return $projects;
    }





    function getSearchProjectsCount($sort, $type, $searchName, $pagename, $shopStatus)
    {
        if ($pagename == 'stations') {
            $sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `projects` c";
            $sql .= " LEFT JOIN `projects_langs` cl ON c.`id` = cl.`project_id` ";
            $sql .= $shopStatus >= 0 && $shopStatus != null && $shopStatus != '' ? "INNER JOIN `shops` sh ON (sh.`station_id` = c.`id`  AND  sh.`rented`='{$shopStatus}')" :
                "";

            $sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar'   ";
            $sql .= $pagename == 'stations' ? "AND  c.`project_type`='stations'" : '';
            $sql .= $searchName ? ' AND cl.`name` like "%' . $searchName . '%"' : '';
            $sql .= $shopStatus >= 0 && $shopStatus != null && $shopStatus != '' ? "GROUP BY c.`id`":"";
        } else {
            $sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `projects` c";
            $sql .= " LEFT JOIN `projects_langs` cl ON c.`id` = cl.`project_id` ";
            $sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' ";
            $sql .= $pagename == 'projects' ? "AND  c.`project_type`='projects'" : '';
            $sql .= $pagename == 'companies' ? "AND  c.`project_type`='companies'" : '';
            $sql .= $searchName ? ' AND cl.`name` like "%' . $searchName . '%"' : '';

            $sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
        }


        // echo $sql; die();

        return $this->mDb->getOne($sql);
    }

    function getProjectsCount($pagename, $shopStatus)
    {
        if ($pagename == 'stations') {

            $sql = "SELECT COUNT(c.`id`) as 'count' FROM `projects` c ";
            $sql .= $shopStatus >= 0 && $shopStatus != null && $shopStatus != '' ? " INNER JOIN `shops` sh ON (sh.`station_id` = c.`id`  AND  sh.`rented`='{$shopStatus}')" :
                "";
            $sql .= "where c.`project_type`='{$pagename}' ";
           $sql .= $shopStatus >= 0 && $shopStatus != null && $shopStatus != '' ? "GROUP BY c.`id`":"";
        } else {

            $sql = "SELECT COUNT(c.`id`) as 'count' FROM `projects` c ";
            $sql .= "where c.`project_type`='{$pagename}'";
        }


        // echo $sql; die();
        return $this->mDb->getOne($sql);
    }


    function addEditProject($temp, $img, $uploadedFile, $catId)
    {

        //    print_r($temp['filesImages']);
        $dataImagesObj = json_decode($temp['filesImages']);
        $newDataImagesObj = $this->convert_object_to_array($dataImagesObj);

        $dataLangObj = json_decode($temp['langs']);
        $newDataLangObj = $this->convert_object_to_array($dataLangObj);
        $lon = $temp['lon'];
        $lat = $temp['lat'];
        $websiteUrl = $temp['websiteUrl'];
        $pagename = $temp['pagename'];
        $user_id = $temp["user_id"];
        $email = $temp['email'];
        $status = $temp['status'] == 'true' ? "1" : "0";
        $sort = $temp['sort'];
        $url = $temp['url'];

        $id = $temp['id'];

        if ($id  < 1) {
            // add

            $dateTime = date('Y-m-d H:i:s');

            $sql = "INSERT INTO `projects` SET ";
            $sql .= " `added_by` = '{$user_id}', ";
            $sql .= " `status` = '{$status}', ";
            $sql .= " `sort` = '{$sort}', ";
            $sql .= " `lat` = '{$lat}', ";
            $sql .= " `websiteUrl` = '{$websiteUrl}', ";
            $sql .= " `lon` = '{$lon}', ";
            $sql .= " `email` = '{$email}', ";
            $sql .= " `project_type` = '{$pagename}', ";
            $sql .= " `date_added` = '{$dateTime}' ,";
            if(!empty($url)){
                $sql .= " `url` = '{$url}',";
            }else{
                $sql .= " `url` = '',";
            }
            $sql .= " `img` = '{$img}',";
            // die($img);
            $sql .= "`stationfile`= '{$uploadedFile}'";
            //         echo $sql;
            //         die();
            $this->mDb->query($sql);

            $last_project_id = $this->mDb->getLastInsertId();
            if ($pagename == 'stations' || $pagename == 'companies') {
                foreach ($newDataImagesObj as $key2 => $val2) {
                    if ($val2['img']) {
                        $stmt = "INSERT INTO `projects_images` SET ";
                        $stmt .= " `project_id` = '{$last_project_id}', ";
                        $stmt .= " `img` = '{$val2['img']}'";
                        $res = $this->mDb->query($stmt);
                    }
                }
            }

            foreach ($newDataLangObj as $key => $value) {
                $description = addslashes($value['description']);
                $title = addslashes($value['title']);
                $address = addslashes($value['address']);
                // $sub_title = addslashes($value['sub_title']);

                $stmt = "INSERT INTO `projects_langs` SET ";
                $stmt .= " `project_id` = '{$last_project_id}', ";
                $stmt .= " `lang_code` = '{$value['lang_code']}', ";
                $stmt .= " `name` = '{$title}', ";
                $stmt .= " `address` = '{$address}', ";
                $stmt .= " `description` = '{$description}'";

                $res = $this->mDb->query($stmt);
            }
            if ($pagename == 'companies') {
                $dataphonsObj = json_decode($temp['phones']);
                $newdataphonsObj = $this->convert_object_to_array($dataphonsObj);
                foreach ($newdataphonsObj as $key => $value) {

                    $phone = addslashes($value['phone']);
                    if ($phone != null) {
                        $stmt = "INSERT INTO `phones` SET ";
                        $stmt .= " `company_id` = '{$last_project_id}', ";
                        $stmt .= " `phone_number` = '{$phone}',";
                        $stmt .= " `date_added` = '{$dateTime}' ";
                        //                    echo $stmt;
                        //                    die();
                        $res = $this->mDb->query($stmt);
                    }
                }
            }




            return $last_project_id;
        } else {
            // edit
            $check_query = "SELECT `id` FROM `projects` WHERE `id` = '{$id}'  AND `project_type`='{$pagename}' ";
            $check_result = $this->mDb->getOne($check_query);
            if ($check_result === false) {
                return 403;
            } else {
                $sql = "UPDATE `projects` SET ";
                $sql .= " `added_by` = '{$user_id}', ";
                $sql .= " `lon` = '{$lon}', ";
                $sql .= " `lat` = '{$lat}', ";
                $sql .= " `websiteUrl` = '{$websiteUrl}', ";
                $sql .= " `email` = '{$email}', ";
                if(!empty($url)){
                    $sql .= " `url` = '{$url}',";
                }else{
                    $sql .= " `url` = '',";
                }
                $sql .= " `sort` = '{$sort}', ";
                $sql .= " `status` = '{$status}' ";

                if ($img) {

                    // Delete old image from the server
                    $img_query = "SELECT `img` FROM `projects` WHERE `id` = '{$id}'";

                    //                echo $img_query ;
                    //                die();

                    $img_result = $this->mDb->getOne($img_query);

                    $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'projects/' . $img_result;

                    if (file_exists($old_img)) {
                        @unlink($old_img);
                    }

                    $sql .= ",`img`='{$img}' ";
                }
                if ($uploadedFile != '' || $uploadedFile != null) {

                    // Delete old image from the server
                    $img_query = "SELECT `stationfile` FROM `projects` WHERE `id` = '{$id}'";
                    $img_result = $this->mDb->getOne($img_query);

                    $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'projects/files' . $img_result;

                    if (file_exists($old_img)) {
                        @unlink($old_img);
                    }

                    $sql .= ",`stationfile`='{$uploadedFile}' ";
                }
                $sql .= " WHERE `id` = '{$id}' AND  `project_type`='{$pagename}'";


                $this->mDb->query($sql);

                foreach ($newDataLangObj as $key => $value) {
                    $description = addslashes($value['description']);
                    $title = addslashes($value['title']);
                    $address = addslashes($value['address']);
                    // $sub_title = addslashes($value['sub_title']);
                    $stmt = "UPDATE `projects_langs` SET ";
                    $stmt .= " `name` = '{$title}', ";
                    $stmt .= " `address` = '{$address}', ";
                    $stmt .= " `description` = '{$description}'";
                    $stmt .= " WHERE `lang_code`='{$value['lang_code']}' AND `project_id` = '{$id}' ";
                    $res = $this->mDb->query($stmt);
                }









                if ($pagename == 'companies') {
                    $sqll = "DELETE FROM `phones` ";
                    $sqll .= " WHERE `company_id` ='{$id}' ";
                    $this->mDb->query($sqll);
                    //                echo "ffdd";
                    //                die();
                    $dateTime = date('Y-m-d H:i:s');
                    $dataphonsObj = json_decode($temp['phones']);
                    $newdataphonsObj = $this->convert_object_to_array($dataphonsObj);
                    foreach ($newdataphonsObj as $key => $value) {
                        $phone = addslashes($value['phone']);
                        if ($phone != null) {
                            $stmt = "INSERT INTO `phones` SET ";
                            $stmt .= " `company_id` = '{$id}', ";
                            $stmt .= " `phone_number` = '{$phone}',";
                            $stmt .= " `date_added` = '{$dateTime}' ";
                            //                    echo $stmt;
                            //                    die();
                            $res = $this->mDb->query($stmt);
                        }
                    }
                }




                if ($pagename == 'stations' || $pagename == 'companies') {
                    $sqll = "DELETE FROM `projects_images` ";
                    $sqll .= " WHERE `project_id` ='{$id}' ";
                    $this->mDb->query($sqll);

                    foreach ($newDataImagesObj as $key2 => $val2) {
                        if ($val2['img']) {
                            $stmt = "INSERT INTO `projects_images` SET ";
                            $stmt .= " `project_id` = '{$id}', ";
                            $stmt .= " `img` = '{$val2['img']}'";
                            $res = $this->mDb->query($stmt);
                        }
                    }
                }









                return $res;
            }
        }
    }







    function getOneProject($data)
    {
        //    var_dump($data);
        //    die();
        $id = $data['id'];
        $pagename = $data['pagename'];
        $result = array();

        $sql = "SELECT `id`, `added_by`, `status`, `sort`,`websiteUrl`,`stationfile`,`img`,`url`,`lon`,`lat`,`email` FROM `projects`";
        $sql .= " WHERE `id` = '{$id}' AND `project_type`='{$pagename}' ";
        $result = $this->mDb->getRow($sql);

        $stmt = "SELECT cl.`lang_code`, cl.`name`, cl.`description` ,cl.`address` FROM `projects_langs` cl";
        $stmt .= " WHERE cl.`project_id` = '{$id}'";

        $result['langs'] = $this->mDb->getAll($stmt);
        if ($pagename == 'companies') {
            $stmt = "SELECT  cl.`phone_number` as 'phone' FROM `phones` cl";
            $stmt .= " WHERE cl.`company_id` = '{$id}'";
            $result['phones'] = $this->mDb->getAll($stmt);
        }

        if ($pagename == 'stations' || $pagename == 'companies') {
            $stmt = "SELECT  cl.`img`  FROM `projects_images` cl";
            $stmt .= " WHERE cl.`project_id` = '{$id}'";

            $result['images'] = $this->mDb->getAll($stmt);
        }


        return $result;
    }
    function getCategoryByParentID($id, $type)
    {
        $result = array();
        $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `projects` c";
        $sql .= " LEFT JOIN `projects_langs` cl ON c.`id` = cl.`project_id` ";
        $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' and c.`service_type`='{$type}'";
        //    echo $sql;
        $result = $this->mDb->getAll($sql);
        return $result;
    }



    function deleteProject($ids)
    {

        $tempids = '(' . implode(',', $ids) . ')';

        $sql = "SELECT `img` FROM `projects`";
        $sql .= " WHERE `id` IN " . $tempids;
        $images = $this->mDb->getAll($sql);

        $sqll = "DELETE FROM `projects_langs` ";
        $sqll .= " WHERE `project_id` IN " . $tempids;

        $this->mDb->query($sqll);

        $catsql = "DELETE FROM `projects` ";
        $catsql .= " WHERE `id` IN " . $tempids;

        if ($this->mDb->query($catsql)) {

            if (count($images) > 0) {
                for ($i = 0; $i < count($images); $i++) {
                    $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'projects/' . $images[$i]['img'];
                    if (file_exists($old_img)) {
                        @unlink($old_img);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }







}
