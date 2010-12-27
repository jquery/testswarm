<?php

	$pdo = null;
	try {
		$pdo = new PDO(
			$config['database']['dsn'],
			$config['database']['username'],
			$config['database']['password'],
			array(PDO::ATTR_PERSISTENT => true)
		);
		// Set Errorhandling to Exception
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// UTF-8 Support for MySQL
		if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
			$pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
		}
	} catch (PDOException $e) {
		die ("Problem setting up database {$config['database']['dsn']}: " . $e->getMessage());
	}

