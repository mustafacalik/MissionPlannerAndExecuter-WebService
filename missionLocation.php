<?php
 
/*
 * Following code will get single soldier details
 * A soldier is identified by soldier id (pid)
 */
 
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// connecting to db
$db = new DB_CONNECT();
 date_default_timezone_set('Europe/Istanbul');

if (isset($_GET["mission_id"]) && isset($_GET["soldier_id"]) ) {
    $mission_id= $_GET['mission_id'];
    $soldier_id= $_GET['soldier_id'];
 
    $situationSQL= mysql_query("select * from location where missionID=$mission_id");
 
    if (!empty($situationSQL)) {
    
	$response["success"] = 1;
	$response["situation"]="NOTACTIVATED";

        if (mysql_num_rows($situationSQL) > 0) {
        	$situationSQL= mysql_query("select * from location where missionID=$mission_id and status='END'");
		if (mysql_num_rows($situationSQL) > 0) {
			$response["situation"]="END";
        	}else{
     			$situationSQL= mysql_query("select * from location where missionID=$mission_id and status='START'");
			if (mysql_num_rows($situationSQL) > 0) {
				$response["situation"]="STARTED";
				if(isset($_GET["lat"]) && isset($_GET["lon"]) && isset($_GET["status"]) ) {
				    	$lat= $_GET['lat'];
   					$lon= $_GET['lon'];
    					$status= $_GET['status'];
    					$dd=date('Y-m-d H:m:s',time());
    					mysql_query("insert into location(missionID,soldierID,latitude,longitude,time,status) values 			($mission_id,$soldier_id,$lat,$lon,'$dd','$status')");
				}
				
				$resultLocationSQL= mysql_query("select * from location where missionID=$mission_id and id in (select max(id) from location where missionID=$mission_id group by soldierID) group by soldierID;");
				if (mysql_num_rows($resultLocationSQL) > 0) {
					$response["location"] = array();
					while ($row= mysql_fetch_array($resultLocationSQL)){
            					$location= array();
            					$location["soldierID"] = $row["soldierID"];
            					$location["latitude"] = $row["latitude"];
            					$location["longitude"] = $row["longitude"];
            					$location["time"] = $row["time"];
            					$location["status"] = $row["status"];
                        			array_push($response["location"], $location);
					}
				}
			}else{
		     		$readySQL= mysql_query("select * from location where missionID=$mission_id and status='READY' and soldierID=$soldier_id");
			     	$soldiersReadySQL= mysql_query("select * from location where missionID=$mission_id and status='READY'");
			     	if (mysql_num_rows($readySQL) > 0) {
					$response["situation"]="READY";
					$response["count"]=mysql_num_rows($soldiersReadySQL);
				}else{
					$response["situation"]="ACTIVATED";
					$response["count"]=mysql_num_rows($soldiersReadySQL);
				}
			}
        	}
        }
        echo json_encode($response);
    } else {
        $response["success"] = 0;
        $response["message"] = "SQL Problem!";
 
        echo json_encode($response);
    }
} else {
    $response["success"] = 0;
    $response["message"] = "required field is missing";
 
    echo json_encode($response);
}
?>