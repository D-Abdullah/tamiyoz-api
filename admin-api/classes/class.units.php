<?php

$unitsObj = new Units();

class Units {

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


function getSomeUnits($aStart, $aLimit, $sort, $type, $searchName,$searchCountry,$searchStage,$searchGrade,$searchSemester,$searchSubject) {
	$sql = "SELECT sub.id, sub.name ,sub.sort,sub.semester,sub.status, sub.date_added, u.full_name AS 'user_full_name' ,";
    $sql .= " cuntl.`name` as 'country_name',";
    $sql .= " stagel.`name` as 'stage_name',";
    $sql .= " gradel.`name` as 'grade_name' , ";
    $sql .= " subject.`name` as 'subject_name'";
    $sql .= "  FROM `units` sub ";
    $sql .= " LEFT JOIN `subjects` subject ON  ( sub.`subject_id` = subject.`id`)";
    $sql .= " LEFT JOIN `grades_langs` gradel ON  ( sub.`grade_id` = gradel.`grade_id` AND gradel.`lang_code` = 'ar')";
    $sql .= " LEFT JOIN `countries_langs` cuntl ON  ( sub.`country_id` = cuntl.`country_id` AND cuntl.`lang_code` = 'ar') ";
    $sql .= " LEFT JOIN `stages_langs` stagel ON  ( sub.`stage_id` = stagel.`stage_id` AND stagel.`lang_code` = 'ar') ";
    $sql .= " LEFT JOIN `users` u ON u.id = sub.added_by ";
	$sql.= " WHERE sub.id > 0";
    $sql.= $searchName ? ' AND sub.name like "%'.$searchName.'%"' : '';
    $sql.= $searchCountry ? " AND sub.`country_id` = '{$searchCountry}'" : "";
    $sql.= $searchStage ? " AND sub.`stage_id` = '{$searchStage}'" : "";
    $sql.= $searchGrade ? " AND sub.`grade_id` = '{$searchGrade}'" : "";
    $sql.= $searchSemester ? " AND sub.`semester` = '{$searchSemester}'" : "";
    $sql.= $searchSubject ? " AND `subject_id` = '{$searchSubject}'" : "";
	$sql .= " ORDER BY sub.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchUnitsCount($sort, $type, $searchName,$searchCountry,$searchStage,$searchGrade,$searchSemester,$searchSubject) {
	$sql = "SELECT COUNT(`id`) as 'result_count' FROM `units`";
	$sql.= " WHERE `id` > 0";

    $sql.= $searchName ? ' AND `name` like "%'.$searchName.'%"' : '';
    $sql.= $searchCountry ? " AND `country_id` = '{$searchCountry}'" : "";
    $sql.= $searchStage ? " AND `stage_id` = '{$searchStage}'" : "";
    $sql.= $searchGrade ? " AND `grade_id` = '{$searchGrade}'" : "";
    $sql.= $searchSemester ? " AND `semester` = '{$searchSemester}'" : "";
    $sql.= $searchSubject ? " AND `subject_id` = '{$searchSubject}'" : "";

	$sql .= " ORDER BY {$sort} {$type}";

//	echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getUnitsCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `units`";
	return $this -> mDb -> getOne($sql); 
}

function addEditUnit($request, $lang_id) {

//    var_dump($request); die();

    $dataLangObj = json_decode($request['unit_lessons']);
    $newDataLangObj = $this->convert_object_to_array($dataLangObj);

    unset($request['action']);

    if ($lang_id === '')
    {
        // Add
        unset($request['userType']);
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s');
//        print_r($request); die();
        $sql = "INSERT INTO `units` SET ";
        $sql .= " `added_by` = '{$request['user_id']}',";
        $sql .= " `country_id` = '{$request['country_id']}',";
        $sql .= " `stage_id` = '{$request['stage_id']}',";
        $sql .= " `grade_id` = '{$request['grade_id']}',";
        $sql .= " `subject_id` = '{$request['subject_id']}',";
        $sql .= " `semester` = '{$request['semester']}',";
        $sql .= " `name` = '{$request['name']}',";
        $sql .= " `sort` = '{$request['sort']}',";
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= " `date_added` = '{$dateTime}'";
//         echo $sql; die();
        $this -> mDb ->query($sql);
        $last_grade_id = $this -> mDb ->getLastInsertId();
        foreach ($newDataLangObj as $key => $value) {

            $stmt = "INSERT INTO `unit_lessons`(`unit_id`, `name`) ";
            $stmt .=" VALUES ('{$last_grade_id}', '{$value['name']}')";
            $res = $this -> mDb -> query($stmt);
        }
        return $res;

    }
    else
    {
        // Edit
        $check_query = "SELECT `id` FROM `units` WHERE `id` = '{$lang_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            return 403;
        }
        else{
                
            $sql = "UPDATE `units` SET ";
            $sql .= " `added_by` = '{$request['user_id']}',";
            $sql .= " `country_id` = '{$request['country_id']}',";
            $sql .= " `stage_id` = '{$request['stage_id']}',";
            $sql .= " `grade_id` = '{$request['grade_id']}',";
            $sql .= " `semester` = '{$request['semester']}',";
            $sql .= " `subject_id` = '{$request['subject_id']}',";
            $sql .= " `sort` = '{$request['sort']}',";
            $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
            $sql .= "`name`='{$request['name']}'";
            $sql .= " WHERE `id` = '{$lang_id}'";
              $res=$this -> mDb -> query($sql);

            foreach ($newDataLangObj as $key => $value) {

                if($value['id'] == ''){
                    $stmt = "INSERT INTO `unit_lessons`(`unit_id`, `name`) ";
                    $stmt .=" VALUES ('{$lang_id}', '{$value['name']}')";
                    $this -> mDb -> query($stmt);
                }else{
                    $sql = "UPDATE `unit_lessons` SET ";
                    $sql .= " `name` = '{$value['name']}'";
                    $sql .= " WHERE `id` = '{$value['id']}'";
                    $this -> mDb -> query($sql);
                }
            }


             return $res;
        } 
    }

}

function getOneUnit($id) {
	$sql = "SELECT * FROM `units`";
	$sql .= " WHERE `id` = '$id' ";
	$res=$this -> mDb -> getRow($sql);
	if($res){
        $sql = "SELECT `id`, `name`,`unit_id` FROM `unit_lessons`";
        $sql .= " WHERE `unit_id` = '$id' ";
        $res['unit_lessons']=$this -> mDb -> getAll($sql);
    }
	return $res;
}

function deleteUnit($ids) {
    $res = false;
    $tempids='('.implode(',',$ids).')';
    $sqll = "DELETE FROM `units` ";
    $sqll.= " WHERE `id` IN ".$tempids;

    $catsql = "DELETE FROM `unit_lessons` ";
    $catsql.= " WHERE `unit_id` IN ".$tempids;
    $this-> mDb-> query($catsql);
    return $this-> mDb-> query($sqll);
}
function deleteOneLesson($ids) {
    $res = false;
    $catsql = "DELETE FROM `unit_lessons` ";
    $catsql.= " WHERE `id` = '{$ids}' ";
//    echo  $catsql;die();
    return $this-> mDb-> query($catsql);
}

function getAllSubjects() {
    $sql = "SELECT `id`, `name`, `img` FROM `units`";
    return $this -> mDb -> getAll($sql); 
}


}?>