<?php
	require_once('connect.php');

	session_start();

	$data = json_decode(file_get_contents("php://input"));

	/* global variables */
	$GLOBALS["conn"] = $conn;

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
	$response_data = ["status" => "200"];
	$u_id = $_SESSION["user_uuid"];
	/* get user data */
	$result = get_rank_by_rank_name($data->rank_name);

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
	}

	echo json_encode($response_data);

	$conn->close();
?>