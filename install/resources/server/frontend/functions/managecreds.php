<?php 
function managecreds() {
	//get all the necessary configuration values from the configuration file
	$ini_array = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/../backend/.my.cnf",true);
	$user = $ini_array['credchecking']['user'];
	$password = $ini_array['credchecking']['password'];
	$host = $ini_array['credchecking']['host'];
	$database = $ini_array['credchecking']['database'];
	//connect to the credential management database
	$connstring = "host=" . $host . " port=5432 dbname=" . $database . " user= " . $user ." password=" . $password;
	$connection = pg_pconnect($connstring);
	//return the connection object
	return $connection;
}
?>
