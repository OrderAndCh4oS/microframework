<?php
	require_once '../vendor/autoload.php';
	require_once '../app/doctrine/bootstrap.php';
	require_once '../app/functions/helpers.php';
	require_once '../app/functions/email.php';
	require_once '../app/functions/csrf.php';
	require_once '../src/Page.php';
	require_once '../src/Products.php';
	require_once '../src/Cart.php';

	session_cache_limiter(false);
	session_start();

	$stripe = array(
		"secret_key"      => "sk_test_ulnPyOrXXj9DbKJT0LtsWSBS",
		"publishable_key" => "pk_test_R69Y6hhJYAhvL9AdIqvcRnPQ"
	);

	$app = new \Slim\Slim(array(
		'view' => new \Slim\Views\Twig(),
		'mode' => 'development',
		'cookies.encrypt' => true,
		'cookies.secret_key' => 'Shithead27',
		'cookies.cipher' => MCRYPT_RIJNDAEL_256,
		'cookies.cipher_mode' => MCRYPT_MODE_CBC
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

	require_once '../app/routes/routes.php';

	require_once '../app/functions/cookies.php';

	$app->run();