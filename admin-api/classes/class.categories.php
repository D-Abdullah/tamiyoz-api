<?php

$categoryObj = new Categories(); 

class Categories {

var $mDb; 
var $mConfig; 

function Categories() {
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

function getSomeCategories($aStart, $aLimit, $sort, $type, $searchName,$category_type) {

	$sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,";
    $sql .= " clp.`name` as 'parent_name',";
    $sql .= " c.`date_added`,";
    $sql .= " c.`sort`,";
    $sql .= " c.`parent_id`,";
    $sql .= " c.`status`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `categories` c ";
    $sql .= " LEFT JOIN `categories_langs` cl ON c.`id` = cl.`category_id` ";
    $sql .= " LEFT JOIN `categories_langs` clp ON c.`parent_id` = clp.`category_id` ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0 AND c.`category_type` = '{$category_type}' AND cl.`lang_code` = 'ar' and ((clp.`lang_code`='ar' and c.`parent_id` !='0') OR (c.`parent_id`='0'))";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchCategoriesCount($sort, $type, $searchName,$category_type) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `categories` c";
    $sql .= " LEFT JOIN `categories_langs` cl ON c.`id` = cl.`category_id` ";
	$sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' AND c.`category_type` = '{$category_type}'";

    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';

	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	//echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getCategoriesCount($type) {
	$sql = "SELECT COUNT(`id`) as 'count' FROM `categories` where `category_type`='{$type}'";
	return $this -> mDb -> getOne($sql); 
}

function addEditCategory($request, $img, $category_id) {

    // print_r($request); die();

    $categoryLangObj = json_decode($request['categoryLangObj']);
    $newcategoryLangObj = $this->convert_object_to_array($categoryLangObj);

    unset($request['action']);
    unset($request['categoryLangObj']);

    $res = false; 

    if ($category_id === '' && $request['operation'] === 'add')
    {
        // Add
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `categories` SET "; 
        $sql .= " `added_by` = '{$request['added_by']}',"; 
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',"; 
        $sql .= $request['category_type'] === 'training' ? " `category_type` = 'training'," : "";
        $sql .= " `date_added` = '{$dateTime}',";
        $sql .= " `sort` = '{$request['sort']}',"; 
        $sql .= " `parent_id` = '{$request['parent_id']}',";
        $sql .= " `img` = '{$img}'";
//         echo $sql; die();
        $this -> mDb ->query($sql);
        $last_category_id = $this -> mDb ->getLastInsertId();
        foreach ($newcategoryLangObj as $key => $value) {
            $stmt = "INSERT INTO `categories_langs`(`category_id`, `lang_code`, `name`, `description`) ";
            $stmt .=" VALUES ('{$last_category_id}', '{$value['lang_code']}', '{$value['category_name']}', '{$value['category_description']}')";
            $res = $this -> mDb -> query($stmt);
        }

            return $res;

    }
    else
    {

        // Edit

        $check_query = "SELECT `id` FROM `categories` WHERE `id` = '{$category_id}'";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            
            return 403;

        }
        else{
                
            $sql = "UPDATE `categories` SET "; 
            $sql .= " `added_by` = '{$request['added_by']}', ";
            $sql .= " `sort` = '{$request['sort']}', ";
            $sql .= " `parent_id` = '{$request['parent_id']}',";
            $sql .= $request['status'] === 'true' ? " `status` = '1'" : " `status` = '0'";

            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `img` FROM `categories` WHERE `id` = '{$category_id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'categories/' . $img_result;

                if (file_exists($old_img)) {
                    @unlink($old_img);
                }

                $sql .= ",`img`='{$img}' ";
            }

            $sql .= " WHERE `id` = '{$category_id}'";

            // echo $sql; die();

            $this -> mDb -> query($sql);

            foreach ($newcategoryLangObj as $key => $value) {

                $stmt = "UPDATE `categories_langs` SET";
                $stmt .=" `name`='{$value['category_name']}', ";
                $stmt .=" `description`='{$value['category_description']}' ";
                $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `category_id` = '{$category_id}'";
                $res = $this -> mDb -> query($stmt);
                
            }

                return $res;

        } 
    }

}

function getOneCategory($id) {

    $result = array();

    $sql = "SELECT `id`, `added_by`, `status`, `sort`, `img`,`parent_id` FROM `categories`";
    $sql .= " WHERE `id` = '{$id}'";
    $result = $this -> mDb -> getRow($sql); 

    $stmt = "SELECT cl.`lang_code`, cl.`name`, cl.`description` FROM `categories_langs` cl";
    $stmt .= " WHERE cl.`category_id` = '{$id}'";
    $result['langs'] = $this -> mDb -> getAll($stmt);

    return $result;

}
function getCategoryByParentID($id,$type) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `categories` c";
    $sql .= " LEFT JOIN `categories_langs` cl ON c.`id` = cl.`category_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' and c.`category_type`='{$type}'";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}



function deleteCategory($ids) {

    $tempids='('.implode(',',$ids).')';

    $sql = "SELECT `img` FROM `categories`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    $sqll = "DELETE FROM `categories_langs` ";
    $sqll.= " WHERE `category_id` IN ".$tempids;

    $this-> mDb-> query($sqll);

    $catsql = "DELETE FROM `categories` ";
    $catsql.= " WHERE `id` IN ".$tempids;

    if($this-> mDb-> query($catsql)){

        if(count($images)>0){
            for ($i=0;$i<count($images);$i++){
                $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'categories/'. $images[$i]['img'];
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