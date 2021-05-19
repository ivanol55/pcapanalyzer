<html>
	<?php 
	//sets the webroot path from server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//checks if the user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//sets up a database connection
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	// gets the database name with the connection specified before
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//gets the current css theme
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	//gets a list of databases
	include $basepath . "/functions/dblist.php";
	$dbList = dblist($connection);
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet;?>.css">
		<title>PCAPAnalyzer - Change database</title>
	</head>
	<body>
		<?php
		//displays the menu
		include $basepath . "/functions/menu.php";
		$page = "changedatabase";
		menu($page, $database);
		?>
		<br><br><br><br><br>
		<h1>Pick database to use</h1>
		<form action="select.php" method="get">
			<select id="database" name="database">
				<?php
				//shows a list of databases to use
				foreach($dbList as $db ) {
					echo "<option value=\"" . $db . "\">" . $db . "</option>";
					}
				?>
			</select> 
			<input value="confirm" type="submit" name="submit"/> 
		</form>
		<br><br><br>
		<h1>or</h1>
		<br><br><br>
		<h1>Pick database to drop</h1>
		<form action="drop.php" method="get">
			<select id="database" name="database">
			<?php
				//shows a list of databases to drop, omitting packetstream.
				foreach($dbList as $db) {
					if ($db != "packetstream") {
						echo "<option value=\"" . $db . "\">" . $db . "</option>";
					}
					}
			?>
			</select>
			<input value="drop" type="submit" name="submit"/> 
		</form>
	</body>
</html>
