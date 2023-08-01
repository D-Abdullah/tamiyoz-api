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


    function checkCatProductsExist($request) {
        $category_id = trim($request['category_id']);

        $sql = "SELECT COUNT(`id`) FROM `products` ";
        $sql.= " WHERE `category_id` = '{$category_id}' ";
        return $this->mDb->getOne($sql);
    }

    function getAllNewsCount($lang_code) {

        $result=array();

        $sql = "SELECT count(tc.id) as 'newsCount' FROM `news` tc ";
        $sql .= " LEFT JOIN `news_langs` tcl ON tc.`id` = tcl.`news_id` ";
        $sql.= " where tc.`status` ='1' ";
        $sql.= "and tcl.`lang_code`='{$lang_code}'";

        $result=$this -> mDb -> getOne($sql);

        return $result;
    }

    function getAllNews($start,$aItemsPerPage,$lang_code) {

        $result=array();

        $sql = "SELECT tc.`id`,tc.`img`,tc.date_added,tc.`category_id`,tcl.`name`,tcl.`description`,tccl.`name` as 'category_name' FROM `news` tc ";
        $sql .= " LEFT JOIN `news_langs` tcl ON tc.`id` = tcl.`news_id` ";
        $sql .= " LEFT JOIN `categories_langs` tccl ON tc.`category_id` = tccl.`category_id` ";
        $sql.= " where tc.`status` ='1' ";
        $sql.= " and tcl.`lang_code`='{$lang_code}' and tccl.`lang_code`='{$lang_code}'";
        $sql.= " order by tc.`sort` ";
        $sql.= "  LIMIT $start,$aItemsPerPage ";
//                        echo  $sql;die();
        $result=$this -> mDb -> getAll($sql);

        return $result;
    }




    function getAllLastestNews($lang_code) {

        $result=array();

        $sql = "SELECT n.`id`,n.`img`,n.`category_id`,n.`date_added`,nl.`name`,nl.`description`,cl.`name` as 'category_name' FROM `news` n ";
        $sql .= " LEFT JOIN `news_langs` nl ON n.`id` = nl.`news_id` ";
        $sql .= " LEFT JOIN `categories_langs` cl ON n.`category_id` = cl.`category_id` ";
        $sql.= " where n.`status` ='1' ";
        $sql.= "and nl.`lang_code`='{$lang_code}' AND cl.`lang_code` = '{$lang_code}' ";
        $sql.= "order by n.`sort`";
        $res=$this -> mDb -> getAll($sql);
        return $res;
    }


    function getOneNewsDetails($id,$lang_code) {

        $res=array();
        $sql = "SELECT tc.`id`,tc.`img`,tc.`category_id`,tc.`date_added`,tccl.`name` as 'category_name',tcl.`name`,tcl.`description` FROM  `news` tc ";
        $sql .= " LEFT JOIN `categories_langs` tccl ON tc.`category_id` = tccl.`category_id` ";
        $sql .= " LEFT JOIN `news_langs` tcl ON tc.`id` = tcl.`news_id` ";
        $sql.= " where tc.`status` ='1' AND tc.`id` ='{$id}' ";
        $sql.= "and tcl.`lang_code`='{$lang_code}' AND tccl.`lang_code`='{$lang_code}'";
        //   echo $sql;die();
        $res=$this -> mDb -> getRow($sql);

        return $res;
    }





}?>