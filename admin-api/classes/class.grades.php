<?php

$gradesObj = new Grades();

class Grades {

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

function getSomeGrades($aStart, $aLimit, $sort, $type, $searchName,$searchCountry,$searchStage) {
	$sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,";
    $sql .= " cuntl.`name` as 'country_name',";
    $sql .= " stagel.`name` as 'stage_name',";
    $sql .= " c.`date_added`,";
    $sql .= " c.`status`,";
    $sql .= " c.`sort`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `grades` c ";
    $sql .= " LEFT JOIN `grades_langs` cl ON c.`id` = cl.`grade_id` ";
    $sql .= " LEFT JOIN `countries_langs` cuntl ON  ( c.`country_id` = cuntl.`country_id` AND cuntl.`lang_code` = 'ar') ";
    $sql .= " LEFT JOIN `stages_langs` stagel ON  ( c.`stage_id` = stagel.`stage_id` AND stagel.`lang_code` = 'ar') ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0  AND cl.`lang_code` = 'ar'  ";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
    $sql.= $searchCountry ? " AND c.`country_id` = '{$searchCountry}'" : "";
    $sql.= $searchStage ? " AND c.`stage_id` = '{$searchStage}'" : "";
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
function getStagesWithCountryId($country_id) {
	$sql = "SELECT cu.* ,";
    $sql .= " cul.`name`";
    $sql .= " FROM `stages` cu ";
    $sql .= " LEFT JOIN `stages_langs` cul ON cu.`id` = cul.`stage_id` ";
	$sql .= " WHERE cu.`id` > 0  AND cul.`lang_code` = 'ar' AND cu.`country_id` = '{$country_id}' ";
//	 echo $sql; die();
	return $this -> mDb -> getAll($sql);
}
function getGradesWithStageId($stage_id) {
	$sql = "SELECT gr.* ,";
    $sql .= " grl.`name`";
    $sql .= " FROM `grades` gr ";
    $sql .= " LEFT JOIN `grades_langs` grl ON gr.`id` = grl.`grade_id` ";
	$sql .= " WHERE gr.`id` > 0  AND grl.`lang_code` = 'ar' AND gr.`stage_id` = '{$stage_id}' ";
//	 echo $sql; die();
	return $this -> mDb -> getAll($sql);
}

function getSubjectsWithGradeID($grade_id,$semester) {
	$sql = "SELECT sub.* ";

    $sql .= " FROM `subjects` sub ";
	$sql .= " WHERE sub.`id` > 0 AND sub.`grade_id` = '{$grade_id}' ";
    $sql .=$semester ? " AND sub.`semester` = '{$semester}' " :"";
//	 echo $sql; die();
	return $this -> mDb -> getAll($sql);
}

function getUnitsWithSubjectID($subject_id) {
	$sql = "SELECT un.* ";
    $sql .= " FROM `units` un ";
	$sql .= " WHERE un.`id` > 0 AND un.`subject_id` = '{$subject_id}' ";

//	 echo $sql; die();
	return $this -> mDb -> getAll($sql);
}
function getLessonsWithUnitID($unit_id) {
	$sql = "SELECT un.* ";
    $sql .= " FROM `unit_lessons` un ";
	$sql .= " WHERE un.`id` > 0 AND un.`unit_id` = '{$unit_id}' ";

//	 echo $sql; die();
	return $this -> mDb -> getAll($sql);
}

function getSearchGradesCount($sort, $type, $searchName,$searchCountry,$searchStage) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `grades` c";
    $sql .= " LEFT JOIN `grades_langs` cl ON c.`id` = cl.`grade_id` ";
	$sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' ";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
    $sql.= $searchCountry ? " AND c.`country_id` = '{$searchCountry}'" : "";
    $sql.= $searchStage ? " AND c.`stage_id` = '{$searchStage}'" : "";
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	//echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getGradesCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `grades` ";
	return $this -> mDb -> getOne($sql); 
}

function addEditGrade($request, $img, $grade_id) {

    $dataLangObj = json_decode($request['gradeLangObj']);
    $newDataLangObj = $this->convert_object_to_array($dataLangObj);

    unset($request['action']);
    unset($request['countryLangObj']);

    $res = false; 

    if ($grade_id === '' && $request['operation'] === 'add')
    {
        // Add
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `grades` SET ";
        $sql .= " `added_by` = '{$request['added_by']}',"; 
        $sql .= " `country_id` = '{$request['country_id']}',";
        $sql .= " `stage_id` = '{$request['stage_id']}',";
        $sql .= " `sort` = '{$request['sort']}',";
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= " `date_added` = '{$dateTime}',";
        $sql .= " `img` = '{$img}'";
//        echo $sql; die();
        $this -> mDb ->query($sql);
        $last_grade_id = $this -> mDb ->getLastInsertId();
        foreach ($newDataLangObj as $key => $value) {

            $stmt = "INSERT INTO `grades_langs`(`grade_id`, `lang_code`, `name`) ";
            $stmt .=" VALUES ('{$last_grade_id}', '{$value['lang_code']}', '{$value['grade_name']}')";
            $res = $this -> mDb -> query($stmt);
        }
            return $res;
    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `grades` WHERE `id` = '{$grade_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `grades` SET ";
            $sql .= " `added_by` = '{$request['added_by']}', ";
            $sql .= " `country_id` = '{$request['country_id']}', ";
            $sql .= " `stage_id` = '{$request['stage_id']}',";
            $sql .= " `sort` = '{$request['sort']}',";
            $sql .= " `stage_id` = '{$request['stage_id']}', ";
            $sql .= $request['status'] === 'true' ? " `status` = '1'" : " `status` = '0'";

            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `img` FROM `grades` WHERE `id` = '{$grade_id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'grades/' . $img_result;

                if (file_exists($old_img)) {
                    @unlink($old_img);
                }

                $sql .= ",`img`='{$img}' ";
            }

            $sql .= " WHERE `id` = '{$grade_id}'";

//             echo $sql; die();

            $this -> mDb -> query($sql);

            foreach ($newDataLangObj as $key => $value) {

                $stmt = "UPDATE `grades_langs` SET";
                $stmt .=" `name`='{$value['grade_name']}' ";
               
                $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `grade_id` = '{$grade_id}'";

                $res = $this -> mDb -> query($stmt);
                
            }

                return $res;

        } 
    }

}

function getOneGrade($id) {

    $result = array();

    $sql = "SELECT `id`, `added_by`, `status`, `img`,`country_id`,`stage_id`,`sort` FROM `grades`";
    $sql .= " WHERE `id` = '{$id}'";
    $result = $this -> mDb -> getRow($sql); 

    $stmt = "SELECT cl.`lang_code`, cl.`name` FROM `grades_langs` cl";
    $stmt .= " WHERE cl.`grade_id` = '{$id}'";
    $result['langs'] = $this -> mDb -> getAll($stmt);

    return $result;

}
function getGradeByParentID($id) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `grades` c";
    $sql .= " LEFT JOIN `grades_langs` cl ON c.`id` = cl.`grade_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' ";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}



function deleteGrade($ids) {

    $tempids='('.implode(',',$ids).')';

    $sql = "SELECT `img` FROM `grades`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    $sqll = "DELETE FROM `grades_langs` ";
    $sqll.= " WHERE `grade_id` IN ".$tempids;

    $this-> mDb-> query($sqll);

    $catsql = "DELETE FROM `grades` ";
    $catsql.= " WHERE `id` IN ".$tempids;

    if($this-> mDb-> query($catsql)){

        if(count($images)>0){
            for ($i=0;$i<count($images);$i++){
                $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'grades/'. $images[$i]['img'];
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