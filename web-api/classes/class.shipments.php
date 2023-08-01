<?php

$shipmentsObj = new shipments();

class shipments
{

    public $mDb;
    public $mConfig;
    public $dateTime;
    public $lang_code;
    public $offersObj;

    public function shipments()
    {
        global $Config;
        // global LANGUAGE;
   
        $this->mDb = new iplus();
        $this->mConfig = $Config;
        $this->dateTime = date('Y-m-d H:i:s');
        $this->lang_code = LANGUAGE;

    }

    public function getShipmentsByParams($temp)
    {

        if ($temp['shipment_status'] == 'waiting') {
            $awaitingOffer = $this->getAwaitingOfferShipments($temp['user_id']);
            $awaitingOfferString = '';

            for ($i = 0; $i < count($awaitingOffer); $i++) {
                # code...
                $awaitingOfferString = $awaitingOffer[$i]['id'] . "," . $awaitingOfferString;
            }
            $awaitingOfferString = substr($awaitingOfferString, 0, -1);
        }
        //  echo "<pre>";print_r($awaitingOfferString);die();

        // echo "$place_id";die();
        $sql = "SELECT s.* , al.shipment_name ,
                pl_from.pla_name as  from_pla_name,
                pl_to.pla_name as  to_pla_name,
                al_from.airport_name as  from_airport_name,
                al_to.airport_name as  to_airport_name,
                cl_to.company_name as  to_company_name,
                 u.user_name AS 'addedBy' FROM shipments s ";
        $sql .= " LEFT JOIN `users` u ON u.id = s.admin_id ";
        $sql .= " LEFT JOIN `shipment_langs` al ON s.id = al.shipment_id ";
        $sql .= " LEFT JOIN `place_langs` pl_from ON   pl_from.place_id = s.from_place_id ";
        $sql .= " LEFT JOIN `place_langs` pl_to ON     pl_to.place_id = s.to_place_id ";
        $sql .= " LEFT JOIN `airport_langs` al_from ON al_from.airport_id = s.from_airport_id ";
        $sql .= " LEFT JOIN `airport_langs` al_to ON   al_to.airport_id = s.to_airport_id ";
        $sql .= " LEFT JOIN `company_langs` cl_to ON   cl_to.company_id = s.to_company_id ";
        if ($temp['shipment_status'] == 'appling') {
            $sql .= " LEFT JOIN `offers` o ON   o.shipment_id = s.id ";
        }

        $sql .= " WHERE  s.active = '1'  AND al.lang_code = '{$this->lang_code}' ";
        // search
        $sql .= $temp['from_airport_id'] ? " AND s.from_airport_id ='{$temp['from_airport_id']}' " : '';
        $sql .= $temp['from_place_id'] ? " AND s.from_place_id ='{$temp['from_place_id']}' " : '';
        $sql .= $temp['to_airport_id'] ? " AND s.to_airport_id ='{$temp['to_airport_id']}' " : '';
        $sql .= $temp['to_place_id'] ? " AND s.to_place_id ='{$temp['to_place_id']}' " : '';
        $sql .= $temp['from_weight'] ? " AND s.weight >='{$temp['from_weight']}' " : '';
        $sql .= $temp['to_weight'] ? " AND s.weight <='{$temp['to_weight']}' " : '';
        if ($temp['shipment_status'] == 'waiting') {
            $sql .= " AND  s.shipment_status = 'waiting'  ";
            $sql .= $awaitingOfferString ? "AND s.id  not IN ({$awaitingOfferString}) " : "";

        } else if ($temp['shipment_status'] == 'appling') {
            $sql .= " AND  s.shipment_status = 'waiting' AND o.user_id ='{$temp['user_id']}'  ";
        } else {
            $sql .= $temp['selected_user_id'] ? " AND s.selected_user_id ='{$temp['selected_user_id']}' " : '';
            //  $sql .=  $place_id ? " AND s.place_id = '{$place_id}' " : ' ';
            $sql .= $temp['shipment_status'] && $temp['shipment_status'] != 'not_done' ? " AND s.shipment_status ='{$temp['shipment_status']}' " : '';
            $sql .= $temp['shipment_status'] == 'not_done' ? " AND (s.shipment_status ='selected'  ||  s.shipment_status ='underExecution' ) " : " ";

        }

        $sql .= " Group by s.id ORDER BY s.sort , s.id desc ";
        $sql .= $temp['limit'] ? " LIMIT {$temp['start']}, {$temp['limit']}" : '';
        // echo LANGUAGE ;die();
        //  echo $sql;die();

        $res = $this->mDb->getAll($sql);
        if ($res) {
            for ($i = 0; $i < count($res); $i++) {
                $res[$i]['added_offer_id'] = $this->checkAddOffer($res[$i]['id'], $temp['user_id']);
                $res[$i]['CountOfOffers'] = $this->getCountOfOffersByShipmentId($res[$i]['id']);
                $res[$i]['from_airport_name'] = $res[$i]['from_airport_id'] == 'all' ? 'كل المطارات' : $res[$i]['from_airport_name'];
                $res[$i]['to_airport_name'] = $res[$i]['to_airport_id'] == 'all' ? 'كل المطارات' : $res[$i]['to_airport_name'];
            }
        }

        return $res;
    }

