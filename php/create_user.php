<?php

	$servername = "localhost";
	$username = "root";
	$password = "acjiccz1246";
	$dbname = "hle_dev";

	// create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	// check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	echo "success";

	$conn->close();
?>