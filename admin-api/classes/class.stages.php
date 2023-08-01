<?php

$stagesObj = new Stages();

class Stages {

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

function getSomeStages($aStart, $aLimit, $sort, $type, $searchName,$searchCountry) {
	$sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,";
    $sql .= " cuntl.`name` as 'country_name',";
    $sql .= " c.`date_added`,";
    $sql .= " c.`status`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `stages` c ";
    $sql .= " LEFT JOIN `stages_langs` cl ON c.`id` = cl.`stage_id` ";
    $sql .= " LEFT JOIN `countries_langs` cuntl ON  ( c.`country_id` = cuntl.`country_id` AND cuntl.`lang_code` = 'ar') ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0  AND cl.`lang_code` = 'ar'  ";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
    $sql.= $searchCountry ? " AND c.`country_id` = '{$searchCountry}'" : "";
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
//	 echo $sql; die();
	return $this -> mDb -> getAll($sql); 
}
function getAllCountries() {
	$sql = "SELECT cu.* ,";
    $sql .= " cul.`name`";
    $sql .= " FROM `countries` cu ";
    $sql .= " LEFT JOIN `countries_langs` cul ON cu.`id` = cul.`country_id` ";
	$sql .= " WHERE cu.`id` > 0  AND cul.`lang_code` = 'ar' ";
//	 echo $sql; die();
	return $this -> mDb -> getAll($sql);
}

function getSearchStagesCount($sort, $type, $searchName,$searchCountry) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `stages` c";
    $sql .= " LEFT JOIN `stages_langs` cl ON c.`id` = cl.`stage_id` ";
	$sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' ";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
    $sql.= $searchCountry ? " AND c.`country_id` = '{$searchCountry}'" : "";
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	//echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getStagesCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `stages` ";
	return $this -> mDb -> getOne($sql); 
}

function addEditStage($request, $img, $stage_id) {

    $dataLangObj = json_decode($request['stageLangObj']);
    $newDataLangObj = $this->convert_object_to_array($dataLangObj);

    unset($request['action']);
    unset($request['countryLangObj']);

    $res = false; 

    if ($stage_id === '' && $request['operation'] === 'add')
    {
        // Add
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `stages` SET ";
        $sql .= " `added_by` = '{$request['added_by']}',"; 
        $sql .= " `country_id` = '{$request['country_id']}',";
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= " `date_added` = '{$dateTime}',";
        $sql .= " `img` = '{$img}'";
//         echo $sql; die();
        $this -> mDb ->query($sql);
        $last_stage_id = $this -> mDb ->getLastInsertId();
        foreach ($newDataLangObj as $key => $value) {
            $stmt = "INSERT INTO `stages_langs`(`stage_id`, `lang_code`, `name`) ";
            $stmt .=" VALUES ('{$last_stage_id}', '{$value['lang_code']}', '{$value['stage_name']}')";
            $res = $this -> mDb -> query($stmt);
        }
            return $res;
    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `stages` WHERE `id` = '{$stage_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `stages` SET ";
            $sql .= " `added_by` = '{$request['added_by']}', ";
            $sql .= " `country_id` = '{$request['country_id']}', ";
            $sql .= $request['status'] === 'true' ? " `status` = '1'" : " `status` = '0'";

            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `img` FROM `stages` WHERE `id` = '{$stage_id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'stages/' . $img_result;

                if (file_exists($old_img)) {
                    @unlink($old_img);
                }

                $sql .= ",`img`='{$img}' ";
            }

            $sql .= " WHERE `id` = '{$stage_id}'";

//             echo $sql; die();

            $this -> mDb -> query($sql);

            foreach ($newDataLangObj as $key => $value) {

                $stmt = "UPDATE `stages_langs` SET";
                $stmt .=" `name`='{$value['stage_name']}' ";
               
                $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `stage_id` = '{$stage_id}'";

                $res = $this -> mDb -> query($stmt);
                
            }

                return $res;

        } 
    }

}

function getOneStage($id) {

    $result = array();

    $sql = "SELECT `id`, `added_by`, `status`, `img`,`country_id` FROM `stages`";
    $sql .= " WHERE `id` = '{$id}'";
    $result = $this -> mDb -> getRow($sql); 

    $stmt = "SELECT cl.`lang_code`, cl.`name` FROM `stages_langs` cl";
    $stmt .= " WHERE cl.`stage_id` = '{$id}'";
    $result['langs'] = $this -> mDb -> getAll($stmt);

    return $result;

}
function getStageByParentID($id) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `stages` c";
    $sql .= " LEFT JOIN `stages_langs` cl ON c.`id` = cl.`stage_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' ";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}



function deleteStage($ids) {

    $tempids='('.implode(',',$ids).')';

    $sql = "SELECT `img` FROM `stages`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    $sqll = "DELETE FROM `stages_langs` ";
    $sqll.= " WHERE `stage_id` IN ".$tempids;

    $this-> mDb-> query($sqll);

    $catsql = "DELETE FROM `stages` ";
    $catsql.= " WHERE `id` IN ".$tempids;

    if($this-> mDb-> query($catsql)){

        if(count($images)>0){
            for ($i=0;$i<count($images);$i++){
                $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'stages/'. $images[$i]['img'];
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