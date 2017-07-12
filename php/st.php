<?php
	require_once('stripe-php-4.9.1/init.php');

	$data = json_decode(file_get_contents("php://input"));

	header('Content-Type:application/json');

	// Token is created using Stripe.js or Checkout!
	// Get the payment token submitted by the form:
	$token = $data->stripeToken;
	$response = ["token" => $token];

	try {
	    \Stripe\Stripe::setApiKey("sk_test_bdniWrjDEe80C2Mx9cNvqNW4");

	    $charge = \Stripe\Charge::create(array(
	        "amount" => 5000,
		  	"currency" => "usd",
		  	"source" => $data->stripeToken
	    ));

	    $response["charge"] = $charge;
	    echo json_encode($response);

	} catch (\Stripe\Error\ApiConnection $e) {
	    // Network problem, perhaps try again.
	    //echo json_encode($token);
	} catch (\Stripe\Error\InvalidRequest $e) {
	    // You screwed up in your programming. Shouldn't happen!
	    //echo json_encode($token);
	} catch (\Stripe\Error\Api $e) {
	    // Stripe's servers are down!
	    //echo json_encode($token);
	} catch (\Stripe\Error\Card $e) {
	    // Card was declined.
	    //echo json_encode($token);
	}

	//echo json_encode($token);
?>