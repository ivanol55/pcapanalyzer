<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//set webroot from server because php is weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//check if user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//get the current css theme from memory
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		?>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //set the css stylesheet?>.css">
		<title>PCAPAnalyzer - New user created</title>
		<meta http-equiv="refresh" content="2; url=/">
	</head>
	<body>
		<?php
		//connect to the credential management database
		include $basepath . "/functions/managecreds.php" ;
		$connection = managecreds();
		//escape the username value to avoid sql injection
		$username = pg_escape_string($_POST["username"]);
		$query = "SELECT COUNT(username) FROM users WHERE username LIKE '" . $username . "'";
		//check if username exists already
		$checkusername = pg_query($connection, $query);
		while($row = pg_fetch_row($checkusername)) {
			$notexists = $row[0];
		}
		//if it doesn't exist, create the user
		if ($notexists == 0) {
			//generate the bcrypt hash
			$password = password_hash(pg_escape_string($_POST["password"]), PASSWORD_BCRYPT);
			//insert the user
			$query = "INSERT INTO users VALUES ('" . $username . "', '" . $password . "')";
			pg_query($connection, $query);
			//return a success message and log the user out
			echo "<br><br><br>";
			echo "<h1>User created! logging you out for safety...</h1>";
			unset($_SESSION["username"]);
		} else {
			//if it exists, display an error and go back
			echo "<br><br><br>";
			echo "<h1>User with that name already exists. returning to homepage...</h1>";
		}
		?>
	</body>
</html>
