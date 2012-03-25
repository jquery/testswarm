<?php

	$search_user = preg_replace( "/[^a-zA-Z0-9_ -]/", "", $swarmContext->getRequest()->getVal( "item" ) );

	$title = "$search_user";
	$scripts =
		'<script src="' . swarmpath( 'js/jquery.js' ) . '"></script>'
		. '<script src="' . swarmpath( 'js/pretty.js' ) . '"></script>'
		. '<script src="' . swarmpath( 'js/user.js' ) . '"></script>';

