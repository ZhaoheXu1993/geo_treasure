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

	// $sql = "select user_uuid, fb_id from user where fb_id='fb000'";
	// $result = $conn->query($sql);
	// while($row = $result->fetch_assoc()) {
	// 	echo json_encode($row);
	// }

	//echo "success";

	//$conn->close();
?>