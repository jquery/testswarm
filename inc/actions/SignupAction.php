<?php
/**
 * "Signup" action.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class SignupAction extends Action {

	/**
	 * @actionMethod POST: Required.
	 * @actionParam username string
	 * @actionParam password string
	 */
	public function doAction() {
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

		$this->doCreateUser( $username, $password );

		$request->setSessionData( "username", $username );
		$request->setSessionData( "auth", "yes" );
	}

	/**
	 * Creates the actual user, seperated from doAction to allow
	 * make internal use easier, also use or modify the session.
	 * @param $username string
	 * @param $password string
	 * @return bool
	 */
	public function doCreateUser( $username, $password ) {
		$db = $this->getContext()->getDB();

		if ( !$username || !$password ) {
			$this->setError( "missing-parameters" );
			return false;
		}

		// Validate user name (github.com/jquery/testswarm/issues/118)
		// Only allow lowercase a-z, 0-9 and dashed, must start with a letter
		if ( !preg_match( "/^[a-z][-a-z0-9]*$/", $username ) ) {
			$this->setError( "invalid-input", "Username may only contain lowercase a-z, 0-9 and dashes and must start with a letter." );
			return false;
		}

		// Check if this user name is already taken
		$row = $db->getRow(str_queryf( "SELECT id FROM users WHERE name = %s;", $username ));

		if ( $row ) {
			$this->setError( "invalid-input", "Username \"$username\" is already taken." );
			return false;
		}

		// Random between 1,000,000,000 and 9,999,999,999
		$seedHash = sha1( mt_rand( 1000000000, 9999999999 ) );
		$passwordHash = sha1( $seedHash . $password );
		$authTokenHash = sha1( mt_rand( 1000000000, 9999999999 ) );

		// Create the user
		$isInserted = $db->query(str_queryf(
			"INSERT INTO users
			(name, updated, created, seed, password, auth)
			VALUES(%s, %s, %s, %s, %s, %s);",
			$username,
			swarmdb_dateformat( SWARM_NOW ),
			swarmdb_dateformat( SWARM_NOW ),
			$seedHash,
			$passwordHash,
			$authTokenHash
		));

		$newUserId = $db->getInsertId();
		if ( !$isInserted || !$newUserId ) {
			$this->setError( "internal-error", "Insertion of user into database failed." );
			return false;
		}

		$this->setData( array(
			"status" => "logged-in",
			"username" => $username,
			"userID" => $newUserId,
		) );

		return true;
	}
}
