<?php
	require_once('connect.php');

	session_start();

	$data = json_decode(file_get_contents("php://input"));

	/* global variables */
	$GLOBALS["conn"] = $conn;

	/* get user items */
	function get_user_items ($u_id) {
		$sql = "SELECT attr_name, value FROM user_attr WHERE user_uuid='$u_id'";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* get max bomb */
	function get_unbomb_treasure() {
		$sql = "SELECT count(*) FROM treasure WHERE is_bomb=0 and is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}
	
	header('Content-Type:application/json');
	$response_data = ["status" => "200"];
	$response_data["total_helmet"] = 0;
	$response_data["used_helmet"] = 0;
	$response_data["total_bomb"] = 0;
	$response_data["used_bomb"] = 0;

	$u_id = $_SESSION["user_uuid"];

	$result = get_unbomb_treasure();
	$count = $result->fetch_assoc();
	$response_data["max_bomb"] = $count["count(*)"];

	$result = get_user_items($u_id);

	if ($result->num_rows > 0) {
		$i = 0;
		while($row = $result->fetch_assoc()) {
			if ($row["attr_name"] == "total_helmet") {
				$response_data["total_helmet"] = $row["value"];
			} else if ($row["attr_name"] == "total_bomb") {
				$response_data["total_bomb"] = $row["value"];
			} else if ($row["attr_name"] == "used_helmet") {
				$response_data["used_helmet"] = $row["value"];
			} else if ($row["attr_name"] == "used_bomb") {
				$response_data["used_bomb"] = $row["value"];
			} else {}

			$i++;
		}
	}

	echo json_encode($response_data);

	$conn->close();
?>