<?php
	// Database connection (replace with your database details)
	$servername = "localhost";
	$username = "kodinng_mk";
	$password = "300Spartans@2024";
	$dbname = "kodinng_krixusdb";

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
?>