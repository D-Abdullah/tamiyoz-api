<?php

$ShopsObj1 = new Shops();

class Shops
{

    var $mDb;
    var $mConfig;
    var $mMailer;

    function __construct()
    {
        global $Config;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
        $this->mMailer = new Mailer();
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





    function getSomeShops($aStart, $aLimit, $sort, $type, $searchName, $code, $searchStation, $stationID)
    {
        $sql = "SELECT b.`id`,";
        $sql .= " bl.`name`,";
        $sql .= " bl.`description`,";
        $sql .= " b.`date_added`,";
        $sql .= " b.`status`,";
        $sql .= " b.`rented`,";
        $sql .= " b.`url`,";
        $sql .= " b.`img`,";
        $sql .= " ccl.`name` as 'station_name',";
        $sql .= " u.`full_name` AS 'user_full_name'";
        $sql .= " FROM `shops` b ";
        $sql .= " LEFT JOIN `shops_langs` bl ON b.`id` = bl.`shop_id` ";
        $sql .= " LEFT JOIN `projects_langs` ccl ON b.`station_id` = ccl.`project_id` AND ccl.`lang_code` = '{$code}'   ";
        $sql .= " LEFT JOIN `users` u ON u.`id` = b.`added_by` ";
        $sql .= " WHERE b.`station_id` ='{$stationID}' AND b.`status`='1' AND bl.`lang_code` = '{$code}'";
        $sql .= $searchName ? ' AND bl.name like "%' . $searchName . '%"' : '';
        $sql .= $searchStation ? " AND b.`station_id` = '{$searchStation}'" : "";
        $sql .= " ORDER BY b.{$sort} {$type}";
        $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

        $res = $this->mDb->getAll($sql);
        for ($i = 0; $i < count($res); $i++) {
            $res[$i]['description'] = strip_tags($res[$i]['description']);
            $res[$i]['description'] = html_entity_decode($res[$i]['description']);
            $stmt = "SELECT  bl.`img` FROM `shops_images` bl";
            $stmt .= " WHERE bl.`shop_id` = '{$res[$i]['id']}'";
            $res[$i]['img'] = empty($this->mDb->getOne($stmt)) ? $res[$i]['img'] : $this->mDb->getOne($stmt);
        }


        //  print_r($res);
        //  die();
        return  $res;
    }

    function getSearchShopsCount($sort, $type, $searchName, $searchStation, $code)
    {
        $sql = "SELECT COUNT(bl.`id`) as 'result_count' FROM `shops_langs` bl ";
        $sql .= "left join shops b on bl.shop_id = b.id  WHERE bl.`id` >
        0 AND b.`status`='1'  AND bl.`lang_code` = '{$code}' ";
        $sql .= $searchName ? ' AND bl.`name` like "%' . $searchName . '%"' : '';
        $sql .= $searchStation ? " AND b.`station_id` = '{$searchStation}'" : "";

        $sql .= " ORDER BY bl.{$sort} {$type}";


        //        SELECT COUNT(bl.`id`) as 'result_count' FROM
        //        `shops_langs` bl left join shops b on bl.shop_id = b.id  WHERE bl.`id` >
        //        0 AND bl.lang_code='ar' AND  b.`station_id` = '5' ORDER BY bl.id DESC
        //	echo $sql; die();

        return $this->mDb->getOne($sql);
    }


    //    function getSearchshopsCount($sort, $type, $searchName,$searchCatagory) {
    //        $sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `shops` c";
    ////        $sql .= " LEFT JOIN `shops_langs` cl ON c.`id` = cl.`shop_id` ";
    ////        $sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar'";
    //        $sql.= $searchCatagory ? " AND c.`station_id` = '{$searchCatagory}'" : "";
    //        $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
    //
    //        $sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY {$sort} {$type}";
    //
    ////        echo $sql; die();
    //
    //        return $this -> mDb -> getOne($sql);
    //    }

    function getShopsCount()
    {
        $sql = "SELECT COUNT(`id`) as 'count' FROM `shops` WHERE `status`='1'";
        return $this->mDb->getOne($sql);
    }
    function getTrainingstations()
    {
        $result = array();
        $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `stations` c";
        $sql .= " LEFT JOIN `stations_langs` cl ON c.`id` = cl.`station_id` ";
        $sql .= " WHERE  cl.`lang_code`='ar'";
        //    echo $sql;
        $result = $this->mDb->getAll($sql);
        return $result;
    }

    function getAllStations()
    {
        $sql = "SELECT cu.* ,";
        $sql .= " cul.`name`";
        $sql .= " FROM `stations` cu ";
        $sql .= " LEFT JOIN `stations_langs` cul ON cu.`id` = cul.`station_id` ";
        $sql .= " WHERE cu.`id` > 0  AND cul.`lang_code` = 'ar' ";
        //	 echo $sql; die();
        return $this->mDb->getAll($sql);
    }

    function getbooksWithCatId($cat_id)
    {
        $sql = "SELECT bs.* ,";
        $sql .= " bkl.`name`,";
        $sql .= " ccl.`name` as 'category_name',";
        $sql .= " u.`full_name` AS 'user_full_name'";
        $sql .= " FROM `shops` bs ";
        $sql .= " LEFT JOIN `shops_langs` bkl ON bs.`id` = bkl.`shop_id` ";
        $sql .= " LEFT JOIN `stations_langs` ccl ON bs.`station_id` = ccl.`station_id`
        AND ccl.`lang_code` = 'ar'";
        $sql .= " LEFT JOIN `users` u ON u.`id` = bs.`added_by` ";
        $sql .= " WHERE bs.`id` > 0  AND bkl.`lang_code` = 'ar' AND bs.`station_id` = '{$cat_id}' ";
        //         echo $sql; die();
        return $this->mDb->getAll($sql);
    }



    function getOneShop($id, $code)
    {
        $sql = "SELECT c.`id`, c.`shop_file`, c.`status`,c.`url`,`rented`, c.`station_id`, c.`sort`, c.`img`, cl.`name`, ccl.`name` as 'station_name' ,cl.`description` FROM `shops` c";
        $sql .= " LEFT JOIN `shops_langs` cl ON  cl.`shop_id`=c.`id`";
        $sql .= " LEFT JOIN `projects_langs` ccl ON c.`station_id` = ccl.`project_id`";
        $sql .= " WHERE c.`id` = '{$id}' AND c.`status`= '1'   AND ccl.`lang_code` = '{$code}'  AND cl.`lang_code` = '{$code}' ";
        // echo $sql;
        // die();

        $result = $this->mDb->getRow($sql);
        $stmt = "SELECT  cl.`img`  FROM `shops_images` cl";
        $stmt .= " WHERE cl.`shop_id` = '{$id}'";
        $result['img'] = empty($this->mDb->getAll($stmt)) ? $result['img'] : $this->mDb->getAll($stmt);

        return $result;
    }



    function getBookByParentID($id, $type)
    {
        $result = array();
        $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `stations` c";
        $sql .= " LEFT JOIN `stations_langs` cl ON c.`id` = cl.`station_id` ";
        $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' and c.`category_type`='{$type}'";
        //    echo $sql;
        $result = $this->mDb->getAll($sql);
        return $result;
    }
}
