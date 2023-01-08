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
 * @author Timo Tijhof
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

// Use dirname since __DIR__ is PHP 5.3+ and we're going to use it to
// display an error to older PHP versions.
require_once dirname( __FILE__ ) . '/initError.php' ;

// Minimum PHP version
if ( !function_exists( 'version_compare' ) || version_compare( phpversion(), '5.4.0' ) < 0 ) {
	swarmInitError( 'TestSwarm requires at least PHP 5.4.0' );
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
define( 'SWARM_DBCON_DEFAULT', 10 ); // obsolete
define( 'SWARM_DBCON_PERSISTENT', 11 ); // deprecated

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
	'ApihelpAction' => 'inc/actions/ApihelpAction.php',
	'CleanupAction' => 'inc/actions/CleanupAction.php',
	'ClientAction' => 'inc/actions/ClientAction.php',
	'ClientsAction' => 'inc/actions/ClientsAction.php',
	'GetrunAction' => 'inc/actions/GetrunAction.php',
	'InfoAction' => 'inc/actions/InfoAction.php',
	'JobAction' => 'inc/actions/JobAction.php',
	'LoginAction' => 'inc/actions/LoginAction.php',
	'LogoutAction' => 'inc/actions/LogoutAction.php',
	'PingAction' => 'inc/actions/PingAction.php',
	'ProjectAction' => 'inc/actions/ProjectAction.php',
	'ProjectsAction' => 'inc/actions/ProjectsAction.php',
	'ResultAction' => 'inc/actions/ResultAction.php',
	'SaverunAction' => 'inc/actions/SaverunAction.php',
	'SwarmstateAction' => 'inc/actions/SwarmstateAction.php',
	'WipejobAction' => 'inc/actions/WipejobAction.php',
	'WiperunAction' => 'inc/actions/WiperunAction.php',
	# Pages
	'AddjobPage' => 'inc/pages/AddjobPage.php',
	'ApiDebugPage' => 'inc/pages/ApiDebugPage.php',
	'ClientPage' => 'inc/pages/ClientPage.php',
	'ClientsPage' => 'inc/pages/ClientsPage.php',
	'Error404Page' => 'inc/pages/Error404Page.php',
	'Error500Page' => 'inc/pages/Error500Page.php',
	'HomePage' => 'inc/pages/HomePage.php',
	'InfoPage' => 'inc/pages/InfoPage.php',
	'JobPage' => 'inc/pages/JobPage.php',
	'LoginPage' => 'inc/pages/LoginPage.php',
	'LogoutPage' => 'inc/pages/LogoutPage.php',
	'ProjectPage' => 'inc/pages/ProjectPage.php',
	'ProjectsPage' => 'inc/pages/ProjectsPage.php',
	'ResultPage' => 'inc/pages/ResultPage.php',
	'RunPage' => 'inc/pages/RunPage.php',
	'SaverunPage' => 'inc/pages/SaverunPage.php',
);

function swarmAutoLoader( $className ) {
	global $swarmAutoLoadClasses, $swarmInstallDir;

	if ( !isset( $swarmAutoLoadClasses[$className] ) ) {
		return false;
	}

	$filename = $swarmAutoLoadClasses[$className];
	require_once "$swarmInstallDir/$filename";

	return true;
}

spl_autoload_register( 'swarmAutoLoader' );

if ( !is_readable( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
	swarmInitError( 'Dependencies missing. Run "composer install".' );
}
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

/**@}*/

/**
 * Load and validate settings
 * @{
 */
// Generic utilities that we still need globally unconditionally
require_once __DIR__ . '/utilities.php';

$defaultSettingsJSON = "$swarmInstallDir/config/defaultSettings.json";
$localSettingsPHP = "$swarmInstallDir/config/localSettings.php";

// Verify that the configuration files exists and are readable
if ( !is_readable( $defaultSettingsJSON ) || !is_readable( $localSettingsPHP ) ) {
	swarmInitError( 'One or more configuration files were not readable by the server.' );
}

$defaultSettings = json_decode( file_get_contents( $defaultSettingsJSON ) );
if ( !$defaultSettings ) {
	swarmInitError( 'Unable to parse defaultSettings.json' );
}
'@phan-var stdClass $defaultSettings';

$localSettings = require $localSettingsPHP;
if ( !is_object( $localSettings ) ) {
	error_log( 'TestSwarm Warning: Invalid return value for local settings. Type: ' . gettype( $localSettings ) . '.' );
	$localSettings = array();
}
'@phan-var stdClass|array $localSettings';

$swarmConfig = object_merge( $defaultSettings, $localSettings );

unset( $defaultSettingsJSON, $localSettingsPHP, $defaultSettings, $localSettings );

// Verify browserSets are valid.
foreach ( $swarmConfig->browserSets as $browserSet => $browsers ) {
	foreach ( $browsers as $i => $uaID ) {
		if ( !isset( $swarmConfig->userAgents->$uaID ) ) {
			error_log( 'TestSwarm Warning: Unregistered browserSet entry "' . $uaID . '" in "' . $browserSet . '".' );
			unset( $browsers[$i] );
		}
	}
	// Re-index as straight numerical array (in case some indexes were unset above)
	$swarmConfig->browserSets->$browserSet = array_values( array_unique( $browsers ) );
}

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
	swarmInitError( 'Caching directory must exist and be writable by the webserver.' );
}

// Refresh control
// The value in settings file is for changes by the local administrator.
// this one is for internal changes, e.g. to be increased when for example
// ./js/run.js changes significantly.
$refresh_control = 4; // 2012-06-11
$swarmConfig->client->refreshControl += $refresh_control;

unset( $server, $refresh_control );

/**@}*/


/**
 * Custom PHP settings
 * @{
 */
if ( $swarmConfig->debug->phpErrorReporting || SWARM_ENTRY === 'SCRIPT' ) {
	error_reporting( E_ALL & ~E_DEPRECATED );
	ini_set( 'display_errors', '1' );
} else {
	error_reporting( 0 );
}

// Increase the session timeout to two weeks (3600 * 24 * 14)
ini_set( 'session.gc_maxlifetime', '1209600' );

/**@}*/


/**
 * Context
 * @{
 */
$swarmContext = new TestSwarmContext( $swarmConfig );

/**@}*/
