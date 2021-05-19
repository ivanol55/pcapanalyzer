<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//sets uo the server basepath because php is weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//checks if the user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//sets up a first database connection
		include $basepath . "/functions/dbsetup.php" ;
		$connection = setupdb();
		//sets the database from the last connection
		include $basepath . "/functions/setdatabase.php";
		$database = setdatabase($connection);
		//sets up css
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		//gets a list of databases
		include $basepath . "/functions/dblist.php";
		$dbList = dblist($connection);
		?>
		<link rel="stylesheet" type="text/css" href="../css/<?php echo $stylesheet;?>.css">
		<title>PCAPAnalyzer - Reset filters</title>
		<meta http-equiv="refresh" content="2; url=index.php?page=1" />
	</head>
	<body>
		<?php
		//Unsets all of the filter variables
		$values = array("machineid","sourcemac","destinationmac","sourceip","destinationip","protocol","sourceport","destinationport","info");
		foreach ($values as $value) {
			unset($_SESSION[$value]);
		}
		?>
		<br><br>
		<h1>Filters unset! returning to packetStream...</h1>
	</body>
</html>
