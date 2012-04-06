<?php
/**
 * This is the main front-end entry point for scripts.
 * All API responses start here.
 * @example ./api.php (same as: api.php?format=json&action=info)
 * @example ./api.php?action=scores
 * @example ./api.php?format=php&action=job&item=1
 * @example ./api.php?format=jsonp&action=swarmstate
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */
// Valid entry point
define( 'TESTSWARM', basename( __FILE__ ) );

require_once "inc/init.php";

$action = $swarmContext->getRequest()->getVal( "action", "info" );
$format = $swarmContext->getRequest()->getVal( "format", "json" );
$className = ucfirst( $action ) . "Action";
$className = class_exists( $className ) ? $className : null;

if ( !Api::isGreyFormat( $format ) ) {
	session_start();
}

if ( $className ) {
	try {
		$actionObj = $className::newFromContext( $swarmContext );
		$actionObj->doAction();
		$response = array();

		if ( $actionObj->getError() ) {
			$response["error"] = $actionObj->getError();
		}
		if ( $actionObj->getData() ) {
			$response[$action] = $actionObj->getData();
		}
	} catch ( Exception $e ) {
		$response = array(
			"error" => array(
				"code" => "internal-error",
				"info" => "An internal error occurred. Action could not be performed. Error message:\n" . $e->getMessage(),
			),
		);
	}
} else {
	$response = array(
		"error" => array(
			"code" => "invalid-input",
			"info" => "Action `$action` does not exist",
		),
	);
}

$api = Api::newFromContext( $swarmContext );
$api->setFormat( $format );
$api->setResponse( $response );
$api->output();

exit;
