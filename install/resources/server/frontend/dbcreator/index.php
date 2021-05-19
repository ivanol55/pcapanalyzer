<html>
	<?php 
	//sets the docroot from server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//checks if the user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//sets up a database connection
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//sets the database variable from the connection before
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//gets the current css theme
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet;?>.css">
		<title>PCAPAnalyzer - New database</title>
	</head>
	<body>
		<?php
		//displays the menu
		include $basepath . "/functions/menu.php";
		$page = "dbcreator";
		menu($page, $database);
		?>
		<br><br><br>
		<form action="upload.php" enctype="multipart/form-data" method="POST">
			<h2>Name the database you want to create</h2><br>
			<input type="text" name="dbName" id="dbName" required/><br><br>
			Upload .pcap files to feed the new database<br><br>
			<input type="file" name="file[]" multiple id="file" accept="application/vnd.tcpdump.pcap"><br><br>
			<input type="submit" value="Create new database">
		</form>
		<h3 class="warning">The page will stay loading until the process is done. Do not close the page while it is working.</h3>
	</body>
</html>
