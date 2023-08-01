<?php

$payment_waysObj = new payment_ways(); 

class payment_ways {

var $mDb; 
var $mConfig; 
var $dateTime;
	
function payment_ways() {
	global $Config; 
	$this -> mDb = new iplus(); 
	$this -> mConfig = $Config; 
	$this -> dateTime = date('Y-m-d H:i:s');
}

function getPayment_ways() {
	$sql = "SELECT p.*   FROM payment_systems p";
 
	$sql.= " WHERE p.status ='1'    ";


	$sql .= " ORDER BY p.sort ";

     // echo $sql; die();

	return $this -> mDb -> getAll($sql); 
}
 
}?>