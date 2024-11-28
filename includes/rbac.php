<?php
	// Starts session
	session_start();

	// Check if the user is logged in
	if (!isset($_SESSION['username'])) {
		header("Location: login.php");
    exit;
	}
	
	// Sets user permissions as variables
	$admin = isset($_SESSION['admin']) ? $_SESSION['admin'] : '';
	$access_group_id = isset($_SESSION['access_group_id']) ? $_SESSION['access_group_id'] : '';
?>