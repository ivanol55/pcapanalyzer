<html>
	<?php
	//set the webroot path with server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//check if the user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//setup database connection
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//set database from the connection generated earlier
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//get the current css theme from memory
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //set the css stylesheet?>.css">
		<title>PCAPAnalyzer - User management</title>
	</head>
	<body>
		<?php
		//display the menu
		include $basepath . "/functions/menu.php";
		$page = "usermanagement";
		menu($page, $database);
		?>
		<br><br><br><br><br>
		<h1>Create a new user</h1>
		<form action="create.php" method="post">
			<label for="username">Username:</label> <input type="text" id="username" name="username"><br><br>
  			<label for="password">Password:</label> <input type="password" id="password" name="password"><br><br>
		<input value="create" type="submit" name="submit"/> 
		</form>
		<br><br><br>
		<h1>Drop a user</h1>
		<?php
		//connect to the user management database
		include $basepath . "/functions/managecreds.php";
		$connection = managecreds();
		//get user list
		$queryusers = pg_query($connection, "SELECT username FROM users;");
		echo "<table>";
		while($row = pg_fetch_row($queryusers)) {
			$row = implode($row);
			//display user and "drop user" button
			echo "<tr><td>" . $row . "</td><td><a href=\"drop.php?username=" . $row . "\">drop user</a></td></tr>";
			}
		echo "</table>";
		?>
	</body>
</html>
