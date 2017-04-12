<?php
	require_once('connect.php');

	$data = json_decode(file_get_contents("php://input"));
	$get_user_sql = "SELECT user_uuid FROM user WHERE fb_id='$data->fb_id'";

	header('Content-Type:application/json');

	$result = $conn->query($get_user_sql);
	$response_data = [];

	$row = $result->fetch_assoc();

	if ($result->num_rows > 0) {
		// existing user
		$response_data = ["user_uuid" => $row["user_uuid"]];
		echo json_encode($response_data);
	} else {
		// no such user, create a new one
		$uniqid = uniqid();
		$create_user_sql = "INSERT INTO user (user_uuid, name, email, fb_id, img_url) 
							VALUES ('$uniqid', '$data->name', '$data->email','$data->fb_id', '$data->img_url')";
		$conn->query($create_user_sql);
		$response_data = ["user_uuid" => $uniqid];
		echo json_encode($response_data);
	}

	$conn->close();
?>