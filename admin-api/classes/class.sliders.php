<?php

$slidersObj = new Sliders();

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

function getSomeSliders($aStart, $aLimit, $sort, $type, $searchName) {

	$sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,";
    $sql .= " c.`date_added`,";
    $sql .= " c.`status`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `sliders` c ";
    $sql .= " LEFT JOIN `sliders_langs` cl ON c.`id` = cl.`slider_id` ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0  AND cl.`lang_code` = 'ar' ";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
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

function addEditSlider($request, $img, $slider_id) {

    $dataLangObj = json_decode($request['countryLangObj']);
    $newDataLangObj = $this->convert_object_to_array($dataLangObj);
      $sort=$request['sort']?$request['sort']:0;
    $url=$request['SliderUrl']?$request['SliderUrl']:'';
    unset($request['action']);
    unset($request['countryLangObj']);

    $res = false; 

    if ($slider_id === '' && $request['operation'] === 'add')
    {
        // Add
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `sliders` SET ";
        $sql .= " `added_by` = '{$request['added_by']}',";
        $sql .= " `sort` = '{$sort}',";
        $sql .= " `url` = '{$url}',";
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= " `date_added` = '{$dateTime}',";
        $sql .= " `img` = '{$img}'";
//         echo $sql; die();
        $this -> mDb ->query($sql);
        $last_slider_id = $this -> mDb ->getLastInsertId();
        foreach ($newDataLangObj as $key => $value) {
            $stmt = "INSERT INTO `sliders_langs`(`slider_id`, `lang_code`, `name`,`description`,`description_2`) ";
            $stmt .=" VALUES ('{$last_slider_id}', '{$value['lang_code']}', '{$value['country_name']}','{$value['slider_des']}','{$value['slider_des_2']}')";

            $res = $this -> mDb -> query($stmt);

        }
            return $res;
    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `sliders` WHERE `id` = '{$slider_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `sliders` SET ";
            $sql .= " `added_by` = '{$request['added_by']}', ";
            $sql .= " `sort` = '{$sort}',";
            $sql .= " `url` = '{$url}',";
            $sql .= $request['status'] === 'true' ? " `status` = '1' " : " `status` = '0'";

            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `img` FROM `sliders` WHERE `id` = '{$slider_id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'sliders/' . $img_result;

                if (file_exists($old_img)) {
                    @unlink($old_img);
                }

                $sql .= ",`img`='{$img}' ";
            }

            $sql .= " WHERE `id` = '{$slider_id}'";



            $this -> mDb -> query($sql);

            foreach ($newDataLangObj as $key => $value) {

                $stmt = "UPDATE `sliders_langs` SET";
                $stmt .=" `name`='{$value['country_name']}',";
                $stmt .=" `description`='{$value['slider_des']}' ,";
                $stmt .=" `description_2`='{$value['slider_des_2']}' ";
                $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `slider_id` = '{$slider_id}'";
//                 echo  $stmt;
//                 die();
                $res = $this -> mDb -> query($stmt);
                
            }

                return $res;

        } 
    }

}

function getOneSlider($id) {

    $result = array();

    $sql = "SELECT `id`, `added_by`, `status`,`url`, `img`,`sort` FROM `sliders`";
    $sql .= " WHERE `id` = '{$id}'";
    $result = $this -> mDb -> getRow($sql); 

    $stmt = "SELECT cl.`lang_code`, cl.`name` , cl.`description`,cl.`description_2` FROM `sliders_langs` cl";
    $stmt .= " WHERE cl.`slider_id` = '{$id}'";
    $result['langs'] = $this -> mDb -> getAll($stmt);

    return $result;

}
function getPartnerByParentID($id) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `sliders` c";
    $sql .= " LEFT JOIN `sliders_langs` cl ON c.`id` = cl.`slider_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' ";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}



function deleteSlider($ids) {

    $tempids='('.implode(',',$ids).')';

    $sql = "SELECT `img` FROM `sliders`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    $sqll = "DELETE FROM `sliders_langs` ";
    $sqll.= " WHERE `slider_id` IN ".$tempids;

    $this-> mDb-> query($sqll);

    $catsql = "DELETE FROM `sliders` ";
    $catsql.= " WHERE `id` IN ".$tempids;

    if($this-> mDb-> query($catsql)){

        if(count($images)>0){
            for ($i=0;$i<count($images);$i++){
                $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'sliders/'. $images[$i]['img'];
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