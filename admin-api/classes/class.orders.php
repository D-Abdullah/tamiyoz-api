<?php

$orderObj = new Orders(); 

class Orders {

var $mDb; 
var $mConfig; 
 var $user;

function Orders() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config; 
	$this->user = new users();
}

function getSomeOrders($aStart, $aLimit, $sort, $type, $searchId, $searchUserName, $searchDateFrom, $searchDateTo,$searchStatus) {

	$sql = "SELECT o.*";
    $sql .= " FROM `orders` o ";
	$sql .= " WHERE o.`id` > 0  ";
    $sql .= $searchId ? ' AND o.`id` like "%'.$searchId.'%"' : '';
    $sql.= $searchUserName ? ' AND o.`name` like "%'.$searchUserName.'%"' : '';
    $sql.= $searchDateFrom ? " AND o.`date_added` BETWEEN '{$searchDateFrom}' AND '{$searchDateTo}'" : "";
    $sql.= $searchStatus ? " AND o.`status` = '{$searchStatus}'" : "";
	$sql .= " ORDER BY o.`id` {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
//    echo $sql; die();
	return $this -> mDb -> getAll($sql); 
}

function getSearchOrdersCount($sort, $type, $searchId, $searchUserName, $searchDateFrom, $searchDateTo,$searchStatus) {
	$sql = "SELECT COUNT(o.`id`) as 'result_count'  FROM `orders` o ";
	$sql .= " WHERE o.`id` > 0";
    $sql .= $searchId ? ' AND o.`id` like "%'.$searchId.'%"' : '';
    $sql.= $searchUserName ? ' AND o.`name` like "%'.$searchUserName.'%"' : '';
    $sql.= $searchDateFrom ? " AND o.`date_added` BETWEEN '{$searchDateFrom}' AND '{$searchDateTo}'" : "";
    $sql.= $searchStatus ? " AND o.`status` = '{$searchStatus}'" : "";
    $sql .= " ORDER BY o.`id` {$type}";

// 	echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getOrdersCount() {

	$sql = "SELECT COUNT(`id`) as 'count' FROM `orders`";

	return $this -> mDb -> getOne($sql); 
}
function getCarTypes() {
  $sql = "SELECT c.`id`, c.`img`, cl.`name`, cl.`description`,c.`type` FROM `cars_types` c";
        $sql .= " LEFT JOIN `cars_types_langs` cl ON c.`id` = cl.`car_id` ";
        $sql .= " WHERE c.`id` > 0 AND c.`status`='1'";
        $sql .= "AND cl.`lang_code` = 'ar'" ;
        $sql .= " ORDER BY c.`sort` ASC";
        $res = $this -> mDb -> getAll($sql);
        return $res;
}


function  getChatForThisOrder($requist){


    $requist['page']=$requist['page'] ?$requist['page']:'1';
    $aLimit=10;
    $aStart =$requist['page']? $requist['page']*$aLimit-$aLimit:"0";

    $sqll = "SELECT `id` FROM `{$this->mPrefix}chat_rooms`";
    $sqll .= "WHERE `order_id`='{$requist['order_id']}'";
    $roomId =$this->mDb->getOne($sqll);


    $sql = "SELECT m.* FROM `{$this->mPrefix}messages` m ";
    $sql .= " WHERE m.`chat_rooms_id`='{$roomId}'";
    $sql .= " ORDER BY m.`date_added` DESC ";
    $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
    $listMessages =$this->mDb->getAll($sql);
    $reversed = array_reverse($listMessages);
    return $reversed;
}

function getOneOrder($id) {

    $sql = "SELECT o.* FROM `orders` o";
    $sql .= " WHERE o.`id` = '{$id}'  ";
    $res = $this -> mDb -> getRow($sql);
    return $res;

}





function deleteOrder($ids) {
    $tempids='('.implode(',',$ids).')';

  

    $sqll = "DELETE FROM `orders` ";
    $sqll.= " WHERE `id` IN ".$tempids;
    
    $sqlln = "DELETE FROM `order_days_visits` ";
    $sqlln.= " WHERE `order_id` IN ".$tempids;
    
    $this-> mDb-> query($sqlln);
//    echo $sql; die();
    return $this-> mDb-> query($sqll);
}

function changeOrderStatus($request) {

    // print_r($request); die();

    $res = false;

    if ($request['status'] === 'canceled') {
        // Update Status

        $sql = "UPDATE `orders` SET ";
        $sql .= " `status`='canceled', `rejected_reason`='{$request['rejected_reason']}' ";
        $sql .= " WHERE `id`='{$request['id']}' ";
        $res = $this -> mDb -> query($sql);

    }
    elseif ($request['status'] === 'ok') {
        // Update Status

        $sql = "UPDATE `orders` SET ";
        $sql .= " `status`='ok' ";
        $sql .= " WHERE `id`='{$request['id']}' ";
        $res = $this -> mDb -> query($sql);

    }
    else{
        return;
    }

    return $res;

}


}?>