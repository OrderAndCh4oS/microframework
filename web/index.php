<?php
require_once '../vendor/autoload.php';
require_once '../app/doctrine/bootstrap.php';
require_once '../app/functions/helpers.php';
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
    'view'                => new \Slim\Views\Twig(),
    'mode'                => 'development',
    'cookies.encrypt'     => true,
    'cookies.secret_key'  => 'Shithead27',
    'cookies.cipher'      => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
));

$view = $app->view();
require_once '../app/settings.php';

$view->setTemplatesDirectory('./../templates');
$view->parserOptions = array(
    'debug' => true,
    'cache' => dirname(__FILE__).'/cache'
);

$view->parserExtensions = array(
    new \Twig_Extension_Debug(),
    new \Slim\Views\TwigExtension(),
);

$app->container->singleton(/**
 * @return \Respect\Validation\Validator
 */
    'v', function () {
    return new Respect\Validation\Validator();
});

/**
 * @param string $role
 * @param $entityManager
 *
 * @return Closure
 */
$authenticate = function ($role = 'USER', $entityManager) {
    return function () use ($role, $entityManager) {
        $app = \Slim\Slim::getInstance();
        if (isset($_SESSION['username'])) {
            $userRepository = $entityManager->getRepository('Sarcoma\Users\Users');
            $user           = $userRepository->findOneBy(array(
                'username' => $_SESSION['username']
            ));
            if (!\Sarcoma\Users\Users::auth($user->getRole(), $role)) {
                $app->flash('message', 'Login required');
                $app->redirect($app->urlFor('login'));
            }
        } else {
            $app->flash('message', 'Login required');
            $app->redirect($app->urlFor('login'));
        }
    };
};

require_once '../app/routes/routes.php';

require_once '../app/functions/cookies.php';

$app->run();