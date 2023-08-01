<?php

$levelsObj = new levels(); 

class levels {

var $mDb; 
var $mConfig; 

function levels() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config; 
}


function getSomeLevels($aStart, $aLimit, $sort, $type, $searchName) {
	$sql = "SELECT l.id, l.level_name, l.date_added, u.full_name AS 'user_full_name' FROM `user_levels` l";
	$sql .= " LEFT JOIN `users` u ON u.id = l.user_id ";
	$sql.= " WHERE l.id > 0";
    $sql.= $searchName ? ' AND l.level_name like "%'.$searchName.'%"' : '';
//	$sql.=  $level_name ? " AND l.level_name ='{$level_name}' " : '';
	$sql .= " ORDER BY l.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchLevelsCount($sort, $type, $searchName,$level_name) {
	$sql = "SELECT COUNT(`id`) as 'result_count' FROM `user_levels`";
	$sql.= " WHERE `id` > 0";
    $sql.= $searchName ? ' AND `level_name` like "%'.$searchName.'%"' : '';
//	$sql.= $level_name &&  $level_name !=''&&  $level_name !=null? " AND `level_name` ='{$level_name}' " : '';

	$sql .= " ORDER BY {$sort} {$type}";

//	echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getLevelsCount(    ) {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `user_levels` ";
//    echo  $sql;
//    die();
	return $this -> mDb -> getOne($sql); 
}

function addEditLevel($temp) {

	// print_r($temp); die();

	if ($temp['id'] === null || $temp['id'] === '') {
		// add
		unset($temp['id']);

		$dateTime = date('Y-m-d H:i:s');

		$sql = "INSERT INTO `user_levels` SET "; 
		foreach ($temp as $k => $v) {
		    if($v ==''){
                $v='0';
            }
			$sql .= "`{$k}`='{$v}',"; 
		}
		$sql .= " `date_added` = '{$dateTime}'";
// echo $sql;die();
		$res = $this -> mDb -> query($sql); 
		return $res;

	}
	else{
		// edit
		$sql = "UPDATE `user_levels` SET "; 
		foreach ($temp as $k => $v) {
            if($v ==''){
                $v='0';
            }
			$sql .= "`{$k}`='{$v}',"; 
		}
		$sql = substr($sql, 0, -1);
		$sql .= " WHERE `id` = '{$temp['id']}'"; 
//     echo $sql;die();

		$res = $this -> mDb -> query($sql); 
		return $res; 

	}

}

function getOneLevel($id,$leveltype,$provider_id) {
	$sql = "SELECT * FROM `user_levels`";
	$sql .= " WHERE `id` = '$id' ";
	$sql .= $leveltype == 'provider' ?" AND `provider_id` = '$provider_id' ":"";
	return $this -> mDb -> getRow($sql); 
}

function deleteLevel($ids) {
	$sql = "DELETE FROM `user_levels` ";
	$sql.= " WHERE `id` = 0";
	foreach ($ids as $id) {
	    $sql .= " OR `id` = '{$id}' ";
	}

	$res = $this-> mDb-> query($sql);
	return $res;
}


}?>