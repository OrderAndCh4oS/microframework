<?php

	$app->get( '/', function () use ( $app ) {
		$app->render( 'home.twig' );
    })->name('home');

	$app->get('/message/', function () use ($app) {
		$app->render('message.twig');
		$app->flashKeep();
	})->name('message');

	require_once 'user-routes.php';
	require_once 'page-routes.php';
	require_once 'product-routes.php';
	require_once 'checkout-routes.php';
