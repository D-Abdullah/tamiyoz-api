<?php

$placesObj = new places();

class places {

    var $mDb;
    var $mConfig;
    var $dateTime;
    var $lang_code;

    function places() {
        global $Config;
        // global LANGUAGE; 
        $this->mDb = new iplus();
        $this->mConfig = $Config;
        $this->dateTime = date('Y-m-d H:i:s');
        $this->lang_code = LANGUAGE;
    }

    function getAllPlaces($temp) {

        $sql = "SELECT p.id,p.city_id , pl.pla_name FROM places p ";
        $sql .= " LEFT JOIN `place_langs` pl ON p.id = pl.place_id  AND pl.lang_code = '{$temp['lang']}' ";
        $sql.= " WHERE p.active ='1' and p.city_id ='{$temp['cityId']}'";
        $sql.=$temp['searchName'] ? ' AND pl.pla_name like "%' . $temp['searchName'] . '%"   ' : '';
        $sql .= "ORDER BY  pl.pla_name asc";

        // echo LANGUAGE ;die();
        //  echo $sql; die();

        $res = $this->mDb->getAll($sql);


        return $res;
    }

    
 
    
    
    function getSearchplacesCount($temp) {
        $sql = "SELECT COUNT(p.id) as 'result_count' FROM places p ";
        $sql .= " LEFT JOIN `place_langs` pl ON p.id = pl.place_id ";
        $sql.= " WHERE p.id > 0 AND pl.lang_code = '{$this->lang_code}' ";
        $sql.= $temp['place_type'] ? " AND p.place_type = '{$temp['place_type']}' " : '';
        $sql.= $temp['place_type'] == 'city' ? " AND p.country_id  = '{$temp['parent_id']}'  " : '';

        $sql.=$temp['searchName'] ? ' AND pl.pla_name like "%' . $temp['searchName'] . '%"   ' : '';

        // echo $sql; die();

        return $this->mDb->getOne($sql);
    }

}

?>