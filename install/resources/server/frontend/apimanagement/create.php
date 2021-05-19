<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//set the webroot path from server because php is weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//check if user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//get the current css theme from memory
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		?>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //set the css stylesheet?>.css">
		<title>PCAPAnalyzer - New API key created</title>
		<meta http-equiv="refresh" content="2; url=/">
	</head>
	<body>
		<?php
		//connect to the credential management database
		include $basepath . "/functions/managecreds.php" ;
		$connection = managecreds();
		//generate a sha256 hash from a random string
		$apikey = hash('sha256', rand());
		//get the current datetime
		$datetime = date("Y-m-d H:i:s");
		//insert the API key and the current time to the apikeys table
		$query = "INSERT INTO apikeys VALUES ('" . $apikey . "','" . $datetime . "')";
		$querycountusers = pg_query($connection, $query);
		//return a success message and exit
		echo "<br><br><br>";
		echo "<h1>API key created! returning to homepage...</h1>";	
		?>
	</body>
</html>
