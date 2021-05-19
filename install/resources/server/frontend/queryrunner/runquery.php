<?php 
//sets the memory limit to infinity so you can dump as many data as you need
ini_set('memory_limit', '-1');
//sets de docroot from server because php is weird
$basepath = $_SERVER["DOCUMENT_ROOT"];
//checks if the user is logged in
include $basepath . "/functions/checklogin.php" ;
checklogin();
// sets up the database connection
include $basepath . "/functions/dbsetup.php" ;
$connection = setupdb();
//sets the database from the connection established before
include $basepath . "/functions/setdatabase.php";
$database = setdatabase($connection);
//gets the stylesheet name currently set on memory
include $basepath . "/functions/setcss.php";
$stylesheet = setcss();
//connects to a database with the name set before
include $basepath . "/functions/dbconnect.php";
$connection = dbconnect($database); 
$fileContents = "";
$assembledRow = "";
//runs the query that it got on the form against the database selected
$data = pg_query($connection, $_GET["query"]);
while($row = pg_fetch_row($data)) {
	//builds the  csv text variable
	foreach ($row as $column) {
		$assembledRow = $assembledRow . $column . ",";
		}
	$assembledRow = substr($assembledRow, 0, -1);
	$assembledRow = $assembledRow . "\n";
	$fileContents = $fileContents . $assembledRow;
	$assembledRow = "";
	}
//opens a file with a specified name
$filename = "query-" . date("Y-m-d_h:i:s", time()) . ".csv";
$file = fopen($filename, "w");
//Dumps the csv variable content into the file
fwrite($file, $fileContents);
fclose($file);
//sets file headers for downloading the csv
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=" . $filename);
header("Content-Type: application/csv; ");
//sends the download and closes the file
readfile($filename);
unlink($filename);
?>
