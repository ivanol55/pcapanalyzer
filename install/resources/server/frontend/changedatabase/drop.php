<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//set the webroot path from server because php is weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//check if the user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//get the currently set css theme in mmemory
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		?>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //set the css sylesheet ?>.css">
		<title>PCAPAnalyzer - Dropped database</title>
		<meta http-equiv="refresh" content="2; url=/">
	</head>
	<body>
		<?php
		//setup a database connection
		include $basepath . "/functions/dbsetup.php" ;
		$connection = setupdb();
		//set database variable from connection
		include $basepath . "/functions/setdatabase.php";
		$database = setdatabase($connection);
		//get a database list
		include $basepath . "/functions/dblist.php";
		$dbList = dblist($connection);
		//removes packetstream from the droppable databases
		$packetstreamPos = array_search("packetstream", $dbList);
		unset($dbList[$packetstreamPos]);
		//checks if the database is droppable
		if (!in_array($_GET["database"], $dbList)) {
			//error message if not droppable
			echo "<h1>You're not allowed to drop that database! Nothing changed. returning to homepage...</h1>";
			exit();
		} else {
			//if droppable, terminate all connections
			$query = "SELECT pg_terminate_backend (pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '" . $_GET["database"] . "'";
			$querydiscon = pg_query($connection, $query);
			//drop the database
			$query = "DROP DATABASE " . $_GET["database"];
			$querydrop = pg_query($connection, $query);
			//set the connection back to packetstream as default
			$_SESSION["database"] = "packetstream";
			echo "<h1>Database dropped! You are now using packetStream. returning to start page...</h1>";
		}
		?>
	</body>
</html>
