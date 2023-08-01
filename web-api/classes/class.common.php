<?php

$CommonObj = new Common();

class Common {

    var $mDb; 
    var $mConfig; 
    var $mMailer;

    function __construct() {
        global $Config; 
        $this->mDb = new iplus(); 
        $this->mConfig = $Config; 
        $this->mMailer = new Mailer();

    }

    function getAllLanguages() {

        $sql = "SELECT * FROM `languages` ";
        $sql .= "order by id  DESC ";

        $res = $this -> mDb -> getAll($sql);
        return $res;
    }





    function getAllSlides() {
        $sql = "SELECT * FROM `slider` where status ='1' order by `sort`";
        $res = $this -> mDb -> getAll($sql);
        return $res;
    }


    function  getHeaderAdvertisements(){
        $sql = "SELECT * FROM `advertisements`";
        $sql .= " WHERE `position` ='header' AND status ='1'";

        return $this->mDb->getAll($sql);

    }
    
     function  getBottomAdvertisements(){
        $sql = "SELECT * FROM `advertisements`";
        $sql .= " WHERE `position` ='bottom' AND status ='1'";

        return $this->mDb->getAll($sql);

    }

 function  getSideAdvertisements(){
        $sql = "SELECT * FROM `advertisements`";
        $sql .= " WHERE `position` ='side'  AND status ='1'";

        return $this->mDb->getAll($sql);

    }

 function  getCenterAdvertisements(){
        $sql = "SELECT * FROM `advertisements`";
        $sql .= " WHERE `position` ='center'  AND status ='1'";

        return $this->mDb->getAll($sql);

    }
    

    
     function seandOrderToAdmin($requist) {
         $id=$requist['user']['user_id'];
         $data=$this -> mDb -> getRow("SELECT email,full_name FROM `users` WHERE id=$id");
         
        
		$dateTime = date('Y-m-d H:i:s');

		$sql = "INSERT INTO `orders` SET "; 
        $sql .= " `user_id` = '{$requist['user']['user_id']}', ";
        $sql .= " `total_cost` = '{$requist['general_total']}', ";
        $sql .= " `currency` = '{$requist['currency']}', ";
         $sql .= " `pay_method` = '{$requist['pay_method']}', ";
		$sql .= " `date_added` = '{$dateTime}'";

		$this -> mDb -> query($sql); 
		$last_order_id = $this -> mDb -> getLastInsertId();
	
		if($last_order_id > 0){
		    
		    
		    	$sqll = "INSERT INTO `order_address` SET "; 
                $sqll .= " `order_id` = '{$last_order_id}', ";
                $sqll .= " `address` = '{$requist['user']['address']}', ";
                $sqll .= " `country` = '{$requist['user']['country']}', ";
                $sqll .= " `city` = '{$requist['user']['city']}', ";
                $sqll .= " `user_name` = '{$requist['user']['user_name']}', ";
                $sqll .= " `post_code` = '{$requist['user']['post_code']}', ";
                $sqll .= " `home_description` = '{$requist['user']['home_description']}', ";
        		$sqll .= " `additional_inf` = '{$requist['user']['additional_inf']}'";
		    
	        	$this -> mDb -> query($sqll);
	        
		    	foreach ($requist['products'] as $product) {
		    	   
		    	     $ssql =" UPDATE `products` SET `number_pieces_available` = (`number_pieces_available` -  '{$product['quantity']}')  WHERE `id` = '{$product['product_id']}' ";
                     $this -> mDb -> query($ssql);
		    	    
		    	    
	        
                    $stmt  = "INSERT INTO `order_products` SET "; 
        	        $stmt .= " `order_id` = '{$last_order_id}', ";
        	        $stmt .= " `product_id` = '{$product['product_id']}', ";
        	        $stmt .= " `quantity` = '{$product['quantity']}', ";
        	        $stmt .= " `price` = '{$product['price']}', ";
        	        $stmt .= " `total` = '{$product['total']}'";
        		    $this -> mDb -> query($stmt); 
                    
                }
                
            $this->seandToAdminNewOrderEmail($last_order_id,$data['email'],$data['full_name']);


        return $last_order_id;
		    
		}else{
		 return true;   
		}

    }

  
       function seandToAdminNewOrderEmail($order_id,$useremail,$username) {
         
            $subject = "طلب جديد من المستخدم ".$username;
            
              $msg = " <div style =\" padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; \">";
              $msg .= " <h2>تم اضافة طلب جديد  رقم  "." <span>$order_id</span>"."</h2>";
              $msg .="  <h3><a href='".'https://tmormadina.com/admin-area/orders/show-order/'."$order_id"."'>"."إضغط هنا"."</a></h3>";
              $msg .= " </div>";
              
              $adminemail=$this->mConfig['site_email'];
          
            $send=  $this->mMailer->sendEmail($adminemail, $subject, $msg,$useremail,$useremail);
          return $send;
        }

