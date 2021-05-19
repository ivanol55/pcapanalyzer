<?php 
function dbconnect($database) {
	//configure necessary values from the config file
	$ini_array = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/../backend/.my.cnf",true);
	$user = $ini_array['client']['user'];
	$password = $ini_array['client']['password'];
	$host = $ini_array['client']['host'];
	//connect to the specified database from the function parameter
	$connstring = "host=" . $host . " port=5432 dbname=" . $database . " user= " . $user ." password=" . $password;
	$connection = pg_pconnect($connstring);
	//return the connection object
	return $connection;
}
?>
