<?php

//require_once('../classes/iplus.php');
$devicesApi = new devices();

class devices {

    var $mDb;
    var $mMailer;
    var $prefix;
    var $mConfig;

    function devices() {
        $this->mDb = new iplus();
        $this->mMailer = new Mailer();
        $this->mMailer->mConfig = $Config;
        $this->prefix = $Config['prefix'];
        $this->mConfig = $Config;
    }

    function checkDevicesToken($data) {
        $sql = "SELECT `id` FROM `device_token` WHERE `device_token_id`='{$data['device_token_id']}' ";
        return $this->mDb->getOne($sql);
    }

    function checkDevicesUser($data, $login = '') {
        $sql = "SELECT `id` FROM `devices` WHERE `device_token_id`='{$data['token_id']}' ";
        $sql .= " AND `user_id` ='{$data['user_id']}' ";
        $sql .= $login ? " AND `login`='{$login}'" : '';
        return $this->mDb->getOne($sql);
    }

    function updateDevices($device_token_id = 0, $userId = 0, $login = 0) {

        $sql = "UPDATE  `devices` SET  `login`='{$login}'";
        $sql .= " where `device_token_id`='{$device_token_id}' and `user_id`='{$userId}'";

        $res = $this->mDb->query($sql);
        return $device_token_id;
    }

    function updateDevicesLogOut($temp) {
        $sql = "UPDATE  `devices` SET  `login`='0'";
        $sql .= " where `device_token_id`='{$temp['token_id']}' and `user_id`='{$temp['user_id']}'";
//       echo $sql;
        return $this->mDb->query($sql);
    }

    function checkDevices($data) {
        $sql = "SELECT `id` FROM `devices` where `device_token_id`='{$data['token_id']}' ";
        $sql .= " and `user_id` ='{$data['user_id']}' ";
        return $this->mDb->getOne($sql);
    }

    function addDevicesToken($data) {
        $sql = "INSERT INTO `device_token` SET ";
        $sql .= "`device_token_id`='{$data['device_token_id']}' ,`type`='{$data['type']}',";
        $sql .= "`date_added`='" . date('Y-m-d H:i:s') . "'";
        $this->mDb->query($sql);
        return $this->mDb->getLastInsertId();
    }

    function addDevices($data) {
        $sql = "INSERT INTO `devices` SET ";
        $sql .= "`device_token_id`='{$data['token_id']}' ,`user_id`='{$data['user_id']}',`lang_code`='{$data['lang_code']}',";
        $sql .= "`login`='1' ,";
        $sql .= "`date_added`='" . date('Y-m-d H:i:s') . "'";
        $this->mDb->query($sql);
//        return $this->mDb->getLastInsertId();
        return $data['token_id'];
    }

    function removeAddDevices($data, $id) {
        $sql = "DELETE FROM `devices`  WHERE `id`='{$id}';";
        $sql .= "INSERT INTO `devices` SET ";
        $sql .= "`device_token_id`='{$data['token_id']}' ,`user_id`='{$data['user_id']}',";
        $sql .= "`login`='1' ,";
        $sql .= "`date_added`='" . date('Y-m-d H:i:s') . "'";
        $this->mDb->query($sql);
//        return $this->mDb->getLastInsertId();
        return $data['token_id'];
    }

}
