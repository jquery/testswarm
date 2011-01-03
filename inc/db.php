<?php

	function sqlite_regexp_match($regexp, $str) {
		if (preg_match("/$regexp/", $str, $matches)) {
			return $matches[0];
		}
		return false;
	}

	function sqlite_concat() {
		return implode('', func_get_args());
	}

	function sqlite_date_format($dt, $fmt) {
		return $dt;
	}

	function sqlite_if($clause, $if_true, $if_false) {
		return $clause ? $if_true : $if_false;
	}

	function sql_datetime_now() {
		$dt = new DateTime('now');
		return $dt->format('Y-m-d H:i:s');
	}

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
		if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
			$pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
		} else if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
			$pdo->sqliteCreateFunction('regexp', 'sqlite_regexp_match', 2);
			$pdo->sqliteCreateFunction('concat', 'sqlite_concat');
			$pdo->sqliteCreateFunction('sha1', 'sha1', 1);
			$pdo->sqliteCreateFunction('date_format', 'sqlite_date_format', 2);
			$pdo->sqliteCreateFunction('if', 'sqlite_if', 3);
		}

	} catch (PDOException $e) {
		die ("Problem setting up database {$config['database']['dsn']}: " . $e->getMessage());
	}

