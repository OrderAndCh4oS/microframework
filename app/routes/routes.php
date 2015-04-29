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
			$newPageName = $_POST['name'];
			$newPageSlug = $_POST['slug'];

			if (CSRF::check($_POST['csrf'])) {
				$page = new Page();
				$page->setName($newPageName);
				$page->setSlug($newPageSlug, $entityManager);

				$entityManager->persist($page);
				$entityManager->flush();

				$app->flash('message','Page Added Successfully!');
				$app->redirect($app->urlFor('message'));
			}
		}
		$app->render('add-page.twig', array(
			'csrf' => CSRF::generate()
		));
	})->via('GET', 'POST');


	$app->get('/list-pages/', function () use ($app, $entityManager) {

		$pageRepository = $entityManager->getRepository('Page');
		$pages = $pageRepository->findAll();
		$content = "<ul class=\"page-links\">";
		foreach ($pages as $page) {
			$content .= '<li><a href="'.$app->urlFor($page->getId()).'">'.$page->getName().'</a></li>';
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

		$app->map('/edit/'.$page->getId().'/', function () use ($app, $entityManager, $page) {

			if ($app->request->isPost()) {
				$name = $_POST['name'];
				$slug = $_POST['slug'];
				$id = $_POST['id'];

				if (CSRF::check($_POST['csrf'])) {

					$page = $entityManager->find('Page', $id);
					$page->setName($name);
					$page->setSlug($slug, $entityManager);

					$entityManager->flush();

					$app->flash('message', 'Page Updated Successfully!');
					$app->redirect($app->urlFor('message'));
				}
			}

			$content = array(
				'name' => $page->getName(),
				'slug' => $page->getSlug(),
				'id' => $page->getId()
			);
			$app->render('edit-page.twig', array(
				'content' => $content,
				'csrf' => CSRF::generate()
			));

		})->via('GET', 'POST')->name($page->getId());
	}