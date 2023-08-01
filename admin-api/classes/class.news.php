<?php

$NewsWebObj = new News();

class News {

    var $mDb; 
    var $mConfig; 
    var $mMailer;

    function __construct() {
        global $Config; 
        $this->mDb = new iplus(); 
        $this->mConfig = $Config; 
        $this->mMailer = new Mailer();

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

    function getSomeTrainingCourses($aStart, $aLimit, $sort, $type, $searchName) {

        $sql = "SELECT c.`id`,";
        $sql .= " c.`date_added`,";
        $sql .= " c.`sort`,";
        $sql .= " cl.`name`,";
        $sql .= " ccl.`name` as 'category_name',";
        $sql .= " c.`status`,";
        $sql .= " c.`img`,";
        $sql .= " u.`full_name` AS 'user_full_name'";
        $sql .= " FROM `news` c ";
        $sql .= " LEFT JOIN `news_langs` cl ON c.`id` = cl.`news_id` ";
        $sql .= " LEFT JOIN `categories_langs` ccl ON c.`category_id` = ccl.`category_id` ";
        $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
        $sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar' AND ccl.`lang_code` = 'ar'";
        $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';
        $sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
        $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

//	 echo $sql; die();

        return $this -> mDb -> getAll($sql);
    }

    function getSearchtrainingCoursesCount($sort, $type, $searchName) {
        $sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `news` c";
        $sql .= " LEFT JOIN `news_langs` cl ON c.`id` = cl.`news_id` ";
        $sql .= " WHERE c.`id` > 0 AND cl.`lang_code` = 'ar'";

        $sql .= $searchName ? ' AND cl.`name` like "%'.$searchName.'%"' : '';

        $sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

        //echo $sql; die();

        return $this -> mDb -> getOne($sql);
    }

    function getTrainingCoursesCount() {
        $sql = "SELECT COUNT(`id`) as 'count' FROM `news`";
        return $this -> mDb -> getOne($sql);
    }
    function getTrainingCategories() {
        $result = array();
        $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `categories` c";
        $sql .= " LEFT JOIN `categories_langs` cl ON c.`id` = cl.`category_id` ";
        $sql .= " WHERE  cl.`lang_code`='ar' and c.`category_type`='news'";
//    echo $sql;
        $result = $this -> mDb -> getAll($sql);
        return $result;
    }

    function addEditNews($request, $img) {


        $categoryLangObj = json_decode($request['langs']);
        $newcategoryLangObj = $this->convert_object_to_array($categoryLangObj);

        $langs = $newcategoryLangObj;
        $user_id = $request['user_id'];
        $category_id = $request['category_id'];
        $status = $request['status'] == 'true' ? "1" : "0";
        $id = $request['id'];
        $res = false;


        if ($id < 1 ) {
            // Add
            $dateTime = date('Y-m-d H:i:s');

            $sql = "INSERT INTO `news` SET ";
            $sql .= " `added_by` = '{$user_id}',";
            $sql .= " `status` = '{$status}', ";
            $sql .= " `category_id` = '{$category_id}', ";
            $sql .= " `date_added` = '{$dateTime}' ,";
            $sql .= " `sort` = '{$request['sort']}',";

            $sql .= " `img` = '{$img}'";

            $this -> mDb ->query($sql);
            $last_category_id = $this -> mDb ->getLastInsertId();
            foreach ($langs as $value) {

                $description = addslashes($value['description']);
                $name = addslashes($value['title']);
                $stmt = "INSERT INTO `news_langs` SET ";
                $stmt .= " `news_id` = '{$last_category_id}', ";
                $stmt .= " `lang_code` = '{$value['lang_code']}', ";
                $stmt .= " `name` = '{$name}', ";
                $stmt .= " `description` = '{$description}'";

                $res = $this -> mDb -> query($stmt);
            }
            return $res;

        }
        else
        {

            // Edit

            $check_query = "SELECT `id` FROM `news` WHERE `id` = '{$id}'";
            $check_result = $this -> mDb -> getOne($check_query);

            if ($check_result === false) {

                return 403;

            }
            else{

                $sql = "UPDATE `news` SET ";
                $sql .= " `added_by` = '{$user_id}', ";
                $sql .= " `sort` = '{$request['sort']}', ";
                $sql .= " `category_id` = '{$category_id}',";
                $sql .= " `status` = '{$status}' ";

                if ($img != '' || $img != null) {

                    // Delete old image from the server
                    $img_query = "SELECT `img` FROM `news` WHERE `id` = '{$id}'";
                    $img_result = $this -> mDb -> getOne($img_query);

                    $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'news/' . $img_result;

                    if (file_exists($old_img)) {
                        @unlink($old_img);
                    }

                    $sql .= ",`img`='{$img}' ";
                }

                $sql .= " WHERE `id` = '{$id}'";

                // echo $sql; die();

                $this -> mDb -> query($sql);

                foreach ($newcategoryLangObj as $key => $value) {

                    $stmt = "UPDATE `news_langs` SET";
                    $stmt .=" `name`='{$value['category_name']}', ";
                    $stmt .=" `description`='{$value['category_description']}' ";
                    $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `news_id` = '{$category_id}'";
                    $res = $this -> mDb -> query($stmt);

                }

                foreach ($langs as $value) {

                    $description = addslashes($value['description']);
                    $name = addslashes($value['title']);
                    $stmt = " UPDATE `news_langs` SET ";
                    $stmt .= " `name` = '{$name}', ";
                    $stmt .= " `description` = '{$description}'";
                    $stmt .=" WHERE `lang_code`='{$value['lang_code']}' AND `news_id` = '{$id}'";

                    $res = $this -> mDb -> query($stmt);
                }

                return $res;

            }
        }

    }

    function getOneTrainingCourses($id) {

        $result = array();

        $sql = "SELECT `id`, `added_by`, `status`, `category_id`, `sort`, `img` FROM `news`";
        $sql .= " WHERE `id` = '{$id}'";
        $result = $this -> mDb -> getRow($sql);

        $stmt = "SELECT cl.`lang_code`, cl.`name`, cl.`description` FROM `news_langs` cl";
        $stmt .= " WHERE cl.`news_id` = '{$id}'";
        $result['langs'] = $this -> mDb -> getAll($stmt);

        return $result;

    }




    function deleteTrainingCourses($ids) {

        $tempids='('.implode(',',$ids).')';

        $sql = "SELECT `img` FROM `news`";
        $sql .= " WHERE `id` IN ".$tempids;
        $images = $this -> mDb -> getAll($sql);

        $sqll = "DELETE FROM `news_langs` ";
        $sqll.= " WHERE `news_id` IN ".$tempids;

        $this-> mDb-> query($sqll);

        $catsql = "DELETE FROM `news` ";
        $catsql.= " WHERE `id` IN ".$tempids;

        if($this-> mDb-> query($catsql)){

            if(count($images)>0){
                for ($i=0;$i<count($images);$i++){
                    $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'news/'. $images[$i]['img'];
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