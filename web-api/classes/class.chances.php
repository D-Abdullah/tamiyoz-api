<?php

$ChanceObj1 = new Chance();

class Chance {

var $mDb; 
var $mConfig; 

function Chance() {
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

function getSomeChances($aStart, $aLimit, $sort, $type, $searchName,$code) {

	$sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,cl.`description`,";
    // $sql .= " clp.`name` as 'parent_name',";
    $sql .= " c.`date_added`,";
    $sql .= " c.`sort`,";
    $sql .= " c.`parent_id`,";
    $sql .= " c.`status`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `chances` c ";
    $sql .= " LEFT JOIN `chances_langs` cl ON  cl.`chance_id`= c.`id` ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0    AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

	//  echo $sql; die();
$res=$this -> mDb -> getAll($sql); 
for($i=0; $i<count($res);$i++){
$res[$i]['description']=strip_tags($res[$i]['description']);
$res[$i]['description']=html_entity_decode($res[$i]['description']);
}
	return $res; 
}

function getSearchChancesCount($sort, $type, $searchName,$code) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `chances` c";
    $sql .= " LEFT JOIN `chances_langs` cl ON c.`id` = cl.`chance_id` ";
	$sql .= " WHERE c.`id` > 0 AND c.`status`= '1' AND cl.`lang_code` = '{$code}' ";

    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';

	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	// echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getChancesCount($type) {
    // echo "dsddsdsddsadwerwer";
    //     die();
	$sql = "SELECT COUNT(`id`) as 'count' FROM `chances` where `status`= '1' ";
    // echo $sql;
    // die();
	return $this -> mDb -> getOne($sql); 
}



function getOneChance($id,$code) {
    $sql = "SELECT c.`id`, c.`added_by`, c.`status`, c.`sort`, c.`img`,cl.`description` ,cl.`name` FROM `chances` c";
    $sql .= " LEFT JOIN `chances_langs` cl ON  cl.`chance_id`=c.`id`";
    $sql .= " WHERE c.`id` = '{$id}'AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";

//    echo  $sql;
//    die();
    $result= $this -> mDb -> getAll($sql);

    return $result;

}
function getCategoryByParentID($id,$type) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `chances` c";
    $sql .= " LEFT JOIN `chances_langs` cl ON c.`id` = cl.`chance_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' and c.`service_type`='{$type}'";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}








}?>