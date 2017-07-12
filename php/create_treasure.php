<?php
	require_once('connect.php');

	session_start();

	header('Content-Type:application/json');
	$data = json_decode(file_get_contents("php://input"));
	/* global variable */
	$GLOBALS["conn"] = $conn;

	/* insert treasure */
	function generateUniqueID() {
		$token = substr(md5(uniqid(rand(), true)),0,6);  // creates a 6 digit token
		$sql = "SELECT treasure_uuid FROM treasure WHERE treasure_uuid=$token";
		$result = $GLOBALS["conn"]->query($sql);
		if ($result->num_rows != 0) {
		  generateUniqueID();
		} else {
		  return $token;
		}
	}

	function create_treasure($lat, $lng, $item, $description) {
		$uniqid = generateUniqueID();
		$sql = "INSERT INTO treasure (treasure_uuid, latitude, longtitude, item, description) 
				VALUES ('$uniqid', '$lat', '$lng', '$item', '$description')";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* status: 
		200: success
		404: failed
	*/
	$result = create_treasure(floatval($data->lat), floatval($data->lng), intval($data->item), $data->description);
	if ($result) {
		$response_data = ["status" => "200"];
	} else {
		$response_data = ["status" => "404"];
	}

	echo json_encode($response_data);

	$conn->close();
?>