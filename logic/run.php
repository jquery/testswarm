<?php
	if ( $_REQUEST['user'] ) {
		$_SESSION['username'] = ereg_replace("[^a-zA-Z0-9_ -]", "", $_REQUEST['user']);
		session_write_close();
		header("Location: /run/");
		exit;
	}

	include "inc/init.php";

	$title = "Run the Test Swarm";
	$scripts = "";

	if ( $client_id ) {
		$scripts = "<script type='text/javascript'>var client_id = $client_id;</script>";
	}

	$scripts .= '<script type="text/javascript" src="/js/jquery.js"></script>' .
						  '<script type="text/javascript" src="/js/run.js"></script>';

?>
