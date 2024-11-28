<?php
	// Starts session
	session_start();

	// Check if the user is logged in
	if (!isset($_SESSION['username'])) {
		header("Location: ../login.php");
    exit;
	}
	
	// Sets user permissions as variables
	$admin = isset($_SESSION['admin']) ? $_SESSION['admin'] : '';
	$access_group_id = isset($_SESSION['access_group_id']) ? $_SESSION['access_group_id'] : '';
	
		// Check if access_group_id is not 1
	if (!isset($_SESSION['access_group_id']) || $_SESSION['access_group_id'] != "1") {
		echo "Access Denied";
    exit; // Stop further execution if access is denied
	}

?>