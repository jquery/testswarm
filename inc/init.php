<?php
	$username = $_SESSION['username'];
	if ( !$username ) {
		$username = $_REQUEST['user'];
	}
	$username = ereg_replace("[^a-z0-9_ -]", "", $username);
	$client_id = ereg_replace("[^0-9]", "", $_REQUEST['client_id']);

	if ( $client_id ) {
		$result = mysql_queryf("SELECT user_id, useragent_id FROM clients WHERE id=%u;", $client_id);

		if ( $row = mysql_fetch_array($result) ) {
			$user_id = $row[0];
			$useragent_id = $row[1];

			# If the client ID is already provided, update its record so
			# that we know that it's still alive
			mysql_queryf("UPDATE clients SET updated=NOW() WHERE id=%u;", $client_id);

		# TODO: Improve error message quality.
		} else {
			echo "Client doesn't exist.";
			exit();
		}

	# We need a username to set up an account
	# TODO: Improve error message quality.
	} else if ( !$username ) {
		echo "Username required. ?user=USERNAME.";
		exit();

	# The user is setting up a new client session
	} else {
		# Figure out the exact useragent that the user is using
		$result = mysql_queryf("SELECT id from useragents WHERE engine=%s AND %s REGEXP version AND os=%s;", $browser, $version, $os);

		if ( $row = mysql_fetch_array($result) ) {
			$useragent_id = $row[0];

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
			$result = mysql_queryf("INSERT INTO users (name,created) VALUES(%s,NOW());", $username);
			$user_id = intval(mysql_insert_id());
		}

		# Insert in a new record for the client and get its ID
		mysql_queryf("INSERT INTO clients (user_id, useragent_id, useragent, ip, created) VALUES(%u,%u,%s,%s,NOW());", $user_id, $useragent_id, $useragent, $ip);
		$client_id = mysql_insert_id();
	}

	$_SESSION['username'] = $username;
?>
