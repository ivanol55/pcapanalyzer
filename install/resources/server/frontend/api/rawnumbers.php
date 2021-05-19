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
//count the total number of packets the database
$querytotal = pg_query($connection, "select count(*) from main;");
while($row = pg_fetch_row($querytotal)) {
	$row = implode($row);
	$totalrows = $row;
}
//if the database is packetstream, count the machineid units. If not, return a 0.
if ($database == "packetstream") {
$querymachines = pg_query($connection, "select count(*) from (SELECT DISTINCT machineid FROM main) t;");
while($row = pg_fetch_row($querymachines)) {
	$row = implode($row);
	$totaldatasources = $row;
	}
} else {
	$totaldatasources = 0;
}
//count the external IPs on the database
$queryextips = pg_query($connection, "SELECT COUNT(*) FROM (SELECT DISTINCT destinationip FROM main WHERE destinationip NOT LIKE '10.%' AND destinationip !~ '^172[.](1[6-9]|2[0-9]|3[0,1])[.]*' AND destinationip NOT LIKE '192.168.%') ips;");
while($row = pg_fetch_row($queryextips)) {
	$row = implode($row);
	$externalips = $row;
	}
//count the total count of MACs in the database
$querytotalmacs = pg_query($connection, "SELECT COUNT(*) FROM (SELECT DISTINCT * FROM (SELECT DISTINCT sourcemac FROM main UNION SELECT DISTINCT destinationmac FROM main) macs) total");
while($row = pg_fetch_row($querytotalmacs)) {
	$row = implode($row);
	$totalmacs = $row;
	}
//count the total of multicast packets on the database
$querymulticasts = pg_query($connection, "SELECT COUNT(*) FROM (SELECT destinationip FROM main WHERE destinationip ~ '^2(2[4-9]|3[2-9])[.]*') multicasts");
while($row = pg_fetch_row($querymulticasts)) {
	$row = implode($row);
	$totalmulticasts = $row;
	}
//count how many distinct protocols there is on the database
$queryprotocols = pg_query($connection, "SELECT COUNT(*) FROM (SELECT DISTINCT protocol FROM main) protocols
");
while($row = pg_fetch_row($queryprotocols)) {
	$row = implode($row);
	$totalprotocols = $row;
	}
//craft the JSON response
$json = "{\"code\":200,\"packetcount\":" . $totalrows . ",\"datasourcecount\":" . $totaldatasources . ",\"externalipcount\":" . $externalips . ",\"maccount\":" . $totalmacs . ",\"multicastcount\":" . $totalmulticasts . ",\"protocolcount\":" . $totalprotocols .  "}";
//return the JSON response
echo $json;
}
