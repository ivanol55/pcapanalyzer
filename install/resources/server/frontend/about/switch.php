<html>
	<?php 
	//set webroot from server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//check if the user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//setup a database connection
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//set the database variable from the connection from before
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//get the current css theme in memory
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	?>
	<head>
		<meta http-equiv="Refresh" content="2; url='../index.php'" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //set the current css stylesheet?>.css">	
		<title>PCAPAnalyzer - CSS theme changed</title>
	</head>
	<body>
		<?php
		//rotate the css stylesheet between light and dark, unless the provided stylesheet is invaled, in which case it defaults to dark
		if (!isset($_GET["stylesheet"]) OR !in_array($_GET["stylesheet"], array("light", "dark"))) {
			echo "<h1>You can't set that as a theme! returning to the frontpage with the current theme...</h1>";
		} else if ($_GET["stylesheet"] == "light") {
			$_SESSION["stylesheet"] = "dark";
			echo "<h1>Changed theme to dark! returning to start page...</h1>";
		} else {
			$_SESSION["stylesheet"] = "light";
			echo "<h1>Changed theme to light! returning to start page...</h1>";
		}
		$stylesheet = $_SESSION["stylesheet"];
		?>
	</body>
</html>
