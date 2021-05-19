<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//get the webroot path from server because php is weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//check if the user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//get the current css theme from memory
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		?>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //set the css stylesheet?>.css">
		<title>PCAPAnalyzer - Dropped API Key</title>
		<meta http-equiv="refresh" content="2; url=/">
	</head>
	<body>
		<?php
		//connect to the credentials management database
		include $basepath . "/functions/managecreds.php";
		$connection = managecreds();
		//escape string to avoid sql injection from parameter
		$key = pg_escape_string($_GET["key"]);
		//delete the provided API key
		$query = "DELETE FROM apikeys WHERE key LIKE '" . $key . "'";
		$querydropkey = pg_query($connection, $query);
		//display a success message and go back
		echo "<br><br><br>";
		echo "<h1>API dropped! Returning to the homepage...</h1>";
		?>
	</body>
</html>
