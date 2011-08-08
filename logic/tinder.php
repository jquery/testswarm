<?php

	$search_user = preg_replace("/[^a-zA-Z0-9_ -]/", "", $_GET["user"]);

	$title = "$search_user";
	$scripts = '<script type="text/javascript" src="' . swarmpath( 'js/jquery.js' ) . '"></script>' .
		'<script type="text/javascript" src="' . swarmpath( 'js/pretty.js' ) . '"></script>' .
		'<script type="text/javascript" src="' . swarmpath( 'js/view.js' ) . '"></script>';

