<?php
	require_once '../vendor/autoload.php';
	require_once '../app/doctrine/bootstrap.php';
	require_once '../app/functions/helpers.php';
	require_once '../app/functions/email.php';
	require_once '../app/functions/csrf.php';
	require_once '../src/Page.php';

	session_cache_limiter(false);
	session_start();

	$app = new \Slim\Slim(array(
		'view' => new \Slim\Views\Twig(),
		'mode' => 'development'
	));

	$view = $app->view();
	require_once '../app/settings.php';


	$view->setTemplatesDirectory( './../templates' );
	$view->parserOptions = array(
		'debug' => true,
		'cache' => dirname(__FILE__) . '/cache'
	);

	$view->parserExtensions = array(
		new \Twig_Extension_Debug(),
		new \Slim\Views\TwigExtension(),
	);

	$app->container->singleton('v', function () {
		return new Respect\Validation\Validator();
	});

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
	require_once '../app/routes/routes.php';

	require_once '../app/functions/cookies.php';

	$app->run();