     function seandOrderToAdminEmail($requist) {
      
      
      
      ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

        $subject = "New User Order";
        
          $msg = " <div style =\" padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto; \">";
        
          $msg .= " <h2>Order User Information</h2>";
        $msg .= "
          <table class=\"table\" style=\"width: 100%;
    max-width: 100%;
    margin-bottom: 20px;border-collapse: collapse;
    border-spacing: 0; background-color: transparent;\">
              <thead>
                <tr style=\"text-align: left;\">
                  <th scope=\"col\" style=\"line-height: 30.2px;\">User Name :</th>
                  <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['name']."</td>
                  <th scope=\"col\" style=\"line-height: 30.2px;\">Email :</th>
                  <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['email']."</td>
                </tr>
                <tr style=\"text-align: left;\">
                  <th scope=\"col\" style=\"line-height: 30.2px;\">Phone :</th>
                  <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['phone']."</td>
                  <th scope=\"col\" style=\"line-height: 30.2px;\">Address :</th>
                  <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['address']."</td>
                </tr>
                <tr style=\"text-align: left;\">
                    <th scope=\"col\" style=\"line-height: 30.2px;\">Company Name :</th>
                    <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['company_name']."</td>
                    <th scope=\"col\" style=\"line-height: 30.2px;\">Post Code :</th>
                    <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['post_code']."</td>
                </tr>
                <tr style=\"text-align: left;\">
                    <th scope=\"col\" style=\"line-height: 30.2px;\">Country :</th>
                    <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['country']."</td>
                    <th scope=\"col\" style=\"line-height: 30.2px;\">City :</th>
                    <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['city']."</td>
                </tr>
                <tr style=\"text-align: left;\">
                    <th scope=\"col\" style=\"line-height: 30.2px;\">Additional information :</th>
                    <td scope=\"col\" style=\"line-height: 30.2px;\">".$requist['user']['additional_inf']."</td>
                    <th scope=\"col\" style=\"line-height: 30.2px;\"></th>
                    <td scope=\"col\" style=\"line-height: 30.2px;\"></td>
                    
                </tr>
              </thead>
              
            </table>
         ";

        $msg .= "<h2>Order Details</h2> <br>";
        $msg .= "   <table class=\"table\" style=\"width: 100%;
    max-width: 100%;
    margin-bottom: 20px;border-collapse: collapse;
    border-spacing: 0; background-color: transparent;\">
              <thead>
                <tr>
                  <th scope=\"col\">product number</th>
                  <th scope=\"col\">product Name</th>
                  <th scope=\"col\">Price</th>
                  <th scope=\"col\">Quantity</th>
                  <th scope=\"col\">Total</th>
                </tr>
              </thead>
              <tbody>";

            for ($i=0;$i<count($requist['products']);$i++){
                $msg .= "
                
                 <tr>
                  <th style=\"text-align: center;
    line-height: 30.2px;\" >".$requist['products'][$i]['product_id']."</th>
                  <td style=\"text-align: center;
    line-height: 30.2px;\">".$requist['products'][$i]['name']."</td>
                  <td style=\"text-align: center;
    line-height: 30.2px;\">".$requist['products'][$i]['price']."</td>
                  <td style=\"text-align: center;
    line-height: 30.2px;\">".$requist['products'][$i]['quantity']."</td>
                  <td style=\"text-align: center;
    line-height: 30.2px;\">".$requist['products'][$i]['total']."</td>
                </tr>
                
                ";


            }




            $msg.="
              </tbody>
            </table>
         ";
  $msg.="
              </tbody>
            </table>
         ";
       
 
        $msg.=" <br><br> total cost :  "." ". $requist['general_total'];
        
          $msg.=" </div>";
        
        
// echo $msg;die();


       $send=  $this->mMailer->sendEmail('info@chinaprice.net', $subject, $msg,"info@chinaprice.net",$requist['user']['email']);

return $send;
    }






