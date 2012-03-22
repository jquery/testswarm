<?php
/**
 * Initialize global variables related to a user,
 * for requests that require a username.
 *
 * @todo Deprecate these globals:
 * - $username
 * - $user_id
 * - $client_id
 *
 * @since 0.1.0
 * @package TestSwarm
 */
	global $swarmContext;

	$bi = $swarmContext->getBrowserInfo();
	$db = $swarmContext->getDB();
	$request = $swarmContext->getRequest();

	$username = $request->getSessionData( "username", $request->getVal( "user" ) );
	if ( !$username ) {
		$username = $_REQUEST["user"];
	}
	$username = preg_replace( "/[^a-zA-Z0-9_ -]/", "", $username );
	if ( $username ) {
		$_SESSION["username"] = $username;
	}

	# We need a username to set up an account
	if ( !$username ) {
		// @todo Improve error message quality.
		exit( "Username required. ?user=USERNAME." );
	}

	$client_id = preg_replace( "/[^0-9]/", "", $request->getVal( "client_id" ) );

	// Client passed
	if ( $client_id ) {
		// Verify that the client exists,
		// And get the user ID.
		$user_id = $db->getOne(str_queryf(
			"SELECT
				user_id
			FROM
				clients
			WHERE id=%u
			LIMIT 1;",
			$client_id
		));

		if ( $user_id ) {
			// If the client ID is already provided, update its record so
			// that we know that it's still alive
			$db->query(str_queryf(
				"UPDATE clients SET updated=%s WHERE id=%u LIMIT 1;",
				swarmdb_dateformat( SWARM_NOW ),
				$client_id
			));

		} else {
			// @todo Improve error message quality.
			echo "Client doesn't exist.";
			exit;
		}

	// No client id passed, create one
	} else {

		// If the useragent isn't known, abort with an error message
		if ( !$bi->isKnownInTestSwarm() ) {
			echo "Your browser is not supported for testing right now.\n"
				. "Browser: {$bi->getBrowserCodename()} Version: {$bi->getBrowserVersion()}";
			exit;
		}

		// Figure out what the user's ID number is
		$user_id = $db->getOne(str_queryf( "SELECT id FROM users WHERE name=%s;", $username ));

		// If the user doesn't have one, create a new user account
		if ( !$user_id ) {
			$db->query(str_queryf(
				"INSERT INTO users (name, created, updated, seed) VALUES(%s, %s, %s, RAND());",
				$username,
				swarmdb_dateformat( SWARM_NOW ),
				swarmdb_dateformat( SWARM_NOW )
			));
			$user_id = $db->getInsertId();
		}

		// Insert in a new record for the client and get its ID
		$db->query(str_queryf(
			"INSERT INTO clients (user_id, useragent_id, useragent, os, ip, created)
			VALUES(%u, %u, %s, %s, %s, %s);",
			$user_id,
			$bi->getSwarmUserAgentID(),
			$bi->getRawUA(),
			$bi->getOsCodename(),
			$request->getIP(),
			swarmdb_dateformat( SWARM_NOW )
		));

		$client_id = $db->getInsertId();
	}
