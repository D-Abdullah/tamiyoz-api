<?php

$TestsObj = new Tests();

class Tests {

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

function getSomeTests($aStart, $aLimit, $sort, $type, $searchTitle,$searchCountry,$searchStage,$searchGrade,$searchSemester,$searchSubject) {
	$sql = "SELECT te.id,te.type,te.sort, te.status, te.date_added, te.name, u.full_name AS 'user_full_name' , ";
    $sql .= " cuntl.`name` as 'country_name',";
    $sql .= " stagel.`name` as 'stage_name',";
    $sql .= " gradel.`name` as 'grade_name' , ";
    $sql .= " subject.`name` as 'subject_name'";
    $sql .=" FROM `tests` te";
	$sql .= " LEFT JOIN `users` u ON u.id = te.added_by ";
    $sql .= " LEFT JOIN `subjects` subject ON  ( te.`subject_id` = subject.`id`)";
    $sql .= " LEFT JOIN `grades_langs` gradel ON  ( te.`grade_id` = gradel.`grade_id` AND gradel.`lang_code` = 'ar')";
    $sql .= " LEFT JOIN `countries_langs` cuntl ON  ( te.`country_id` = cuntl.`country_id` AND cuntl.`lang_code` = 'ar') ";
    $sql .= " LEFT JOIN `stages_langs` stagel ON  ( te.`stage_id` = stagel.`stage_id` AND stagel.`lang_code` = 'ar') ";


    $sql.= " WHERE te.id > 0 ";

    $sql.= $searchTitle ? ' AND te.name like "%'.$searchTitle.'%"' : '';
    $sql.= $searchCountry ? " AND te.`country_id` = '{$searchCountry}'" : "";
    $sql.= $searchStage ? " AND te.`stage_id` = '{$searchStage}'" : "";
    $sql.= $searchGrade ? " AND te.`grade_id` = '{$searchGrade}'" : "";
    $sql.= $searchSemester ? " AND te.`semester` = '{$searchSemester}'" : "";
    $sql.= $searchSubject ? " AND te.`subject_id` = '{$searchSubject}'" : "";
	$sql .= " ORDER BY te.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchTestsCount($sort, $type, $searchTitle,$searchCountry,$searchStage,$searchGrade,$searchSemester,$searchSubject) {
	$sql = "SELECT COUNT(te.`id`) as 'result_count' FROM `tests` te";
	$sql .= " WHERE te.`id` > 0 ";

    $sql .= $searchTitle ? ' AND te.`name` like "%'.$searchTitle.'%"' : '';
    $sql.= $searchCountry ? " AND te.`country_id` = '{$searchCountry}'" : "";
    $sql.= $searchStage ? " AND te.`stage_id` = '{$searchStage}'" : "";
    $sql.= $searchGrade ? " AND te.`grade_id` = '{$searchGrade}'" : "";
    $sql.= $searchSemester ? " AND te.`semester` = '{$searchSemester}'" : "";
    $sql.= $searchSubject ? " AND te.`subject_id` = '{$searchSubject}'" : "";

	$sql .= " ORDER BY te.{$sort} {$type}";

//	echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getTestsCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `tests`";
	return $this -> mDb -> getOne($sql); 
}

function addEditTest($temp) {

    $testQ = json_decode($temp['test_questions']);
    $testObj = $this->convert_object_to_array($testQ);

	$user_id = $temp['user_id'];
	$status = $temp['status'] == 'true' ? "1" : "0";
	$id = $temp['id'];

	if ($id === null || $id === ''  || ($id < 1 ) ) {
		// add

		$dateTime = date('Y-m-d H:i:s');

		$sql = "INSERT INTO `tests` SET "; 
        $sql .= " `added_by` = '{$user_id}', ";
        $sql .= " `country_id` = '{$temp['country_id']}', ";
        $sql .= " `stage_id` = '{$temp['stage_id']}', ";
        $sql .= " `grade_id` = '{$temp['grade_id']}', ";
        $sql .= " `semester` = '{$temp['semester']}', ";
        $sql .= " `subject_id` = '{$temp['subject_id']}', ";
        $sql .= $temp['unit_id'] ? " `unit_id` = '{$temp['unit_id']}', ":"";
        $sql .= $temp['lesson_id'] ?" `lesson_id` = '{$temp['lesson_id']}', ":"";
        $sql .= " `type` = '{$temp['type']}', ";
        $sql .= " `name` = '{$temp['name']}', ";
        $sql .= " `status` = '{$status}', ";
        $sql .= " `sort` = '{$temp['sort']}', ";
		$sql .= " `date_added` = '{$dateTime}'";

		$this -> mDb -> query($sql); 
		$last_page_id = $this -> mDb -> getLastInsertId();
if($last_page_id > 0){
		foreach ($testObj as $value) {
			$title = $value['title'];
			if($title !=''){
                $stmt = "INSERT INTO `test_questions` SET ";
                $stmt .= " `test_id` = '{$last_page_id}', ";
                $stmt .= " `title` = '{$title}' ";
                $id = $this -> mDb -> queryreturnlastid($stmt);
                if($id > 0 && count($value['test_question_options']) > 0){
                    for($ii=0;$ii<count($value['test_question_options']) ;$ii++) {
                        $titleo = $value["test_question_options"][$ii]['title'];
                        $statuss =$value["test_question_options"][$ii]['status'] == 'true' ? "1" : "0";
                        $stmto = "INSERT INTO `test_question_options` SET ";
                        $stmto .= " `test_question_id` = '{$id}', ";
                        $stmto .= " `title` = '{$titleo}', ";
                        $stmto .= " `status` = '{$statuss}', ";
                        $stmto .= " `note` = '{$value["test_question_options"][$ii]['note']}' ";

                        $this -> mDb -> query($stmto);
                    }
                }
            }
        }
}
        return $last_page_id;
	}
	else{
		// edit
		$check_query = "SELECT `id` FROM `tests` WHERE `id` = '{$id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            return 403;
        }
        else{

			$sql = "UPDATE `tests` SET ";
            $sql .= " `added_by` = '{$user_id}', ";
            $sql .= " `country_id` = '{$temp['country_id']}', ";
            $sql .= " `stage_id` = '{$temp['stage_id']}', ";
            $sql .= " `grade_id` = '{$temp['grade_id']}', ";
            $sql .= " `semester` = '{$temp['semester']}', ";
            $sql .= " `subject_id` = '{$temp['subject_id']}', ";
            $sql .= $temp['unit_id'] ? " `unit_id` = '{$temp['unit_id']}', ":"`unit_id` = '',";
            $sql .= $temp['lesson_id'] ?" `lesson_id` = '{$temp['lesson_id']}', ":"`lesson_id` = '',";
            $sql .= " `type` = '{$temp['type']}', ";
            $sql .= " `name` = '{$temp['name']}', ";
            $sql .= " `sort` = '{$temp['sort']}', ";
	        $sql .= " `status` = '{$status}' ";
			$sql .= " WHERE `id` = '{$id}'";
            $res=$this -> mDb -> query($sql);
            if($id > 0 && $res > 0){
                foreach ($testObj as $value) {
                    if($value['title'] !=''&& $value['id'] < 1){
                        $stmt = "INSERT INTO `test_questions` SET ";
                        $stmt .= " `test_id` = '{$id}', ";
                        $stmt .= " `title` = '{$value['title']}' ";
                        $qid = $this -> mDb -> queryreturnlastid($stmt);
                        if($qid > 0 && count($value['test_question_options']) > 0){
                            for($ii=0;$ii<count($value['test_question_options']) ;$ii++) {
                                $titleo = $value["test_question_options"][$ii]['title'];
                                $statuss =$value["test_question_options"][$ii]['status'] == 'true' ? "1" : "0";
                                $stmto = "INSERT INTO `test_question_options` SET ";
                                $stmto .= " `test_question_id` = '{$qid}', ";
                                $stmto .= " `title` = '{$titleo}', ";
                                $stmto .= " `status` = '{$statuss}', ";
                                $stmto .= " `note` = '{$value["test_question_options"][$ii]['note']}' ";
                                $this -> mDb -> query($stmto);
                            }
                        }
                    }elseif ($value['title'] !=''&& $value['id'] >= 1){
                        $stmtq = "UPDATE `test_questions` SET ";
                        $stmtq .= " `title` = '{$value['title']}' ";
                        $stmtq .= " WHERE `id`='{$value['id']}' AND `test_id` = '{$id}' ";
                        $this -> mDb -> query($stmtq);
                        if(count($value['test_question_options']) > 0){
                            foreach ($value['test_question_options'] as $testObjOp) {
                                $statuss =$testObjOp['status'] == 'true' ? "1" : "0";
                                if($testObjOp['title'] !='' &&$testObjOp['id'] < 1){
                                    $stmto = "INSERT INTO `test_question_options` SET ";
                                    $stmto .= " `test_question_id` = '{$value['id']}', ";
                                    $stmto .= " `title` = '{$testObjOp['title']}', ";
                                    $stmto .= " `status` = '{$statuss}', ";
                                    $stmto .= " `note` = '{$testObjOp['note']}' ";
                                    $this -> mDb -> query($stmto);
                                }elseif($testObjOp['title'] !='' &&$testObjOp['id'] > 1){
                                    $stmtqup = "UPDATE `test_question_options` SET ";
                                    $stmtqup .= " `title` = '{$testObjOp['title']}', ";
                                    $stmtqup .= " `status` = '{$statuss}', ";
                                    $stmtqup .= " `note` = '{$testObjOp['note']}' ";
                                    $stmtqup .= " WHERE `id`='{$testObjOp['id']}' AND `test_question_id` = '{$value['id']}' ";

                                    $this -> mDb -> query($stmtqup);
                                }
                            }
                        }
                    }
                }
            }
        	return $res;
		}
	}
}

