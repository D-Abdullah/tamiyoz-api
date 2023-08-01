<?php

$userObj = new users();

class users {

    public $mDb;
    public $mConfig;
    public $dateTime;

    public function users() {
        global $Config;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
        $this->dateTime = date('Y-m-d H:i:s');
    }

    public function cheackByParam($col = '', $value = '', $id = '', $col2 = '', $value2 = '') {
        $sql = "SELECT `{$col}` FROM `{$this->mPrefix}users` ";
        $sql .= "WHERE `{$col}`='{$value}' ";
        $sql .= $id ? " and  `id`!='{$id}' " : "";
        $res = $this->mDb->getOne($sql);

        return $res;
    }

    public function addEditUser($temp, $user_id = 0) {

        if (!$user_id) {
            $temp['status'] = '1';

            // Add
            if ($temp['user_type'] == 'provider') {
                $temp['level_type'] = 'provider';
                $temp['user_level'] = '8';
            }
            $temp['password'] = password_hash($temp['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO `users` SET ";
            foreach ($temp as $k => $v) {
                $sql .= ($k == 'activeKey' || $k == 'callKey' || $k == 'confirm_code' || $k == 'lang' || $k == 'mode') ? "" : "`{$k}`='{$v}',";
            }
            $sql .= " `date_added` = '{$this->dateTime}' ";
//            echo $sql . "<hr>";

            $this->mDb->query($sql);

            $id = $this->mDb->getLastInsertId();

            if ($id) {
                $this->insertAuthCode($id, password_hash($id . $this->mConfig['apihash'], PASSWORD_DEFAULT));
            }
            $temp['id'] = $id;
            return $this->getUserDetailsInfo($temp);
        } else {
            // false


            if ($temp['password']) {
                $temp['password'] = password_hash($temp['password'], PASSWORD_DEFAULT);
            } else {
                unset($temp['password']);
            }
            $sql = "UPDATE `users` SET ";
            foreach ($temp as $k => $v) {
                $sql .= ($k == 'confirmPassword' || $k == 'user_id' || $k == 'lang') ? "" : "`{$k}`='{$v}',";
            }
            $sql = substr($sql, 0, -1);

            $sql .= " WHERE `id` = '{$user_id}'";

            $res = $this->mDb->query($sql);
//              echo $sql. "<hr>";
            if ($res) {

                $temp['id'] = $user_id;
                return $this->getUserDetailsInfo($temp);
            } else {
                return false;
            }
        }
    }

    public function addRepresentative($temp) {
        $temp['status'] = '1';
        $temp['provider_id'] = $temp['user_id'];
        $temp['password'] = password_hash($temp['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO `users` SET ";
        foreach ($temp as $k => $v) {
            $sql .= ($k == 'activeKey' || $k == 'callKey' || $k == 'user_id' || $k == 'lang') ? "" : "`{$k}`='{$v}',";
        }
        $sql .= " `date_added` = '{$this->dateTime}' ";
//            echo $sql . "<hr>";
        $this->mDb->query($sql);
        $id = $this->mDb->getLastInsertId();
        if ($id) {
            $this->insertAuthCode($id, password_hash($id . $this->mConfig['apihash'], PASSWORD_DEFAULT));
        }
        $temp['id'] = $temp['user_id'];
        return $this->getUserDetailsInfo($temp);
    }

    public function addEditProviderInfo($temp) {
        if (!$temp['id']) {
            $temp['provider_id'] = $temp['user_id'];
            $sql = "INSERT INTO `providers_info` SET ";
            foreach ($temp as $k => $v) {
                $sql .= ($k == 'user_id' || $k == 'id' || $k == 'lang') ? "" : "`{$k}`='{$v}',";
            }
            $sql .= " `date_added` = '{$this->dateTime}' ";
//            echo $sql . "<hr>";
            $res = $this->mDb->query($sql);
            if ($res) {
                $temp['id'] = $temp['user_id'];
                return $this->getUserDetailsInfo($temp);
            } else {
                return false;
            }
        } else {
            $sql = "UPDATE `providers_info` SET ";
            foreach ($temp as $k => $v) {
                $sql .= ($k == 'user_id' || $k == 'id' || $k == 'lang') ? "" : "`{$k}`='{$v}',";
            }
            $sql = substr($sql, 0, -1);
            $sql .= " WHERE `id` = '{$temp['id']}'";
            $res = $this->mDb->query($sql);
//              echo $sql. "<hr>";
            if ($res) {
                $temp['id'] = $temp['user_id'];
                return $this->getUserDetailsInfo($temp);
            } else {
                return false;
            }
        }
    }

    public function insertAuthCode($aId, $authentication_code) { //health
        $sql = " UPDATE `users` SET `authentication_code` = '{$authentication_code}'";
        $sql .= " WHERE `id` = '{$aId}'";
        // echo $sql; die();
        return $this->mDb->query($sql);
    }

    public function getUserDetailsInfo($data) {
        $sql = "SELECT u.*,plc.pla_name city_name,pld.pla_name district_name FROM `users` u";
        $sql .= " LEFT JOIN `place_langs` plc ON (u.city_id = plc.place_id  AND plc.lang_code = '{$data['lang']}') ";
        $sql .= " LEFT JOIN `place_langs` pld ON (u.district_id = pld.place_id  AND pld.lang_code = '{$data['lang']}') ";
        $sql .= " WHERE u.`id` = '{$data['id']}' ";
        $result = $this->mDb->getRow($sql);
        if ($result['user_type'] == 'provider') {
            $result['provider_info'] = $this->getProvidersInfoById($result['id']);
            $temp = array();
            $temp['provider_id'] = $result['id'];
            $temp['user_type'] = 'representative';
            $temp['aStart'] = '0';
            $temp['aLimit'] = '3';
            $result['representative'] = $this->getMembersByTypeApi($temp);
            $result['representative_count'] = $this->getMembersByTypeApiCount($temp);
        }
        return $result;
    }

    public function checkUserLogin($mobile) {
        $sql = "SELECT `id`, `password` FROM `users` ";
        $sql .= " WHERE `mobile` = '{$mobile}'";
//         echo($sql)."<hr>";
        return $this->mDb->getRow($sql);
    }

    public function getForgateDateByParams($temp) {
        $sql = "SELECT `mobile`,`id`,email FROM `users` WHERE (mobile  = '{$temp['mobile']}') ";
        return $this->mDb->getRow($sql);
    }

    public function changePassword($id, $aPassword) { //health
        $sql = "UPDATE `users` SET `password` = '{$aPassword}' ";
        $sql .= "WHERE `id` = '{$id}' ";
        return $this->mDb->query($sql);
    }

    function getMembersByTypeApi($data) {
//        $maximum_debt = $this->mConfig['maximum_debt'];
        $sql = "SELECT u.id,u.img,u.full_name,u.mobile,u.lat,u.lon";
        $sql .=$data['user_type'] == 'provider' ? " ,pi.details,pi.price,pi.type,pi.number_of_days " : " ";
        $sql .= $data['lat'] && $data['lon'] ? " ,(3959 * acos(cos(radians({$data['lat']})) * cos(radians(u.lat)) * cos(radians(u.lon) - radians({$data['lon']})) + sin(radians({$data['lat']})) * sin(radians(u.lat)))) AS distance" : "";
        $sql .= " FROM `users` u ";
        $sql .= $data['user_type'] == 'provider' ? " INNER  JOIN `providers_info` pi ON (pi.provider_id = u.id and pi.price !='') " : " ";
        $sql .=" WHERE u.user_type = '{$data['user_type']}' AND u.status = '1' ";
        $sql .= $data['provider_id'] ? " and u.provider_id='{$data['provider_id']}' " : " ";
        $sql .= $data['lat'] && $data['lon'] ? " ORDER BY distance ASC " : " ORDER BY  u.id DESC ";
        $sql .= $data['aLimit'] ? " LIMIT {$data['aStart']}, {$data['aLimit']}" : '';
//        echo $sql . "<hr>";
        $res = $this->mDb->getAll($sql);
        if ($res) {
            for ($i = 0; $i < count($res); $i++) {
                $rate = $this->getRatingByUserId($res[$i]['id']);
                $res[$i]['rating'] = $rate['rating'];
                $res[$i]['width'] = $rate['rating'] * 20;
                $res[$i]['countRating'] = $rate['countRating'];
            }
        }
        return $res;
    }

    function getMembersByTypeApiCount($data) {
        $sql = "SELECT count(id) ";
        $sql .= " FROM `users` WHERE user_type = '{$data['user_type']}' AND status = '1' ";
        $sql .= $data['provider_id'] ? " and provider_id='{$data['provider_id']}' " : " ";
//         echo $sql."<hr>";
        $res = $this->mDb->getOne($sql);
        return $res;
    }

    function getRatingByUserId($id) {
        $sql = " SELECT AVG(rating) as rating,count(id) as countRating FROM `{$this->mPrefix}rating`  ";
        $sql .= " WHERE rater_id ='{$id}' ";
        return $this->mDb->getRow($sql);
    }

    function updatePhotoInfo($temp) {
        $sql = "UPDATE `users` SET ";
        $sql .= " img = '{$temp['img']}'";
        $sql .= "WHERE id='{$temp['id']}'";
//        echo $sql."<hr>";
        return $this->mDb->query($sql);
    }

    function getAvatarById($id) {
        $sql = "SELECT img FROM `{$this->mPrefix}users` ";
        $sql .= "WHERE  `id`='{$id}'  ";
        // echo $sql."<hr>";
        return $this->mDb->getOne($sql);
    }

    public function getProvidersInfoById($provider_id) {
        $sql = " SELECT * FROM `{$this->mPrefix}providers_info`  ";
        $sql .= " WHERE provider_id ='{$provider_id}' ";
//         echo $sql."<hr>";
        return $this->mDb->getRow($sql);
    }

    public function removeFileFormServier($file, $folder) {
        $old_file = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . $folder . '/' . $file;
        $old_file_thumb = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . $folder . '/' . 'thumbs/' . $file;

        if (file_exists($old_file)) {
            @unlink($old_file);
        }
        if (file_exists($old_file_thumb)) {
            @unlink($old_file_thumb);
        }
    }

    function DeleteUser($data) {
        $sql = " DELETE  FROM `{$this->mPrefix}users`  ";
        $sql .= " WHERE id ='{$data['id']}' ";
//         echo $sql."<hr>";
        $del = $this->mDb->query($sql);
        if ($del) {
            $this->removeFileFormServier($data['img'], 'users');
            $res = $this->getUserDetailsInfo(array('id' => $data['user_id']));
        }
        return $res;
    }

    function updateMobileInUsers($temp) {
        $sql = "UPDATE `users` SET ";
        $sql .= " mobile = '{$temp['mobile']}'";
        $sql .= "WHERE id='{$temp['id']}'";
//        echo $sql."<hr>";
        return $this->mDb->query($sql);
    }

    public function getAuthorizationCodeByUserId($user_id) {
        $sql = "SELECT authentication_code FROM `{$this->mPrefix}users` ";
        $sql .= "WHERE `id`='{$user_id}' ";
//        echo $sql."<hr>";
        $res = $this->mDb->getOne($sql);
        return $res;
    }

//    -----------------------------




    public function getUserLevel($id) {
        $query = "SELECT u.`id` AS 'user_id_in_users', ul.* FROM `users` u";
        $query .= " LEFT JOIN `user_levels` ul ON ul.id = u.user_level ";
        $query .= " WHERE u.`id` = '{$id}' ";
        //echo $query; die();
        return $this->mDb->getRow($query);
    }

    public function getUserPasswordByIdApi($aId) {
        $sql = "SELECT `password` FROM `users` ";
        $sql .= " WHERE `id` = '{$aId}' ";
        return $this->mDb->getOne($sql);
    }

// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: End helthy  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::





  

    public function getUserInfoByEmailForMembers($email) {
        $query = "SELECT `id`, `email`, `full_name` FROM `users` ";
        $query .= " WHERE `email` = '{$email}' AND `user_level` is null ";
        return $this->mDb->getRow($query);
    }

    // new kslany

    public function selectImageByParams($id, $col_name, $table) {
        $query = "SELECT `{$col_name}` FROM `{$table}` WHERE `id` = '{$id}'";
        return $this->mDb->getOne($query);
    }

    public function getSomeSettingsInfo($someSettingsString) { //  string ***,***
        $arraySettingsName = explode(',', $someSettingsString);
        $sql = "SELECT  name , value  FROM `settings` ";
        if (count($arraySettingsName) > 0) {
            $sql .= " where  `name` = '{$arraySettingsName[0]}' ";
            if (count($arraySettingsName) > 1) {
                for ($i = 1; $i < count($arraySettingsName); $i++) {
                    $sql .= " || `name` = '{$arraySettingsName[$i]}' ";
                }
            }
        }

        // echo $sql;die();
        $res = $this->mDb->getAll($sql);
        return $res;
    }

}
