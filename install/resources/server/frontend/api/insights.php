<?php 
//set the content type to return a json
header('Content-Type: application/json');
//set the webroot from server because php is weird
$basepath = $_SERVER["DOCUMENT_ROOT"];
//connect to the credential management database
include $basepath . "/functions/managecreds.php";
$connection = managecreds();
//test if the API key is valid
include $basepath . "/functions/checkapikey.php";
checkapikey($connection);
//setup a database connection to the provided database
include $basepath . "/functions/dbsetup.php" ;
$connection = setupdb();
//get a database list from the connection from before
include $basepath . "/functions/dblist.php";
$dbList = dblist($connection);
//check if provided database is not on the available list. If not, or not provided, return 404
if (!in_array($_GET["database"], $dbList) or !isset($_GET["database"]) or $_GET["database"] == "") {
	echo "{\"code\":404}";
} else {
//set the database as the one recieved from the url
$database = $_GET["database"];
//connect to the database with the provided name
include $basepath . "/functions/dbconnect.php";
$connection = dbconnect($database); 
//generate a string of private IPs on the selected database
$queryprivateips = pg_query($connection, "SELECT DISTINCT destinationip FROM main WHERE (destinationip LIKE '10.%' OR destinationip ~ '^172[.](1[6-9]|2[0-9]|3[0,1])[.]*' OR destinationip LIKE '192.168.%') AND destinationip !~ '^2(2[4-9]|3[2-9])[.]*';");
$privateips = "";
while($row = pg_fetch_row($queryprivateips)) {
	$privateips = $privateips . "\"" . $row[0] . "\"" . ",";
}
$privateips = substr($privateips, 0, -1);

//generate a string of public IPs on the selected database
$querypublicips = pg_query($connection, "SELECT DISTINCT destinationip FROM main WHERE destinationip NOT LIKE '10.%' AND destinationip !~ '^172[.](1[6-9]|2[0-9]|3[0,1])[.]*' AND destinationip NOT LIKE '192.168.%' AND destinationip !~ '^2(2[4-9]|3[2-9])[.]*';");
$publicips = "";
while($row = pg_fetch_row($querypublicips)) {
	$publicips = $publicips . "\"" . $row[0] . "\"" . ",";
}
$publicips = substr($publicips, 0, -1);

//generate a string of MACs on the selected database
$querymacaddresses = pg_query($connection, "SELECT DISTINCT sourcemac FROM main UNION SELECT DISTINCT destinationmac FROM main;");
$macaddresses = "";
while($row = pg_fetch_row($querymacaddresses)) {
	$macaddresses = $macaddresses . "\"" . $row[0] . "\"" . ",";
}
$macaddresses = substr($macaddresses, 0, -1);

//generate a string of protocols on the selected database
$queryprotocols = pg_query($connection, "SELECT DISTINCT protocol FROM main;");
$protocols = "";
while($row = pg_fetch_row($queryprotocols)) {
	$protocols = $protocols . "\"" . $row[0] . "\"" . ",";
}
$protocols = substr($protocols, 0, -1);

//generate a string of destination well-known ports on the selected database
$queryknownports = pg_query($connection, "SELECT DISTINCT destinationport FROM main WHERE destinationport <= 1024;");
$knownports = "";
while($row = pg_fetch_row($queryknownports)) {
	$knownports = $knownports . "\"" . $row[0] . "\"" . ",";
}
$knownports = substr($knownports, 0, -1);

//generate a string of destination registered ports on the selected database
$queryregisteredports = pg_query($connection, "SELECT DISTINCT destinationport FROM main WHERE destinationport > 1024 AND destinationport <= 49152");
$registeredports = "";
while($row = pg_fetch_row($queryregisteredports)) {
	$registeredports = $registeredports . "\"" . $row[0] . "\"" . ",";
}
$registeredports = substr($registeredports, 0, -1);
//generate the json string
$json = "{\"code\":200,\"privateips\":[" . $privateips . "]" . ",\"publicips\":[" . $publicips . "],\"macaddresses\":[" . $macaddresses . "],\"protocolsfound\":[" . $protocols . "],\"wellknownports\":[" . $knownports . "],\"registeredports\":[" . $registeredports . "]}";
//return the json string
echo $json;
}
