<?php

$subjectObj = new Subjects();

class Subjects {

var $mDb; 
var $mConfig; 

function __construct() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config; 
}

function getSomeSubjects($aStart, $aLimit, $sort, $type, $searchName,$searchCountry,$searchStage,$searchGrade,$searchSemester) {
	$sql = "SELECT sub.id, sub.name,sub.img ,sub.sort,sub.semester,sub.status, sub.date_added, u.full_name AS 'user_full_name' ,";
    $sql .= " cuntl.`name` as 'country_name',";
    $sql .= " stagel.`name` as 'stage_name',";
    $sql .= " gradel.`name` as 'grade_name'";
    $sql .= "  FROM `subjects` sub ";
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
	$sql .= " ORDER BY sub.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchSubjectsCount($sort, $type, $searchName,$searchCountry,$searchStage,$searchGrade,$searchSemester) {
	$sql = "SELECT COUNT(`id`) as 'result_count' FROM `subjects`";
	$sql.= " WHERE `id` > 0";

    $sql.= $searchName ? ' AND `name` like "%'.$searchName.'%"' : '';
    $sql.= $searchCountry ? " AND `country_id` = '{$searchCountry}'" : "";
    $sql.= $searchStage ? " AND `stage_id` = '{$searchStage}'" : "";
    $sql.= $searchGrade ? " AND `grade_id` = '{$searchGrade}'" : "";
    $sql.= $searchSemester ? " AND `semester` = '{$searchSemester}'" : "";

	$sql .= " ORDER BY {$sort} {$type}";

//	echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getSubjectsCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `subjects`";
	return $this -> mDb -> getOne($sql); 
}

function addEditSubject($request, $img, $lang_id) {

    //var_dump($request); die();

    unset($request['action']);

    if ($lang_id === '')
    {
        // Add
        unset($request['userType']);
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s');
//        print_r($request); die();
        $sql = "INSERT INTO `subjects` SET ";
        $sql .= " `added_by` = '{$request['user_id']}',";
        $sql .= " `country_id` = '{$request['country_id']}',";
        $sql .= " `stage_id` = '{$request['stage_id']}',";
        $sql .= " `grade_id` = '{$request['grade_id']}',";
        $sql .= " `semester` = '{$request['semester']}',";
        $sql .= " `name` = '{$request['name']}',";
        $sql .= " `sort` = '{$request['sort']}',";
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= " `date_added` = '{$dateTime}',";
        $sql .= " `img` = '{$img}'";
//         echo $sql; die();
        return $this -> mDb -> query($sql); 
    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `subjects` WHERE `id` = '{$lang_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `subjects` SET ";
            $sql .= " `added_by` = '{$request['user_id']}',";
            $sql .= " `country_id` = '{$request['country_id']}',";
            $sql .= " `stage_id` = '{$request['stage_id']}',";
            $sql .= " `grade_id` = '{$request['grade_id']}',";
            $sql .= " `semester` = '{$request['semester']}',";
            $sql .= " `sort` = '{$request['sort']}',";
            $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
            $sql .= "`name`='{$request['name']}'";

            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `lang_image` FROM `subjects` WHERE `id` = '{$lang_id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'subjects/' . $img_result;

                if (file_exists($old_img)) {
                    @unlink($old_img);
                }

                $sql .= ",`img`='{$img}' ";
            }

            $sql .= " WHERE `id` = '{$lang_id}'"; 

            return $this -> mDb -> query($sql);

        } 
    }

}

function getOneSubject($id) {
	$sql = "SELECT * FROM `subjects`";
	$sql .= " WHERE `id` = '$id' ";
	return $this -> mDb -> getRow($sql); 
}

function deleteSubject($ids) {

    $res = false;

    $sql = "DELETE FROM `subjects` ";
    $sql.= " WHERE `id` = 0";
    foreach ($ids as $id) {

        $query = "SELECT `img` FROM `subjects` WHERE `id` = '{$id}'";
        $result = $this -> mDb -> getOne($query);

        $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'subjects/' . $result;

        if (file_exists($old_img)) {
            @unlink($old_img);
        }

        $sql .= " OR `id` = '{$id}' ";
    }

    $res = $this-> mDb-> query($sql);
    return $res;
}

function getAllSubjects() {
    $sql = "SELECT `id`, `name`, `img` FROM `subjects`";
    return $this -> mDb -> getAll($sql); 
}


}?>