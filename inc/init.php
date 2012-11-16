<?php
/**
 * This the main initialization file for TestSwarm.
 * All web requests have to go through here,
 * and do so as early as possible.
 *
 * This file should NEVER throw exceptions as they can't be
 * caught in a nice way until either Api or Page is reached.
 * Instead critical issues with the environment should just
 * result in a straight death sentence of the proces and
 * send out a plain text message. Currently:
 * - phpversion support
 * - initialize swarm configuration and load local settings
 * - cache dir existance and writability
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */

/**
 * Environmental requirements
 * @{
 */

// Protect against web entry
if ( !defined( 'SWARM_ENTRY' ) ) {
	exit;
}

// Minimum PHP version
if ( !function_exists( 'version_compare' ) || version_compare( phpversion(), '5.3.2' ) < 0 ) {
	echo '<b>TestSwarm Fatal:</b> TestSwarm requires at least PHP 5.3.2';
	exit;
}

$swarmInstallDir = dirname( __DIR__ );

/**@}*/

/**
 * Defines
 * @{
 */

// Indicator for time/date functions to interpret argument as 'now',
// instead of a unix timestamp.
define( 'SWARM_NOW', 0 );

// Database::open, connection types
define( 'SWARM_DBCON_DEFAULT', 10 );
define( 'SWARM_DBCON_PERSISTENT', 11 );

/**@}*/

/**
 * AutoLoader
 * @{
 */
$swarmAutoLoadClasses = array(
	# Main includes
	'Action' => 'inc/Action.php',
	'Api' => 'inc/Api.php',
	'BrowserInfo' => 'inc/BrowserInfo.php',
	'Client' => 'inc/Client.php',
	'Database' =>'inc/Database.php',
	'DerivativeWebRequest' => 'inc/WebRequest.php',
	'MaintenanceScript' => 'inc/MaintenanceScript.php',
	'Page' => 'inc/Page.php',
	'TestSwarmContext' => 'inc/TestSwarm.php',
	'WebRequest' => 'inc/WebRequest.php',
	# Actions
	'AddjobAction' => 'inc/actions/AddjobAction.php',
	'CleanupAction' => 'inc/actions/CleanupAction.php',
	'GetrunAction' => 'inc/actions/GetrunAction.php',
	'InfoAction' => 'inc/actions/InfoAction.php',
	'JobAction' => 'inc/actions/JobAction.php',
	'LoginAction' => 'inc/actions/LoginAction.php',
	'LogoutAction' => 'inc/actions/LogoutAction.php',
	'PingAction' => 'inc/actions/PingAction.php',
	'ProjectsAction' => 'inc/actions/ProjectsAction.php',
	'ResultAction' => 'inc/actions/ResultAction.php',
	'SaverunAction' => 'inc/actions/SaverunAction.php',
	'ScoresAction' => 'inc/actions/ScoresAction.php',
	'SignupAction' => 'inc/actions/SignupAction.php',
	'SwarmstateAction' => 'inc/actions/SwarmstateAction.php',
	'UserAction' => 'inc/actions/UserAction.php',
	'WipejobAction' => 'inc/actions/WipejobAction.php',
	'WiperunAction' => 'inc/actions/WiperunAction.php',
	# Pages
	'AddjobPage' => 'inc/pages/AddjobPage.php',
	'ApiDebugPage' => 'inc/pages/ApiDebugPage.php',
	'Error404Page' => 'inc/pages/Error404Page.php',
	'Error500Page' => 'inc/pages/Error500Page.php',
	'HomePage' => 'inc/pages/HomePage.php',
	'InfoPage' => 'inc/pages/InfoPage.php',
	'JobPage' => 'inc/pages/JobPage.php',
	'LoginPage' => 'inc/pages/LoginPage.php',
	'LogoutPage' => 'inc/pages/LogoutPage.php',
	'ProjectsPage' => 'inc/pages/ProjectsPage.php',
	'ResultPage' => 'inc/pages/ResultPage.php',
	'RunPage' => 'inc/pages/RunPage.php',
	'SaverunPage' => 'inc/pages/SaverunPage.php',
	'ScoresPage' => 'inc/pages/ScoresPage.php',
	'SignupPage' => 'inc/pages/SignupPage.php',
	'UserPage' => 'inc/pages/UserPage.php',
	# Libs
	'UA' => 'inc/libs/ua-parser/php/UAParser.php',
);

