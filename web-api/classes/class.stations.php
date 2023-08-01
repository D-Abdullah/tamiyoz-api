<?php
//use chillerlan\QRCode\QRCode;
//use chillerlan\QRCode\QROptions;




//declare(strict_types=1);
$stationObj1 = new Station();



//echo "ssss";


//die();
class Station {

var $mDb; 
var $mConfig;
 var $options;
function Station() {
	global $Config;
//    $this->options = new QROptions(
//        [
////            'eccLevel' => QRCode::ECC_L,
//            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
////            'version' => 5,
//        ]
//    );
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

function getSomeStations($aStart, $aLimit, $sort, $type, $searchName,$code) {
    $sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,";
    // $sql .= " clp.`name` as 'parent_name',";
    $sql .= " c.`date_added`,";
    $sql .= " c.`sort`,";
     $sql .= " c.`parent_id`,";
    $sql .= " c.`status`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `stations` c ";
    $sql .= " LEFT JOIN `stations_langs` cl ON c.`id` = cl.`station_id` ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0    AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
   $res=$this -> mDb -> getAll($sql);
//qrcode
//   for ($i=0;$i<count($res);$i++){
//            $qrcode = (new QRCode(new QROptions(
//                [
//                    'eccLevel' => QRCode::ECC_L,
//                    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
//                    'version' => 5,
//                ]
//            )))->render("station/".$res[$i]['id']);
////       $qrcode = (new QRCode)->render("station/".$res[$i]['id']);
//       $res[$i]['qrcode']=$qrcode;
//   }
    return $res;


}

function getSearchstationsCount($sort, $type, $searchName,$code) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `stations` c";
    $sql .= " LEFT JOIN `stations_langs` cl ON c.`id` = cl.`station_id` ";
	$sql .= " WHERE c.`id` > 0 AND c.`status`= '1' AND cl.`lang_code` = '{$code}'";

    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';

	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	// echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getstationsCount($type) {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `stations` where `status`= '1'";
    // echo $sql;
    // die();
	return $this -> mDb -> getOne($sql); 
}


function getOneStation($id) {

    $result = array();

    $sql = "SELECT `id`, `added_by`, `status`, `sort`, `img`,`lon`,`lat`,`parent_id` FROM `stations`";
    $sql .= " WHERE `id` = '{$id}' AND`status`= '1'";
    $result = $this -> mDb -> getRow($sql); 

    $stmt = "SELECT cl.`lang_code`, cl.`name`, cl.`description` FROM `stations_langs` cl";
    $stmt .= " WHERE cl.`station_id` = '{$id}'";
    $result['langs'] = $this -> mDb -> getAll($stmt);

    return $result;

}
function getCategoryByParentID($id,$type) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `stations` c";
    $sql .= " LEFT JOIN `stations_langs` cl ON c.`id` = cl.`station_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' and c.`service_type`='{$type}'";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}






}?>