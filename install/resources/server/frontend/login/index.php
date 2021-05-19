<html>
	<?php
	//get the webroot value from server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//get the css theem variable from memory
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //set the css stylesheet?>.css">
		<title>PCAPAnalyzer - Login</title>
	</head>
	<body>
		<br><br><br><br><br>
		<div>
			<form action="check.php" method=post>
				<label for="username"><h1>Username</h1></label>
				<input type="text" id="username" name="username"><br>
				<label for="password"><h1>Password</h1></label>
				<input type="password" id="password" name="password"><br><br><br>
				<input type="submit" value="Submit">
			</form>
		</div>
	<?php
	//display the login error, if any
	session_start();
	if (isset($_SESSION["loginerror"])) {
		echo $_SESSION["loginerror"];
		unset($_SESSION["loginerror"]);
	}
?>


	</body>
</html>
