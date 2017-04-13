<?php
	require_once('connect.php');

	session_start();

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
		$sql = "SELECT treasure_uuid, latitude, longtitude FROM treasure WHERE is_taken=1 and is_deleted=0
				ORDER BY updated_at desc limit 10";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function get_current_treasure($t_id) {
		$sql = "SELECT treasure_uuid, latitude, longtitude FROM treasure WHERE treasure_uuid='$t_id' and is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* status:
		200: ok
		404: session loss
	*/
	header('Content-Type:application/json');
	if (!isset($_SESSION["user_uuid"])) {
		$response_data = ["status" => "404"];
		echo json_encode($response_data);
	} else if ($_SESSION["user_uuid"] == null) {
		$response_data = ["status" => "404"];
		echo json_encode($response_data);
	} else {
		$response_data = ["status" => "200"];

		$u_id = $_SESSION["user_uuid"];
		$t_id = $_SESSION["treasure_uuid"];

		/* get user data */
		$result = get_user_info($u_id);
		$row = $result->fetch_assoc();
		$url = stripcslashes($row["img_url"]);
		$user_info = array('user_uuid' => $row["user_uuid"],
						   'name' => $row["name"],
						   'img_url' => $url);
		$response_data["user_info"] = $user_info;

		/* get treasure list */
		$result = get_recent_taken_treasure();
		while($row = $result->fetch_assoc()) {
			if ($row["treasure_uuid"] == $t_id) {
				continue;
			}
			$treasure_list[] = array('treasure_uuid' => $row["treasure_uuid"],
									 'latitude' => $row["latitude"],
									 'longtitude' => $row["longtitude"]);
		}
		$response_data["treasure_list"] = $treasure_list;

		/* get current treasure info */
		$result = get_current_treasure($t_id);
		$row = $result->fetch_assoc();
		$current_treasure = array('treasure_uuid' => $row["treasure_uuid"],
								  'latitude' => $row["latitude"],
								  'longtitude' => $row["longtitude"]);
		$response_data["current_treasure"] = $current_treasure;

		echo json_encode($response_data);
	}



	$conn->close();
?>