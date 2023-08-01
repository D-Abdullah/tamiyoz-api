<?php

$TrainingCoursesWebObj = new TrainingCourses();

class TrainingCourses
{

    var $mDb;
    var $mConfig;
    var $mMailer;

    function __construct()
    {
        global $Config;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
        $this->mMailer = new Mailer();

    }


    function getMainCategoriesTrainingCourses($lang_code)
    {

        $result = array();

        $sqlq = "SELECT c.id,cl.`name`  FROM `categories` c ";
        $sqlq .= " LEFT JOIN `categories_langs` cl ON c.`id` = cl.`category_id` ";
        $sqlq .= "where c.`status` ='1' AND c.`category_type` ='training'";
        $sqlq .= "and cl.`lang_code`='{$lang_code}'";
        $sqlq .= "order by c.`sort` LIMIT 4";
        $result = $this->mDb->getAll($sqlq);

        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                $sql = "SELECT tc.`id`,tc.`img`,tc.`category_id`,tcl.`name`,tcl.`description` FROM `training_courses` tc ";
                $sql .= " LEFT JOIN `training_courses_langs` tcl ON tc.`id` = tcl.`training_courses_id` ";
                $sql .= " where tc.`status` ='1' AND tc.`category_id` ='{$result[$i]['id']}' ";
                $sql .= "and tcl.`lang_code`='{$lang_code}'";
                $sql .= "order by tc.`sort` LIMIT 6";
                $res = $this->mDb->getAll($sql);
                if (count($res) > 0) {
                    $result[$i]['training_courses'] = $res;
                } else {
                    unset($cat, $result);
                }

            }
        }

        return $result;
    }

    function getAllTrainingCoursesAndNews($lang_code)
    {

        $result = array();

        $sql = "SELECT tc.`id`,tcl.`name` FROM `training_courses` tc ";
        $sql .= " LEFT JOIN `training_courses_langs` tcl ON tc.`id` = tcl.`training_courses_id` ";
        $sql .= " where tc.`status` ='1'  ";
        $sql .= "and tcl.`lang_code`='{$lang_code}'";
        $sql .= "order by tc.`id` DESC LIMIT 2";
        $result['courses'] = $this->mDb->getAll($sql);

        $sql = "SELECT tc.`id`,tcl.`name` FROM `news` tc ";
        $sql .= " LEFT JOIN `news_langs` tcl ON tc.`id` = tcl.`news_id` ";
        $sql .= " where tc.`status` ='1' ";
        $sql .= " and tcl.`lang_code`='{$lang_code}'";
        $sql .= " order by tc.`id` DESC   LIMIT 2 ";
//    echo  $sql;die();
        $result['news'] = $this->mDb->getAll($sql);

        return $result;
    }

    function getTrainingCoursesCount($lang_code)
    {

        $result = array();

        $sql = "SELECT count(tc.id) as 'catcoursesCount' FROM `training_courses` tc ";
        $sql .= " LEFT JOIN `training_courses_langs` tcl ON tc.`id` = tcl.`training_courses_id` ";
        $sql .= " where tc.`status` ='1' ";
        $sql .= "and tcl.`lang_code`='{$lang_code}'";

        $result = $this->mDb->getOne($sql);

        return $result;
    }

    function getAllTrainingCourses($start, $aItemsPerPage, $lang_code)
    {

        $result = array();

        $sql = "SELECT tc.`id`,tc.`img`,tc.`category_id`,tcl.`name`,tcl.`description`,tccl.`name` as 'category_name' FROM `training_courses` tc ";
        $sql .= " LEFT JOIN `training_courses_langs` tcl ON tc.`id` = tcl.`training_courses_id` ";
        $sql .= " LEFT JOIN `categories_langs` tccl ON tc.`category_id` = tccl.`category_id` ";
        $sql .= " where tc.`status` ='1' ";
        $sql .= " and tcl.`lang_code`='{$lang_code}' and tccl.`lang_code`='{$lang_code}'";
        $sql .= " order by tc.`sort` ";
        $sql .= "  LIMIT $start,$aItemsPerPage ";
//                        echo  $sql;die();
        $result = $this->mDb->getAll($sql);

        return $result;
    }

    function getTrainingCourseDetails($id, $lang_code)
    {

        $res = array();
        $sql = "SELECT tc.`id`,tc.`img`,tc.`category_id`,tccl.`name` as 'category_name',tcl.`name`,tcl.`description` FROM          `training_courses` tc ";
        $sql .= " LEFT JOIN `categories_langs` tccl ON tc.`category_id` = tccl.`category_id` ";
        $sql .= " LEFT JOIN `training_courses_langs` tcl ON tc.`id` = tcl.`training_courses_id` ";
        $sql .= " where tc.`status` ='1' AND tc.`id` ='{$id}' ";
        $sql .= "and tcl.`lang_code`='{$lang_code}' AND tccl.`lang_code`='{$lang_code}'";
//                        echo $sql;die();
        $res['courses'] = $this->mDb->getRow($sql);
        $sqll = "SELECT tc.`id`,tc.`img`,tc.`category_id`,tcl.`name`,tcl.`description` FROM `training_courses` tc ";
        $sqll .= " LEFT JOIN `training_courses_langs` tcl ON tc.`id` = tcl.`training_courses_id` ";
        $sqll .= " where tc.`status` ='1' AND tc.`category_id` ='{$res['courses']['category_id']}' ";
        $sqll .= "and tcl.`lang_code`='{$lang_code}'";
        $sqll .= "order by tc.`sort`";


        $res['related'] = $this->mDb->getAll($sqll);
        return $res;
    }


} ?>