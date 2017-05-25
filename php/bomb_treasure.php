<?php
  require_once('connect.php');

  session_start();

  $data = json_decode(file_get_contents("php://input"));
	header('Content-Type:application/json');

  /* global variable */
	$GLOBALS["conn"] = $conn;
  $u_id = $data->user_uuid;

  /* Get all treasre info from user_treasure */
  function get_user_treasure($u_id) {
		$sql = "SELECT treasure_uuid, latitude, longtitude, item FROM user_treasure WHERE is_taken = 1 and is_deleted = 0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

  /* Update user taken treasure status */
  function putback_treasure($t_id){
    $sql = "UPDATE treasure SET is_taken = 0 WHERE treasure_uuid = '$t_id'";
    $result = $GLOBALS["conn"]->query($sql);
		return $result;
  }

  /* Delete user_treasure */
  function delete_treasure(){
    $sql = "DELETE FROM user_treasure";
    return $result;
  }

  /* status:
    404: User treasure is empty;
    200: BOMB;

  */

  $result = get_user_treasure($u_id);
  if ($result->num_rows <= 0 ){
    $response_data = ["status" => "404"]
    echo json_encode($response_data);
  } else {
    $response_data = ["status" => "200"]
    // Delete user treasure and put them back
    $row = $result -> fetch_assoc();
    $current_treasure = $row["treasure_uuid"];
    $result = putback_treasure($current_treasure);

    $result = delete_treasure();

    /*
    $current_treasure_info = array ( array('treasure_uuid' => $row["treasure_uuid"],
								  'latitude' => $row["latitude"],
								  'longtitude' => $row["longtitude"],
                  'item' => $row["item"]);
    $result = insert_treasure($current_treasure_info);
    */
    echo json_encode($response_data);
  }

  $conn->close();

?>
