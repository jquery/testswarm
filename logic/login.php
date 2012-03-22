<?php
	$title = "Login";

	$request = $swarmContext->getRequest();

	if ( isset( $_SESSION["username"] ) && isset( $_SESSION["auth"] ) && $_SESSION["auth"] == "yes" ) {
		$username = $_SESSION["username"];
		header("Location: " . swarmpath( "user/$username/" ) );
		exit;
	}

	$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $request->getVal( "username" ) );
	$password = $request->getVal( "password" );
	$error = "";

	if ( $request->wasPosted() && $username && $password ) {

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
			$_SESSION["username"] = $username;
			$_SESSION["auth"] = "yes";

			session_write_close();
			header("Location: " . swarmpath( "user/$username/" ) );
			exit();
		} else {
			$error = "<p>Error: Incorrect username or password.</p>";
		}

	}
