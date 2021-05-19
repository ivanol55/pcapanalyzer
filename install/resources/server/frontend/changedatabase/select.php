<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//gets the webroot path from server because php is weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//checks if the user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//gets the current css theme set in memory
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		?>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //sets the css theme?>.css">
		<title>PCAPAnalyzer - Changed database</title>
		<meta http-equiv="refresh" content="2; url=/">
	</head>
	<body>
		<?php
		//setup a database connection
		include $basepath . "/functions/dbsetup.php" ;
		$connection = setupdb();
		//get the active database from the connection set before
		include $basepath . "/functions/setdatabase.php";
		$database = setdatabase($connection);
		//get a list of databases
		include $basepath . "/functions/dblist.php";
		$dbList = dblist($connection);
		//check if trying to select a database that doesn't exist at runtime
		if (!in_array($_GET["database"], $dbList)) {
			echo "<h1>You're not allowed to set that database as active! Nothing changed. returning to homepage...</h1>";
			exit();
		} else {
			//set the active database to the one sent by form
			$_SESSION["database"] = $_GET["database"];
			echo "<h1>Database changed! returning to start page...</h1>";
		}
		?>
	</body>
</html>