function getOneTest($id) {

	$result = array();

	$sql = "SELECT * FROM `tests`";
	$sql .= " WHERE `id` = '{$id}'";
	$result = $this -> mDb -> getRow($sql); 

	$stmt = "SELECT pl.* FROM `test_questions` pl";
	$stmt .= " WHERE pl.`test_id` = '{$id}'";
	$result['test_questions'] = $this -> mDb -> getAll($stmt);

	if(count($result['test_questions'])){

        for($ii=0;$ii<count($result['test_questions']) ;$ii++) {
            $stmto = "SELECT tqo.* FROM `test_question_options` tqo ";
            $stmto .= " WHERE tqo.`test_question_id` = ".$result['test_questions'][$ii]['id'];
//            echo $stmto;die();
            $result['test_questions'][$ii]['test_question_options'] = $this -> mDb -> getAll($stmto);
        }
    }


	return $result;

}

function deleteTest($ids) {
    
    $tempids='('.implode(',',$ids).')';


    $sql = "SELECT `id` FROM `test_questions`";
    $sql .= " WHERE `test_id` IN ".$tempids;
    $qids = $this -> mDb -> getAll($sql);

    $sqll = "DELETE FROM `test_questions` ";
    $sqll.= " WHERE `test_id` IN ".$tempids;
    $this-> mDb-> query($sqll);


    if(count($qids)>0){
        for ($i=0;$i<count($qids);$i++){
            $sqll = "DELETE FROM `test_question_options` ";
            $sqll.= " WHERE `test_question_id` = '{$qids[$i]['id']}'";
            $this-> mDb-> query($sqll);
        }
    }

    $catsql = "DELETE FROM `tests` ";
    $catsql.= " WHERE `id` IN ".$tempids;

	return  $this-> mDb-> query($catsql);
}
function deleteQuestionOption($ids) {

    $tempids='('.implode(',',$ids).')';
    $sqll = "DELETE FROM `test_question_options` ";
    $sqll.= " WHERE `id` IN ".$tempids;
	return  $this-> mDb-> query($sqll);
}


}?>