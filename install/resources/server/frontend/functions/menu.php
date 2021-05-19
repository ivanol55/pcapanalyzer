<?php
function menu($page, $database) {
	//displays the menu. For every ntry, checks if the $page variable is the same. if true, makes it active on css and will be displayed red
	echo "<ul>";
		echo "<li><a ";
			if($page == "home") { echo "class=\"active\" ";} else {echo "";}
		echo "href=\"/\">Home</a></li>";
		echo "<li><a ";
			if($page == "packetstream") { echo "class=\"active\" ";} else {echo "";}
		echo "href=\"/packetstream/\">PacketStream</a></li>";
		echo "<li><a ";
			if($page == "dataglance") { echo "class=\"active\" ";} else {echo "";} 
		echo "href=\"/dataglance/\">DataGlance</a></li>";
		echo "<li><a ";
			if($page == "queryrunner") { echo "class=\"active\" ";} else {echo "";}
		echo "href=\"/queryrunner/\">QueryRunner</a></li>";
		echo "<li><a ";
			if($page == "dbcreator") { echo "class=\"active\" ";} else {echo "";}
		echo "href=\"/dbcreator/\">DbCreator</a></li>";
		echo "<li style=\"float:right\"><a ";
			if($page == "about") { echo "class=\"active\" ";} else {echo "";}
		echo "href=\"/about/\">About</a></li>";
		echo "<li style=\"float:right\"><a ";
			if($page == "changedatabase") { echo "class=\"active\" ";} else {echo "";}
		echo "href=\"/changedatabase/\">Current database: " . $database . "</a></li>";
	echo "</ul>";
}
?>
