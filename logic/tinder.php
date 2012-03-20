<?php

	$search_user = preg_replace( "/[^a-zA-Z0-9_ -]/", "", $swarmRequest->getVal( "user" ) );

	$title = "$search_user";
	$scripts =
		'<script src="' . swarmpath( 'js/jquery.js' ) . '"></script>'
		. '<script src="' . swarmpath( 'js/pretty.js' ) . '"></script>'
		. '<script src="' . swarmpath( 'js/view.js' ) . '"></script>';

