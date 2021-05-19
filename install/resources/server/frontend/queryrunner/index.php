<html>
	<?php
	//set docroot from server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//check if user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//setup database connection
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//sets the database value from the system
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//sets the current css theme
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet;i //sets the css stylesheet?>.css">	
		<title>PCAPAnalyzer - queryRunner</title>
	</head>
	<body>
		<?php
		//display menu
		include $basepath . "/functions/menu.php";
		$page = "queryrunner";
		menu($page, $database);
		?>
		<br><br><br>
		<h1>SELECT query to run on the <?php echo $database;?> database data (table is 'main')</h1>
		<form action="runquery.php">
			<textarea name="query"></textarea><br><br>
			<input type="submit" value="Run query"/>
		</form>
	</body>
</html>
