<?php
	$title = "Login";

	if ( $_SESSION['username'] && $_SESSION['auth'] == 'yes' ) {
		$username = $_SESSION['username'];
		header("Location: $contextpath/user/$username/");
		exit();
	}

	$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $_POST['username']);
	$password = $_POST['password'];
	$error = "";

	if ( $username && $password ) {

		$sth = $pdo->prepare('SELECT id FROM users WHERE name=? AND password=SHA1(CONCAT(seed, ?)) LIMIT 1;');
		$sth->execute(array($username, $password));

		if ($row = $sth->fetch()) {
			$_SESSION['username'] = $username;
			$_SESSION['auth'] = "yes";

			session_write_close();
			header("Location: " . $GLOBALS['contextpath'] . "/user/$username/");
			exit();
		} else {
			$error = "<p>Error: Incorrect username or password.</p>";
		}

	}
?>
