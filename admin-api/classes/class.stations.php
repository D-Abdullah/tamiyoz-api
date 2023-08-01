<?php
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;




//declare(strict_types=1);
$stationObj = new Station();



//echo "ssss";


//die();
class Station {

var $mDb; 
var $mConfig;
 var $options;
function Station() {
	global $Config;
    $this->options = new QROptions(
        [
//            'eccLevel' => QRCode::ECC_L,
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
//            'version' => 5,
        ]
    );
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

function getSomeStations($pagename,$aStart, $aLimit, $sort, $type, $searchName) {
//    echo "dssa";
//    die();

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
	$sql .= " WHERE c.`id` > 0   AND cl.`lang_code` = 'ar'";
//    $sql .= $pagename=='stations'?"AND  c.`stations_type`='stations'": '';
//    $sql .= $pagename=='projects'?"AND  c.`stations_type`='projects'": '';
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
//    echo $sql;
//    die();
   $res=$this -> mDb -> getAll($sql);

//   return $res;
//qrcode

//    echo '<img src="'.$qrcode.'" alt="QR Code" />';
   for ($i=0;$i<count($res);$i++){
            $qrcode = (new QRCode(new QROptions(
                [
                    'eccLevel' => QRCode::ECC_L,
                    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                    'version' => 5,
                ]
            )))->render("station/".$res[$i]['id']);
//       $qrcode = (new QRCode)->render("station/".$res[$i]['id']);
       $res[$i]['qrcode']=$qrcode;
   }
    return $res;

//   for ($i=0;$i<count($res);$i++){
//       echo '<img src="'.$res[$i]['qrcode'].'" alt="QR Code" />';
//   }

//   die();



}

function getSearchstationsCount($sort, $type, $searchName,$service_type) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `stations` c";
    $sql .= " LEFT JOIN `stations_langs` cl ON c.`id` = cl.`station_id` ";
	$sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' ";

    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';

	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	// echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getstationsCount($type) {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `stations`";
    // echo $sql;
    // die();
	return $this -> mDb -> getOne($sql); 
}





    function addEditStation($temp,$img) {

        $dataLangObj = json_decode($temp['langs']);
        $newDataLangObj = $this->convert_object_to_array($dataLangObj);
        $lon=$temp['lon'];
        $lat=$temp['lat'];
        $user_id = $temp["user_id"];

        $status = $temp['status'] == 'true' ? "1" : "0";
        $sort = $temp['sort'] ;

        $id = $temp['id'];

        if ($id  < 1) {
            // add

            $dateTime = date('Y-m-d H:i:s');

            $sql = "INSERT INTO `stations` SET ";
            $sql .= " `added_by` = '{$user_id}', ";
            $sql .= " `status` = '{$status}', ";
            $sql .= " `sort` = '{$sort}', ";
            $sql .= " `lat` = '{$lat}', ";
            $sql .= " `lon` = '{$lon}', ";
            $sql .= " `date_added` = '{$dateTime}' ,";
            $sql .= " `img` = '{$img}'";
//         echo $sql;
//         die();
            $this -> mDb -> query($sql);

            $last_station_id = $this -> mDb -> getLastInsertId();
            foreach ($newDataLangObj as $key => $value) {
                $description = addslashes($value['description']);
                $title = addslashes($value['title']);
                // $sub_title = addslashes($value['sub_title']);

                $stmt = "INSERT INTO `stations_langs` SET ";
                $stmt .= " `station_id` = '{$last_station_id}', ";
                $stmt .= " `lang_code` = '{$value['lang_code']}', ";
                $stmt .= " `name` = '{$title}', ";
                // $stmt .= " `sub_title` = '{$sub_title}', ";
                $stmt .= " `description` = '{$description}'";

                $res = $this -> mDb -> query($stmt);

            }
            return $last_station_id;
        }
        else{
            // edit
            $check_query = "SELECT `id` FROM `stations` WHERE `id` = '{$id}'";
            $check_result = $this -> mDb -> getOne($check_query);
            if ($check_result === false) {
                return 403;
            }
            else{
                $sql = "UPDATE `stations` SET ";
                $sql .= " `added_by` = '{$user_id}', ";
                $sql .= " `lon` = '{$lon}', ";
                $sql .= " `lat` = '{$lat    }', ";
                $sql .= " `status` = '{$status}' ";
                if ($img != '' || $img != null) {

                    // Delete old image from the server
                    $img_query = "SELECT `img` FROM `stations` WHERE `id` = '{$id}'";
                    $img_result = $this -> mDb -> getOne($img_query);

                    $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'stations/' . $img_result;

                    if (file_exists($old_img)) {
                        @unlink($old_img);
                    }

                    $sql .= ",`img`='{$img}' ";
                }
                $sql .= " WHERE `id` = '{$id}'";

                $this -> mDb -> query($sql);

                foreach ($newDataLangObj as $key => $value) {
                    $description = addslashes($value['description']);
                    $title = addslashes($value['title']);
                    // $sub_title = addslashes($value['sub_title']);
                    $stmt = "UPDATE `stations_langs` SET ";
                    $stmt .= " `name` = '{$title}', ";
                    // $stmt .= " `sub_title` = '{$sub_title}', ";
                    $stmt .= " `description` = '{$description}'";
                    $stmt .= " WHERE `lang_code`='{$value['lang_code']}' AND `station_id` = '{$id}' ";
                    $res = $this -> mDb -> query($stmt);
                }
                return $res;
            }
        }
    }







function getOneStation($id) {

    $result = array();

    $sql = "SELECT `id`, `added_by`, `status`, `sort`, `img`,`lon`,`lat`,`parent_id` FROM `stations`";
    $sql .= " WHERE `id` = '{$id}'";
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



function deleteProject($ids) {

    $tempids='('.implode(',',$ids).')';

    $sql = "SELECT `img` FROM `stations`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    $sqll = "DELETE FROM `stations_langs` ";
    $sqll.= " WHERE `station_id` IN ".$tempids;

    $this-> mDb-> query($sqll);

    $catsql = "DELETE FROM `stations` ";
    $catsql.= " WHERE `id` IN ".$tempids;

    if($this-> mDb-> query($catsql)){

        if(count($images)>0){
            for ($i=0;$i<count($images);$i++){
                $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'stations/'. $images[$i]['img'];
                if (file_exists($old_img)) {
                    @unlink($old_img);
                }
            }
        }
        return true;
    }
    else{
        return false;
    }
}






}?>