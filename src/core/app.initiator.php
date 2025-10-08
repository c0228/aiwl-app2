<?php 
// define('PROJECT_ROOT', );


/* Logger Declaration in JSON */ 
// include('./../../vendor/apache/log4php/src/main/php/Logger.php'); 
// Logger::configure('./../../config/log-config.xml'); 
	
/* Property Files */
$propertyFile = './../../config/app-properties.ini';
$APP_PROPERTIES = parse_ini_file($propertyFile);

/* App Configuration Variables */
$PROJ_MODE = $APP_PROPERTIES["PROJ_MODE"];
$PROJ_APP_TZ = $APP_PROPERTIES["PROJ_APP_TZ"];

/* Database Credentials */
$DB_SERVERNAME = $APP_PROPERTIES[$PROJ_MODE."_DB_SERVERNAME"];
$DB_NAME = $APP_PROPERTIES[$PROJ_MODE."_DB_NAME"];
$DB_USER = $APP_PROPERTIES[$PROJ_MODE."_DB_USER"];
$DB_PASSWORD = $APP_PROPERTIES[$PROJ_MODE."_DB_PASSWORD"];
$PROJ_URL = $APP_PROPERTIES[$PROJ_MODE."_PROJ_URL"];

ini_set('max_execution_time',300);
date_default_timezone_set($PROJ_APP_TZ);

$database = new Database($DB_SERVERNAME,$DB_NAME,$DB_USER,$DB_PASSWORD);

// Allow from any Origin
if(isset($_SERVER['HTTP_ORIGIN'])){
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header("Access-Control-Allow-Credentials: true");
	header("Access-Control-Max-Age: 86400"); // cache for 1 day
}

// Access-control headers are received during OPTIONS requests
if($_SERVER["REQUEST_METHOD"] == 'OPTIONS') {
	if(isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	if(isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	exit(0);
}

header('Content-Type: application/json');