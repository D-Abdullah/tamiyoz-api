<?php

$slidersObj2 = new Sliders();

class Sliders {

    var $mDb;
    var $mConfig;

    function __construct() {
        global $Config;
        $this -> mDb = new iplus();
        $this -> mConfig = $Config;
    }

    function convert_object_to_array($data) {

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map(__METHOD__, $data);
        }
        else {
            return $data;
        }
    }

    function getSomeSliders($aStart, $aLimit, $sort, $type, $searchName,$code) {

        $sql = "SELECT c.`id`,";
//        $sql .= " cl.`name`,";
        $sql .= " cl.`description`,";
        $sql .= " cl.`description_2`,";
        $sql .= " c.`status`,";
        $sql .= " c.`img`,";
        $sql .= " c.`url`,";
        $sql .= " c.`date_added`,";
        $sql .= " u.`full_name` AS 'user_full_name'";
        $sql .= " FROM `sliders` c ";
        $sql .= " LEFT JOIN `sliders_langs` cl ON c.`id` = cl.`slider_id` ";
        $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
        $sql .= " WHERE c.`id` > 0  AND  c.`status`= 1  AND cl.`lang_code` = '{$code}' ";
        $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
        $sql .= "ORDER BY c.`sort`";
        $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

        return $this -> mDb -> getAll($sql);
    }

    function getSearchSlidersCount($sort, $type, $searchName) {
        $sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `sliders` c";
        $sql .= " LEFT JOIN `sliders_langs` cl ON c.`id` = cl.`slider_id` ";
        $sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' ";
        $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
        $sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

        //echo $sql; die();

        return $this -> mDb -> getOne($sql);
    }

    function getSlidersCount() {
        $sql = "SELECT COUNT(`id`) as 'count' FROM `sliders` ";
        return $this -> mDb -> getOne($sql);
    }


    function getOneSlider($id,$code) {

        $sql = "SELECT c.`id`, c.`added_by`, c.`status`, c.`img` ,cl.`name` FROM `sliders` c";
        $sql .= " LEFT JOIN `sliders_langs` cl ON  cl.`slider_id`=c.`id`";
        $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
        $sql .= " WHERE c.`id`  = '{$id}'  AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";
//    echo $sql;
//    die();
        $result= $this -> mDb->getRow($sql);
        return $result;

    }








}?>