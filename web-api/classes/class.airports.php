<?php

$airportsObj = new airports();

class airports
{

    public $mDb;
    public $mConfig;
    public $dateTime;
    public $lang_code;

    public function airports()
    {
        global $Config;
        // global LANGUAGE;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
        $this->dateTime = date('Y-m-d H:i:s');
        $this->lang_code = LANGUAGE;

    }

    public function getAirportsByParams($temp)
    {
        // echo "$temp['place_id']";die();
        $sql = "SELECT a.* , al.airport_name ,pl.pla_name as pla_name, u.user_name AS 'addedBy' FROM airports a ";
        $sql .= " LEFT JOIN `users` u ON u.id = a.admin_id ";
        $sql .= " LEFT JOIN `airport_langs` al ON a.id = al.airport_id ";
        $sql .= " LEFT JOIN `place_langs` pl ON pl.place_id = a.place_id ";

        $sql .= " WHERE a.id > 0 AND al.lang_code = '{$this->lang_code}' ";

        $sql .= $temp['searchName'] ? ' AND al.airport_name like "%' . $temp['searchName'] . '%"  ' : '';
		 $sql .=  $temp['place_id'] ? " AND a.place_id = '{$temp['place_id']}' " : ' ';
       

        $sql .= " Group by a.id ORDER BY a.sort ,al.airport_name asc ";
        // $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
        // echo LANGUAGE ;die();
        //   echo $sql; die();

        $res = $this->mDb->getAll($sql);
        // if ($res) {
        //     for ($i = 0; $i < count($res); $i++) {
        //         $res[$i]['CountOfShipmentsImport'] = $this->getCountOfShipmentsImportByAirportId($res[$i]['id']);
        //         $res[$i]['CountOfShipmentsExport'] = $this->getCountOfShipmentsExportByAirportId($res[$i]['id']);


        //     }
        // }

        return $res;
    }
    // public function getCountOfShipmentsImportByAirportId($id)
    // {
    //     $sql = "SELECT count(id) FROM `shipments`";
    //     $sql .= " WHERE `to_airport_id` = '{$id}' ";
    //     return $this->mDb->getOne($sql)   ? $this->mDb->getOne($sql) : 0   ;
    // }


    // public function getCountOfShipmentsExportByAirportId($id)
    // {
    //     $sql = "SELECT count(id) FROM `shipments`";
    //     $sql .= " WHERE `from_airport_id` = '{$id}' ";
    //     return $this->mDb->getOne($sql)   ? $this->mDb->getOne($sql) : 0   ;
    // }



    public function getSearchAirportsCount($temp)
    {
        $sql = "SELECT COUNT(a.id) as 'result_count' FROM airports a ";
        $sql .= " LEFT JOIN `airport_langs` al ON a.id = al.airport_id ";

        $sql .= " WHERE a.id > 0 AND al.lang_code = '{$this->lang_code}' ";

        $sql .= $temp['searchName'] ? ' AND ( al.airport_name like "%' . $temp['searchName'] . '%" || a.key_word like "%' . $temp['searchName'] . '%" ) ' : '';
        $sql .=  $temp['place_id'] ? " AND a.place_id = '{$temp['place_id']}' " : ' ';

        // echo $sql; die();

        return $this->mDb->getOne($sql);
    }

   

}
