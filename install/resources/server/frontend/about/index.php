<html>
	<?php 
	//set the webroot basepath from server because php is weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//check if user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//setup a connection to the database backend
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//get the database variable from the connection before
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//get the current css theme in memory
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	//connect to the selected database
	include $basepath . "/functions/dbconnect.php";
	$connection = dbconnect($database); 
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet;?>.css">	
		<title>PCAPAnalyzer - About</title>
	</head>
	<body>
		<?php
		//display menu
		include $basepath . "/functions/menu.php";
		$page = "about";
		menu($page, $database);
		?>	
		<br><br><br><br>
		<h1>The PCAPAnalyzer Software project is licensed under the <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">GNU General Public License version 3</a>, originally created by <a href="https://github.com/ivanol55">Ivanol55</a>. The full license can be found in the project's repository.</h1>
		<br><br>
		<h1>The currently active theme is:</h1> 
		<h1><?php echo $stylesheet; //set the css stylesheet ?></h1>
		<form action="switch.php">
			<input type="hidden" name="stylesheet" value="<?php echo $stylesheet;?>">
			<input type="submit" value="Change theme">
		</form>
		<br><br>
		<h1><a>Admin zone</a></h1>
		<table>
			<tr>
				<td><h1><a href="/usermanagement/"> User management </a></h1></td>
				<td><h1><a href="/apimanagement/"> API management </a></td></h1>
			</tr>
		</table>
	</body>
</html>
