<html>
	<?php
	//Sets the server base path because php is weird 
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//checks if the user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//sts up a database connection
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//sets a database from the connection from earlier
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//sets the css theme
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	//gets a database list
	include $basepath . "/functions/dblist.php";
	$dbList = dblist($connection);
	?>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet;?>.css">
		<title>PCAPAnalyzer - dataGlance</title>
	</head>
	<body>
		<?php
		//displays the menu
		include $basepath . "/functions/menu.php";
		$page = "dataglance";
		menu($page, $database);
		?>
		<br><br><br>
		<div class="grid-container">
			<table class="grid-item" width="80%">
				<tr><th colspan="5">private range IPs found</th></tr>
				<?php
				//connects to the database set in the header
				include $basepath . "/functions/dbconnect.php";
				$connection = dbconnect($database);
				//gets a list of internal IPs on the database
				$queryinternalips = pg_query($connection, "SELECT DISTINCT destinationip FROM main WHERE (destinationip LIKE '10.%' OR destinationip ~ '^172[.](1[6-9]|2[0-9]|3[0,1])[.]*' OR destinationip LIKE '192.168.%') AND destinationip !~ '^2(2[4-9]|3[2-9])[.]*';");
				$count = 1;
				//displays a list of the internal IPs on a table
				while($row = pg_fetch_row($queryinternalips)) {
					$row = implode($row);
					//remove unnecessary empty boxes
					if ($count == 1) {
						echo "<tr><td>" . $row . "</td>";
						$count = $count + 1;
					} else if (in_array($count, array(2, 3, 4))) {
						echo "<td>" . $row . "</td>";
						$count = $count + 1;
					} else {
						echo "<td>" . $row . "</td></tr>";
						$count = 1;
					}
					}
				?>
			</table>
			<table class="grid-item" width="80%">
				<tr><th colspan="5">Public range IPs found</th></tr>
				<?php
				//connects to the database set in the header
				$connection = dbconnect($database);
				//gets a list of external IPs on the database
				$queryinternalips = pg_query($connection, "SELECT DISTINCT destinationip FROM main WHERE destinationip NOT LIKE '10.%' AND destinationip !~ '^172[.](1[6-9]|2[0-9]|3[0,1])[.]*' AND destinationip NOT LIKE '192.168.%' AND destinationip !~ '^2(2[4-9]|3[2-9])[.]*';");
				$count = 1;
				//displays the list of external IPs
				while($row = pg_fetch_row($queryinternalips)) {
					$row = implode($row);
					//remove unnecessary empty boxes
					if ($count == 1) {
						echo "<tr><td>" . $row . "</td>";
						$count = $count + 1;
					} else if (in_array($count, array(2, 3, 4))) {
						echo "<td>" . $row . "</td>";
						$count = $count + 1;
					} else {
						echo "<td>" . $row . "</td></tr>";
						$count = 1;
					}
					}
				?>
			</table>
		</div>
		<div class="grid-container">
			<table class="grid-item" width="80%">
				<tr><th colspan="5">MAC addresses found</th></tr>
				<?php
				//connect to the previously set database
				$connection = dbconnect($database);
				//get the different MACs on the database
				$querymacs = pg_query($connection, "SELECT DISTINCT sourcemac FROM main UNION SELECT DISTINCT destinationmac FROM main");
				$count = 1;
				//display the distinct MACs on the database
				while($row = pg_fetch_row($querymacs)) {
					$row = implode($row);
					//remove unnecessary empty boxes
					if ($count == 1) {
						echo "<tr><td>" . $row . "</td>";
						$count = $count + 1;
					} else if (in_array($count, array(2, 3, 4))) {
						echo "<td>" . $row . "</td>";
						$count = $count + 1;
					} else {
						echo "<td>" . $row . "</td></tr>";
						$count = 1;
					}
					}
				?>
			</table>
			<table class="grid-item" width="80%">
				<tr><th colspan="5">Protocols found</th></tr>
				<?php
				//connect to the previously set database
				$connection = dbconnect($database);
				//get the different protocols on the database
				$queryprotocols = pg_query($connection, "SELECT DISTINCT protocol FROM main");
				$count = 1;
				//display the different protocols on the database
				while($row = pg_fetch_row($queryprotocols)) {
					$row = implode($row);
					//remove unnecessary empty boxes
					if ($count == 1) {
						echo "<tr><td>" . $row . "</td>";
						$count = $count + 1;
						} else if (in_array($count, array(2, 3, 4))) {
						echo "<td>" . $row . "</td>";
						$count = $count + 1;
					} else {
						echo "<td>" . $row . "</td></tr>";
						$count = 1;
					}
					}
				?>
			</table>
		</div>
		<div class="grid-container">
			<table class="grid-item" width="80%">
				<tr><th colspan="5">well-known ports used to communicate</th></tr>
				<?php
				//connect to the previously set database
				$connection = dbconnect($database);
				//get the different well-known ports on the database
				$querywellknown = pg_query($connection, "SELECT DISTINCT destinationport FROM main WHERE destinationport <= 1024");
				$count = 1;
				//display the different well-known ports on the database
				while($row = pg_fetch_row($querywellknown)) {
					$row = implode($row);
					//remove unnecessary empty boxes
					if ($count == 1) {
						echo "<tr><td>" . $row . "</td>";
						$count = $count + 1;
					} else if (in_array($count, array(2, 3, 4))) {
						echo "<td>" . $row . "</td>";
						$count = $count + 1;
					} else {
						echo "<td>" . $row . "</td></tr>";
						$count = 1;
					}
					}
				?>
			</table>
			<table class="grid-item" width="80%">
				<tr><th colspan="5">Non well-known ports used to communicate</th></tr>
				<?php
				//connect to the previously set database
				$connection = dbconnect($database);
				//get the different registered ports on the database
				$queryregistered = pg_query($connection, "SELECT DISTINCT destinationport FROM main WHERE destinationport > 1024 AND destinationport <= 49152");
				$count = 1;
				//display the different registered ports on the database
				while($row = pg_fetch_row($queryregistered)) {
					$row = implode($row);
					//remove unnecessary empty boxes
					if ($count == 1) {
						echo "<tr><td>" . $row . "</td>";
						$count = $count + 1;
					} else if (in_array($count, array(2, 3, 4))) {
						echo "<td>" . $row . "</td>";
						$count = $count + 1;
					} else {
						echo "<td>" . $row . "</td></tr>";
						$count = 1;
					}
					}
				?>
			</table>
		</div>
	</body>
</html>
