<?php
	$username = getItem("username", $_SESSION, getItem("user", $_REQUEST, ""));
	if ( !$username ) {
		$username = $_REQUEST["user"];
	}
	$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $username);
	if ( $username ) {
		$_SESSION["username"] = $username;
	}
	# We need a username to set up an account
	if ( !$username ) {
		# TODO: Improve error message quality.
		exit("Username required. ?user=USERNAME.");
	}
	$client_id = preg_replace("/[^0-9]/", "", getItem("client_id", $_REQUEST, ""));

	if ( $client_id ) {
		$result = mysql_queryf("SELECT user_id, useragent_id FROM clients WHERE id=%u LIMIT 1;", $client_id);

		if ( $row = mysql_fetch_array($result) ) {
			$user_id = $row[0];
			$useragent_id = $row[1];

			# If the client ID is already provided, update its record so
			# that we know that it's still alive
			mysql_queryf("UPDATE clients SET updated=NOW() WHERE id=%u LIMIT 1;", $client_id);

		# TODO: Improve error message quality.
		} else {
			echo "Client doesn't exist.";
			exit();
		}
	# The user is setting up a new client session
	} else {
		# Figure out the exact useragent that the user is using
		$result = mysql_queryf("SELECT id, name from useragents WHERE engine=%s AND %s REGEXP version;", $browser, $version);

		if ( $row = mysql_fetch_array($result) ) {
			$useragent_id = $row[0];
			$useragent_name = $row[1];

		# If the useragent isn't needed, failover with an error message
		# TODO: Improve error message quality.
		} else {
			echo "Browser is not needed for testing. Browser: $browser Version: $version";
			exit();
		}

		# Figure out what the user's ID number is
		$result = mysql_queryf("SELECT id FROM users WHERE name=%s;", $username);

		if ( $row = mysql_fetch_array($result) ) {
			$user_id = intval($row[0]);

		# If the user doesn't have one, create a new user account
		} else {
			$result = mysql_queryf("INSERT INTO users (name,created,seed) VALUES(%s,NOW(),RAND());", $username);
			$user_id = intval(mysql_insert_id());
		}

		# Insert in a new record for the client and get its ID
		mysql_queryf("INSERT INTO clients (user_id, useragent_id, useragent, os, ip, created) VALUES(%u,%u,%s,%s,%s,NOW());", $user_id, $useragent_id, $useragent, $os, $ip);
		$client_id = mysql_insert_id();
	}
