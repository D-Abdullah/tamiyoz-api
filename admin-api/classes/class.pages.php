<?php

$pagesObj = new pages(); 

class pages {

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
function getSomePages($aStart, $aLimit, $sort, $type, $searchTitle) {
	$sql = "SELECT p.id, p.status, p.date_added, pl.title, pl.sub_title, u.full_name AS 'user_full_name' FROM pages p";
	$sql .= " LEFT JOIN `users` u ON u.id = p.user_id ";
	$sql .= " LEFT JOIN `pages_langs` pl ON p.id = pl.page_id ";
	$sql.= " WHERE p.id > 0 AND pl.lang_code = 'ar'";

    $sql.= $searchTitle ? ' AND pl.title like "%'.$searchTitle.'%"' : '';

	$sql .= " ORDER BY pl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchPagesCount($sort, $type, $searchTitle) {
	$sql = "SELECT COUNT(p.`id`) as 'result_count' FROM pages p";
	$sql .= " LEFT JOIN `pages_langs` pl ON p.id = pl.page_id ";
	$sql .= " WHERE p.`id` > 0 AND pl.lang_code = 'ar'";

    $sql .= $searchTitle ? ' AND pl.`title` like "%'.$searchTitle.'%"' : '';

	$sql .= " ORDER BY pl.{$sort} {$type}";

	//echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getPagesCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `pages`";
	return $this -> mDb -> getOne($sql); 
}

function addEditPage($temp,$img) {

    $dataLangObj = json_decode($temp['langs']);
    $newDataLangObj = $this->convert_object_to_array($dataLangObj);

// 	var_dump($temp); die();

	$user_id = $temp['user_id'];
	$status = $temp['status'] == 'true' ? "1" : "0";
	$id = $temp['id'];

	if ($id  < 1) {
		// add

		$dateTime = date('Y-m-d H:i:s');

		$sql = "INSERT INTO `pages` SET "; 
        $sql .= " `user_id` = '{$user_id}', ";
        $sql .= " `status` = '{$status}', ";
		$sql .= " `date_added` = '{$dateTime}' ,";
        $sql .= " `img` = '{$img}'";

		$this -> mDb -> query($sql);

		$last_page_id = $this -> mDb -> getLastInsertId();

		foreach ($newDataLangObj as $key => $value) {
            $description = addslashes($value['description']);
			$title = addslashes($value['title']);
			$sub_title = addslashes($value['sub_title']);

            $stmt = "INSERT INTO `pages_langs` SET "; 
	        $stmt .= " `page_id` = '{$last_page_id}', ";
	        $stmt .= " `lang_code` = '{$value['lang_code']}', ";
	        $stmt .= " `title` = '{$title}', ";
	        $stmt .= " `sub_title` = '{$sub_title}', ";
	        $stmt .= " `description` = '{$description}'";
			$res = $this -> mDb -> query($stmt); 
            
        }
        return $last_page_id;
	}
	else{
		// edit
		$check_query = "SELECT `id` FROM `pages` WHERE `id` = '{$id}'";
        $check_result = $this -> mDb -> getOne($check_query);
        if ($check_result === false) {
            return 403;
        }
        else{
			$sql = "UPDATE `pages` SET "; 
			$sql .= " `user_id` = '{$user_id}', ";

	        $sql .= " `status` = '{$status}' ";
            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `img` FROM `pages` WHERE `id` = '{$id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'pages/' . $img_result;

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
				$sub_title = addslashes($value['sub_title']);
	            $stmt = "UPDATE `pages_langs` SET "; 
		        $stmt .= " `title` = '{$title}', ";
		        $stmt .= " `sub_title` = '{$sub_title}', ";
		        $stmt .= " `description` = '{$description}'";
		        $stmt .= " WHERE `lang_code`='{$value['lang_code']}' AND `page_id` = '{$id}' ";
//               echo $stmt;
//               die();
				$res = $this -> mDb -> query($stmt);
	        }

        	return $res;
		}
	}
}

function getOnePage($id) {

	$result = array();

	$sql = "SELECT `id`, `user_id`, `status` FROM `pages`";
	$sql .= " WHERE `id` = '{$id}'";
	$result = $this -> mDb -> getRow($sql); 

	$stmt = "SELECT pl.`lang_code`, pl.`title`, pl.`sub_title`, pl.`description` FROM `pages_langs` pl";
	$stmt .= " WHERE pl.`page_id` = '{$id}'";
	$result['langs'] = $this -> mDb -> getAll($stmt);

	return $result;

}

function deletePage($ids) {
    
    $tempids='('.implode(',',$ids).')';

    $sqll = "DELETE FROM `pages_langs` ";
    $sqll.= " WHERE `page_id` IN ".$tempids;
    $this-> mDb-> query($sqll);


    $sql = "SELECT `img` FROM `pages`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    if(count($images)>0){
        for ($i=0;$i<count($images);$i++){
            $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'pages/'. $images[$i]['img'];
            if (file_exists($old_img)) {
                @unlink($old_img);
            }
        }
    }

    $catsql = "DELETE FROM `pages` ";
    $catsql.= " WHERE `id` IN ".$tempids;

	return  $this-> mDb-> query($catsql);
}


}?>