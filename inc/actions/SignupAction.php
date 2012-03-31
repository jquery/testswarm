<?php
/**
 * "Signup" action.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class SignupAction extends Action {

	public function doAction() {

		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		// Already logged in ?
		if ( $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) === "yes" ) {
			$this->setData( array(
				"status" => "logged-in",
				"username" => $request->getSessionData( "username" ),
			) );
			return;
		}

		if ( !$request->wasPosted() ) {
			$this->setError( "requires-post" );
			return;
		}

		$username = $request->getVal( "username" );
		$password = $request->getVal( "password" );

		if ( !$username || !$password ) {
			$this->setError( "missing-parameters" );
			return;
		}

		// Validate user name (github.com/jquery/testswarm/issues/118)
		// Only allow lowercase a-z, 0-9 and dashed, must start with a letter
		if ( !preg_match( "/^[a-z][-a-z0-9]*$/", $username ) ) {
			$this->setError( "invalid-input", "Username may only contain lowercase a-z, 0-9 and dashes and must start with a letter." );
			return;
		}

		// Check if this user name is already taken
		$row = $db->getRow(str_queryf( "SELECT id FROM users WHERE name = %s;", $username ));

		if ( $row ) {
			$this->setError( "invalid-input", "Username \"$username\" is already taken." );
			return;
		}

		// Create the user
		$db->query(str_queryf(
			"INSERT INTO users (name, created, seed) VALUES(%s, %s, RAND());",
			$username,
			swarmdb_dateformat( SWARM_NOW )
		));
		$userID = $db->getInsertId();

		$db->query(str_queryf(
			"UPDATE
				users
			SET
				updated = %s,
				password = SHA1(CONCAT(seed, %s)),
				auth = SHA1(RAND())
			WHERE	id = %u
			LIMIT 1;",
			swarmdb_dateformat( SWARM_NOW ),
			$password,
			$userID
		));

		$request->setSessionData( "username", $username );
		$request->setSessionData( "auth", "yes" );

		$this->setData( array(
			"status" => "logged-in",
			"username" => $username,
		) );
	}
}
