<?php 
function checkapikey($connection) {
	//get all apikeys from provided connection to the credentials database
	$querykeys = pg_query($connection, "SELECT key FROM apikeys");
	//make a list with the api keys
	$apikeylist = array();
	while($row = pg_fetch_row($querykeys)) {
		$apikeylist[] = $row[0];
	}
	//if the api key is not valid, return a 403 forbidden code and exit
	if (!isset($_GET["apikey"]) or !in_array($_GET["apikey"], $apikeylist)) {
		echo "{\"code\":403}";
		exit();
	}
}
?>
