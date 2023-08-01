<?php

$ChanceObj = new Chance(); 

class Chance {

var $mDb; 
var $mConfig; 

function Chance() {
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

function getSomeChances($aStart, $aLimit, $sort, $type, $searchName,$service_type) {

	$sql = "SELECT c.`id`,";
    $sql .= " cl.`name`,";
    // $sql .= " clp.`name` as 'parent_name',";
    $sql .= " c.`date_added`,";
    $sql .= " c.`sort`,";
    $sql .= " c.`parent_id`,";
    $sql .= " c.`status`,";
    $sql .= " c.`img`,";
    $sql .= " u.`full_name` AS 'user_full_name'";
    $sql .= " FROM `chances` c ";
    $sql .= " LEFT JOIN `chances_langs` cl ON c.`id` = cl.`chance_id` ";
	$sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
	$sql .= " WHERE c.`id` > 0   AND cl.`lang_code` = 'ar'";
    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
	$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}

function getSearchChancesCount($sort, $type, $searchName,$service_type) {
	$sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `chances` c";
    $sql .= " LEFT JOIN `chances_langs` cl ON c.`id` = cl.`chance_id` ";
	$sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' ";

    $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';

	$sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

	// echo $sql; die();

	return $this -> mDb -> getOne($sql); 
}

function getChancesCount($type) {
    // echo "dsddsdsddsadwerwer";
    //     die();
	$sql = "SELECT COUNT(`id`) as 'count' FROM `chances`";
    // echo $sql;
    // die();
	return $this -> mDb -> getOne($sql); 
}


function addEditChance($temp,$img) {

    $dataLangObj = json_decode($temp['langs']);
    $newDataLangObj = $this->convert_object_to_array($dataLangObj);

	// var_dump($temp); die();

	$user_id = $temp['user_id'];
	$status = $temp['status'] == 'true' ? "1" : "0";
    $sort = $temp['sort'] ;

	$id = $temp['id'];

	if ($id  < 1) {
		// add

		$dateTime = date('Y-m-d H:i:s');

		$sql = "INSERT INTO `chances` SET "; 
        $sql .= " `added_by` = '{$user_id}', ";
        $sql .= " `status` = '{$status}', ";
        $sql .= " `sort` = '{$sort}', ";
		$sql .= " `date_added` = '{$dateTime}' ,";
        $sql .= " `img` = '{$img}'";
        // echo $sql;
        // die(); 
		$this -> mDb -> query($sql);
        
		$last_chance_id = $this -> mDb -> getLastInsertId();
       
		foreach ($newDataLangObj as $key => $value) {
            $description = addslashes($value['description']);
			$title = addslashes($value['title']);
			// $sub_title = addslashes($value['sub_title']);

            $stmt = "INSERT INTO `chances_langs` SET "; 
	        $stmt .= " `chance_id` = '{$last_chance_id}', ";
	        $stmt .= " `lang_code` = '{$value['lang_code']}', ";
	        $stmt .= " `name` = '{$title}', ";
	        // $stmt .= " `sub_title` = '{$sub_title}', ";
	        $stmt .= " `description` = '{$description}'";
          
			$res = $this -> mDb -> query($stmt); 
            
        }
        return $last_chance_id;
	}
	else{
		// edit
		$check_query = "SELECT `id` FROM `chances` WHERE `id` = '{$id}'";
        $check_result = $this -> mDb -> getOne($check_query);
        if ($check_result === false) {
            return 403;
        }
        else{
			$sql = "UPDATE `chances` SET "; 
			$sql .= " `added_by` = '{$user_id}', ";

	        $sql .= " `status` = '{$status}' ";
            if ($img != '' || $img != null) {

                // Delete old image from the server
                $img_query = "SELECT `img` FROM `chances` WHERE `id` = '{$id}'";
                $img_result = $this -> mDb -> getOne($img_query);

                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'chances/' . $img_result;

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
				// $sub_title = addslashes($value['sub_title']);
	            $stmt = "UPDATE `chances_langs` SET "; 
		        $stmt .= " `name` = '{$title}', ";
		        // $stmt .= " `sub_title` = '{$sub_title}', ";
		        $stmt .= " `description` = '{$description}'";
		        $stmt .= " WHERE `lang_code`='{$value['lang_code']}' AND `chance_id` = '{$id}' ";
				$res = $this -> mDb -> query($stmt);
	        }
        	return $res;
		}
	}
}





// function addEditChance($request, $img, $chance_id) {

//     // print_r($request); die();

//     $categoryLangObj = json_decode($request['categoryLangObj']);
//     $newcategoryLangObj = $this->convert_object_to_array($categoryLangObj);

//     unset($request['action']);
//     unset($request['categoryLangObj']);

//     $res = false; 

//     if ($chance_id === '' && $request['operation'] === 'add')
//     {
//         // Add
//         unset($request['operation']);
//         $dateTime = date('Y-m-d H:i:s');

//         $sql = "INSERT INTO `chances` SET "; 
//         $sql .= " `added_by` = '{$request['added_by']}',"; 
//         $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',"; 
//         $sql .= $request['service_type'] === 'training' ? " `service_type` = 'training'," : "";
//         $sql .= " `date_added` = '{$dateTime}',";
//         $sql .= " `sort` = '{$request['sort']}',"; 
//         $sql .= " `parent_id` = '{$request['parent_id']}',";
//         $sql .= " `img` = '{$img}'";
// //         echo $sql; die();
//         $this -> mDb ->query($sql);
//         $last_chance_id = $this -> mDb ->getLastInsertId();
//         foreach ($newcategoryLangObj as $key => $value) {
//             $stmt = "INSERT INTO `chances_langs`(`chance_id`, `lang_code`, `name`, `description`) ";
//             $stmt .=" VALUES ('{$last_chance_id}', '{$value['lang_code']}', '{$value['category_name']}', '{$value['category_description']}')";
//             $res = $this -> mDb -> query($stmt);
//         }

//             return $res;

//     }
//     else
//     {

//         // Edit

//         $check_query = "SELECT `id` FROM `chances` WHERE `id` = '{$chance_id}'";
//         $check_result = $this -> mDb -> getOne($check_query);

//         if ($check_result === false) {
            
//             return 403;

//         }
//         else{
                
//             $sql = "UPDATE `chances` SET "; 
//             $sql .= " `added_by` = '{$request['added_by']}', ";
//             $sql .= " `sort` = '{$request['sort']}', ";
//             $sql .= " `parent_id` = '{$request['parent_id']}',";
//             $sql .= $request['status'] === 'true' ? " `status` = '1'" : " `status` = '0'";

//             if ($img != '' || $img != null) {

//                 // Delete old image from the server
//                 $img_query = "SELECT `img` FROM `chances` WHERE `id` = '{$chance_id}'";
//                 $img_result = $this -> mDb -> getOne($img_query);

//                 $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'chances/' . $img_result;

//                 if (file_exists($old_img)) {
//                     @unlink($old_img);
//                 }

//                 $sql .= ",`img`='{$img}' ";
//             }

//             $sql .= " WHERE `id` = '{$chance_id}'";

//             // echo $sql; die();

//             $this -> mDb -> query($sql);

//             foreach ($newcategoryLangObj as $key => $value) {

//                 $stmt = "UPDATE `chances_langs` SET";
//                 $stmt .=" `name`='{$value['category_name']}', ";
//                 $stmt .=" `description`='{$value['  ']}' ";
//                 $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `chance_id` = '{$chance_id}'";
//                 $res = $this -> mDb -> query($stmt);
                
//             }

//                 return $res;

//         } 
//     }

// }

function getOneChance($id) {

    $result = array();

    $sql = "SELECT `id`, `added_by`, `status`, `sort`, `img`,`parent_id` FROM `chances`";
    $sql .= " WHERE `id` = '{$id}'";
    $result = $this -> mDb -> getRow($sql); 

    $stmt = "SELECT cl.`lang_code`, cl.`name`, cl.`description` FROM `chances_langs` cl";
    $stmt .= " WHERE cl.`chance_id` = '{$id}'";
    $result['langs'] = $this -> mDb -> getAll($stmt);

    return $result;

}
function getCategoryByParentID($id,$type) {
    $result = array();
    $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `chances` c";
    $sql .= " LEFT JOIN `chances_langs` cl ON c.`id` = cl.`chance_id` ";
    $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' and c.`service_type`='{$type}'";
//    echo $sql;
    $result = $this -> mDb -> getAll($sql);
    return $result;

}



function deleteChance($ids) {

    $tempids='('.implode(',',$ids).')';

    $sql = "SELECT `img` FROM `chances`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    $sqll = "DELETE FROM `chances_langs` ";
    $sqll.= " WHERE `chance_id` IN ".$tempids;

    $this-> mDb-> query($sqll);

    $catsql = "DELETE FROM `chances` ";
    $catsql.= " WHERE `id` IN ".$tempids;

    if($this-> mDb-> query($catsql)){

        if(count($images)>0){
            for ($i=0;$i<count($images);$i++){
                $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'chances/'. $images[$i]['img'];
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