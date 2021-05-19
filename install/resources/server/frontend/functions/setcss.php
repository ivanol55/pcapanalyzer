<?php
function setcss() {
	session_start();
	//if the css session variable is not set, or is not in the valid values, set it to dark.
	if (!isset($_SESSION["stylesheet"]) OR !in_array($_SESSION["stylesheet"], array("light","dark"))) {
		$stylesheet = "dark";
		$_SESSION["stylesheet"] = "dark";
	} else {
		//if exists, set it as the session variable
		$stylesheet = $_SESSION["stylesheet"];
	}
	//return the stylesheet theme name
	return $stylesheet;
}
?>
