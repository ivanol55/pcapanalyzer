<?php 
//set the response type as json
header('Content-Type: application/json');
//get the webroot path from server because php is weird
$basepath = $_SERVER["DOCUMENT_ROOT"];
//generate a connection to the credentials database
include $basepath . "/functions/managecreds.php";
$connection = managecreds();
//test if the API key is valid
include $basepath . "/functions/checkapikey.php";
checkapikey($connection);
//configure a connection to the database backend
include $basepath . "/functions/dbsetup.php";
$connection = setupdb();
//get the database value from the connection set beforehand
include $basepath . "/functions/setdatabase.php";
$database = setdatabase($connection);
//list databases
include $basepath . "/functions/dblist.php";
$dbList = dblist($connection);
//generate the json string
$json = "{\"code\":200,\"databases\":[";
foreach ($dbList as $db) {
	$json = $json . "\"" . $db . "\",";
}
$json = substr($json, 0, -1);
$json = $json . "]}";
//return the json
echo $json;
