<?php
function setdatabase($connection) {
	//Get all the database names from the provided connection
	$queryDBList = pg_query($connection, "select datname FROM pg_database WHERE datname LIKE 'packetstream' OR datname LIKE 'analysis_%';");
	//generate a databse names array
	while($row = pg_fetch_row($queryDBList)) {
		$row = implode($row);
		$dbList[] = $row;
		}
	session_start();
	//get the necessary configuration entries from the configuration file on the server
	$ini_array = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/../backend/.my.cnf",true);
	if (!isset($_SESSION["database"]) OR !in_array($_SESSION["database"], $dbList)) {
		//if the database session variable is not set or is not in the possible databases array, return to packetstream
		$database = $ini_array['client']['database'];
		$_SESSION["database"] = $ini_array['client']['database'];
	} else {
		//if it is valid, save it to variable
		$database = $_SESSION["database"];
	}
	//return the database variable
	return $database;
}
?>
