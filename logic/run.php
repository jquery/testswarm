<?php
	include "inc/init.php";

	$title = "Run the Test Swarm";

	$scripts = "";

	if ( $_REQUEST['user'] ) {
		session_write_close();
		header("Location: /run/");
		exit;
	}

	if ( $client_id ) {
		$scripts = "<script type='text/javascript'>var client_id = $client_id;</script>";
	}

	$scripts .= '<script type="text/javascript" src="/js/jquery.js"></script>' .
						  '<script type="text/javascript" src="/js/run.js"></script>';

?>
