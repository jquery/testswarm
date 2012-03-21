<?php
/**
 * Initialize global variables related to a user,
 * for requests that require a username.
 * Sets the following globals:
 * - $username
 * - $user_id
 * - $client_id
 *
 * @since 0.1.0
 * @package TestSwarm
 */
	global $swarmBrowser, $swarmRequest;

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

	// Client passed
	if ( $client_id ) {
		// Verify that the client exists,
		// And get the user ID.
		$result = mysql_queryf(
			"SELECT
				user_id
			FROM
				clients
			WHERE id=%u
			LIMIT 1;",
			$client_id
		);

		if ( $row = mysql_fetch_array($result) ) {
			$user_id = $row[0];

			// If the client ID is already provided, update its record so
			// that we know that it's still alive
			mysql_queryf(
				"UPDATE clients SET updated=%s WHERE id=%u LIMIT 1;",
				swarmdb_dateformat( SWARM_NOW ),
				$client_id
			);

		// TODO: Improve error message quality.
		} else {
			echo "Client doesn't exist.";
			exit();
		}

	// No client id passed, create one
	} else {

		// If the useragent isn't known, abort with an error message
		if ( !$swarmBrowser->isKnownInTestSwarm() ) {
			echo "Your browser is not supported for testing right now.\n"
				. "Browser: {$swarmBrowser->getBrowserCodename()} Version: {$swarmBrowser->getBrowserVersion()}";
			exit;
		}

		// Figure out what the user's ID number is
		$result = mysql_queryf( "SELECT id FROM users WHERE name=%s;", $username );

		if ( $row = mysql_fetch_array( $result ) ) {
			$user_id = intval( $row[0] );

		// If the user doesn't have one, create a new user account
		} else {
			$result = mysql_queryf(
				"INSERT INTO users (name, created, updated, seed) VALUES(%s, %s, %s, RAND());",
				$username,
				swarmdb_dateformat( SWARM_NOW ),
				swarmdb_dateformat( SWARM_NOW )
			);
			$user_id = intval( mysql_insert_id() );
		}

		// Insert in a new record for the client and get its ID
		mysql_queryf(
			"INSERT INTO clients (user_id, useragent_id, useragent, os, ip, created)
			VALUES(%u, %u, %s, %s, %s, %s);",
			$user_id,
			$swarmBrowser->getSwarmUserAgentID(),
			$swarmBrowser->getRawUA(),
			$swarmBrowser->getOsCodename(),
			$swarmRequest->getIP(),
			swarmdb_dateformat( SWARM_NOW )
		);

		$client_id = mysql_insert_id();
	}
