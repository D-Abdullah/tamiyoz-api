<?php

$statisticsObj = new Statistics(); 

class Statistics {

var $mDb; 
var $mConfig; 

function Statistics() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config; 
}

function getHomeDriversLocations() {

    $result = array();
    $datenow= date('Y-m-d H:i:s');
    $yesterday=date('Y-m-d H:i:s',strtotime($datenow. ' - 1 days'));

    $sql = "SELECT ul.`user_id`,ul.`lat`,ul.`lon`,u.`full_name` FROM `user_location` ul";
    $sql .=" LEFT JOIN `users` u ON u.`id` = ul.`user_id` ";
    $sql .= " WHERE u.`id` > 0 AND u.`status`='1' AND u.`user_type` ='representative' AND  ( ul.`date_updated` BETWEEN '{$yesterday}'  AND '{$datenow}' )";
//  echo  $sql;die();
    $result = $this -> mDb -> getAll($sql);
    return $result;
}

function getHomeStatistics() {

	$result = array();
    $sql_chances= "SELECT COUNT(`id`) FROM `chances`";
    $result['chances'] = $this -> mDb -> getOne($sql_chances);

    $sql_partners = "SELECT COUNT(`id`) FROM `partners`";
    $result['partners'] = $this -> mDb -> getOne($sql_partners);


    $sql_projects = "SELECT COUNT(`id`) FROM `projects` where `project_type`='projects'";
    $result['projects'] = $this -> mDb -> getOne($sql_projects);

    $sql_services = "SELECT COUNT(`id`) FROM `services`";
    $result['services'] = $this -> mDb -> getOne($sql_services);


    $sql_shops = "SELECT COUNT(`id`) FROM `shops`";
    $result['shops'] = $this -> mDb -> getOne($sql_shops);


    $sql_stations = "SELECT COUNT(`id`) FROM `projects` where `project_type`='stations'";
    $result['stations'] = $this -> mDb -> getOne($sql_stations);







//    $sql_products = "SELECT COUNT(`id`) FROM `products`";
//	$result['products'] = $this -> mDb -> getOne($sql_products);

//	$sql_categories = "SELECT COUNT(`id`) FROM `categories`";
//	$result['categories'] = $this -> mDb -> getOne($sql_categories);

	$sql_pages = "SELECT COUNT(`id`) FROM `pages`";
	$result['pages'] = $this -> mDb -> getOne($sql_pages);

//    $sql_pages1 = "SELECT COUNT(`id`) FROM `units`";
//	$result['units'] = $this -> mDb -> getOne($sql_pages1);


    $sql_languages = "SELECT COUNT(`id`) FROM `languages`";
	$result['languages'] = $this -> mDb -> getOne($sql_languages);

//	$sql_countries = "SELECT COUNT(`id`) FROM `countries`";
//
//	$result['countries'] = $this -> mDb -> getOne($sql_countries);


//	$sql_members = "SELECT COUNT(`id`) FROM `users` WHERE `user_type` IS NOT NULL";
//	$result['members'] = $this -> mDb -> getOne($sql_members);

	$sql_management = "SELECT COUNT(`id`) FROM `users` WHERE `user_level` IS NOT NULL  ";
	$result['management'] = $this -> mDb -> getOne($sql_management); 

	$sql_user_levels = "SELECT COUNT(`id`) FROM `user_levels` ";
	$result['user_levels'] = $this -> mDb -> getOne($sql_user_levels); 
//
//	$sql_stages = "SELECT COUNT(`id`) FROM `stages`";
//	$result['stages'] = $this -> mDb -> getOne($sql_stages);

//    $sql_grades = "SELECT COUNT(`id`) FROM `grades`";
//    $result['grades'] = $this -> mDb -> getOne($sql_grades);


    return $result;
}


}?>