    public function getSearchShipmentsCount($temp)
    {

        if ($temp['shipment_status'] == 'waiting') {
            $awaitingOffer = $this->getAwaitingOfferShipments($temp['user_id']);
            $awaitingOfferString = '';

            for ($i = 0; $i < count($awaitingOffer); $i++) {
                # code...
                $awaitingOfferString = $awaitingOffer[$i]['id'] . "," . $awaitingOfferString;
            }
            $awaitingOfferString = substr($awaitingOfferString, 0, -1);
        }

        $sql = "SELECT COUNT(s.id)   FROM shipments s ";
        // $sql .= " LEFT JOIN `shipment_langs` al ON s.id = al.shipment_id ";

        // $sql .= " WHERE s.id > 0 AND al.lang_code = '{$this->lang_code}' ";

        if ($temp['shipment_status'] == 'appling') {
            $sql .= " LEFT JOIN `offers` o ON   o.shipment_id = s.id ";
        }

        $sql .= " WHERE  s.active = '1'  ";

        // search
        $sql .= $temp['from_airport_id'] ? " AND s.from_airport_id ='{$temp['from_airport_id']}' " : '';
        $sql .= $temp['from_place_id'] ? " AND s.from_place_id ='{$temp['from_place_id']}' " : '';
        $sql .= $temp['to_airport_id'] ? " AND s.to_airport_id ='{$temp['to_airport_id']}' " : '';
        $sql .= $temp['to_place_id'] ? " AND s.to_place_id ='{$temp['to_place_id']}' " : '';
        $sql .= $temp['from_weight'] ? " AND s.weight >='{$temp['from_weight']}' " : '';
        $sql .= $temp['to_weight'] ? " AND s.weight <='{$temp['to_weight']}' " : '';
        $sql .= $place_id ? " AND s.place_id = '{$place_id}' " : ' ';

        if ($temp['shipment_status'] == 'waiting') {
            $sql .= " AND  s.shipment_status = 'waiting'  ";
            $sql .= $awaitingOfferString ? "AND s.id  not IN ({$awaitingOfferString}) " : "";

        } else if ($temp['shipment_status'] == 'appling') {
            $sql .= " AND  s.shipment_status = 'waiting' AND o.user_id ='{$temp['user_id']}'  ";
        } else {
            $sql .= $temp['selected_user_id'] ? " AND s.selected_user_id ='{$temp['selected_user_id']}' " : '';
            //  $sql .=  $place_id ? " AND s.place_id = '{$place_id}' " : ' ';
            $sql .= $temp['shipment_status'] AND $temp['shipment_status'] != 'not_done' ? " AND s.shipment_status ='{$temp['shipment_status']}' " : '';
            $sql .= $temp['shipment_status'] == 'not_done' ? " AND (s.shipment_status ='selected'  ||  s.shipment_status ='underExecution' ) " : " ";

        }

        //   echo $sql; die();

        return $this->mDb->getOne($sql);
    }

    public function getCountOfOffersByShipmentId($id)
    {
        $sql = "SELECT count(id) FROM `offers`";
        $sql .= " WHERE `shipment_id` = '{$id}' ";
        return $this->mDb->getOne($sql) ? $this->mDb->getOne($sql) : 0;
    }

