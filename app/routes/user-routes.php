<?php

    use Sarcoma\Users\Users;
    use Sarcoma\Email\Email;

    $app->get('/register/', function () use ($app) {
        $app->render('register.twig', array(
            'csrf' => functions\CSRF::generate()
        ));
    })->name('register');

    $app->post('/create-user/', function () use ($app, $entityManager) {

        $username  = trim($_POST['username']);
        $email     = trim($_POST['email']);
        $password  = trim($_POST['password']);
        if (functions\CSRF::check($_POST['csrf'])) {
            $user  = new Users();
            $error = $user->validate($username, $email, $password, $entityManager);

            if (empty($error)) {

                $user->persistUser($username, $email, $password);
                $user->setActivationToken();
                $entityManager->persist($user);
                $entityManager->flush();

                $createUser = ($user->getId() ? true : false);

                if ($createUser) {
                    $message = new Email();
                    $message->setText(array(
                        'Welcome to the site' => 'Please follow the link below to activate your account.',
                        'Activation Code'     => $user->getActivationToken()
                    ));
                    $message->setEmailTitle('Account Activation');

                    $mail = new PHPMailer();

                    $mail->IsSMTP();

                    $mail->IsHTML(true);

                    $mail->FromName = 'Slim';
                    $mail->From     = 'test@test.com';
                    $mail->AddAddress($email, $username);

                    $mail->Subject = 'Account Activation';
                    $mail->Body    = $message->getMessage();

                    // todo: Test Activation Mail (gmail)
                    if ($mail->Send() || true) {
                        $app->flash('message', 'User created successfully');
                        $app->redirect($app->urlFor('message'));
                    } else {
                        $app->flash('message', 'There was an error sending activation details');
                        $app->redirect($app->urlFor('message'));
                    }
                } else {
                    $app->flash('message', 'User could not be created');
                    $app->redirect($app->urlFor('message'));
                }
            } else {
                $error['message'] = 'User could not be created, please check fields';
                $app->flash('error', $error);
                $app->redirect($app->urlFor('register'));
            }
        }
        $app->render('register.twig', array(
            'csrf' => functions\CSRF::generate()
        ));
    });