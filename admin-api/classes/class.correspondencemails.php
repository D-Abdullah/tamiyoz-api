<?php

$correspondenceMailsObj = new correspondenceMails();

class correspondenceMails {

var $mDb; 
var $mConfig; 

function correspondenceMails() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config; 
}

function getSomecorrespondenceMails($aStart, $aLimit, $sort, $type, $searchName) {
	$sql = "SELECT lan.id, lan.status,lan.email,lan.date_added, u.full_name AS 'user_full_name' FROM `correspondence_mails` lan";
	$sql .= " LEFT JOIN `users` u ON u.id = lan.added_by ";
	$sql.= " WHERE lan.id > 0";
    $sql.= $searchName ? ' AND lan.email like "%'.$searchName.'%"' : '';
	$sql .= " ORDER BY lan.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchcorrespondenceMailsCount($sort, $type, $searchName) {
	$sql = "SELECT COUNT(`id`) as 'result_count' FROM `correspondence_mails`";
	$sql.= " WHERE `id` > 0";

    $sql.= $searchName ? ' AND `email` like "%'.$searchName.'%"' : '';

	$sql .= " ORDER BY {$sort} {$type}";

	//echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getcorrespondenceMailsCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `correspondence_mails`";
	return $this -> mDb -> getOne($sql); 
}

function addEditcorrespondenceMails($request, $lang_id) {

    //var_dump($request); die();
//    $status=$request['status']==true? '1':'0';
    unset($request['action']);

    if ($lang_id === '')
    {
        // Add
        unset($request['userType']);
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s'); 

        $sql = "INSERT INTO `correspondence_mails` SET ";
        $sql .= " `date_added` = '{$dateTime}',";
        $sql .= " `email` = '{$request['email']}',";
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= " `added_by` = '{$request['added_by']}'";

//         echo $sql; die();

        return $this -> mDb -> query($sql); 
    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `correspondence_mails` WHERE `id` = '{$lang_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `correspondence_mails` SET ";
            $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
            $sql .= "`email`='{$request['email']}'";
            $sql .= " WHERE `id` = '{$lang_id}'";
            return $this -> mDb -> query($sql);

        } 
    }

}

function getOnecorrespondenceMails($id) {
	$sql = "SELECT `id`,`status`,`added_by`, `email`, `date_added` FROM `correspondence_mails`";
	$sql .= " WHERE `id` = '$id' ";
	return $this -> mDb -> getRow($sql); 
}

function deletecorrespondenceMails($ids) {

    $res = false;

    $sql = "DELETE FROM `correspondence_mails` ";
    $sql.= " WHERE `id` = 0";
    foreach ($ids as $id) {
        $query = "SELECT `email` FROM `correspondence_mails` WHERE `id` = '{$id}'";
        $result = $this -> mDb -> getOne($query);
        $sql .= " OR `id` = '{$id}' ";
    }

    $res = $this-> mDb-> query($sql);
    return $res;
}




}?>