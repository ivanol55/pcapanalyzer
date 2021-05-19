<?php
function filterquery($values) {
		//craft the first part of the query
		$query = "SELECT packettimestamp, machineid, sourcemac, destinationmac, sourceip, destinationip, protocol, sourceport, destinationport, info FROM main WHERE packettimestamp IS NOT NULL";
		//for every value in the $values filtering array, check if it needs number or string comparison
		foreach ($values as $value) {
			//if it's a number column, use the = comparator to build the query part
			if (in_array($value, array("sourceport", "destinationport"))) {
				if (isset($_GET[$value]) and $_GET[$value] != "") {
					$_SESSION[$value] = pg_escape_string($_GET[$value]);
					$query = $query . " AND " . $value . " = " . $_SESSION[$value];
				} else if (isset($_SESSION[$value])) {
					$query = $query . " AND " . $value . " = " . $_SESSION[$value];
				}	
			} elseif ($value == "info" ) {
			//if it's the info column, add %'s to allow wildcard search
				if (isset($_GET[$value]) and $_GET[$value] != "") {
					$_SESSION[$value] = pg_escape_string($_GET[$value]);
					$query = $query . " AND " . $value . " LIKE '%" . $_SESSION[$value] . "%'";
				} else if (isset($_SESSION[$value])) {
					$query = $query . " AND " . $value . " LIKE '%" . $_SESSION[$value] . "%'";
				}
			} else {
				//if it's anything else, search for specific exact text
				if (isset($_GET[$value]) and $_GET[$value] != "") {
					$_SESSION[$value] = pg_escape_string($_GET[$value]);
					$query = $query . " AND " . $value . " LIKE '" . $_SESSION[$value] . "'";
				} else if (isset($_SESSION[$value])) {
					$query = $query . " AND " . $value . " LIKE '" . $_SESSION[$value] . "'";
				}
			}
		}
	//when done, return the crafted query string
	return $query;
	}
?>
