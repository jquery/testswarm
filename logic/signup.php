<?php
	$title = "Signup";

	$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $_POST['username']);
	$password = $_POST['password'];
	$email = $_POST['email'];
	$request = $_POST['request'];
	$error = "";

	if ( $username && $password ) {

		# Figure out what the user's ID number is
		$sth = $pdo->prepare('SELECT id, password FROM users WHERE name=?;');
		$sth->execute(array($username));

		if ($row = $sth->fetch()) {
			$user_id = intval($row[0]);
			$has_pass = $row[1];

		# If the user doesn't have one, create a new user account
		} else {
			$sth = $pdo->prepare('INSERT INTO users (name, created, updated, seed) VALUES(?,?,?,?);');
			$sth->execute(array($username, sql_datetime_now(), time(), mt_rand()));
			$user_id = $pdo->lastInsertId();
		}

		if ( $has_pass ) {
			$error = "<p>Error: Account is already created. Please <a href='$contextpath/login/'>login</a> instead.</p>";
		} else {
			$sth = $pdo->prepare('UPDATE users SET updated=?, password=SHA1(CONCAT(seed, ?)), email=?, request=?, auth=SHA1(?), updated=? WHERE id=?');
			$sth->execute(array(time(), $password, $email, $request, mt_rand(), time(), $user_id));

			$_SESSION['username'] = $username;
			$_SESSION['auth'] = "yes";

			session_write_close();
			header("Location: " . $GLOBALS['contextpath'] . "/user/$username/");
			exit();
		}

	}
?>
