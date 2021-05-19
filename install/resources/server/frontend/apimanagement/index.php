<html>
	<?php
	//set the webroot path from server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//check if the user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//setup a database connection
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//get the database name from the connection created earlier
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//get the current css theme from memory
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet;?>.css">
		<title>PCAPAnalyzer - API Management</title>
	</head>
	<body>
		<?php
		//display the menu
		include $basepath . "/functions/menu.php";
		$page = "apimanagement";
		menu($page, $database);
		?>
		<br><br><br><br><br>
		<h1>Create a new API key</h1>
		<form action="create.php" method="post">
		<input value="create" type="submit" name="submit"/> 
		</form>
		<br><br><br>
		<h1>Drop an API key</h1>
		<?php
		//connect to the credential management database
		include $basepath . "/functions/managecreds.php";
		$connection = managecreds();
		//get all the API keys and their creation dates
		$querykeys = pg_query($connection, "SELECT key, creation_date FROM apikeys;");
		echo "<table>";
		//display the api keys, creation date, and an option to delete them
		while($row = pg_fetch_row($querykeys)) {
			echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td><a href=\"drop.php?key=" . $row[0] . "\">delete key</a></td></tr>";
			}
		echo "</table>";
		?>
	</body>
</html>
