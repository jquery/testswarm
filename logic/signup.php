<?php
	$title = "Signup";

	$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $_POST["username"]);
	$password = $_POST["password"];
	$email = $_POST["email"];
	$request = $_POST["request"];
	$error = "";

	if ( $username && $password ) {

		# Figure out what the user's ID number is
		$result = mysql_queryf("SELECT id, password FROM users WHERE name=%s;", $username);

		if ( $row = mysql_fetch_array($result) ) {
			$user_id = intval($row[0]);
			$has_pass = $row[1];

		# If the user doesn't have one, create a new user account
		} else {
			$result = mysql_queryf("INSERT INTO users (name,created,seed) VALUES(%s,NOW(),RAND());", $username);
			$user_id = intval(mysql_insert_id());
		}

		if ( $has_pass ) {
			$error = '<p>Error: Account is already created. Please <a href="' . swarmpath( "login/" ) . '">login</a> instead.</p>';
		} else {
			mysql_queryf("UPDATE users SET updated=NOW(), password=SHA1(CONCAT(seed, %s)), email=%s, request=%s, auth=SHA1(RAND()) WHERE id=%u LIMIT 1;", $password, $email, $request, $user_id);

			$_SESSION["username"] = $username;
			$_SESSION["auth"] = "yes";

			session_write_close();
			header("Location: " . swarmpath( "user/$username/" ) );
			exit();
		}

	}
