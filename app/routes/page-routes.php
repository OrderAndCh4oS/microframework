<?php

$app->get('/view/:slug/', function ($slug) use ($app, $entityManager) {
    $pageRepository = $entityManager->getRepository('Page');
    $page           = $pageRepository->findOneBy(array(
        'slug' => $slug
    ));

    $app->render('/pages/view-page.twig', array(
        'name'    => $page->getName(),
        'content' => $page->getContent()
    ));
})->name('view');

$app->group('/admin/pages', $authenticate('USER', $entityManager), function () use ($app, $entityManager) {

    $app->get('/list-pages/', function () use ($app, $entityManager) {

        $pageRepository = $entityManager->getRepository('Page');
        $pages          = $pageRepository->findAll();
        $content        = "<table>";
        foreach ($pages as $page) {
            $content .= '<tr>';
            $content .= '<td><a href="'.$app->urlFor('view',
                    array('slug' => $page->getSlug())).'">'.$page->getName().'</a></td>';
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

    $app->map('/add-page/', function () use ($app, $entityManager) {
        $content = array();
        if ($app->request->isPost()) {
            $content['name']    = $_POST['name'];
            $content['slug']    = $_POST['slug'];
            $content['content'] = $_POST['content'];
            $content['excerpt'] = $_POST['excerpt'];

            if (functions\CSRF::check($_POST['csrf'])) {
                $page = new Page();
                $page->setName($content['name']);
                $page->setSlug($content['slug'], $entityManager);
                $page->setContent($content['content']);
                $page->setExcerpt($content['excerpt']);

                $entityManager->persist($page);
                $entityManager->flush();

                $app->flash('message', 'Page Added Successfully!');
                $app->redirect($app->urlFor('message'));
            }
        }
        $app->render('pages/page-form.twig', array(
            'content' => $content,
            'csrf'    => functions\CSRF::generate()
        ));
    })->via('GET', 'POST')->name('add-page');

    $app->map('/pages/edit/:id/', function ($id) use ($app, $entityManager) {
        $page = $entityManager->find('Page', $id);

        $content = array(
            'name'    => $page->getName(),
            'slug'    => $page->getSlug(),
            'content' => $page->getContent(),
            'excerpt' => $page->getExcerpt(),
            'id'      => $page->getId()
        );

        if ($app->request->isPost()) {
            $content['name']    = $_POST['name'];
            $content['slug']    = $_POST['slug'];
            $content['content'] = $_POST['content'];
            $content['excerpt'] = $_POST['excerpt'];

            if (functions\CSRF::check($_POST['csrf'])) {
                $page->setName($content['name']);
                $page->setSlug($content['slug'], $entityManager);
                $page->setContent($content['content']);
                $page->setExcerpt($content['excerpt']);

                $entityManager->persist($page);
                $entityManager->flush();

                $app->flash('message', 'Page Updated Successfully!');
                $app->redirect($app->urlFor('message'));
            }
        }

        $app->render('/pages/page-form.twig', array(
            'title'   => 'Edit Page',
            'content' => $content,
            'csrf'    => functions\CSRF::generate()
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
            'csrf' => functions\CSRF::generate()
        ));
    })->via('GET', 'POST')->name('delete')->conditions(array('id' => '\d+'));
});