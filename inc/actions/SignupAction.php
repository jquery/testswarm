<?php
/**
 * "Signup" action.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class SignupAction extends Action {

	public function doAction() {

		$request = $this->getContext()->getRequest();

		// Already logged in ?
		if ( $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) == "yes" ) {
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

		$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $request->getVal( "username" ) );
		$password = $request->getVal( "password" );

		if ( !$username || !$password ) {
			$this->setError( "missing-parameters" );
			return;
		}

		# Figure out what the user's ID is
		$result = mysql_queryf("SELECT id, password FROM users WHERE name = %s;", $username);

		if ( $row = mysql_fetch_array($result) ) {
			$this->setError( "account-already-exists" );
			return;
		}

		# If the user doesn't have one, create a new user account
		$result = mysql_queryf(
			"INSERT INTO users (name, created, seed) VALUES(%s, %s, RAND());",
			$username,
			swarmdb_dateformat( SWARM_NOW )
		);
		$user_id = intval( mysql_insert_id() );

		mysql_queryf(
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
			$user_id
		);

		$request->setSessionData( "username", $username );
		$request->setSessionData( "auth", "yes" );

		$this->setData( array(
			"status" => "logged-in",
			"username" => $username,
		) );
	}
}
