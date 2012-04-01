<?php
/**
 * "Login" action.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */

class LoginAction extends Action {

	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		// Already logged in ?
		if ( $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) == "yes" ) {
			$username = $request->getSessionData( "username" );
		// Try logging in
		} else {

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

			$res = $db->query(str_queryf(
				"SELECT id
				FROM users
				WHERE	name = %s
				AND 	password = SHA1(CONCAT(seed, %s))
				LIMIT 1;",
				$username,
				$password
			));

			if ( $res && $db->getNumRows( $res ) > 0 ) {
				// Start logged-in session
				$request->setSessionData( "username", $username );
				$request->setSessionData( "auth", "yes" );

			} else {
				$this->setError( "invalid-input" );
				return;
			}
		}

		// We're still here, logged-in succeeded!
		$this->setData( array(
			"status" => "logged-in",
			"username" => $username,
		) );
		return;
	}
}
