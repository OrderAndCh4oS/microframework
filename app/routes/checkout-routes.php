<?php

$app->get('/view-cart/', function () use ($app, $entityManager) {

    $cartRepository = $entityManager->getRepository('Cart');
    $cartContents   = $cartRepository->findBy(array(
        'user_id' => $app->getCookie('uid')
    ));
    $cart           = "<table>";
    $cart .= "<th><td>Product Name</td><td>Price</td><td>Quantity</td></th>";
    foreach ($cartContents as $item) {
        $product = $entityManager->find('Product', $item->getProductId());

        $cart .= '<tr>';
        $cart .= '<td>'.$product->getName().'</a></td>';
        $cart .= '<td>'.$item->getQuantity().'</a></td>';
        $cart .= '<td>£'.$product->getPrice().'</a></td>';
        $cart .= '</tr>';
    }
    $cart .= "</table>";

    $app->render('cart.twig', array(
        'cart' => $cart
    ));
})->name('view-cart');

$app->map('/checkout/', function () use ($app, $entityManager, $stripe) {

    $cartRepository = $entityManager->getRepository('Cart');
    $cartContents   = $cartRepository->findBy(array(
        'user_id' => $app->getCookie('uid')
    ));
    $cart           = "<table>";
    $cart .= "<th><td>Product Name</td><td>Price</td><td>Quantity</td></th>";
    $total = 0;
    foreach ($cartContents as $item) {
        $product = $entityManager->find('Product', $item->getProductId());
        $cart .= '<tr>';
        $cart .= '<td>'.$product->getName().'</a></td>';
        $cart .= '<td>'.$item->getQuantity().'</a></td>';
        $cart .= '<td>£'.$product->getPrice().'</a></td>';
        $cart .= '</tr>';
        $total += $product->getPrice();
    }
    $cart .= '<tr><td colspan="2" class="align-right">Total</td><td>£'.$total.'</td></tr>';
    $cart .= "</table>";

    if ($app->request->isPost()) {

        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // Create the charge on Stripe's servers - this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
                "amount"      => ($total * 100), // amount in cents, again
                "currency"    => "gbp",
                "source"      => $token,
                "description" => "Example charge"
            ));
            $app->flash('message', 'Product Purchased Successfully!');
            $app->redirect($app->urlFor('message'));
        } catch (\Stripe\Error\Card $e) {
            $app->flash('message', 'There was an error processing your payment');
            $app->redirect($app->urlFor('message'));
        }
    }

    $app->render('checkout.twig', array(
        'cart' => $cart
    ));
})->via('GET', 'POST');