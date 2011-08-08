<?php
	require "inc/init.php";

	$user = getItem("username", $_REQUEST, getItem("username", $_SESSION, ""));

	if ( !getItem("user", $_REQUEST, false) && $user ) {
		header("Location: " . swarmpath( "run/$user/" ) );
		exit;
	}

	$result = mysql_queryf("SELECT name FROM clients, useragents WHERE clients.id=%u AND useragents.id=useragent_id LIMIT 1;", $client_id);

	if ( $row = mysql_fetch_array($result) ) {
		$useragent_name = $row[0];
	}

	$title = "Run the Test Swarm";
	$scripts = "";

	if ( $client_id ) {
		$scripts = "<script type='text/javascript'>var client_id = $client_id;</script>";
	}

	$scripts .= '<script type="text/javascript" src="' . swarmpath( 'js/jquery.js' ) .'"></script>' .
						  '<script type="text/javascript" src="' . swarmpath( 'js/run.js' ) . '?' . time() . '"></script>';
