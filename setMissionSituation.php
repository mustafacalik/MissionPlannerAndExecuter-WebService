<?php
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// connecting to db
$db = new DB_CONNECT();

if (isset($_GET["mission_id"]) && isset($_GET["soldier_id"]) && isset($_GET["lat"]) && isset($_GET["lon"]) && isset($_GET["status"]) ) {
    $mission_id= $_GET['mission_id'];
    $soldier_id= $_GET['soldier_id'];
    $lat= $_GET['lat'];
    $lon= $_GET['lon'];
    $status= $_GET['status'];
    $dd=date('Y-m-d H:i:s',time());
     
    mysql_query("insert into location(missionID,soldierID,latitude,longitude,time,status) values ($mission_id,$soldier_id,$lat,$lon,'$dd','$status')");
    echo "success";
}else{
	echo "required parameters!!";
}

?>