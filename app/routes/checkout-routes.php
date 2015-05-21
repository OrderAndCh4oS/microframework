<?php

	$app->get('/view-cart/', function () use ($app, $stripe) {

		$app->render('page.twig', array(
			'content' => ''
		));
	});

	$app->get('/checkout/', function () use ($app, $stripe) {
		$app->render('checkout.twig', array());
	});

	$app->post('/charge/', function () use ($app, $stripe) {
		\Stripe\Stripe::setApiKey($stripe['secret_key']);

		// Get the credit card details submitted by the form
		$token = $_POST['stripeToken'];

		// Create the charge on Stripe's servers - this will charge the user's card
		try {
			$charge = \Stripe\Charge::create(array(
				"amount"      => 1000, // amount in cents, again
				"currency"    => "gbp",
				"source"      => $token,
				"description" => "Example charge"
			));
			$app->flash('message', 'Product Purchased Successfully!');
			$app->redirect($app->urlFor('message'));
		} catch (\Stripe\Error\Card $e) {
			$app->flash('message', 'There was an error processing your payment');
			$app->redirect($app->urlFor('message'));
		}
	});