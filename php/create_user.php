<?php
	require_once('connect.php');

	$data = json_decode(file_get_contents("php://input"));
	$sql = "SELECT user_uuid, fb_id FROM user WHERE fb_id='fb000'";
	//echo json_encode($conn);
	$result = $conn->query($sql);
	$response_data = [];

	$row = $result->fetch_assoc();
	//echo json_encode($row);

	if ($result->num_rows > 0) {
		$response_data = ["user_uuid" => $row["user_uuid"]];
		echo json_encode($response_data);
	} else {
		echo 'no user';
	}

	/* get data from json */
	// if (empty($data)) {
	// 	echo " data empty";
	// } else {
	// 	echo $data->name;
	// 	echo json_encode($data);
	// }

	// if (empty($_POST['name'])) {
	// 	echo ' post empty';
	// }

	$conn->close();
?>