    function getAllSocialMedia() {

        $sql  = " SELECT name,value ";
        $sql .= " FROM `{$this->mPrefix}settings` ";
        $sql .= " WHERE  `name`='phone'  OR `name`='site_email' OR `name`='address' OR `name`='facebook' OR `name`='twitter' OR `name`='instagram' OR `name`='linkedin'";
//echo $sql;
        $res  = $this->mDb->getAll($sql);
        foreach ($res as $k => $v){
            $temp[$v['name']] = $v['value'];
        }

        return $temp;
    }

    function getOnePageDetails($id,$lang_code)
    {

        $res = array();
        $sql = "SELECT * FROM  `pages` tc ";
        $sql .= " LEFT JOIN `pages_langs` tccl ON tc.`id` = tccl.`page_id` ";
        $sql .= " where tc.`status` ='1' AND tc.`id` ='{$id}' ";
        $sql .= " AND tccl.`lang_code`='{$lang_code}'";
//                        echo $sql;die();
        $res = $this->mDb->getRow($sql);
        return $res;
    }
 function getAllPages($lang_code)
    {

        $res = array();
        $sql = "SELECT * FROM  `pages` tc ";
        $sql .= " LEFT JOIN `pages_langs` tccl ON tc.`id` = tccl.`page_id` ";
        $sql .= " where tc.`status` ='1'  ";
        $sql .= " AND tccl.`lang_code`='{$lang_code}'";
//                        echo $sql;die();
        $res = $this->mDb->getAll($sql);
        return $res;
    }



    function getPlacesWithCategoryID($request) {
        $category_id = trim($request['category_id']);
        $request['lang_code'] = $request['lang_code'] !== '' ? $request['lang_code'] : "ar";
        $limit=30;
        $index=$request['page']?$request['page']:'1';
        $index=$index*$limit-$limit;

      $sql = "SELECT p.`id`,pl.`name`,p.`img` , p.`lat`,p.`lon`";
      $sql .= $request['lat'] && $request['lon'] ? " ,(1.609344*(3959 * acos(cos(radians({$request['lat']})) * cos(radians(lat)) * cos(radians(lon) - radians({$request['lon']})) + sin(radians({$request['lat']})) * sin(radians(lat))))) AS distance " : "";
      $sql .= " FROM `places` p ";
      $sql .= " LEFT JOIN `places_langs` pl ON p.`id` = pl.`place_id` ";
      $sql .= " WHERE p.`id` > 0 AND p.`status`='1' AND pl.`lang_code` = '{$request['lang_code']}'";
      $sql .= " AND p.`category_id` ='{$category_id}'";
      $sql .= " ORDER BY distance ASC LIMIT {$index}, {$limit}";
        //    echo $sql;die();
       $res = $this -> mDb -> getAll($sql);
      return $res;
    }
    
    
   
    
    function addSubscriber($request){
         $dateTime = date('Y-m-d H:i:s'); 
         
         $sql1 = "SELECT `id`,`status` ";
         $sql1 .= " FROM `mail_subscribers`  ";
         $sql1 .= " WHERE `email` = '{$request['email']}' ";
        $idd= $this -> mDb -> getRow($sql1);
        if($idd['id'] > 0 ){
            
            if($idd['status'] == '0'){
              $ssql =" UPDATE `mail_subscribers` SET `status` = '1' WHERE `email` = '{$request['email']}' ";
              $this -> mDb -> query($ssql);
              
              return 'readd';
            }else{
                  return 'exist';
            }
        }else{
              $sql = "INSERT INTO `mail_subscribers` SET "; 
        $sql .= " `email` = '{$request['email']}' , "; 
        $sql .= " `country` = '{$request['country']}' , "; 
        $sql .= " `status` = '1' , "; 
        $sql .= " `date_added` = '{$dateTime}'"; 
        
        // echo $sql; die();

        return $this -> mDb -> query($sql); 
        }
      
    }
    function addRegistrationAcceptance($request){
         $dateTime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `orders` SET ";
        $sql .= " `email` = '{$request['email']}' , ";
        $sql .= " `name` = '{$request['name']}' , ";
        $sql .= " `country` = '{$request['country']}' , ";
        $sql .= " `city` = '{$request['city']}' , ";
        $sql .= " `governorate` = '{$request['governorate']}' , ";
        $sql .= " `degree` = '{$request['degree']}' , ";
        $sql .= " `degree_date` = '{$request['degree_date']}' , ";
        $sql .= " `mobile` = '{$request['mobile']}' , ";
        $sql .= " `address` = '{$request['address']}' , ";
        $sql .= " `date_added` = '{$dateTime}'";

        // echo $sql; die();

        return $this -> mDb -> query($sql);

    }




}?>