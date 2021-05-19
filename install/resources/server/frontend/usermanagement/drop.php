<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//set the webroot path with server because php is weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//check if user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//get the current css theme from memory
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		?>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet;?>.css">
		<title>PCAPAnalyzer - Dropped user credentials</title>
		<meta http-equiv="refresh" content="2; url=/">
	</head>
	<body>
		<?php
		//connect to the credential management database
		include $basepath . "/functions/managecreds.php";
		$connection = managecreds();
		// get number of users from the database
		$query = "SELECT count(*) FROM users";
		$querycountusers = pg_query($connection, $query);
		while($row = pg_fetch_row($querycountusers)) {
			$count = $row[0];
		}
		//get a list of database users
		$query = "SELECT username FROM users";
		$queryinusers = pg_query($connection, $query);
		$userlist = array();
		while($row = pg_fetch_row($queryinusers)) {
			$userlist[] = $row[0];
		}
		echo "<br><br><br>";
		//check if the user you're dropping exists. if it doesn't, return an error
		if (!in_array(pg_escape_string($_GET["username"]), $userlist)) {
			echo "<h1>You can't drop a user that doesn't exist! returning to the homepage...</h1>";
		} elseif ($count == 1) {
			//if you try to drop the last user, return an error
			echo "<h1>You can't drop all users on the system! returning to the homepage...</h1>";
		} else {
			//if there's no errors, delete the user from the system
			$username = pg_escape_string($_GET["username"]);
			$query = "DELETE FROM users WHERE username LIKE '" . $username . "'";
			$querycountusers = pg_query($connection, $query);
			echo "<h1>User dropped! exiting from the system for security reasons...</h1>";
			unset($_SESSION["username"]);
		}
		?>
	</body>
</html>
