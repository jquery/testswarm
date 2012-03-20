<?php
/**
 * This the main initialization file for TestSwarm.
 * All web requests should go through here as early as possible.
 */

// Global requirements
require "inc/request.php";
require "inc/utilities.php";

// Defines
define( 'SWARM_NOW', 0 );


/**
 * Default settings
 * @{
 */
$swarmRequest = new WebRequest();

$swarmInstallDir = dirname( __DIR__ );

// Verify that the config.ini file exists
if ( !file_exists( "$swarmInstallDir/testswarm.ini" ) ) {
	echo "testswarm.ini missing!\n";
	exit;
}

$swarmConfig = array(
	"general" => array(
		"timezone" => "UTC",
	),
	"database" => array(
		"host" => "localhost",
		"database" => "testswarm",
		"username" => "root",
		"password" => "root",
	),
	"web" => array(
		"contextpath" => "",
		"title" => "Test Swarm",
	),
	"debug" => array(
		"show_exception_details" => "0",
		"php_error_reporting" => "1",
	),
);

// Read configuration options and let the INI file
// override default settings.
$swarmConfig = array_extend( $swarmConfig, parse_ini_file( "$swarmInstallDir/testswarm.ini", true ) );

// Timezone
date_default_timezone_set( $swarmConfig["general"]["timezone"] );

/**@}*/


/**
 * Debugging
 * @{
 */
function swarmExceptionHandler( Exception $e ) {
	global $swarmConfig;

	$msg = "<h2>TestSwarm internal error</h2>\n\n";

	if ( $swarmConfig['debug']['show_exception_details'] === '1' ) {
		$msg .=
			'<p>' . nl2br( htmlspecialchars( $e->getMessage() ) ) .
			'</p><p>Backtrace:</p><p>' . nl2br( htmlspecialchars( $e->getTraceAsString() ) ) .
			"</p>\n";
	} else {
		$msg .=
			'<p>Set <b><tt>debug[show_exception_details] = 1;</tt></b> ' .
			'at the bottom of testswarm.ini to show detailed debugging information.</p>';
	}

	if ( !headers_sent() ) {
		header( $_SERVER["SERVER_PROTOCOL"] . " 500 TestSwarm Internal Error", true, 500 );
	}

	echo $msg;
	exit;
}

set_exception_handler( 'swarmExceptionHandler' );

if ( $swarmConfig['debug']['php_error_reporting'] === '1' ) {
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
}

/**@}*/


/**
 * Session
 * @{
 */

session_start();

// Increase the session timeout to two weeks (3600 * 24 * 14)
ini_set( 'session.gc_maxlifetime', '1209600' );

/**@}*/

