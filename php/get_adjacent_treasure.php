<?php
	require_once('connect.php');

	session_start();

	$data = json_decode(file_get_contents("php://input"));

	/* global variables */
	$GLOBALS["conn"] = $conn;

	//$data = json_decode(file_get_contents("php://input"));
	//echo $_SESSION["user_uuid"];

	/* user */
	function get_user_info($u_id) {
		$sql = "SELECT user_uuid, name, img_url FROM user WHERE user_uuid='$u_id'";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* treasure list */
	function get_recent_taken_treasure() {
		$sql = "SELECT treasure_uuid, latitude, longitude FROM treasure WHERE is_taken=1 and is_deleted=0
				ORDER BY updated_at desc limit 10";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function get_current_treasure($t_id) {
		$sql = "SELECT treasure_uuid, latitude, longitude FROM treasure WHERE treasure_uuid='$t_id' and is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function get_adjacent_treasure($lat, $lng) {
		$sql = "SELECT treasure_uuid, latitude, longitude FROM treasure WHERE latitude>='$lat'-0.0025 and latitude<='$lat'+0.0025 and longitude>='$lng'-0.0025 and longitude<='$lng'+0.0025 and is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* status:
		200: ok
		404: session loss
	*/
	header('Content-Type:application/json');
	$response_data = ["status" => "200"];

	/* get treasure list */
	$result = get_adjacent_treasure($data->lat, $data->lng);
	while($row = $result->fetch_assoc()) {
		$treasure_list[] = array('treasure_uuid' => $row["treasure_uuid"],
								 'latitude' => $row["latitude"],
								 'longitude' => $row["longitude"]);
	}
	$response_data["treasure_list"] = $treasure_list;

	echo json_encode($response_data);

	$conn->close();
?>