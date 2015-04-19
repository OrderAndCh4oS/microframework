<?php

	$app->get( '/', function () use ( $app ) {
		$app->render( 'home.twig' );
	});

	$app->get('/message/', function () use ($app) {
		$app->render('message.twig');
		$app->flashKeep();
	})->name('message');

	$app->map( '/add-page/', function () use ( $app, $entityManager ) {

		if ($app->request->isPost()) {
			$newPageName =$_POST['name'];

			$page = new Page();
			$page->setName($newPageName);

			$entityManager->persist($page);
			$entityManager->flush();

			$app->flash('message','Page Added Successfully!');
			$app->redirect($app->urlFor('message'));
		}

		$app->render( 'add-page.twig' );
	})->via('GET', 'POST');

