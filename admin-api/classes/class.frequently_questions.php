<?php

$frequently_questionsObj = new FrequentlyQuestions();

class FrequentlyQuestions {

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

function getSomeFrequentlyQuestions($aStart, $aLimit, $sort, $type, $searchName) {
	$sql = "SELECT fq.id, fq.status, fq.sort, fq.date_added, fql.`title`, fql.`description`, u.full_name AS 'user_full_name' FROM `frequently_questions` fq";
	$sql .= " LEFT JOIN `users` u ON u.id = fq.added_by ";
    $sql .= " LEFT JOIN `frequently_questions_langs` fql ON (fq.`id` = fql.`question_id` AND fql.`lang_code`='ar') ";
	$sql.= " WHERE fq.id > 0";
    $sql.= $searchName ? ' AND fql.title like "%'.$searchName.'%"' : '';
	$sql .= " ORDER BY fq.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchFrequentlyQuestionsCount($sort, $type, $searchName) {
	$sql = "SELECT COUNT(fq.`id`) as 'result_count' FROM `frequently_questions` fq";
    $sql .= " LEFT JOIN `frequently_questions_langs` fql ON (fq.`id` = fql.`question_id` AND fql.`lang_code`='ar') ";
	$sql.= " WHERE fq.`id` > 0";
    $sql.= $searchName ? ' AND fql.`title` like "%'.$searchName.'%"' : '';
	$sql .= " ORDER BY fq.{$sort} {$type}";

//	echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getFrequentlyQuestionsCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `frequently_questions`";
	return $this -> mDb -> getOne($sql); 
}

function addEditFrequentlyQuestion($request, $question_id) {

//    print_r($request); die();
    $dataLangObj = json_decode($request['LangObj']);
    $newDataLangObj = $this->convert_object_to_array($dataLangObj);

    unset($request['action']);

    if ($question_id === '')
    {
        // Add

        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s');
        $sql = "INSERT INTO `frequently_questions` SET ";
        $sql .= " `added_by` = '{$request['user_id']}',";
        $sql .= " `sort` = '{$request['sort']}',";
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= " `date_added` = '{$dateTime}' ";
        // echo $sql; die();
        $res=$this -> mDb -> query($sql);
        $last_stage_id = $this -> mDb ->getLastInsertId();
        if($last_stage_id >0){
            foreach ($newDataLangObj as $key => $value) {
                $stmt = "INSERT INTO `frequently_questions_langs`(`question_id`, `lang_code`, `title`, `description`) ";
                $stmt .=" VALUES ('{$last_stage_id}', '{$value['lang_code']}', '{$value['title']}', '{$value['description']}')";
                $this -> mDb -> query($stmt);
            }
        }
        return $last_stage_id;
    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `frequently_questions` WHERE `id` = '{$question_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `frequently_questions` SET ";
            $sql .= " `added_by` = '{$request['user_id']}',";
            $sql .= " `sort` = '{$request['sort']}',";
            $sql .= $request['status'] === 'true' ? " `status` = '1' " : " `status` = '0' ";
            $sql .= " WHERE `id` = '{$question_id}'";
           $res= $this -> mDb -> query($sql);

            foreach ($newDataLangObj as $key => $value) {
                $stmt = "UPDATE `frequently_questions_langs` SET";
                $stmt .=" `title`='{$value['title']}' ,";
                $stmt .=" `description`='{$value['description']}' ";
                $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `question_id` = '{$question_id}'";
                $this -> mDb -> query($stmt);
            }

            return $res;

        } 
    }

}

function getOneQuestion($id) {
	$sql = "SELECT fq.`id`, fq.`added_by`, fq.status, fq.sort, fq.`date_added` FROM `frequently_questions` fq";
	$sql .= " WHERE fq.`id` = '$id' ";
    $result=$this -> mDb -> getRow($sql);
	if($result){
        $stmt = "SELECT fql.`lang_code`, fql.`title`, fql.`description` FROM `frequently_questions_langs` fql";
        $stmt .= " WHERE fql.`question_id` = '{$id}'";
        $result['langs'] = $this -> mDb -> getAll($stmt);
    }
    return $result;
}

function deleteFrequentlyQuestion($ids) {

    $res = false;
    $tempids='('.implode(',',$ids).')';

    $sqll = "DELETE FROM `frequently_questions_langs` ";
    $sqll.= " WHERE `question_id` IN ".$tempids;
    $this-> mDb-> query($sqll);

    $catsql = "DELETE FROM `frequently_questions` ";
    $catsql.= " WHERE `id` IN ".$tempids;

    $res= $this-> mDb-> query($catsql);
    return $res;
}


function getAllFrequentlyQuestions() {
    $sql = "SELECT `id`, `title`, `code`, `lang_image` FROM `frequently_questions`";
    return $this -> mDb -> getAll($sql); 
}


}?>