<?php
	require "inc/init.php";

	$user = getItem('username', $_REQUEST, getItem('username', $_SESSION, ''));

	if ( !getItem('user', $_REQUEST, false) && $user ) {
		header("Location: $contextpath/run/$user/");
		exit;
	}

    $sth = $pdo->prepare('SELECT name FROM clients, useragents WHERE clients.id=? AND useragents.id=useragent_id LIMIT 1;');
    $sth->execute(array($client_id));

    if ($row = $sth->fetch()) {
		$useragent_name = $row[0];
	}

	$title = "Run the Test Swarm";
	$scripts = "";

	if ( $client_id ) {
		$scripts = "<script type='text/javascript'>var client_id = $client_id;</script>";
	}

	$scripts .= '<script type="text/javascript" src="' . $GLOBALS['contextpath'] . '/js/jquery.js"></script>' .
						  '<script type="text/javascript" src="' . $GLOBALS['contextpath'] . '/js/run.js?' . time() . '"></script>';
