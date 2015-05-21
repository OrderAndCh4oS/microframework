<?php

	$app->map( '/add-page/', function () use ( $app, $entityManager ) {

		if ($app->request->isPost()) {
			$name = $_POST['name'];
			$slug = $_POST['slug'];
			$content = $_POST['content'];
			$excerpt = $_POST['excerpt'];

			if (functions\CSRF::check($_POST['csrf'])) {
				$page = new Page();
				$page->setName($name);
				$page->setSlug($slug, $entityManager);
				$page->setContent($content);
				$page->setExcerpt($excerpt);

				$entityManager->persist($page);
				$entityManager->flush();

				$app->flash('message','Page Added Successfully!');
				$app->redirect($app->urlFor('message'));
			}
		}
		$app->render('pages/add-page.twig', array(
			'csrf' => functions\CSRF::generate()
		));
	})->via('GET', 'POST');


	$app->get('/list-pages/', function () use ($app, $entityManager) {

		$pageRepository = $entityManager->getRepository('Page');
		$pages = $pageRepository->findAll();
		$content = "<table>";
		foreach ($pages as $page) {
			$content .= '<tr>';
			$content .= '<td><a href="'.$app->urlFor('view', array('slug' => $page->getSlug())).'">'.$page->getName().'</a></td>';
			$content .= '<td><a href="'.$app->urlFor('edit', array('id' => $page->getId())).'">Edit Page</a></td>';
			$content .= '<td><a href="'.$app->urlFor('delete', array('id' => $page->getId())).'">Delete Page</a></td>';
			$content .= '</tr>';
		}
		$content .= "</table>";
		$app->render('page.twig', array(
			'content' => $content
		));
	})->name('list-pages');

	# Add routes for all pages.

	$app->get('/view/:slug/', function ($slug) use ($app, $entityManager) {
		$pageRepository = $entityManager->getRepository('Page');
		$page = $pageRepository->findOneBy(array(
			'slug' => $slug
		));

		$app->render('view-page.twig', array(
			'name' => $page->getName(),
			'content' => $page->getContent()
		));
	})->name('view');


	$app->map('/edit/:id/', function ($id) use ($app, $entityManager) {
		$page = $entityManager->find('Page', $id);

		if ($app->request->isPost()) {
			$name = $_POST['name'];
			$slug = $_POST['slug'];
			$content = $_POST['content'];
			$excerpt = $_POST['excerpt'];

			if (functions\CSRF::check($_POST['csrf'])) {
				$page->setName($name);
				$page->setSlug($slug, $entityManager);
				$page->setContent($content);
				$page->setExcerpt($excerpt);

				$entityManager->persist($page);
				$entityManager->flush();

				$app->flash('message','Page Added Successfully!');
				$app->redirect($app->urlFor('message'));
			}
		}

		$content = array(
			'name' => $page->getName(),
			'slug' => $page->getSlug(),
			'content' => $page->getContent(),
			'excerpt' => $page->getExcerpt(),
			'id' => $page->getId()
		);
		$app->render('edit-page.twig', array(
			'content' => $content,
			'csrf' =>  functions\CSRF::generate()
		));

	})->via('GET', 'POST')->name('edit')->conditions(array('id' => '\d+'));

	$app->map('/delete/:id/', function ($id) use ($app, $entityManager) {

		if ($app->request->isPost()) {
			if (isset($_POST['delete'])) {
				if (functions\CSRF::check($_POST['csrf'])) {
					$page = $entityManager->find('Page', $id);
					$entityManager->remove($page);
					$entityManager->flush();

					$app->flash('message', 'Page Deleted Successfully!');
					$app->redirect($app->urlFor('message'));
				}
			} else {
				$app->redirect($app->urlFor('list-pages'));
			}
		}

		$app->render('delete.twig', array(
			'csrf' =>  functions\CSRF::generate()
		));

	})->via('GET', 'POST')->name('delete')->conditions(array('id' => '\d+'));