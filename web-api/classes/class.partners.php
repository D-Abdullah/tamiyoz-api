<?php

$partnersObj1 = new Partners();

class Partners {

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

function getSomePartners($aStart, $aLimit, $sort, $type, $searchName,$code) {

	$sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,";
    $sql .= " c.`date_added`,";
    $sql .= " c.`status`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `partners` c ";
    $sql .= " LEFT JOIN `partners_langs` cl ON c.`id` = cl.`partner_id` ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0  AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchPartnersCount($sort, $type, $searchName,$code) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `partners` c";
    $sql .= " LEFT JOIN `partners_langs` cl ON c.`id` = cl.`partner_id` ";
	$sql .= " WHERE c.`id` > 0 AND c.`status`= '1' AND cl.`lang_code` = '{$code}' ";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	//echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getPartnersCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `partners` where `status`= '1' ";
	return $this -> mDb -> getOne($sql); 
}

function getOnePartner($id,$code) {

    $sql = "SELECT c.`id`, c.`added_by`, c.`status`, c.`img` ,cl.`name` FROM `partners` c";
    $sql .= " LEFT JOIN `partners_langs` cl ON  cl.`partner_id`=c.`id`";
    $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
    $sql .= " WHERE c.`id`  = '{$id}'  AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";
//    echo $sql;
//    die();
    $result= $this -> mDb->getRow($sql);
    return $result;

}






function getPartnerByParentID($id) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `partners` c";
    $sql .= " LEFT JOIN `partners_langs` cl ON c.`id` = cl.`partner_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' ";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}



}?>