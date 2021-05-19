<?php 
function checklogin() {
	session_start();
	//if the username session variable is not set, redirect to /login/
	if (!isset($_SESSION["username"])) {
		header("Location:/login/");
	}
}
?>
