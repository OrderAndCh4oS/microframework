<?php
use helpers\helpers;

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
			$newPageSlug =$_POST['slug'];

			$page = new Page();
			$page->setName($newPageName);
			$page->setSlug($newPageSlug, $entityManager);

			$entityManager->persist($page);
			$entityManager->flush();

			$app->flash('message','Page Added Successfully!');
			$app->redirect($app->urlFor('message'));
		}

		$app->render( 'add-page.twig' );
	})->via('GET', 'POST');


	$app->get('/list-pages/', function () use ($app, $entityManager) {

		$pageRepository = $entityManager->getRepository('Page');
		$pages = $pageRepository->findAll();
		$content = "<ul class=\"page-links\">";
		foreach ($pages as $page) {
			$page_slug = helpers\Helpers::slugify($page->getName());
			$content .= '<li><a href="'.$app->urlFor($page_slug).'">'.$page->getName().'</a></li>';
		}
		$content .= "</ul>";
		$app->render('page.twig', array(
			'content' => $content
		));
	})->name('list-pages');

	# Add routes for all pages.
	$pageRepository = $entityManager->getRepository('Page');
	$pages = $pageRepository->findAll();

	foreach ($pages as $page) {

		$page_slug = helpers\Helpers::slugify($page->getName());

		$app->get('/'.$page_slug.'/', function () use ($app, $page) {
			$content = $page->getName();
			$app->render('page.twig', array(
				'content' => $content
			));

		})->name($page_slug);
	}