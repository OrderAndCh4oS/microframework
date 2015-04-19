<?php
	require '../vendor/autoload.php';
	require '../app/doctrine/bootstrap.php';
	include_once '../app/helpers/helpers.php';
	include_once '../app/email/email.php';
	include_once '../src/Page.php';

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

	$app->add(new \Slim\Middleware\SessionCookie(array(
		'expires' => '20 minutes',
		'path' => '/',
		'domain' => null,
		'secure' => false,
		'httponly' => false,
		'name' => 'slim_session',
		'secret' => 'exTw4RUmtSZMuICgk1MT',
		'cipher' => MCRYPT_RIJNDAEL_256,
		'cipher_mode' => MCRYPT_MODE_CBC
	)));

	require '../app/routes/routes.php';

	$app->run();