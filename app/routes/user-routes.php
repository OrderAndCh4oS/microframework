<?php

    use Sarcoma\Users\Users;

    $app->get( '/register/', function () use ( $app ) {
        $app->render( 'register.twig', array(
            'csrf' => functions\CSRF::generate()
        ) );
    } )->name('register');

    $app->post( '/user-details/', function () use ( $app, $entityManager ) {

        $username = trim( $_POST['username'] );
        $email    = trim( $_POST['email'] );
        $password = trim( $_POST['password'] );
        if (functions\CSRF::check($_POST['csrf'])) {
            $user  = new Users();
            $error = $user->validate( $username, $email, $password, $entityManager );

            if (empty( $error )) {

                $user->persistUser( $username, $email, $password );
                $user->setActivationToken();
                $entityManager->persist( $user );
                $entityManager->flush();

                $createUser = ( $user->getId() ? true : false );
                if ($createUser) {
                    $app->flash( 'message', 'User created successfully' );
                    $app->redirect( $app->urlFor( 'message' ) );
                } else {
                    $app->flash( 'message', 'User could not be created' );
                    $app->redirect( $app->urlFor( 'message' ) );
                }
            } else {
                $error['message'] = 'User could not be created, please check fields';
                $app->flash( 'error', $error );
                $app->redirect( $app->urlFor( 'register' ) );
            }
        }
        $app->render( 'register.twig', array(
            'csrf' => functions\CSRF::generate()
        ) );
    } );