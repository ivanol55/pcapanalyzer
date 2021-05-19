<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//Sets basepath from php server info because php paths are weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//checks if the user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//gets the current css theme
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		?>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //Sets the css theme?>.css">
		<title>PCAPAnalyzer - PacketStream</title>
	</head>
	<?php
	//setup a database connection
	include $basepath . "/functions/dbsetup.php" ;
	$connection = setupdb();
	//Gets the current database value, or replaces it
	include $basepath . "/functions/setdatabase.php";
	$database = setdatabase($connection);
	//gets a database list
	include $basepath . "/functions/dblist.php";
	$dbList = dblist($connection);	
	?>
	<body>
		<?php
		//displays the menu
		include $basepath . "/functions/menu.php";
		$page = "packetstream";
		menu($page, $database);
		//sets the page for the system's pagination
		if (!isset($_GET["page"])) {
			$offset = 0;
			$page = 0;
		} elseif ($_GET["page"] == 1) {
			$offset = 0;
			$page = 0;
		} else {
			$offset = (intval($_GET["page"]) * 50) - 50;
			$page = intval($_GET["page"]);
		}
		//connects to the specified database
		$path = $_SERVER["DOCUMENT_ROOT"] . "/functions/dbconnect.php";
		include $path;
		$connection = dbconnect($database); 
		//calculates pagination values
		$pagesquery = pg_query($connection, 'SELECT ROUND(COUNT(*) / 50) AS lastpage FROM main');
		//gets the total number of packets in the connected database
		$rownumquery = pg_query($connection, 'SELECT COUNT(*) FROM main');
		while ($row = pg_fetch_row($rownumquery)) {
			$rownum = implode($row);
			$rownum = intval($rownum);
			}
		//data sanity checks
		if ($rownum < 1) {
			echo "<h1 class=\"warning\">No data here yet!</h1>";
			exit();
		}
		//Sets up filtering
		include $basepath . "/functions/filtering.php";
		//sets up the main data query
		$values = array("machineid","sourcemac","destinationmac","sourceip","destinationip","protocol","sourceport","destinationport","info");
		$query = filterquery($values);
		if (intval($offset) == 0) {
			$query = $query . " ORDER BY id DESC LIMIT 50";
		} else {
			$query = $query . " ORDER BY id DESC LIMIT 50 OFFSET " . $offset;
		}
		$queryshow = pg_query($connection, $query);		
		while($lastPage = pg_fetch_row($pagesquery)) {
			$finalPage = $lastPage[0];
		}
		//more integrity checks
		if (!is_numeric($page)) {
			echo "<h1 class=\"warning\">Page code must be a number!</h1>";
			exit();
		} else if ($page > $finalPage) {
			echo "<h1 class=\"warning\">You're too far back! There's nothing that old here</h1>";
			exit();
		} else if ($page < 0) {
			echo "<h1 class=\"warning\">The page code must be greater than 0!</h1>";
			exit();
		} 
		?>
		<br><br><br>
		<div>
			<form action="index.php?page=1">
				<input type="hidden" name="page" value="1"/>
				Machine ID: <input type="text" name="machineid" <?php if (isset($_SESSION["machineid"])) {echo " value=\"" . $_SESSION["machineid"] . "\"";}?>/>
				source MAC: <input type="text" name="sourcemac" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$" <?php if (isset($_SESSION["sourcemac"])) {echo " value=\"" . $_SESSION["sourcemac"] . "\"";}?>/>
				destination MAC: <input type="text" name="destinationmac" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$" <?php if (isset($_SESSION["destinationmac"])) {echo " value=\"" . $_SESSION["destinationmac"] . "\"";}?>/>
				Source IP: <input type="text" name="sourceip" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" <?php if (isset($_SESSION["sourceip"])) {echo " value=\"" . $_SESSION["sourceip"] . "\"";}?>/>	
				Destination IP: <input type="text" name="destinationip" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" <?php if (isset($_SESSION["destinationip"])) {echo " value=\"" . $_SESSION["destinationip"] . "\"";}?>/>
				Protocol: <input type="text" name="protocol" <?php if (isset($_SESSION["protocol"])) {echo " value=\"" . $_SESSION["protocol"] . "\"";}?>/>
				Source port: <input type="number" name="sourceport" <?php if (isset($_SESSION["sourceport"])) {echo " value=\"" . $_SESSION["sourceport"] . "\"";}?>/>
				Destination port: <input type="number" name="destinationport" <?php if (isset($_SESSION["destinationport"])) {echo " value=\"" . $_SESSION["destinationport"] . "\"";}?>/>
				Info contains: <input type="text" name="info" <?php if (isset($_SESSION["info"])) {echo " value=\"" . $_SESSION["info"] . "\"";}?>/>
				<br>
				<input type="submit" value="apply filters">	
			</form>
			<form action="resetFilters.php"><input type="submit" value="reset filters"/></form>
			<table width="99%">
				<?php
				//dynamic text based on page filters
				if ($offset == 0) {
					echo "<th colspan=\"10\">Last 50 registered packets</th>";
				} else { 
					echo "<th colspan=\"10\">Last 50 registered packets, " . ($offset - 50) . " packet offset</th>";
				}
				?>
				<tr>
					<th>Timestamp</th>
					<th>MachineID</th>
					<th>SourceMAC</th>
					<th>DestinationMAC</th>
					<th>SourceIP</th>
					<th>DestinationIP</th>
					<th>Protocol</th>
					<th>SourcePort</th>
					<th>DestinationPort</th>
					<th>Info</th>
				</tr>
				<?php
				//display data rows from the 50 selected packets
				while ($row = pg_fetch_row($queryshow)) {
					echo "<tr>";
						foreach (range(0,9) as $number) {
							echo "<td>" . $row[$number] . "</td>";
						}
	    				}
				?>
			</table>
		</div>
		<br>
		<table>
			<tr>
				<?php
				//Menu if there is less than 10 pages
				if ($finalPage <= 10) {
					foreach (range(1,$finalPage) as $start) {
						echo "<td class=\"packet-td\">";
							echo "<a class=\"packet-a\" href=\"index.php?page=" . $start . "\">" . $start . "</a>";
						echo "</td>";
						}
				} else {
				//menu if there are more than 10 pages
				if ($page == 0 or $page == 1 ) { 
					// Navigation menu when the page is the first one 
					$start = 1;
					while ($start <= 10) {
						echo "<td class=\"packet-td\">";
							echo "<a class=\"packet-a\" href=\"index.php?page=" . $start . "\">" . $start . "</a>";
						echo "</td>";
						$start = $start + 1;
						}
				echo "<td class=\"packet-td\">...</td>";	
				echo "<td class=\"packet-td\">";
					echo "<a class=\"packet-a\" href=\"index.php?page=" . $finalPage . "\">" . $finalPage . "</a>";
				echo "</td>";
				} elseif ($page == $finalPage) {	
					// Last page of the opssible options
					echo "<td class=\"packet-td\">";
						echo "<a class=\"packet-a\" href=\"index.php?page=1\">1</a>";
					echo "</td>";
					echo "<td class=\"packet-td\">...</td>";
					$start = intval($finalPage) - 9;
					while ($start <= $finalPage) {
						echo "<td class=\"packet-td\">";
							echo "<a class=\"packet-a\" href=\"index.php?page=" . $start . "\">" . $start . "</a>";
						echo "</td>";	
						$start = $start + 1;
	    					}
				} elseif ($page >= 6 && $page <= intval($finalPage - 6)) {
					// Intermediate pages, more than 5 pages away from first and last
					echo "<td class=\"packet-td\">";
						echo "<a class=\"packet-a\" href=\"index.php?page=1\">1</a>";
					echo "</td>";
					echo "<td class=\"packet-td\">...</td>";
					$start = intval($_GET["page"]) - 4;
					while ($start <= $_GET["page"] + 4) {
						echo "<td class=\"packet-td\">";
							echo "<a class=\"packet-a\" href=\"index.php?page=" . $start . "\">" . $start . "</a>";
						echo "</td>";	
						$start = $start + 1;
						}
					echo "<td class=\"packet-td\">...</td>";	
					echo "<td class=\"packet-td\">";
						echo "<a class=\"packet-a\" href=\"index.php?page=" . $finalPage . "\">" . $finalPage . "</a>";
					echo "</td>";
				} elseif ($page <= 5) {
					// First 5 pages
					$start = $page - ($page - 1);
					while ($start <= 10) {
						echo "<td class=\"packet-td\">";
							echo "<a class=\"packet-a\" href=\"index.php?page=" . $start . "\">" . $start . "</a>";
						echo "</td>";
						$start = $start + 1;
						}
				} else {
					// Last 5 pages
		    			echo "<td class=\"packet-td\">";
						echo "<a class=\"packet-a\" href=\"index.php?page=1\">1</a>";
		    			echo "</td>";
					echo "<td class=\"packet-td\">...</td>";
					$start = ($page - 5);
					while ($start <= $finalPage) {
						echo "<td class=\"packet-td\">";
							echo "<a class=\"packet-a\" href=\"index.php?page=" . $start . "\">" . $start . "</a>";
						echo "</td>";
						$start = $start + 1;
						} 
					}
				} 
				?>
			</tr>
		</table>
		<br><br>
		<form action="index.php" method="get" style="text-align:center;">
			<label for="name">Page to go to</label>
			<input type="number" id="page" name="page" required><br><br>
			<input type="submit" value="Go">
		</form>
	</body>
</html>
