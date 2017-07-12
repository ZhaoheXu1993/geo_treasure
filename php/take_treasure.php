<?php
	require_once('connect.php');

	session_start();

	$data = json_decode(file_get_contents("php://input"));
	header('Content-Type:application/json');

	/* global variable */
	$t_id = $data->t_id;
	$GLOBALS["conn"] = $conn;

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

	function get_user_treasure ($u_id) {
		$sql = "SELECT * FROM user_treasure WHERE user_uuid='$u_id'";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* treasure */
	function check_treasure($t_id) {
		$sql = "SELECT * FROM treasure WHERE treasure_uuid='$t_id' and is_deleted=0";
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

	function update_rank($u_id, $rank_name, $n = 1) {
		if ($rank_name != "scan" && $rank_name != "take") {
			return;
		}

		$sql = "UPDATE user_rank SET value=value+'$n' WHERE user_uuid='$u_id' and rank_name='$rank_name'";
		$result = $GLOBALS["conn"]->query($sql);

		return $result;
	}

	function get_bomber ($t_id) {
		$get_bomber_uuid = "SELECT bomber_uuid FROM treasure WHERE treasure_uuid='$t_id'";
		$result = $GLOBALS["conn"]->query($get_bomber_uuid);
		return $result;
	}

	function update_bomber ($t_id) {
		$result = get_bomber($t_id);
		$r = $result->fetch_assoc();
		$bomber_id = $r["bomber_uuid"];

		$update_treasure = "UPDATE treasure SET is_bomb=0, bomber_uuid='' WHERE treasure_uuid='$t_id'";


		$update_bomber_attr = "UPDATE user_attr SET value=value+1 WHERE user_uuid='$bomber_id' and attr_name='used_bomb'";

		$GLOBALS["conn"]->query($update_treasure);
		$GLOBALS["conn"]->query($update_bomber_attr);
	}

	function check_user_helmet ($u_id) {
		$left_helmet = "SELECT `total`.`value`-`used`.`value` AS `value` FROM `user_attr` `total`, `user_attr` `used` WHERE `total`.`user_uuid`='$u_id' and `used`.`user_uuid`='$u_id' and `total`.`attr_name`='total_helmet' and `used`.`attr_name`='used_helmet'";
		$result = $GLOBALS["conn"]->query($left_helmet);
		return $result;
	}

	function consume_helmet ($u_id) {
		$sql = "UPDATE user_attr SET value=value+1 WHERE user_uuid='$u_id' and attr_name='used_helmet'";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function get_treasure ($u_id, $t_id) {
		/* get treasure */
		take_treasure($t_id);
		create_user_treasure($u_id, $t_id);
		
		/* update rank */
		update_rank($u_id, "take");
	}

	function scan_treasure ($u_id, $t_id) {
		/* update rank */
		update_rank($u_id, "scan");
	}

	function get_treasure_from_user ($from_u_id, $to_u_id, $t_list) {
		$sql = "UPDATE user_treasure SET user_uuid='$to_u_id' WHERE user_uuid='$from_u_id' and treasure_uuid IN (" . implode(',', $t_list) . ")";
		echo $sql;
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function return_treasure ($u_id, $t_list) {
		$delete_user_treasure = "";
		$update_treasures = "UPDATE";
	}

	function add_quote ($e) {
		return '\'' . $e .'\'';
	}

	function assign_bombed_treasure ($u_id, $t_id) {
		/* get bomber */
		$res = get_bomber($t_id);
		$r = $res->fetch_assoc();
		$bomber_id = $r["bomber_uuid"];

		/* get user treasure */
		$res = get_user_treasure($u_id);

		$user_treasure_list = array();	
		while ($user_treasure = $res->fetch_assoc()) {
			array_push($user_treasure_list, $user_treasure["treasure_uuid"]);
		}

		$user_treasure_list = array_map("add_quote", $user_treasure_list);

		/* reassign user treasure */
		$num_of_treasure = count($user_treasure_list);
		$num_of_give_bomber = ($num_of_treasure+2) / 3;
		$num_of_return = ($num_of_treasure+1) / 3;

		$transfer_treasure_list = array_slice($user_treasure_list, 0, $num_of_give_bomber);
		$return_treasure_list = array_slice($user_treasure_list, $num_of_give_bomber, $num_of_return);

		/* update user and bomber treasure */
		get_treasure_from_user($from_u_id, $to_u_id, $transfer_treasure_list);
		return_treasure($u_id, $return_treasure_list);

		/* update user and bomber rank */
		update_rank($u_id, "take", - $num_of_return - $num_of_give_bomber);
		update_rank($bomber_id, "take", $num_of_give_bomber);
	}

	/* status: 
		500: no user session;
		404: no such treasure;
		300: treasure has been taken (no bomb);
		201: get the bomb (1. helmet / 2. no helmet -> bomb);
		200: get the treasure

		bomb rules:

		50% -> bomber
		30% -> return to original location
		else -> remain
	*/
	$response_data = ["response_type" => "json"];
	if (!isset($_SESSION["user_uuid"])) {
		$response_data["status"] = "500";
		$response_data["message"] = "loss user session";

		echo json_encode($response_data);
		$conn->close();
		exit();
	}

	$u_id = $_SESSION["user_uuid"];

	/* test reassign function */
	//assign_bombed_treasure("594082909b571", "018e4b");
	//exit();
	
	/* test */
	//get_treasure_from_user('594082909b570', '594082909b571', array_map("add_quote", array('fdffe5')));
	//exit();

	$result = check_treasure($t_id);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		/* bomb? */
		if ($row["is_bomb"] == 1) {
			/* helmet? */
			$user_helmet = check_user_helmet($u_id);

			if ($user_helmet->num_rows > 0) {
				$helmet = $user_helmet->fetch_assoc();
				if ($helmet["value"] > 0) {
					/* helmet */
					consume_helmet($u_id);
					$get_result = get_treasure($u_id, $t_id);
					$response_data["status"] = "200";
					$response_data["message"] = "Congradulations! Your helmet saved your life. You got the treasure!";

					echo json_encode($response_data);
				} else {
					/* no more helmet */
					$response_data["status"] = "201";
					$assignment = assign_bombed_treasure($u_id);
					$response_data["message"] = "Ohhh! Bomb! You don't have any helmet!";

					scan_treasure($u_id, $t_id);

					echo json_encode($response_data);
				}
			} else {
				/* never purchase helmet before */
				$response_data["status"] = "201";
				$assignment = assign_bombed_treasure($u_id);
				$response_data["message"] = "Ohhh! Bomb! Please purchase some helmets to help you!";

				echo json_encode($response_data);
			}

			/* consume bomb */
			update_bomber($t_id);
		} else {
			/* taken? */
			if ($row["is_taken"] == 1) {
				/* taken */
				$response_data["status"] = "300";
				$response_data["message"] = "This treasure has been taken.";
				scan_treasure($u_id, $t_id);

				echo json_encode($response_data);
			} else {
				/* not taken, get the treasure */
				$get_result = get_treasure($u_id, $t_id);
				$response_data["status"] = "200";
				$response_data["message"] = "Congradulations! Get the treasure!";

				echo json_encode($response_data);
			}
		}
	} else {
		$response_data["status"] = "404";
		$response_data["message"] = "no such treasure";

		echo json_encode($response_data);
	}

	// if ($result->num_rows > 0) {
	// 	$response_data = ["status" => "404"];
	// 	echo json_encode($response_data);
	// } else {
	// 	$row = $result->fetch_assoc();
	// 	if ($row["is_taken"] == 1) {
	// 		$response_data = ["status" => "300"];

	// 		/* get user rank */
	// 		$result = get_rank($u_id);
	// 		if ($result->num_rows <= 0) {
	// 			/* create user rank */
	// 			$result = create_rank($u_id);
	// 			$result = create_user_treasure($u_id, $t_id);
	// 		} else {
	// 			/* update user rank: scan + 1 */
	// 			$result = check_user_treasure($u_id, $t_id);
	// 			if ($result->num_rows <= 0) {
	// 				$result = update_rank($u_id, "scan");					
	// 				$result = create_user_treasure($u_id, $t_id);
	// 			}
	// 		}

	// 		echo json_encode($response_data);
	// 	} else {
	// 		$response_data = ["status" => "200"];

	// 		/* user treasure */
	// 		$result = take_treasure($t_id);
	// 		$result = create_user_treasure($u_id, $t_id);

	// 		/* user rank */
	// 		$result = update_rank($u_id, "scan");
	// 		$result = update_rank($u_id, "get");

	// 		/* return item */
	// 		$result = get_item($t_id);
	// 		$item_num = $result->fetch_assoc();
	// 		$response_data["item"] = $item_num["item"];

	// 		$_SESSION["treasure_uuid"] = $t_id;
	// 		echo json_encode($response_data);
	// 	}
	// }

	$conn->close();
?>