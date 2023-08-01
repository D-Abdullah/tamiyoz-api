<?php

$countriesObj = new Countries();

class Countries {

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

function getSomeCountries($aStart, $aLimit, $sort, $type, $searchName) {

	$sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,";
    $sql .= " c.`date_added`,";
    $sql .= " c.`status`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `countries` c ";
    $sql .= " LEFT JOIN `countries_langs` cl ON c.`id` = cl.`country_id` ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0  AND cl.`lang_code` = 'ar' ";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchCountriesCount($sort, $type, $searchName) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `countries` c";
    $sql .= " LEFT JOIN `countries_langs` cl ON c.`id` = cl.`country_id` ";
	$sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' ";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	//echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getCountriesCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `countries` ";
	return $this -> mDb -> getOne($sql); 
}

function addEditCountry($request, $img, $country_id) {

    $dataLangObj = json_decode($request['countryLangObj']);
    $newDataLangObj = $this->convert_object_to_array($dataLangObj);

    unset($request['action']);
    unset($request['countryLangObj']);

    $res = false; 

    if ($country_id === '' && $request['operation'] === 'add')
    {
        // Add
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `countries` SET ";
        $sql .= " `added_by` = '{$request['added_by']}',"; 
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= " `date_added` = '{$dateTime}',";
        $sql .= " `img` = '{$img}'";
//         echo $sql; die();
        $this -> mDb ->query($sql);
        $last_country_id = $this -> mDb ->getLastInsertId();
        foreach ($newDataLangObj as $key => $value) {
            $stmt = "INSERT INTO `countries_langs`(`country_id`, `lang_code`, `name`) ";
            $stmt .=" VALUES ('{$last_country_id}', '{$value['lang_code']}', '{$value['country_name']}')";
            $res = $this -> mDb -> query($stmt);
        }
            return $res;
    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `countries` WHERE `id` = '{$country_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `countries` SET ";
            $sql .= " `added_by` = '{$request['added_by']}', ";
            $sql .= $request['status'] === 'true' ? " `status` = '1'" : " `status` = '0'";

            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `img` FROM `countries` WHERE `id` = '{$country_id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'countries/' . $img_result;

                if (file_exists($old_img)) {
                    @unlink($old_img);
                }

                $sql .= ",`img`='{$img}' ";
            }

            $sql .= " WHERE `id` = '{$country_id}'";

//             echo $sql; die();

            $this -> mDb -> query($sql);

            foreach ($newDataLangObj as $key => $value) {

                $stmt = "UPDATE `countries_langs` SET";
                $stmt .=" `name`='{$value['country_name']}' ";
               
                $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `country_id` = '{$country_id}'";

                $res = $this -> mDb -> query($stmt);
                
            }

                return $res;

        } 
    }

}

function getOneCountry($id) {

    $result = array();

    $sql = "SELECT `id`, `added_by`, `status`, `img` FROM `countries`";
    $sql .= " WHERE `id` = '{$id}'";
    $result = $this -> mDb -> getRow($sql); 

    $stmt = "SELECT cl.`lang_code`, cl.`name` FROM `countries_langs` cl";
    $stmt .= " WHERE cl.`country_id` = '{$id}'";
    $result['langs'] = $this -> mDb -> getAll($stmt);

    return $result;

}
function getCountryByParentID($id) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `countries` c";
    $sql .= " LEFT JOIN `countries_langs` cl ON c.`id` = cl.`country_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' ";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}



function deleteCountry($ids) {

    $tempids='('.implode(',',$ids).')';

    $sql = "SELECT `img` FROM `countries`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    $sqll = "DELETE FROM `countries_langs` ";
    $sqll.= " WHERE `country_id` IN ".$tempids;

    $this-> mDb-> query($sqll);

    $catsql = "DELETE FROM `countries` ";
    $catsql.= " WHERE `id` IN ".$tempids;

    if($this-> mDb-> query($catsql)){

        if(count($images)>0){
            for ($i=0;$i<count($images);$i++){
                $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'countries/'. $images[$i]['img'];
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