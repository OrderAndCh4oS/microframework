<?php
	require '../vendor/autoload.php';

	$app = new \Slim\Slim(array(
		'view' => new \Slim\Views\Twig(),
		'mode' => 'development'
	));

	$view = $app->view();
	$view->setTemplatesDirectory( './../templates' );
	$view->parserOptions = array(
		'debug' => true,
		'cache' => dirname(__FILE__) . '/cache'
	);

	$view->parserExtensions = array(
		new \Twig_Extension_Debug(),
		new \Slim\Views\TwigExtension(),
	);

	$app->get( '/', function () use ( $app ) {
		$app->render( 'home.twig' );
	});

	$app->run();