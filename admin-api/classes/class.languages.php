<?php

$langObj = new languages(); 

class languages {

var $mDb; 
var $mConfig; 

function languages() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config; 
}

function getSomeLanguages($aStart, $aLimit, $sort, $type, $searchName) {
	$sql = "SELECT lan.id, lan.name, lan.code, lan.date_added, lan.lang_image, u.full_name AS 'user_full_name' FROM `languages` lan";
	$sql .= " LEFT JOIN `users` u ON u.id = lan.user_id ";
	$sql.= " WHERE lan.id > 0";

    $sql.= $searchName ? ' AND lan.name like "%'.$searchName.'%"' : '';

	$sql .= " ORDER BY lan.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

	// echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchLanguagesCount($sort, $type, $searchName) {
	$sql = "SELECT COUNT(`id`) as 'result_count' FROM `languages`";
	$sql.= " WHERE `id` > 0";

    $sql.= $searchName ? ' AND `name` like "%'.$searchName.'%"' : '';

	$sql .= " ORDER BY {$sort} {$type}";

	//echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getLanguagesCount() {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `languages`";
	return $this -> mDb -> getOne($sql); 
}

function addEditLanguage($request, $img, $lang_id) {

    //var_dump($request); die();

    unset($request['action']);

    if ($lang_id === '')
    {
        // Add
        unset($request['userType']);
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s'); 

        $sql = "INSERT INTO `languages` SET "; 
        foreach ($request as $k => $v) {
            $sql .= "`{$k}`='{$v}',";
        }
        $sql .= " `date_added` = '{$dateTime}',"; 
        $sql .= " `lang_image` = '{$img}'"; 

        // echo $sql; die();

        return $this -> mDb -> query($sql); 
    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `languages` WHERE `id` = '{$lang_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `languages` SET "; 
            $sql .= "`name`='{$request['name']}',`code`='{$request['code']}'";

            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `lang_image` FROM `languages` WHERE `id` = '{$lang_id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'languages/' . $img_result;

                if (file_exists($old_img)) {
                    @unlink($old_img);
                }

                $sql .= ",`lang_image`='{$img}' ";
            }

            $sql .= " WHERE `id` = '{$lang_id}'"; 

            return $this -> mDb -> query($sql);

        } 
    }

}

function getOneLanguage($id) {
	$sql = "SELECT `id`, `user_id`, `name`, `code`, `lang_image`, `date_added` FROM `languages`";
	$sql .= " WHERE `id` = '$id' ";
	return $this -> mDb -> getRow($sql); 
}

function deleteLanguage($ids) {

    $res = false;

    $sql = "DELETE FROM `languages` ";
    $sql.= " WHERE `id` = 0";
    foreach ($ids as $id) {

        $query = "SELECT `lang_image` FROM `languages` WHERE `id` = '{$id}'";
        $result = $this -> mDb -> getOne($query);

        $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'languages/' . $result;

        if (file_exists($old_img)) {
            @unlink($old_img);
        }

        $sql .= " OR `id` = '{$id}' ";
    }

    $res = $this-> mDb-> query($sql);
    return $res;
}

function getAllLanguages() {
    $sql = "SELECT `id`, `name`, `code`, `lang_image` FROM `languages`";
    return $this -> mDb -> getAll($sql); 
}


}?>