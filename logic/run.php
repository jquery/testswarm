<?php
	require "inc/init-usersession.php";

	$user = getItem("username", $_REQUEST, getItem("username", $_SESSION, ""));

	if ( !getItem("user", $_REQUEST, false) && $user ) {
		header("Location: " . swarmpath( "run/$user/" ) );
		exit;
	}

	$title = "Run the Test Swarm";
	$scripts = "";

	if ( $client_id ) {
		$scripts = '<script>SWARM.client_id = ' . json_encode( $client_id ) . ';</script>';
	}

	$scripts .= '<script src="' . swarmpath( "js/jquery.js" ) .'"></script>'
		. '<script src="' . swarmpath( "js/run.js" ) . '?' . time() . '"></script>';
