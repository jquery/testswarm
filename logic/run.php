<?php
	require "inc/init-usersession.php";

	$request = $swarmContext->getRequest();

	$user = $request->getVal( "username", $request->getSessionData( "username" ) );

	if ( !$request->getVal( "user" ) && $user ) {
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
