<?php

$projectObj1 = new Project();

class Project
{

    var $mDb;
    var $mConfig;

    function Project()
    {
        global $Config;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
    }

    function convert_object_to_array($data)
    {

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map(__METHOD__, $data);
        } else {
            return $data;
        }
    }

    function getSomeProjects($aStart, $aLimit, $sort, $type, $searchName, $code, $pagename)
    {

        $sql = "SELECT c.`id`,";
        $sql .= " cl.`name`, cl.`description`,";
        // $sql .= " clp.`name` as 'parent_name',";
        $sql .= " c.`date_added`,";
        $sql .= " c.`sort`,";
        $sql .= " c.`url`,";
        $sql .= " c.`parent_id`,";
        $sql .= " c.`status`,  c.`project_type`,";
        $sql .= " c.`img`,";
        $sql .= " u.`full_name` AS 'user_full_name'";
        $sql .= " FROM `projects` c ";
        $sql .= " LEFT JOIN `projects_langs` cl ON c.`id` = cl.`project_id` ";
        $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
        $sql .= " WHERE c.`id` > 0   AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";
        $sql .= $pagename == 'stations' ? "AND  c.`project_type`='stations'" : '';
        $sql .= $pagename == 'projects' ? "AND  c.`project_type`='projects'" : '';
        $sql .= $pagename == 'companies' ? "AND  c.`project_type`='companies'" : '';
        $sql .= $searchName ? ' AND cl.`name` like "%' . $searchName . '%"' : '';
        $sql .= "ORDER BY c.`sort`";
        $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

        //	  echo $sql; die();
        $res = $this->mDb->getAll($sql);
        for ($i = 0; $i < count($res); $i++) {
            $res[$i]['description'] = strip_tags($res[$i]['description']);
            $res[$i]['description'] = html_entity_decode($res[$i]['description']);
            if ($pagename == 'stations' || $pagename == 'companies') {
                $stmt = "SELECT  cl.`img` FROM `projects_images` cl";
                $stmt .= " WHERE cl.`project_id` = '{$res[$i]['id']}'";
                $res[$i]['img'] = $this->mDb->getOne($stmt);
            }
        }
        //  print_r($res);
        //  die();
        return  $res;
    }








    function getstationWithAllRentedShop($shopStatus=0,$code)
    {

        $sql = "SELECT ";
        $sql .= "  sh.`id`,shL.`description`, sh.`station_id`,sh.`img` ,cl.`name` as 'stationName', shL.`name` as shopName,";
        $sql .= " sh.`rented` as 'shopStutas'";
        $sql .= " FROM `shops` sh";
        $sql .= " LEFT JOIN `shops_langs` shL ON (shL.`shop_id` = sh.`id` AND shL.`lang_code` = '{$code}' ) ";
        $sql .= " Inner JOIN `projects` c  ON c.`id` = sh.`station_id`" ;
        $sql .= " LEFT JOIN `projects_langs` cl ON (c.`id` = cl.`project_id` AND cl.`lang_code` = '{$code}')
        where c.`project_type`='stations' AND sh.`status`='1'  AND sh.`rented`='{$shopStatus}'";

        $shops= $this->mDb->getAll($sql);
        if (count($shops) > 0) {
            for ($i = 0; $i < count($shops); $i++) {
                $projects[$shops[$i]['station_id']]['station_id'] = $shops[$i]['station_id'];
                $projects[$shops[$i]['station_id']]['stationName'] = $shops[$i]['stationName'];
                $projects[$shops[$i]['station_id']]['shops'][] = $shops[$i];

            }
        }

        //   for($i=0; $i<count($projects);$i++){

        //     $projects[$i]['station_id']['shops'][$i]['description']=strip_tags( $projects[$i]['station_id']['shops'][$i]['description']);
        //     $projects[$i]['station_id']['shops'][$i]['description']=html_entity_decode( $projects[$i]['station_id']['shops'][$i]['description']);
        // }


        return $projects;
    }













    function getProjectsStations($aStart, $aLimit, $sort, $type, $searchName, $code)
    {

        $sql = "SELECT c.`id`,";
        $sql .= " cl.`name`,";
        // $sql .= " clp.`name` as 'parent_name',";
        $sql .= " c.`date_added`,";
        $sql .= " c.`sort`,";
        $sql .= " c.`parent_id`,";
        $sql .= " c.`status`,";
        $sql .= " c.`img`,c.`project_type`,";
        $sql .= " u.`full_name` AS 'user_full_name'";
        $sql .= " FROM `projects` c ";
        $sql .= " LEFT JOIN `projects_langs` cl ON cl.`project_id` =   c.`id`  ";
        $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
        $sql .= " WHERE c.`id` > 0   AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";
        //        $sql.=" AND (c.`project_type`='stations' OR  c.`project_type`='projects')";
        $sql .= $searchName ? ' AND cl.`name` like "%' . $searchName . '%"' : '';
        $sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";
        $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

        //	  echo $sql; die();

        return $this->mDb->getAll($sql);
    }



















    function getSearchProjectsCount($sort, $type, $searchName, $code, $pagename)
    {
        $sql = "SELECT COUNT(c.`id`) as 'result_count' FROM `projects` c";
        $sql .= " LEFT JOIN `projects_langs` cl ON c.`id` = cl.`project_id` ";
        $sql .= " WHERE c.`id` > 0 AND c.`status`= '1' AND cl.`lang_code` = '{$code}' ";
        $sql .= $pagename == 'stations' ? "AND  c.`project_type`='stations'" : '';
        $sql .= $pagename == 'projects' ? "AND  c.`project_type`='projects'" : '';
        $sql .= $searchName ? ' AND cl.`name` like "%' . $searchName . '%"' : '';

        $sql .= $sort === 'sort' ? " ORDER BY c.`sort` {$type}" : " ORDER BY cl.{$sort} {$type}";

        // echo $sql; die();

        return $this->mDb->getOne($sql);
    }

    function getProjectsCount($pagename)
    {
        $sql = "SELECT COUNT(`id`) as 'count' FROM `projects` where `status`= '1'
                    AND `project_type`='{$pagename}'";
        // echo $sql;
        // die();
        return $this->mDb->getOne($sql);
    }









    function getOneProject($id, $code, $pagename)
    {


        $sql = "SELECT c.`id`, c.`added_by`, c.`status`,c.`url`, c.`sort`,c.`email`,c.`websiteUrl`, cl.`name`, cl.`description`,cl.`address`,c.`img`,c.`lon`,c.`lat`,c.`parent_id`,
    c.`stationfile` FROM `projects` c";
        // $result = $this -> mDb -> getRow($sql);
        $sql .= " LEFT JOIN `projects_langs` cl ON  cl.`project_id`=c.`id`";
        $sql .= " LEFT JOIN `users` u ON u.`id` = c.`added_by` ";
        $sql .= " WHERE c.`id`  = '{$id}'  AND c.`status`= '1'  AND cl.`lang_code` = '{$code}'";
        $result = $this->mDb->getRow($sql);

        $sql = "SELECT `phone_number`  from  `phones`";
        $sql .= " WHERE `company_id`= '{$id}'";
        //    echo $sql;
        //    die();


        $result['phones'] = $this->mDb->getAll($sql);
        //        print_r($result);
        //     die();
        if ($pagename == 'stations' || $pagename == 'companies') {
            $stmt = "SELECT  cl.`img`  FROM `projects_images` cl";
            $stmt .= " WHERE cl.`project_id` = '{$id}'";
            $result['images'] = $this->mDb->getAll($stmt);
        }


        return $result;
    }
    function getCategoryByParentID($id, $type)
    {
        $result = array();
        $sql = "SELECT c.`id`, cl.`name`, cl.`description` FROM `projects` c";
        $sql .= " LEFT JOIN `projects_langs` cl ON c.`id` = cl.`project_id` ";
        $sql .= " WHERE c.`parent_id` = '{$id}' and cl.`lang_code`='ar' and c.`service_type`='{$type}'";
        //    echo $sql;
        $result = $this->mDb->getAll($sql);
        return $result;
    }
}
