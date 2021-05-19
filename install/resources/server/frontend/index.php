<html>
	<?php
	//Sets basepath from php server info because php paths are weird
	$basepath = $_SERVER["DOCUMENT_ROOT"];
	//checks if the user is logged in
	include $basepath . "/functions/checklogin.php" ;
	checklogin();
	//sets up a database connection to `postgres`
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//sets the active database with the connection provided before
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//gets the current css theme name
	include $basepath . "/functions/setcss.php";
	$stylesheet = setcss();
	?>
	<head> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //Sets the css theme?>.css">
		<title>PCAPAnalyzer - Home</title>
	</head>
	<body>
		<?php
		//displays the menu
		include $basepath . "/functions/menu.php";
		$page = "home";
		menu($page, $database);
		?>
		<br><br><br><br><br>
		<table width="80%">
			<tr>
				<th class="th-home">Analyzed database</th>
				<th class="th-home">total number of packets</th>
				<th class="th-home">total number of data sources</th>
				<th class="th-home">total number of external IPs contacted</th>
				<th class="th-home">total number of MACs found</th>
				<th class="th-home">total number of multicast packets</th>
				<th class="th-home">Number of different protocols found</th>
			</tr>
			<?php
			//gets a list of available databases
			include $basepath . "/functions/dblist.php";
			$dbList = dblist($connection);
			foreach($dbList as $db) {
			//queries different data points for each database
			$path = $_SERVER["DOCUMENT_ROOT"] . "/functions/dbconnect.php";
			include_once $path;
			$connection = dbconnect($db);
			echo "<tr>";
				echo "<td>" . $db . "</td>";
				//Shows the total number of packets on $db
				$querytotal = pg_query($connection, "select count(*) from main;");
				while($row = pg_fetch_row($querytotal)) {
					$row = implode($row);
					echo "<td>" . $row . "</td>";
					}
				//Shows the total number of machineid's on the system, if the database is packetstream
				$querymachines = pg_query($connection, "select count(*) from (SELECT DISTINCT machineid FROM main) t;");
				if ($db == "packetstream") {
					while($row = pg_fetch_row($querymachines)) {
						$row = implode($row);
						echo "<td>" . $row . "</td>";
						}
				 } else {
					echo "<td>N/A</td>";
				}
				//Shows how many external IPs the database has seen
				$queryextips = pg_query($connection, "SELECT COUNT(*) FROM (SELECT DISTINCT destinationip FROM main WHERE destinationip NOT LIKE '10.%' AND destinationip !~ '^172[.](1[6-9]|2[0-9]|3[0,1])[.]*' AND destinationip NOT LIKE '192.168.%') ips;");
				while($row = pg_fetch_row($queryextips)) {
					$row = implode($row);
					echo "<td>" . $row . "</td>";
					}
				//Shows the total number of MAC addresses the database has logged
				$querytotalmacs = pg_query($connection, "SELECT COUNT(*) FROM (SELECT DISTINCT * FROM (SELECT DISTINCT sourcemac FROM main UNION SELECT DISTINCT destinationmac FROM main) macs) total");
				while($row = pg_fetch_row($querytotalmacs)) {
					$row = implode($row);
					echo "<td>" . $row . "</td>";
					}
				//Counts the total number of multicast packets on the database
				$querymulticasts = pg_query($connection, "SELECT COUNT(*) FROM (SELECT destinationip FROM main WHERE destinationip ~ '^2(2[4-9]|3[2-9])[.]*') multicasts");
				while($row = pg_fetch_row($querymulticasts)) {
					$row = implode($row);
					echo "<td>" . $row . "</td>";
					}
				//Shows how many distinct protocols the database has detected
				$queryprotocols = pg_query($connection, "SELECT COUNT(*) FROM (SELECT DISTINCT protocol FROM main) protocols
");					while($row = pg_fetch_row($queryprotocols)) {
					$row = implode($row);
					echo "<td>" . $row . "</td>";
					}
			echo "</tr>";
	    			}
	    		?>
		</table>
	</body>
</html>
