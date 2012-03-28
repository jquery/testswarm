<?php
/**
 * This is the main front-end entry point for TestSwarm.
 *
 * All HTML-based views served to web browsers start here.
 * The recommended configuration will have web requests
 * rewritten from a path to a query string calling index.php
 *
 * @since 0.1.0
 * @package TestSwarm
 */

require_once "inc/init.php";

$action = preg_replace( "/[^a-z]/", "", $swarmContext->getRequest()->getVal( "action", "" ) );

if ( !$action ) {
	$action = "home";
}

$actionFile = "inc/actions/$action.php";
$pageFile = "inc/pages/$action.php";
$pageObj = $swarmContext->getRequest()->getPageInstance();

// Action
if ( file_exists( $actionFile ) ) {
	require $actionFile;
}

// Page
if ( $pageObj instanceof Page ) {
	$pageObj->output();

} elseif  ( file_exists( $pageFile ) ) {
	require $pageFile;

} else {
	header( $_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404 );
	echo '<h2>TestSwarm: Invalid action</h2>';
	exit;
}
