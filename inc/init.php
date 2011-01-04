<?php
	/**
	 * Get item out of array, falling back on a default if need be.
	 * Complains loudly on failing.
	 */
	function getItem($key, $array, $default=null) {
		if (array_key_exists($key, $array)) {
			return $array[$key];
		} else {
			if (func_num_args() === 3) {
				return $default;
			} else {
				throw Exception('Unable to find key '.$key.' in the array '.var_dump($array, true));
			}
		}
	}
	$username = getItem('username', $_SESSION, getItem('user', $_REQUEST, ''));
	if ( !$username ) {
		$username = $_REQUEST['user'];
	}
	$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $username);
	if ( $username ) {
		$_SESSION['username'] = $username;
	}
	# We need a username to set up an account
	if ( !$username ) {
		# TODO: Improve error message quality.
		exit("Username required. ?user=USERNAME.");
	}
	$client_id = preg_replace("/[^0-9]/", "", getItem('client_id', $_REQUEST, ''));

	if ( $client_id ) {
		$sth = $pdo->prepare('SELECT user_id, useragent_id FROM clients WHERE id=? LIMIT 1;');
		$sth->execute(array($client_id));
		$row = $sth->fetch();

		if ($row) {
			$user_id = $row[0];
			$useragent_id = $row[1];

			# If the client ID is already provided, update its record so
			# that we know that it's still alive
			$sth = $pdo->prepare('UPDATE clients SET updated=? WHERE id=?');
			$sth->execute(array(sql_datetime_now(), $client_id));

		# TODO: Improve error message quality.
		} else {
			echo "Client doesn't exist.";
			exit();
		}
	# The user is setting up a new client session
	} else {
		# Figure out the exact useragent that the user is using
		$sth = $pdo->prepare('SELECT id, name from useragents WHERE engine=? AND ? REGEXP version;');
		$sth->execute(array($browser, $version));

		if ($row = $sth->fetch()) {
			$useragent_id = $row[0];
			$useragent_name = $row[1];

		# If the useragent isn't needed, failover with an error message
		# TODO: Improve error message quality.
		} else {
			echo "Browser is not needed for testing. Browser: $browser Version: $version";
			exit();
		}

		# Figure out what the user's ID number is
		$sth = $pdo->prepare('SELECT id FROM users WHERE name=?;');
		$sth->execute(array($username));

		if ($row = $sth->fetch()) {
			$user_id = intval($row[0]);

		# If the user doesn't have one, create a new user account
		} else {
			$sth = $pdo->prepare('INSERT INTO users (name, created, updated, seed) VALUES(?,?,?,?);');
			$now = sql_datetime_now();
			$sth->execute(array($username, $now, $now, mt_rand()));
			$user_id = intval($pdo->lastInsertId());
		}

		# Insert in a new record for the client and get its ID
		$sth = $pdo->prepare('INSERT INTO clients (user_id, useragent_id, useragent, os, ip, created, updated) VALUES(?,?,?,?,?,?,?);');
		$now = sql_datetime_now();
		$sth->execute(array($user_id, $useragent_id, $useragent, $os, $ip, $now, $now));
		$client_id = $pdo->lastInsertId();
	}
