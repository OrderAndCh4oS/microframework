<?php
	use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
	require_once 'app/doctrine/bootstrap.php';

	return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
