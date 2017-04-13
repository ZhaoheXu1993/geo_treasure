<?php
	require_once('connect.php');

	$data = json_decode(file_get_contents("php://input"));
	header('Content-Type:application/json');

	/* global variable */
	$t_id = $data->treasure_uuid;
	$u_id = $data->user_uuid;
	$GLOBALS["conn"] = $conn;

	//echo json_encode($conn);
	//exit();

	/* user treasure */
	function check_user_treasure($u_id, $t_id) {
		$sql = "SELECT * FROM user_treasure WHERE user_uuid='$u_id' and treasure_uuid='$t_id'";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function create_user_treasure($u_id, $t_id) {
		$sql = "INSERT INTO user_treasure (user_uuid, treasure_uuid) 
				VALUES ('$u_id', '$t_id')";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* treasure */
	function check_treasure($t_id) {
		$sql = "SELECT is_taken FROM treasure WHERE treasure_uuid='$t_id' and is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function get_item($t_id) {
		/* TODO: high concurrency: is_taken=0 */
		$sql = "SELECT item FROM treasure WHERE treasure_uuid='$t_id' and is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function take_treasure($t_id) {
		$sql = "UPDATE treasure SET is_taken=1 WHERE treasure_uuid='$t_id'";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* rank */
	function get_rank($u_id, $t_id) {
		$sql = "SELECT user_uuid FROM user_rank WHERE user_uuid='$u_id' and is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function create_rank($u_id) {
		$sql = "INSERT INTO user_rank (user_uuid, scan_treasure, get_treasure)
				VALUES ('$u_id', 1, 0)";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function update_rank($u_id, $rank_name) {
		if ($rank_name != "scan" && $rank_name != "get") {
			return;
		}

		if ($rank_name == "scan") {
			$sql = "UPDATE user_rank SET scan_treasure = scan_treasure + 1 WHERE user_uuid='$u_id'";
			$result = $GLOBALS["conn"]->query($sql);
		} else if ($rank_name == "get") {
			$sql = "UPDATE user_rank SET get_treasure = get_treasure + 1 WHERE user_uuid='$u_id'";
			$result = $GLOBALS["conn"]->query($sql);
		}

		return $result;
	}

	/* status: 
		404: no such treasure;
		300: treasure has been taken;
		200: treasure available
	*/
	$result = check_treasure($t_id);
	if ($result->num_rows <= 0) {
		$response_data = ["status" => "404"];
		echo json_encode($response_data);
	} else {
		$row = $result->fetch_assoc();
		if ($row["is_taken"] == 1) {
			$response_data = ["status" => "300"];

			/* get user rank */
			$result = get_rank($u_id);
			if ($result->num_rows <= 0) {
				/* create user rank */
				$result = create_rank($u_id);
				$result = create_user_treasure($u_id, $t_id);
			} else {
				/* update user rank: scan + 1 */
				$result = check_user_treasure($u_id, $t_id);
				if ($result->num_rows <= 0) {
					$result = update_rank($u_id, "scan");					
					$result = create_user_treasure($u_id, $t_id);
				}
			}

			echo json_encode($response_data);
		} else {
			$response_data = ["status" => "200"];

			/* user treasure */
			$result = take_treasure($t_id);
			$result = create_user_treasure($u_id, $t_id);

			/* user rank */
			$result = update_rank($u_id, "scan");
			$result = update_rank($u_id, "get");

			/* return item */
			$result = get_item($t_id);
			$item_num = $result->fetch_assoc();
			$response_data["item"] = $item_num["item"];
			echo json_encode($response_data);
		}
	}

	$conn->close();
?>