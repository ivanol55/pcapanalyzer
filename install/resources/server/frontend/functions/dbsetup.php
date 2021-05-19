<?php 
function setupdb() {
	//read the necessary configuration values from the config file
	$ini_array = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/../backend/.my.cnf",true);
	$user = $ini_array['client']['user'];
	$password = $ini_array['client']['password'];
	$host = $ini_array['client']['host'];
	$dbList = array();
	//connect to the postgres database as a first fallback to get database system access, because everyone can access it
	$connstring = "host=" . $host . " port=5432 dbname=postgres user= " . $user ." password=" . $password;
	$connection = pg_pconnect($connstring);
	//return the database connection object
	return $connection;
}
?>
