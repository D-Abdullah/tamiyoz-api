<?php

$userAdmObj = new users();

class users {

var $mDb; 
var $mConfig;
var $notificate;

function users() {
    global $Config; 
    $this -> mDb = new iplus(); 
    $this -> mConfig = $Config;
    $this->notificate = new pushmessage();
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

function signIn($email, $password) {

    $sql = "SELECT `id`, `password` FROM `users` ";
    $sql .= " WHERE ( `email` = '{$email}' OR  `mobile` = '{$email}') AND ( `user_level` IS NOT NULL )";

    $result = $this -> mDb -> getRow($sql);
    if(count($result) > 0){
        $login = password_verify($password, $result['password']);

        if ($login === true) {
            $stmt = "SELECT `id`, `mobile`, `email`, `full_name`, `date_added`, `img`, `user_level` FROM `users` ";
            $stmt .= " WHERE `id` = '{$result['id']}' AND `user_level` IS NOT NULL ";
            return $this -> mDb -> getRow($stmt);
        }
    }
    

}

function getUserLevel($request) {

    $user_id = $request['user_id'];
    $roles = $request['roles'];

    $select = implode(", ", $roles);

    $query = "SELECT u.`id` AS 'user_id_in_users', {$select} FROM `users` u";
    $query .= " LEFT JOIN `user_levels` ul ON ul.id = u.user_level ";
    $query .= " WHERE u.`id` = '{$user_id}' ";
    // echo $query; die();
    return $this -> mDb -> getRow($query);
}

/////////////////////////////////////// Admins ///////////////////////////////////////////////

function getSomeUsers($userType, $aStart, $aLimit, $sort, $type, $searchName,$searchUserPhone,$searchDateFrom,$searchDateTo,$searchUserStatus,$searchUserType) {
 
    $sql = "SELECT u.id, u.email, u.full_name, u.date_added, u.mobile ,u.img, ul.level_name, u.`user_type`,u.`status` FROM `users` u";
    $sql .= " LEFT JOIN `user_levels` ul ON ul.id = u.user_level ";
    $sql.= " WHERE u.id > 0 ";
    $sql.= $userType === "members" ? " AND u.`user_type` IS NOT NULL" : "";
    $sql.= $userType === "admins" ? " AND u.`user_level` IS  NOT NULL AND u.`user_type` IS NULL  ":"";
    $sql.= $searchName ? ' AND u.full_name like "%'.$searchName.'%"' : '';
    $sql.= $searchUserPhone ? ' AND u.`mobile` like "%'.$searchUserPhone.'%"' : '';
    $sql.= $searchDateFrom ? " AND u.`date_added` BETWEEN '{$searchDateFrom}' AND '{$searchDateTo}'" : "";
    $sql.= $searchUserStatus !='' ? " AND u.`status` = '{$searchUserStatus}'" : "";
    $sql.= $searchUserType ? " AND u.`user_type` = '{$searchUserType}'" : "";
    $sql .= " ORDER BY u.{$sort} {$type}";
    $sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';
//        echo $sql; die();
    return $this -> mDb -> getAll($sql); 
}

function getSearchUsersCount($userType, $sort, $type, $searchName,$searchUserPhone,$searchDateFrom,$searchDateTo,$searchUserStatus,$searchUserType) {
    $sql = "SELECT COUNT(`id`) as 'result_count' FROM `users`";
    $sql.= " WHERE `id` > 0";
    $sql.= $userType === "members" ? " AND `user_type` IS NOT NULL " : "";
    $sql.= $userType === "admins" ?  " AND `user_level` IS  NOT NULL AND `user_type` IS NULL  ":"";
    $sql.= $searchName ? ' AND `full_name` like "%'.$searchName.'%"' : '';
    $sql.= $searchUserPhone ? ' AND `mobile` like "%'.$searchUserPhone.'%"' : '';
    $sql.= $searchDateFrom ? " AND `date_added` BETWEEN '{$searchDateFrom}' AND '{$searchDateTo}'" : "";
    $sql.= $searchUserStatus !='' ? " AND `status` = '{$searchUserStatus}'" : "";
    $sql.= $searchUserType ? " AND `user_type` = '{$searchUserType}'" : "";
    $sql .= " ORDER BY {$sort} {$type}";
        // echo $sql; die();
    return $this -> mDb -> getOne($sql); 
}

function getUsersCount($userType) {
    $sql = "SELECT COUNT(`id`) as 'count' FROM `users`";
    $sql.= $userType === "members" ?  " WHERE `user_level` IS NULL" : "";
    $sql.= $userType === "admins" ? " WHERE `user_level` IS NOT NULL" : "";
    return $this -> mDb -> getOne($sql); 
}

function insertAuthCode($aId, $authentication_code) {
    $sql = " UPDATE `users` SET `authentication_code` = '{$authentication_code}'";
    $sql.= " WHERE `id` = '{$aId}'";
    // echo $sql; die();
    return $this->mDb->query($sql);
}


function chickEmail($email) {

        $sql = "SELECT COUNT(`id`) as 'count' FROM `users`";
        $sql.= " WHERE `email` = '{$email}'";
        return $this -> mDb -> getOne($sql);
    }


function getAllCarTypes() {
    $sql = "SELECT c.`id`,ctl.`name` FROM `cars_types` c";
    $sql .= " LEFT JOIN `cars_types_langs` ctl ON c.id = ctl.car_id ";
    $sql.= " WHERE c.`id` > 0 and c.`status` ='1' and ctl.`lang_code`='ar'";

//    echo $sql; die();

    return $this -> mDb -> getAll($sql);

}
    function getAllCities() {
        $sql = "SELECT p.`id`,pl.`pla_name` FROM `places` p";
        $sql .= " LEFT JOIN `place_langs` pl ON p.`id` = pl.`place_id` ";
        $sql .= " WHERE p.`place_type` = 'city' AND pl.`lang_code` = 'ar'";
        return $this -> mDb -> getAll($sql);
    }
    function getDistrictsByCityId($id) {
        $sql = "SELECT p.`id`,pl.`pla_name` FROM `places` p";
        $sql .= " LEFT JOIN `place_langs` pl ON p.`id` = pl.`place_id` ";
        $sql .= " WHERE p.`place_type` = 'district' AND p.`city_id` = '{$id}' AND pl.`lang_code` = 'ar'";
        return $this -> mDb -> getAll($sql);
    }


function chickMobile($mobile) {

        $sql = "SELECT COUNT(`id`) as 'count' FROM `users`";
        $sql.= " WHERE `mobile` = '{$mobile}'";
        return $this -> mDb -> getOne($sql);
    }

function addEditUser($request, $img, $user_id) {

//    print_r($request); die();

    unset($request['action']);

    if ($user_id === '' && $request['operation'] === 'add')
    {
        // Add
        unset($request['operation']);
        $dateTime = date('Y-m-d H:i:s'); 
        $request['password'] = password_hash($request['password'],PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO `users` SET ";
        $sql .= " `full_name` = '{$request['full_name']}',";
        $sql .= $request['userType'] ==='admins' ? " `user_level` = '{$request['user_level']}',":"`user_level` = NULL , ";
        $sql .= $request['userType'] ==='members' ? " `user_type` = '{$request['user_type']}',":"";
        $sql .= $request['email'] && $request['email'] !='' && $request['email'] !=null  ? " `email` = '{$request['email']}'," :"";
        $sql .= " `mobile` = '{$request['mobile']}',";
        $sql .= " `password` = '{$request['password']}',";
        $sql .= $request['notes'] ? " `notes` = '{$request['notes']}',":"";
        $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
        $sql .= $request['country_id'] && $request['country_id'] !='' ? " `country_id` = '{$request['country_id']}',":"";
        $sql .= $request['stage_id'] && $request['stage_id'] !='' ? " `stage_id` = '{$request['stage_id']}',":"";
        $sql .= $request['grade_id'] && $request['grade_id'] !='' ? " `grade_id` = '{$request['grade_id']}',":"";
        $sql .= $request['added_by'] && $request['added_by'] !='' && $request['added_by'] !=null ? " `added_by` = '{$request['added_by']}',":"";
        $sql .= " `img` = '{$img}',";
        $sql .= " `date_added` = '{$dateTime}'";
    //    echo $sql; die();
        $this -> mDb -> query($sql);
        $id = $this->mDb-> getLastInsertId();

        if ($id ) {
            $this->insertAuthCode($id,password_hash($id.$this->mConfig['apihash'],PASSWORD_DEFAULT));
            $request['lon'] && $request['lat'] ? $this->addUserLocation($id,$request['lon'],$request['lat'],$request['area']) :"";
            if($request['user_type'] == 'student'){
                $data = json_decode($request['parentIDs']);
                $newDataObj = $this->convert_object_to_array($data);
                if(count($newDataObj)>0){
                    $this->addStudentParent($id,$newDataObj,$request['user_type'],'add');
                }
            }
            elseif($request['user_type'] == 'parent'){
                $data = json_decode($request['studentIDs']);
                $newDataObj = $this->convert_object_to_array($data);
                if(count($newDataObj)>0){
                    $this->addStudentParent($id,$newDataObj,$request['user_type'],'add');
                }
            }
        }

      return $id;
    }
    else
    {
        // Edit
        $check_query = "SELECT `id` FROM `users` WHERE `id` = '{$user_id}'";
        $check_query .= $request['userType'] === "admins" ? " AND `user_level` IS NOT NULL" : "";
        $check_result = $this -> mDb -> getOne($check_query);

        if ($check_result === false) {
            return 403;
        }
        else{
            $sql = "UPDATE `users` SET ";
            $sql .= $request['userType'] ==='admins' ? " `user_level` = '{$request['user_level']}',":"`user_level` = NULL , ";
            $sql .= $request['userType'] ==='members' ? " `user_type` = '{$request['user_type']}',":"";
            $sql .= $request['email'] && $request['email'] !='' && $request['email'] !=null  ? " `email` = '{$request['email']}'," :"";
            $sql .= $request['notes'] ? " `notes` = '{$request['notes']}',":"";
            $sql .= $request['status'] === 'true' ? " `status` = '1'," : " `status` = '0',";
            $sql .= $request['country_id'] && $request['country_id'] !='' ? " `country_id` = '{$request['country_id']}',":"";
            $sql .= $request['stage_id'] && $request['stage_id'] !='' ? " `stage_id` = '{$request['stage_id']}',":"";
            $sql .= $request['grade_id'] && $request['grade_id'] !='' ? " `grade_id` = '{$request['grade_id']}',":"";
            $sql .= $request['added_by'] && $request['added_by'] !='' && $request['added_by'] !=null ? " `added_by` = '{$request['added_by']}',":"";
            $sql .= "`full_name`='{$request['full_name']}',`mobile`='{$request['mobile']}' ";
            if ($request['password'] == '' || $request['password'] == null) {
            }
            else{
                $request['password'] = password_hash($request['password'],PASSWORD_DEFAULT);
                $sql .= ", `password`='{$request['password']}' ";
            }

            if ($img != '' && $img != null) {
                // Delete old image from the server
                $img_query = "SELECT `img` FROM `users` WHERE `id` = '{$user_id}'";
                $img_query .= $request['userType'] === "admins" ? " AND `user_level` IS NOT NULL" : "";
                $img_result = $this -> mDb -> getOne($img_query);
                $old_img = $_SERVER['DOCUMENT_ROOT'].'/' . $this->mConfig['uploads_path'] . 'users/' . $img_result;
                if (file_exists($old_img)) {
                    @unlink($old_img);
                }
                $sql .= ",`img`='{$img}' ";
            }
            $sql .= " WHERE `id` = '{$user_id}'"; 

            if($request['lon'] && $request['lat']){
                $dateTime = date('Y-m-d H:i:s');
                $sqllo = "UPDATE `user_location` SET ";
                $sqllo .= " `lat` = '{$request['lat']}',";
                $sqllo .= " `lon` = '{$request['lon']}',";
                $sqllo .= " `area` = '{$request['area']}',";
                $sqllo .= " `date_updated` = '{$dateTime}'";
                $sqllo .= " WHERE `user_id` = '{$user_id}'"; 

                $this -> mDb -> query($sqllo);
            }

            if($request['user_type'] == 'student'){
                $data = json_decode($request['parentIDs']);
                $newDataObj = $this->convert_object_to_array($data);
                if(count($newDataObj)>0){
                    $this->addStudentParent($user_id,$newDataObj,$request['user_type'],'edit');
                }
            }
            elseif($request['user_type'] == 'parent'){
                $data = json_decode($request['studentIDs']);
                $newDataObj = $this->convert_object_to_array($data);
                if(count($newDataObj)>0){
                    $this->addStudentParent($user_id,$newDataObj,$request['user_type'],'edit');
                }
            }
          
          return $this -> mDb -> query($sql);
        } 
    }
}

function deleteOldCarImg($oldcarimglicence){
    
            if($oldcarimglicence['owner_licence']){
                    $owner_licence = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/car_owner_licence/'.$oldcarimglicence['owner_licence'];
                    if (file_exists($owner_licence)) {
                        @unlink($owner_licence);
                    }
            }
            if($oldcarimglicence['car_licence']){
                 $car_licence = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/car_owner_licence/'.$oldcarimglicence['car_licence'];
                    if (file_exists($car_licence)) {
                        @unlink($car_licence);
                    }
            }
           if($oldcarimglicence['car_front']){
                 $car_front = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/car_owner_licence/'.$oldcarimglicence['car_front'];
            if (file_exists($car_front)) {
                @unlink($car_front);
            }
           } 
           if($oldcarimglicence['car_back']){
                  $car_back = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/car_owner_licence/'.$oldcarimglicence['car_back'];
            if (file_exists($car_back)) {
                @unlink($car_back);
                }
           }
                    
    
}

 function getRepresentativeRating($representative_id) {

        $sql = " select `id` from `{$this->mPrefix}orders`";
        $sql.= " WHERE `driver_id` = '{$representative_id}' and `status` != 'pending'";
        $arrids=$this->mDb->getAll($sql);
        $ids = array_column($arrids, 'id');
        $tempids='('.implode(',',$ids).')';
            $sql = " select avg(r.`rate`) from `{$this->mPrefix}rating` r";
            $sql .= " LEFT JOIN `orders` o ON r.`order_id` = o.`id` ";
            $sql.= " WHERE `order_id` IN  $tempids";
            return $this->mDb->getOne($sql);
    }


function addUserLocation($user_id, $lon,$lat,$area) {
    $dateTime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO `user_location` SET ";
    $sql .= " `user_id` = '{$user_id}',";
    $sql .= " `lat` = '{$lat}',";
    $sql .= " `lon` = '{$lon}',";
    $sql .= " `area` = '{$area}',";
    $sql .= " `date_added` = '{$dateTime}',";
    $sql .= " `date_updated` = '{$dateTime}'";
    $this -> mDb -> query($sql);
}

function addStudentParent($user_id,$parents,$user_type,$operation_type) {
    if($operation_type == 'edit'){
        if($user_type =='student'){
            $catsql = "DELETE FROM `students_parents` ";
            $catsql.= " WHERE `student_id` = '$user_id'";
            $this -> mDb -> query($catsql);
        }elseif ($user_type =='parent'){
            $catsql = "DELETE FROM `students_parents` ";
            $catsql.= " WHERE `parent_id` = '$user_id'";
            $this -> mDb -> query($catsql);
        }
    }

    $dateTime = date('Y-m-d H:i:s');
    foreach ($parents as $key => $value) {
        $validate=$value['validate'] == 'true' ? '1' : '0';
        $stmt = "INSERT INTO `students_parents`(`student_id`, `parent_id`, `validate`,`date_added`) ";
        if($user_type =='student'){
            $stmt .=" VALUES ('{$user_id}', '{$value['parent_id']}', '{$validate}', '{$dateTime}')";
        }elseif ($user_type =='parent'){
            $stmt .=" VALUES ('{$value['student_id']}','{$user_id}', '{$validate}', '{$dateTime}')";
        }

        $this -> mDb -> query($stmt);
    }

}
function getStudentParent($user_id,$user_type) {

    $sql = "SELECT * FROM `students_parents` ";
    $sql .= " WHERE `id` > 0 ";
    $sql .= $user_type == 'student'? " AND `student_id` = '{$user_id}' ":" AND `parent_id` = '{$user_id}' ";

    $result=$this -> mDb -> getAll($sql);
    return  $result;

}

function getOneUser($userType, $id) {

    $result = array();

    $sql = "SELECT u.`id`, u.`email`, u.`full_name`, u.`mobile`, u.`img`, u.`user_level`, u.`notes`, ul.`level_name`,u.`user_type`,u.`status` FROM `users` u";
    $sql .= " LEFT JOIN `user_levels` ul ON ul.id = u.user_level ";
    $sql .= " WHERE u.`id` = '{$id}' ";
    $sql.= $userType === "members" ? " AND u.`user_type` IS NOT NULL" : " ";
    $sql.= $userType === "admins" ? " AND u.`user_level` IS  NOT NULL AND u.`user_type` IS NULL ":"";

//     echo $sql;die();
    $result=$this -> mDb -> getRow($sql);
    if($result){
        $stmt1 = "SELECT `id`, `area`, `lon`, `lat`,`date_added` FROM `user_location`";
        $stmt1 .= " WHERE `user_id` = '{$id}' ";
        $result['location'] = $this->mDb->getRow($stmt1);
        if($result['user_type']=='student'){
            $result['parentIDs']=  $this->getStudentParent($id,'student');
        }elseif ($result['user_type']=='parent'){
            $result['studentIDs']=  $this->getStudentParent($id,'parent');
        }
    }
   

    return  $result;
}
function getSearchUsersByType($query,$user_type) {
    $result = array();
    $sql = "SELECT u.`id`, u.`full_name`, u.`img` FROM `users` u";
    $sql .= " WHERE u.`id` >0 AND u.`user_type` ='{$user_type}'";
    $sql.= $query ? ' AND u.`id` like "%'.$query.'%"' : '';
    // echo $sql;die();
    $result=$this -> mDb -> getAll($sql);
    return  $result;
}
function getAllProviderDrivers($id) {
    $sql = "SELECT u.`id`, u.`full_name` FROM `users` u";
    $sql .= " WHERE u.`provider_id` = '$id'  AND `user_type`='representative' ";
   // echo $sql;die();
    return  $this -> mDb -> getAll($sql);
}
function addingDriverToOrder($data) {

    $sql = "UPDATE `orders` SET `driver_id` = '{$data['driver_id']}' , `status`='assigned' ";
    $sql .= "WHERE `id` = '{$data['order_id']}' ";
    return $this->mDb->query($sql);
}

function addFinancialOperation($requist) {
    $dateTime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO `money_transactions` SET ";
    $sql .= " `user_id` = '{$requist['user_id']}',";
    $sql .= " `value` = '{$requist['value']}',";
    $sql .= " `type` = 'addfrommanagement',";
    $sql .= " `added_by` = '{$requist['added_by']}',";
    $sql .= " `date_added` = '{$dateTime}'";
    $this -> mDb -> query($sql);
    $id = $this->mDb-> getLastInsertId();
    if($id > 0){
        $msgar="تم إضافة مبلغ "." ". $requist['value'] ." "."للرصيد من خلال الادارة";
        $msgen="An amount  ".  $requist['value'] ."  has been added to the balance through the administration";
        $stmt = "INSERT INTO `money_transactions_group`";
        $stmt .= " (`money_transactions_id`, `lang_code`, `message`) ";
        $stmt .= " VALUES ('{$id}', 'ar', '{$msgar}'),('{$id}', 'en', '{$msgen}') ";
        if($this->mDb->query($stmt)){
            $not_id= $this->addNotification($requist['user_id'],$requist['added_by'],'balance','addfrommanagement','');

            $sqll = "SELECT dvt.`device_token_id` as token ,dv.`lang_code` ";
            $sqll .= " FROM `device_token` dvt";
            $sqll .= " LEFT JOIN `devices` dv ON dvt.`id` = dv.`device_token_id` ";
            $sqll .= " WHERE   dv.`user_id` = '{$requist['user_id']}'  AND dv.`login` ='1'";


                    $resss=$this -> mDb -> getAll($sqll);
                    $device_token=array_column($resss, 'token');
                    $langCods=array_column($resss, 'lang_code');

                        for($i=0;$i<count($device_token) ;$i++){
                            
                            $device_token_ids=array($device_token[$i]);
                            
                                    $data=array(
                                        'title'=>$langCods[$i]['lang_code'] == 'ar' ? "إضافة رصيد " : "added to the balance",
                                        'notifiy_message'=>$langCods[$i]['lang_code'] == 'ar' ? $msgar : $msgen,
                                        'notification_type'=>'balance',
                                        'details'=>'addfrommanagement',
                                        'notification_id'=> $not_id,
                                        'url'=>'',
                                    );
                                    $params=array('device_token_ids'=>$device_token_ids,'data'=>$data);
            
                                    $this ->notificate->sendMessage($params); 
                        }
        }
    }
    return $id;
}

function deleteUser($userType, $ids) {
    $res = false;

  $tempids='('.implode(',',$ids).')';

    $sql = "SELECT `img` FROM `users`";
    $sql .= " WHERE `id` IN ".$tempids;
    $images = $this -> mDb -> getAll($sql);

    $catsqll = "DELETE FROM `user_location` ";
    $catsqll.= " WHERE `user_id` IN ".$tempids;
    $this-> mDb-> query($catsqll);

    $catsqllp = "DELETE FROM `students_parents` ";
    $catsqllp.= " WHERE `student_id` IN ".$tempids . " OR `parent_id` IN ".$tempids;


    $this-> mDb-> query($catsqllp);

    $catsql = "DELETE FROM `users` ";
    $catsql.= " WHERE `id` IN ".$tempids;


$res=$this-> mDb-> query($catsql);

    if($res > 0){

        if(count($images)>0){
            for ($i=0;$i<count($images);$i++){
                $old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/'. $images[$i]['img'];
                if (file_exists($old_img)) {
                    @unlink($old_img);
                }
            }
        }
        return $res;
    }
    else{
        return $res;
    }

}
function deleteStudentsParents($id) {
    $catsql = "DELETE FROM `students_parents` ";
    $catsql.= " WHERE `id` = '$id[0]'";
    return $this -> mDb -> query($catsql);
}
function deleteOneUserCar($user_id,$car_id) {

    $sql = "SELECT * FROM `car_owner`";
    $sql .= " WHERE `id` ='{$car_id}'  AND `user_id` ='{$user_id}' ";
    $carData = $this -> mDb -> getRow($sql);
  if(!empty($carData)){
       $sqll = "SELECT count(`id`) FROM `orders`";
    $sqll .= " WHERE `driver_id` ='{$user_id}'  AND `cars_type_id` ='{$carData['car_type_id']}' AND `status` != 'delivered'  AND `status` != 'canceled' ";
    $carCurrentllyOrders = $this -> mDb -> getOne($sqll);
  
  if($carCurrentllyOrders > 0){
       $Data = [
                'errors' => 'this car has orders not completed yet !',
                'data' => '',
            ];
            return $Data;
  }else{
      
        $carsql = "DELETE FROM `car_owner` ";
        $carsql.= " WHERE `id` ='{$car_id}'  AND `user_id` ='{$user_id}' ";

        if($this-> mDb-> query($carsql)){
    
                    $owner_licence = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/car_owner_licence/'.$carData['owner_licence'];
                    if (file_exists($owner_licence)) {
                        @unlink($owner_licence);
                    }
                      $car_licence = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/car_owner_licence/'.$carData['car_licence'];
                    if (file_exists($car_licence)) {
                        @unlink($car_licence);
                    }
                      $car_front = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/car_owner_licence/'.$carData['car_front'];
                    if (file_exists($car_front)) {
                        @unlink($car_front);
                    }
                      $car_back = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'users/car_owner_licence/'.$carData['car_back'];
                    if (file_exists($car_back)) {
                        @unlink($car_back);
                        }
                 $Data = [
                    'errors' => '',
                    'data' => "car has been deleted successfully",
                ];
              return $Data;
            }
         }
  
      }else{
           $Data = [
                    'errors' => 'this car not exist !',
                    'data' => '',
                ];
                return $Data;
      }
   
}




function getUsersLevels($leveltype,$provider_id) {
    $sql = "SELECT `id`, `level_name` FROM `user_levels` ";
//    $sql .= $leveltype == 'provider' ?" AND `provider_id` = '$provider_id' ":"";

    return $this -> mDb -> getAll($sql); 
}

function getUsersCountRegisteByMe($market_code) {
    $sqll = "SELECT count(`id`) FROM `users`";
    $sqll .= " WHERE `marketing_code` = '{$market_code}' ";
   return $this -> mDb -> getOne($sqll);
}

function getUserDetailsInfo($id) {

    $result = array();
    $sql = "SELECT u.`id`, u.`email`, u.`full_name`,u.`user_type`,u.`status`, u.`mobile`, u.`img`, u.`notes`, u.`date_added` FROM `users` u";
    $sql .= " WHERE u.`id` = '$id' ";
    //  echo $sql; die();
    $result = $this -> mDb -> getRow($sql); 

    $sqlloc = "SELECT * FROM `user_location` ";
    $sqlloc .= " WHERE `user_id` = '$id' ";
    $result['userLocation'] = $this -> mDb -> getRow($sqlloc);

    return $result;
}


///////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////// APP ////////////////////////////////////////////

function is_email($email)
{
    //If the email input string is an e-mail, return true
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

function changePassword($id, $aPassword) {
    $sql = "UPDATE `users` SET `password` = '{$aPassword}' ";
    $sql .= "WHERE `id` = '{$id}' ";
    return $this->mDb->query($sql);
}

function userForgetPassword($email_mobile) {

    $check_email = $this->is_email($email_mobile);

    if($check_email)
    {
        $checkquery = "SELECT `email` FROM `users` ";
        $checkquery .= " WHERE `email` = '{$email_mobile}' ";
        $email_value = $this -> mDb -> getOne($checkquery);

        if ($email_value === false) {
            $Data = ['error'=> "email_does_not_exist"];
            return $Data;
        }
        else{
            $Data = ['error'=> "email_mobile_already_exists"];
            return $Data;
        }

    }
    else
    {
        $checkquery = "SELECT `mobile` FROM `users` ";
        $checkquery .= " WHERE `mobile` = '{$email_mobile}' ";
        $mobile_value = $this -> mDb -> getOne($checkquery);

        if ($mobile_value === false) {
            $Data = ['error'=> "mobile_does_not_exist"];
            return $Data;
        }
        else{
            $Data = ['error'=> "email_mobile_already_exists"];
            return $Data;
        }

    }

}

function getUserInfoByEmail($email) {
    $query = "SELECT `id`, `email`, `full_name` FROM `users` ";
    $query .= " WHERE `email` = '{$email}' ";
    return $this -> mDb -> getRow($query);
}


    function addNotification($receive_user_id,$send_user_id,$page_type,$details,$url) {



        $dateTime = date('Y-m-d H:i:s');
        $sql = "INSERT INTO `{$this->mPrefix}notification` SET ";
        $sql .="receive_user_id='{$receive_user_id}',";
        $sql .="send_user_id='{$send_user_id}',";
        $sql .="page_type='{$page_type}',";
        $sql .="details='{$details}',";
        $sql .="date_added='{$dateTime}'";
//          echo $sql; die();
        return $this->mDb->queryreturnlastid($sql);

    }



}?>