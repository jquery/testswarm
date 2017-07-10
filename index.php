<?php
/**
 * This is the main front-end entry point for TestSwarm.
 *
 * All HTML-based views served to web browsers start here.
 * The recommended configuration will have web requests
 * rewritten from a path to a query string calling index.php
 *
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
// Valid entry point
define( 'SWARM_ENTRY', 'INDEX' );

require_once __DIR__ . '/inc/init.php';

session_start();

$pageObj = $swarmContext->getRequest()->getPageInstance();

if ( $swarmContext->getRequest()->getHeader( 'X-Swarm-Partial' ) ) {
	header( 'X-Swarm-Partial: 1' );
	if ( $pageObj instanceof Page ) {
		$pageObj->outputPartial();
	} else {
		Page::httpStatusHeader( 404 );
	}
	exit;
}

if ( $pageObj instanceof Page ) {
	try {
		$pageObj->output();
	} catch ( Exception $e ) {
		$pageObj = Error500Page::newFromContext( $swarmContext );
		$pageObj->setExceptionObj( $e );
		$pageObj->output();
	}

} else {
	$pageObj = Error404Page::newFromContext( $swarmContext );
	$pageObj->output();
}

exit;
