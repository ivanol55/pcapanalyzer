<?php
function dblist($connection) {
	//with the provided connection, list all databases named packetstream or starting with analysis_
	$queryDBList = pg_query($connection, "select datname FROM pg_database WHERE datname LIKE 'packetstream' OR datname LIKE 'analysis_%';");
	//create an array of the database names
	while($row = pg_fetch_row($queryDBList)) {
		$row = implode($row);
		$dbList[] = $row;
		}
	//return the database array
	return $dbList;
}
?>