function swarmAutoLoader( $className ) {
	global $swarmAutoLoadClasses, $swarmInstallDir;

	if ( !isset( $swarmAutoLoadClasses[$className] ) ) {
		return false;
	}

	$filename = $swarmAutoLoadClasses[$className];
	require_once( "$swarmInstallDir/$filename" );

	return true;
}

spl_autoload_register( 'swarmAutoLoader' );

if ( !is_readable( $swarmAutoLoadClasses['UA'] ) ) {
	echo "<b>TestSwarm Fatal:</b> Submodule missing: inc/libs/ua-parser.";
	exit;
}

/**@}*/

/**
 * Load settings
 * @{
 */
// Generic requirements that we still need globally unconditionally
require_once __DIR__ . '/utilities.php';

$defaultSettingsJSON = "$swarmInstallDir/config/defaultSettings.json";
$localSettingsPHP = "$swarmInstallDir/config/localSettings.php";

// Verify that the configuration files exists and are readable
if ( !is_readable( $defaultSettingsJSON ) ) {
	echo "<b>TestSwarm Fatal:</b> Not readable: $defaultSettingsJSON";
	exit;
}
if ( !is_readable( $localSettingsPHP ) ) {
	echo "<b>TestSwarm Fatal:</b> Not readable: $localSettingsPHP";
	exit;
}

$defaultSettings = json_decode( file_get_contents( $defaultSettingsJSON ) );
$localSettings = require $localSettingsPHP;
if ( !$defaultSettings ) {
	echo '<b>TestSwarm Fatal:</b> Default settings file contains invalid JSON.';
	exit;
}
if ( !is_object( $localSettings ) ) {
	error_log( 'Invalid return value for local settings (type: ' . gettype( $localSettings ) . ').' );
	$localSettings = array();
}

// Ignore the defaults if there are local ones,
// this avoids polution of the browser matrix with old or unwanted
// browsers from the default settings.
if ( isset( $localSettings->browserSets ) ) {
	unset( $defaultSettings->browserSets );
}
$swarmConfig = object_merge( $defaultSettings, $localSettings );

unset( $defaultSettingsJSON, $localSettingsPHP, $defaultSettings, $localSettings );

// Timezone
date_default_timezone_set( $swarmConfig->general->timezone );

// Auto-populate web.server
if ( $swarmConfig->web->server === null ) {
	$server = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
	if ( isset( $_SERVER['HTTP_HOST'] ) ) {
		$server .= $_SERVER['HTTP_HOST'];
	} elseif ( isset( $_SERVER['SERVER_ADDR'] ) ) {
		$server .= $_SERVER['SERVER_ADDR'];
	} else {
		$server .= 'localhost';
	}
	$swarmConfig->web->server = $server;
}

// Magic replacements
$swarmConfig->storage->cacheDir = str_replace( "$1", $swarmInstallDir, $swarmConfig->storage->cacheDir );

// Caching directory must exist and be writable
if ( !is_dir( $swarmConfig->storage->cacheDir ) || !is_writable( $swarmConfig->storage->cacheDir ) ) {
	echo '<b>TestSwarm Fatal</b>: Caching directory must exist and be writable by the script!';
	exit;
}

// Refresh control
// The value in settings file is for changes by the local administrator.
// this one is for internal changes, e.g. to be increased when for example
// ./js/run.js changes significantly.
$refresh_control = 4; // 2012-06-11
$swarmConfig->client->refresh_control += $refresh_control;


/**@}*/


/**
 * Context
 * @{
 */
$swarmContext = new TestSwarmContext( $swarmConfig );

/**@}*/


/**
 * Custom settings
 * @{
 */
if ( $swarmContext->getConf()->debug->phpErrorReporting ) {
	error_reporting( -1 );
	ini_set( 'display_errors', 1 );
}

// Increase the session timeout to two weeks (3600 * 24 * 14)
ini_set( 'session.gc_maxlifetime', '1209600' );

/**@}*/