    public function getShipmentImages($shipment_id)
    {
        $sql = "SELECT `image`  FROM `shipment_images` WHERE shipment_id = '{$shipment_id}'  ";
        return $this->mDb->getAll($sql);

    }

    public function getShipmentDetails($temp)
    {

        $sql = "SELECT s.* , al.shipment_name ,  al.shipment_desc,u2.user_name as selected_user_name ,
        p_from.image as  from_pla_image,
        pl_from.pla_name as  from_pla_name,
        p_to.image as  to_pla_image,
        pl_to.pla_name as  to_pla_name,

        cl_to.company_name  as  to_company_name ,
        al_from.airport_name as  from_airport_name,
        al_to.airport_name as  to_airport_name, u.user_name AS 'addedBy' FROM shipments s ";
        $sql .= " LEFT JOIN `users` u ON u.id = s.admin_id ";
        $sql .= " LEFT JOIN `users` u2 ON u2.id = s.selected_user_id ";

        $sql .= " LEFT JOIN `shipment_langs` al ON s.id = al.shipment_id ";
        $sql .= " LEFT JOIN `place_langs` pl_from ON   pl_from.place_id = s.from_place_id ";
        $sql .= " LEFT JOIN `place_langs` pl_to ON     pl_to.place_id = s.to_place_id ";
        $sql .= " LEFT JOIN `places` p_from ON     p_from.id = s.from_place_id ";
        $sql .= " LEFT JOIN `places` p_to ON     p_to.id = s.to_place_id ";

        $sql .= " LEFT JOIN `airport_langs` al_from ON al_from.airport_id = s.from_airport_id ";
        $sql .= " LEFT JOIN `airport_langs` al_to ON   al_to.airport_id = s.to_airport_id ";
        $sql .= " LEFT JOIN `company_langs` cl_to ON   cl_to.company_id = s.to_company_id ";

        $sql .= " WHERE s.id ={$temp['id']} AND  s.active = '1' AND al.lang_code = '{$this->lang_code}' ";
        $res = $this->mDb->getRow($sql);
        if ($res) {
            $res['offers'] = $this->offersObj->getOffersByType($temp['id'], 'shipment_id');
            $res['images'] = $this->getShipmentImages($temp['id']);

            $res['from_airport_name'] = $res['from_airport_id'] == 'all' ? 'كل المطارات' : $res['from_airport_name'];
            $res['to_airport_name'] = $res['to_airport_id'] == 'all' ? 'كل المطارات' : $res['to_airport_name'];

        }
        // echo "<pre>";print_r($query);die();
        return $res;
    }

    public function checkAddOffer($shipment_id, $user_id)
    {
        $sql = "SELECT  id  FROM `offers`";
        $sql .= " WHERE `shipment_id` = '{$shipment_id}' AND `user_id` = '{$user_id}' ";
        return $this->mDb->getOne($sql) ? $this->mDb->getOne($sql) : 0;
    }

    public function getAwaitingOfferShipments($user_id)
    {
        $sql = "SELECT  s.id  FROM `offers` o";
        $sql .= " LEFT JOIN `shipments` s ON s.id = o.shipment_id ";
        $sql .= " WHERE o.`user_id` = '{$user_id}' and  s.shipment_status = 'waiting' ";
        return $this->mDb->getAll($sql);
    }

    public function checkCode($temp)
    {

        $sql = "SELECT  id  FROM `shipments`";
        $sql .= " WHERE `{$temp['type']}` = '{$temp['code']}' AND `selected_user_id` =  '{$temp['user_id']}'  AND `id` =  '{$temp['shipment_id']}' ";
            //  echo $sql ;die();
         $id =  $this->mDb->getOne($sql)  ;
        //   echo $id ;die(' asdasd');

        return $id ;
    }


    public function confirmCode($temp)
    {

        $status = $temp['type'] == 'given_code'  ? 'underExecution' : 'finished' ;

        $sql = "UPDATE shipments SET  `shipment_status`  = '{$status}'    WHERE  `id` =  '{$temp['shipment_id']}'   ";
        $this->mDb->query($sql);

        $sql = "UPDATE offers SET  `{$temp['type']}`  = '{$temp['code']}' ";
        $sql .= " WHERE  id =  '{$temp['offer_id']}' ";
        return $this->mDb->query($sql) ? true  :false;
    }
 

    

}
