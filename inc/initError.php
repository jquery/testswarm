<?php
/**
 * This file needs to be compatibile with < PHP 5.3.
 */

/**
 * @param string $message
 * @param string $type: cli, js or html.
 */
function swarmInitError( $message, $type = null ) {
	if ( $type === null ) {
		switch ( SWARM_ENTRY ) {
			case 'SCRIPT':
				$type = 'cli';
				break;
			case 'API':
				$type = 'js';
				break;
			case 'INDEX':
			default:
				$type = 'html';
				break;
		}
	}

	if ( $type === 'cli' ) {
		echo $message;
	} elseif ( $type === 'js' ) {
		error_log( 'TestSwarm Fatal error: ' . $message );
		echo "/* $message */\n";
	} else {
		error_log( 'TestSwarm Fatal error: ' . $message );

		header( $_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500 );
		header( 'Content-Type: text/html; charset=utf-8' );
		// Don't cache error pages
		header( 'Cache-control: none' );
		header( 'Pragma: nocache' );

		$basePath = str_replace( '//', '/', pathinfo( $_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME ) . '/' );

		$outputHtml = '<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<title>TestSwarm</title>
	<link rel="stylesheet" href="' . htmlspecialchars( $basePath . 'css/bootstrap.min.css' ) . '">
	<link rel="stylesheet" href="' . htmlspecialchars( $basePath . 'css/testswarm.css' ) . '">
</head>
<body>
	<div class="hero-unit">
		<h1>Internal Error</h1>
		<p>' . htmlspecialchars( $message ) . '</p>
	</div>
</body>
</html>
';
		echo $outputHtml;
	}

	die( E_ERROR );
}
