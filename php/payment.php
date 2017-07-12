<?php
	require_once('connect.php');
	require_once('stripe-php-4.9.1/init.php');

	session_start();

	/* global variable */
	$GLOBALS["conn"] = $conn;

	$data = json_decode(file_get_contents("php://input"));

	header('Content-Type:application/json');

	function generate_order($u_id, $charge, $item, $quantity) {
		$order_id = uniqid();
		$card_last4 = intval($charge->source->last4);
		$amount = floatval($charge->amount / 10000);
		$quantity = intval($quantity);
		$sql = "INSERT INTO `order` (order_uuid, user_uuid, card_last4, item, quantity, amount) 
				VALUES ('$order_id', '$u_id', '$card_last4', '$item', '$quantity', '$amount')";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function give_items_to_user($u_id, $item, $quantity) {
		$total_name = 'total_'.$item;
		$used_name = 'used_'.$item;
		$sql = "SELECT user_uuid FROM user_attr WHERE user_uuid='$u_id' and attr_name='$total_name'";
		$result = $GLOBALS["conn"]->query($sql);

		if ($result->num_rows > 0) {
			/* update */
			$update_sql = "UPDATE `user_attr` SET `value`=`value`+'$quantity' WHERE `user_uuid`='$u_id' and `attr_name`='$total_name'";
			$result = $GLOBALS["conn"]->query($update_sql);
		} else {
			/* create */
			$insert_total_sql = "INSERT INTO `user_attr` (user_uuid, attr_name, value_type, value) VALUES ('$u_id', '$total_name', 'int', '$quantity')";
			$insert_used_sql = "INSERT INTO `user_attr` (user_uuid, attr_name, value_type, value) VALUES ('$u_id', '$used_name', 'int', '0')";
			$result = $GLOBALS["conn"]->query($insert_total_sql);
			$result = $GLOBALS["conn"]->query($insert_used_sql);
		}

		return $result;
	}

	function check_valid_unbomb_treasure () {
		$sql = "SELECT count(*) FROM treasure WHERE is_bomb=0 and is_deleted=0";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	function set_bomber ($u_id) {
		$sql = "UPDATE `treasure` new, (SELECT treasure_uuid FROM `treasure` WHERE is_bomb=0 and is_deleted=0 LIMIT 1) old
SET new.is_bomb=1, new.bomber_uuid='$u_id'
WHERE new.treasure_uuid = old.treasure_uuid";
		$result = $GLOBALS["conn"]->query($sql);
		return $result;
	}

	$params = array(
		"testmode" => "on",
		"private_live_key" => "",
		"public_live_key" => "",
		"private_test_key" => "sk_test_7ZbCs414Njm7gv4sqDzDtrR0",
		"public_test_key" => "pk_test_p1ajCkUpO1pb0XqxsF2MUYFF"
	);

	if ($params['testmode'] == "on") {
		\Stripe\Stripe::setApiKey($params['private_test_key']);
		$pubkey = $params['public_test_key'];
	} else {
		\Stipe\Stripe::setApiKey($params['private_live_key']);
		$pubkey = $params['public_live_key'];
	}

	if (isset($data->stripeToken)) {
		$response_data["token"] = $data->stripeToken;
		$item = $response_data["item"] = $data->item;
		$quantity = $response_data["quantity"] = $data->quantity;
		$response_data["amount"] = $data->amount;

		$amount = str_replace(".", "", $data->amount);
		$description = "HappyLifeEntitled";

		try {
			$u_id = $_SESSION["user_uuid"];

			if ($item == 'bomb') {
				$result = check_valid_unbomb_treasure();		
				$count = $result->fetch_assoc();
				if ($count["count(*)"] < $quantity) {
					$response_data["status"] = "404";
					$response_data["message"] = "Our treasure world has enough bombs because other guys finished this payment before you.";
					echo json_encode($response_data);
					exit();
				}
			}


			$charge = \Stripe\Charge::create(array(
			  'amount'   => floatval($amount) * 100,
			  'currency' => 'usd',
			  'source'  => $data->stripeToken,
			  "description" => $description
			));

			$response_data["charge"] = $charge;
			$response_data["user_uuid"] = $u_id;

			/* generate order */
			$result = generate_order($u_id, $charge, $item, $quantity);

			if ($result) {
				$response_data["order_inserted"] = "true";
			} else {
				$response_data["order_inserted"] = "false";
			}

			/* put into user attribute */
			$result = give_items_to_user($u_id, $item, $quantity);

			/* set bomber */
			if ($item == 'bomb') {
				$result = set_bomber($u_id);
			}

			if ($result) {
				$response_data["item_inserted"] = "true";
			} else {
				$response_data["item_inserted"] = "false";
			}

			$response_data["item_result"] = $result;
	
	 		$response_data["status"] = "200";
			$response_data["message"] = "success";

			echo json_encode($response_data);
		} catch(\Stripe\Error\Card $e) {
		  // Since it's a decline, \Stripe\Error\Card will be caught
		  $body = $e->getJsonBody();
		  $err  = $body['error'];

		  $status = 'Status is:' . $e->getHttpStatus() . "\n";
		  $type = 'Type is:' . $err['type'] . "\n";
		  $code = 'Code is:' . $err['code'] . "\n";
		  // param is '' in this case
		  $param = 'Param is:' . $err['param'] . "\n";
		  $message = 'Message is:' . $err['message'] . "\n";

		  $response_data["status"] = $status;
		  $response_data["type"] = $type;
		  $response_data["code"] = $code;
		  $response_data["param"] = $param;
		  $response_data["message"] = $message;

		  echo json_encode($response_data);
		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
		  // (maybe you changed API keys recently)
		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
		} catch (\Stripe\Error\Base $e) {
		  // Display a very generic error to the user, and maybe send
		  // yourself an email
		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
		}
	} else {
		$response_data = ["status" => "404"];
		echo json_encode($response_data);
	}

	$conn->close();
?>