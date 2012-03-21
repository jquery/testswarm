<?php
/**
 * This the main initialization file for TestSwarm.
 * All web requests have to go through here,
 * and do so as early as possible.
 *
 * @since 0.3.0
 * @package TestSwarm
 */

// Minimum PHP version
if ( !function_exists( 'version_compare' ) || version_compare( phpversion(), '5.2.3' ) < 0 ) {
	echo "TestSwarm requires at least PHP 5.2.3\n";
	exit;
}

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
	"client" => array(
		"cooldown_rate" => "15",
		"update_rate" => "30",
		"timeout_rate" => "180",
		"refresh_control" => "1",
	),
	"debug" => array(
		"show_exception_details" => "0",
		"php_error_reporting" => "0",
	),
);

// Read configuration options and let the INI file
// override default settings.
$swarmConfig = array_extend( $swarmConfig, parse_ini_file( "$swarmInstallDir/testswarm.ini", true ) );

// Timezone
date_default_timezone_set( $swarmConfig["general"]["timezone"] );

// Type conversion
// (parse_ini_file reads everything as strings)

$swarmConfig["debug"]["show_exception_details"] = $swarmConfig["debug"]["show_exception_details"] === "1";
$swarmConfig["debug"]["php_error_reporting"] = $swarmConfig["debug"]["php_error_reporting"] === "1";

$swarmConfig["client"]["cooldown_rate"] = intval( $swarmConfig["client"]["cooldown_rate"] );
$swarmConfig["client"]["update_rate"] = intval( $swarmConfig["client"]["update_rate"] );
$swarmConfig["client"]["timeout_rate"] = intval( $swarmConfig["client"]["timeout_rate"] );
$swarmConfig["client"]["refresh_control"] = intval( $swarmConfig["client"]["refresh_control"] );

/**@}*/


/**
 * Debugging
 * @{
 */
function swarmExceptionHandler( Exception $e ) {
	global $swarmConfig;

	$msg = "<h2>TestSwarm internal error</h2>\n\n";

	if ( $swarmConfig["debug"]["show_exception_details"] === true ) {
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

set_exception_handler( "swarmExceptionHandler" );

if ( $swarmConfig["debug"]["php_error_reporting"] === true ) {
	error_reporting( E_ALL );
	ini_set( "display_errors", 1 );
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

