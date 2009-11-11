<?php

	$search_user = preg_replace("/[^a-zA-Z0-9_ -]/", "", $_GET['user']);

	$title = "$search_user";
	$scripts = '<script type="text/javascript" src="' . $GLOBALS['contextpath'] . '/js/jquery.js"></script>' .
		'<script type="text/javascript" src="' . $GLOBALS['contextpath'] . '/js/pretty.js"></script>' .
		'<script type="text/javascript" src="' . $GLOBALS['contextpath'] . '/js/view.js"></script>';

?>
