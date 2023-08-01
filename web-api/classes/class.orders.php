<?php

$ordersObj = new orders();

class orders {

    public $mDb;
    public $mConfig;

    public function orders() {
        global $Config;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
    }

    public function addOrders($temp) {

        $sql = "INSERT INTO `{$this->mPrefix}orders` SET ";
        foreach ($temp as $k => $v) {
            $sql .= ($k == 'days') ? "" : "`{$k}`='{$v}',";
        }
        $sql .= " `date_added` = '{$this->mDb->getDateNow()}'";
//          echo $sql . "<hr>";
        $res = $this->mDb->query($sql);
        if ($res) {
            $id = $this->mDb->getLastInsertId();
            $total_quantity = $temp['total_quantity'];
            $quantity = $temp['quantity'];
            $quantityDay = '';
            $sql = "INSERT INTO `{$this->mPrefix}order_days_visits`";
            $sql .= "(`order_id`,`status`, `date`,`amount`,`updated_date`,`type`) VALUES ";
            foreach ($temp['days'] as $k_ => $v_) {
                if (($total_quantity > $quantity ) && $quantity) {
                    $quantityDay = $quantity;
                    $total_quantity = ($total_quantity - $quantity);
                } else {
                    $quantityDay = $total_quantity;
                }
                $sql .= "('{$id}','pending','{$v_['date']}','{$quantityDay}', '{$this->mDb->getDateNow()}','1'),";
            }
            $sql = rtrim($sql, ',');
//        echo $sql . "<hr>";
            $res = $this->mDb->query($sql);
        }

        return $id;
    }

    public function getAllOrders($temp) {
        if ($temp['status'] == 'new') {
            $status = " o.`status` = 'pending'";
        } else if ($temp['status'] == 'implementation') {
            $status = " (o.`status` = 'accepted' OR o.`status` = 'assigned') ";
        } else if ($temp['status'] == 'completed') {
            $status = " o.`status` = 'completed'";
        }

        $sql = "SELECT o.id,o.user_id,o.provider_id,o.driver_id,o.total_quantity,o.quantity_type,o.status,o.date_added,";
        $sql .= " u.`full_name` AS 'user_full_name' ,u.img user_img ,";
        $sql .= " ud.`full_name` AS 'provider_full_name' ";
        $sql .= $temp['lat'] && $temp['lon'] ? " ,(3959 * acos(cos(radians({$temp['lat']})) * cos(radians(u.lat)) * cos(radians(u.lon) - radians({$temp['lon']})) + sin(radians({$temp['lat']})) * sin(radians(u.lat)))) AS distance" : "";
        $sql .= " FROM `orders` o ";
        $sql .= " LEFT JOIN `users` u ON u.`id` = o.`user_id` ";
        $sql .= " LEFT JOIN `users` ud ON ud.`id` = o.`provider_id` ";
        $sql .= " WHERE  {$status}  ";
        $sql .= $temp['user_type'] == 'client' ? " AND o.`user_id` ='{$temp['user_id']}' " : "";
        $sql .= $temp['lat'] && $temp['lon'] ? " ORDER BY distance ASC " : " ORDER BY  o.id DESC ";
        $sql .= $temp['aLimit'] ? " LIMIT {$temp['aStart']}, {$temp['aLimit']}" : '';
//            echo $sql."<hr>";
        return $this->mDb->getAll($sql);
    }

    public function getOrderById($temp) {
        $sql = "SELECT o.*,";
        $sql .= " u.`full_name` AS 'user_full_name' ,u.img user_img ,";
        $sql .= " ud.`full_name` AS 'provider_full_name',ud.img provider_img ";
        $sql .= " FROM `orders` o ";
        $sql .= " LEFT JOIN `users` u ON u.`id` = o.`user_id` ";
        $sql .= " LEFT JOIN `users` ud ON ud.`id` = o.`provider_id` ";
        $sql .= " WHERE  o.id='{$temp['id']}'  ";
//            echo $sql."<hr>";
        $res = $this->mDb->getRow($sql);
        if ($res) {
            $res['days'] = $this->getOrderDays($res['id']);
        }


        return $res;
    }

    public function getOrderDays($orderId) {

        $sql = "SELECT odv.*";
        $sql .= " FROM `order_days_visits` odv ";
        $sql .= " WHERE  odv.order_id='{$orderId}'  ";
//            echo $sql."<hr>";
        return $this->mDb->getAll($sql);
    }

}
