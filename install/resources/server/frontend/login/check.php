<html>
	<?php
	//set the webroot path from server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	// get the current css theme from memory
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet;?>.css">
		<title>PCAPAnalyzer - login checking...</title>
	</head>
	<body>
		<?php
		//escape sql injection attempts
		$username = pg_escape_string($_POST["username"]);
		$password = pg_escape_string($_POST["password"]);
		//connect to the credential management database
		include $basepath . "/functions/managecreds.php";
		$connection = managecreds();
		//get username list
		$queryusers = pg_query($connection, "SELECT username FROM users");
		$userlist = array();
		while($row = pg_fetch_row($queryusers)) {
			$userlist[] = $row[0];
		}
		session_start();
		//if the username doesn't exist, return with an error
		if (!in_array($username, $userlist)) {
			$_SESSION["loginerror"] = "<h1><a>Wrong user or password. Try again.</a></h1>";
			sleep(2);
			header("Location: /login/index.php");
			exit();
		}
		//check the password hash for the user
		$query = "SELECT password FROM users WHERE username LIKE '" . $username . "'";
		$querycheckpass = pg_query($connection, $query);
		while($row = pg_fetch_row($querycheckpass)) {
			$hash = $row[0];
		}
		//check bcrypt hash comparison
		if (!password_verify($password, $hash)) {
			//if wrong, return with error
			$_SESSION["loginerror"] = "<h1><a>Wrong user or password. Try again.</a></h1>";
			sleep(2);
			header("Location: /login/index.php");
			exit();
		} else {
			//if right, set username and login
			$_SESSION["username"] = $username;
			header("Location: /");
			exit();
		}
	    	?>
		</table>
	</body>
</html>
