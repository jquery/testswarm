<?php
/**
 * "Login" action.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class LoginAction extends Action {

	public function doAction() {

		$request = $this->getContext()->getRequest();

		if ( isset( $_SESSION["username"] ) && isset( $_SESSION["auth"] ) && $_SESSION["auth"] == "yes" ) {
			$username = $_SESSION["username"];
			$this->redirect( swarmpath( "user/$username" ) );
		}

		if ( !$request->wasPosted() ) {
			return;
		}

		$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $request->getVal( "username" ) );
		$password = $request->getVal( "password" );

		if ( $username && $password ) {

			$result = mysql_queryf(
				"SELECT id
				FROM users
				WHERE	name = %s
				AND 	password = SHA1(CONCAT(seed, %s))
				LIMIT 1;",
				$username,
				$password
			);

			if ( mysql_num_rows( $result ) > 0 ) {
				$request->setSessionData( "username", $username );
				$request->setSessionData( "auth", "yes" );

				$this->redirect( swarmpath( "user/$username" ) );

			}
		}

		// We're still here, show error
		$this->error = array(
			"code" => "invalid-input",
			"info" => "Incorrect username or password.",
		);
	}
}
