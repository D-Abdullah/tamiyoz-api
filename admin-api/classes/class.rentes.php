<?php

$rentObj = new Rente();
class Rente {

var $mDb; 
var $mConfig;
 var $options;
function Rente() {
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

function getSomeRentes($aStart, $aLimit, $sort, $type, $searchNumber,$searchStation,$searchShop) {
    $sql = "SELECT c.* ";
    $sql.=",sh1.`name` as 'shop_name'";
    $sql .= " FROM `rents_requests` c ";
    $sql .= " LEFT JOIN `shops` sh ON c.`shop_id` = sh.`id` ";
    $sql .= " LEFT JOIN `shops_langs` sh1 ON sh1.`shop_id` = sh.`id` ";
    $sql .= " LEFT JOIN `projects` stat ON sh.`station_id` = stat.`id` ";
	$sql .= " WHERE c.`id` > 0  AND sh1.lang_code='ar'";
    $sql .= $searchStation ? " AND sh.`station_id`='{$searchStation}'": '';
    $sql .= $searchNumber ? ' AND c.`phone` like "%'.$searchNumber.'%"' : '';
    $sql .= $searchShop ? " AND c.`shop_id`='{$searchShop}'": '';
    $sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY c.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
//    echo $sql;
//    die();
   $res=$this -> mDb -> getAll($sql);
    return $res;
}

function getSearchRentesCount($sort, $type,$searchNumber,$searchStation,$searchShop) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `rents_requests` c";
//	$sql .= " WHERE c.`id` > 0  ";
    $sql .= " LEFT JOIN `shops` sh ON c.`shop_id` = sh.`id` ";
    $sql .= " LEFT JOIN `shops_langs` sh1 ON sh1.`shop_id` = sh.`id` ";
    $sql .= " LEFT JOIN `projects` stat ON sh.`station_id` = stat.`id` ";
    $sql .= " WHERE c.`id` > 0  AND sh1.lang_code='ar'";
    $sql .= $searchStation ? " AND sh.`station_id`='{$searchStation}'": '';
    $sql .= $searchNumber ? ' AND c.`phone` like "%'.$searchNumber.'%"' : '';
    $sql .= $searchShop ? " AND c.`shop_id`='{$searchShop}'": '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY c.{$sort} {$type}";
//    echo $sql;
//    die();
	return $this -> mDb -> getOne($sql); 
}

function getRentesCount($type) {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `rents_requests`";
	return $this -> mDb -> getOne($sql); 
}

function getOneRente($id) {

    $result = array();
    $sql = "SELECT c.* ";
    $sql.=",sh1.`name` as 'shop_name' ,stat1.`name` as 'station_name' ";
    $sql .= " FROM `rents_requests` c ";
    $sql .= " LEFT JOIN `shops` sh ON c.`shop_id` = sh.`id` ";
    $sql .= " LEFT JOIN `shops_langs` sh1 ON sh1.`shop_id` = sh.`id` ";
    $sql.="LEFT JOIN `projects` stat ON stat.`id`= sh.`station_id`";
    $sql.="LEFT JOIN `projects_langs` stat1 ON stat1.`project_id` = stat.`id`";
    $sql .= " WHERE c.`id` = '{$id}' AND sh1.lang_code='ar' AND stat1.lang_code='ar' ";
//    echo $sql;
//    die();
    $result = $this -> mDb -> getRow($sql);
    return $result;

}

function deleteRente($ids) {
    $tempids='('.implode(',',$ids).')';
    $sqll = "DELETE FROM `rents_requests` ";
    $sqll.= " WHERE `id` IN ".$tempids;
    $this-> mDb-> query($sqll);
        return true;
}


    function getAllStations() {
        $sql = "SELECT cu.* ,";
        $sql .= " cul.`name`";
        $sql .= " FROM `projects` cu ";
        $sql .= " LEFT JOIN `projects_langs` cul ON cu.`id` = cul.`project_id` ";
        $sql .= " WHERE cu.`id` > 0 AND cu.`status`='1'  AND cu.`project_type`='stations'  AND cul.`lang_code` = 'ar' ";
//	 echo $sql; die();
        return $this -> mDb -> getAll($sql);
    }

    function getAllShops() {
        $sql = "SELECT cu.* ,";
        $sql .= " cul.`name`";
        $sql .= " FROM `shops` cu ";
        $sql .= " LEFT JOIN `shops_langs` cul ON cu.`id` = cul.`shop_id` ";
        $sql .= " WHERE cu.`id` > 0 AND cu.`status`='1'   AND cul.`lang_code` = 'ar' ";
//	 echo $sql; die();
        return $this -> mDb -> getAll($sql);
    }



}?>