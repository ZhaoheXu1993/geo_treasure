<?php
	require_once('connect.php');

	session_start();

	/* global variables */
	$GLOBALS["conn"] = $conn;
	$NO_BODY = "no user";

	/* get specific rank, user id, user name and img_url */
	function get_rank_by_rank_name ($rank_name) {
		$sql = "SELECT user.user_uuid, user.name, user.img_url, user_rank.value FROM user
				INNER JOIN user_rank ON user.user_uuid=user_rank.user_uuid
				WHERE user_rank.rank_name='$rank_name' and user_rank.is_deleted=0 ORDER BY user_rank.value DESC, user_rank.updated_at ASC";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* get user rank */
	function get_user_rank_by_rank_name ($u_id, $rank_name) {
		$sql = "SELECT user.name, user.img_url, user_rank.value FROM user
				INNER JOIN user_rank ON user.user_uuid=user_rank.user_uuid
				WHERE user.user_uuid='$u_id' and user_rank.rank_name='$rank_name' and user_rank.is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	/* rank calculator */
	
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
		/* get user data */
		$result = get_rank_by_rank_name("scan");

		if ($result->num_rows > 0) {
			$i = 0;
			while($row = $result->fetch_assoc()) {
				$top_rank[] = array('name' => $row["name"],
							        'img_url' => $row["img_url"],
								    'value' => $row["value"]);
				if ($row["user_uuid"] == $u_id) {
					$user_rank[] = array('rank' => ($i + 1),
						                 'name' => $row["name"],
							     		 'img_url' => $row["img_url"],
								 		 'value' => $row["value"]);
					$response_data["user_rank"] = $user_rank;
				}
				$i++;
			}
			$response_data["top_rank"] = $top_rank;
		} else {
			$response_data["top_rank"] = $NO_BODY;
		}

		echo json_encode($response_data);
	}

	$conn->close();
?>