<?php

require_once './../core/app.database.php';
require_once './../core/app.initiator.php';
require_once './../repo/data.user.accounts.info.php';

$COUNTRIES_DATA_FILE = './../../data/countries.json';

if($_GET["action"]=='COUNTRIES_LIST' && $_SERVER["REQUEST_METHOD"]=='GET'){
	$jsonString = file_get_contents($COUNTRIES_DATA_FILE);
	$data = json_decode($jsonString, true); // true = associative array
	if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
		die('JSON Decode Error: ' . json_last_error_msg());
	}
	$countries = array_keys($data);
	echo json_encode($countries);
} 
else if($_GET["action"]=='STATES_LIST' && $_SERVER["REQUEST_METHOD"]=='GET'){
	if(isset($_GET["country"])){
		$country = $_GET["country"];
		$jsonString = file_get_contents($COUNTRIES_DATA_FILE);
		$data = json_decode($jsonString, true); // true = associative array
		if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
			die('JSON Decode Error: ' . json_last_error_msg());
		}
		echo json_encode( $data[$country] );
	} else {
		echo 'MISSING_COUNTRY';
	}
}
else if($_GET["action"]=='CREATE_USER_ACCOUNT' && $_SERVER["REQUEST_METHOD"]=='POST'){
 $htmlData = json_decode( file_get_contents('php://input'), true );	
 $name = ''; if( array_key_exists("name", $htmlData) ){ $name = $htmlData["name"];   }
 $country = ''; if( array_key_exists("country", $htmlData) ){ $country = $htmlData["country"];  }
 $state = ''; if( array_key_exists("state", $htmlData) ){ $state = $htmlData["state"];  }
 $mobile = ''; if( array_key_exists("mobile", $htmlData) ){ $mobile = $htmlData["mobile"];  }
 $balance = ''; if( array_key_exists("balance", $htmlData) ){ $balance = $htmlData["balance"];  }
 $query = $userAccountsInfo->query_add_newUserAccount($name, $country, $state, $mobile, $balance);
 $result = array();
 try {
	$status = $database->addupdateData($query);
	$message = 'USER_NEW_REGISTERED';
 } catch (mysqli_sql_exception $e) { // Send the MySQL error message directly in JSON
    $status = 'Error';
    $message = $e->getMessage();
 }
 $result["status"] = $status;
 $result["message"] = $message;
 echo json_encode( $result );
}

?>