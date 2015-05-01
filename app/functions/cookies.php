<?php

	if (!isset($_COOKIE['a-ok-cookie']) && !isset($_COOKIE['no-cookies-cookie'])) {
		$view->setData('cookies', 'yep');
	} elseif (isset($_COOKIE['a-ok-cookie']) && !isset($_COOKIE['no-cookies-cookie'])) {
		$view->setData('cookies', 'yep');
	}


	$app->map( '/cookie-policy/', function () use ( $app ) {

		if ($app->request->isPost()) {
			$cookie_switch = $_POST['cookie-switch'];

			if ($cookie_switch == 'Disable') {
				$app->setCookie('no-cookies-cookie', true, '5 years');
				$app->deleteCookie('a-ok-cookie');
			} else {
				$app->setCookie('a-ok-cookie', true, '5 years');
				$app->deleteCookie('no-cookies-cookie');
			}

			$app->redirect($app->urlFor('cookie-policy'));
		}

		if (isset($_COOKIE['no-cookies-cookie'])) {
			$cookieMsg = 'Enable';
		} else {
			$cookieMsg = 'Disable';
		}

		$app->render( 'cookies.twig', array(
			'cookieMsg' => $cookieMsg
		));

	})->via('GET', 'POST')->name('cookie-policy');