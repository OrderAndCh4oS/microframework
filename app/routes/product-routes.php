<?php
	$app->group('/products', function () use ($app, $entityManager) {
		$app->map('/add/', function () use ($app, $entityManager) {

			if ($app->request->isPost()) {
				$name        = $_POST['name'];
				$slug        = $_POST['slug'];
				$description = $_POST['description'];
				$price       = $_POST['price'];

				if (functions\CSRF::check($_POST['csrf'])) {
					$product = new Product();
					$product->setName($name);
					$product->setSlug($slug, $entityManager);
					$product->setDescription($description);
					$product->setPrice($price);

					$entityManager->persist($product);
					$entityManager->flush();

					$app->flash('message', 'Product Added Successfully!');
					$app->redirect($app->urlFor('message'));
				}
			}
			$app->render('products/add-product.twig', array(
				'csrf' => functions\CSRF::generate()
			));
		})->via('GET', 'POST');

		$app->get('/list/', function () use ($app, $entityManager) {

			$productRepository = $entityManager->getRepository('Product');
			$products          = $productRepository->findAll();
			$content           = "<table>";
			foreach ($products as $product) {
				$content .= '<tr>';
				$content .= '<td><a href="' . $app->urlFor('view-product', array('slug' => $product->getSlug())) . '">' . $product->getName() . '</a></td>';
				$content .= '<td><a href="' . $app->urlFor('edit-product', array('id' => $product->getId())) . '">Edit Product</a></td>';
				$content .= '<td><a href="' . $app->urlFor('delete', array('id' => $product->getId())) . '">Delete Product</a></td>';
				$content .= '</tr>';
			}
			$content .= "</table>";
			$app->render('page.twig', array(
				'content' => $content
			));
		})->name('list-products');

		$app->map('/view/:slug/', function ($slug) use ($app, $entityManager) {
			if($app->getCookie('uid')) {
				$uid = $app->getCookie('uid');
			} else {
				$uid = openssl_random_pseudo_bytes(16);
				$uid = bin2hex($uid);
				$app->setCookie('uid', $uid, '1 week');
			}
			$productRepository = $entityManager->getRepository('Product');
			$product           = $productRepository->findOneBy(array(
				'slug' => $slug
			));

			$name = $product->getName();
			$description = $product->getDescription();
			$price = $product->getprice();

			if ($app->request->isPost()) {

				$cart = $entityManager->getRepository('Cart')->findOneBy(array(
					'user_id' => $uid,
					'product_id' => $product->getId()
				));
				if ($cart) {
					$cart->setUserId($uid);
					$cart->setProductId($product->getId());
					$cart->setQuantity($cart->getQuantity() + $_POST['quantity']);
				} else {
					$cart = new Cart();
					$cart->setUserId($uid);
					$cart->setProductId($product->getId());
					$cart->setQuantity($_POST['quantity']);
				}

				$entityManager->persist($cart);
				$entityManager->flush();

				$app->flash('message', 'Added to Basket');
				$app->redirect($app->urlFor('view-product', array('slug' => $slug)));
			}

			$app->render('products/view-product.twig', array(
				'name'    => $name,
				'content' => $description,
				'price' => $price
			));
		})->via('GET', 'POST')->name('view-product');

		$app->map('/edit/:id/', function ($id) use ($app, $entityManager) {
			$product = $entityManager->find('Product', $id);

			if ($app->request->isPost()) {
				$name        = $_POST['name'];
				$slug        = $_POST['slug'];
				$description = $_POST['description'];
				$price       = $_POST['price'];

				if (functions\CSRF::check($_POST['csrf'])) {
					$product->setName($name);
					$product->setSlug($slug, $entityManager);
					$product->setDescription($description);
					$product->setPrice($price);

					$entityManager->persist($product);
					$entityManager->flush();

					$app->flash('message', 'Product Added Successfully!');
					$app->redirect($app->urlFor('message'));
				}
			}

			$content = array(
				'name'    => $product->getName(),
				'slug'    => $product->getSlug(),
				'content' => $product->getContent(),
				'excerpt' => $product->getExcerpt(),
				'id'      => $product->getId()
			);
			$app->render('products/edit-product.twig', array(
				'content' => $content,
				'csrf'    => functions\CSRF::generate()
			));
		})->via('GET', 'POST')->name('edit-product')->conditions(array('id' => '\d+'));

		$app->map('/delete/:id/', function ($id) use ($app, $entityManager) {

			if ($app->request->isPost()) {
				if (isset($_POST['delete'])) {
					if (functions\CSRF::check($_POST['csrf'])) {
						$product = $entityManager->find('Product', $id);
						$entityManager->remove($product);
						$entityManager->flush();

						$app->flash('message', 'Product Deleted Successfully!');
						$app->redirect($app->urlFor('message'));
					}
				} else {
					$app->redirect($app->urlFor('list-products'));
				}
			}

			$app->render('delete.twig', array(
				'csrf' => functions\CSRF::generate()
			));
		})->via('GET', 'POST')->name('delete-product')->conditions(array('id' => '\d+'));
	});