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
 * - testswarm.ini existance
 * - cache dir existance and writability
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
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
if ( !function_exists( 'version_compare' ) || version_compare( phpversion(), '5.3.0' ) < 0 ) {
	echo "<b>TestSwarm Fatal:</b> TestSwarm requires at least PHP 5.3.0\n";
	exit;
}

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
 * Default settings
 * @{
 */
// Generic requirements that we still need globally unconditionally
require_once __DIR__ . "/utilities.php";

$swarmInstallDir = dirname( __DIR__ );

$defaultSettingsFile = "$swarmInstallDir/config/testswarm-defaults.ini";
$localSettingsFile = "$swarmInstallDir/testswarm.ini";

// Verify that the testswarm.ini file exists
if ( !file_exists( $defaultSettingsFile ) ) {
	echo "<b>TestSwarm Fatal:</b> <tt>./config/testswarm-defaults.ini</tt> missing!\n";
	exit;
}
if ( !file_exists( $localSettingsFile ) ) {
	echo "<b>TestSwarm Fatal:</b> <tt>testswarm.ini</tt> missing!\n";
	exit;
}

// Read configuration options and let the INI file
// override default settings.
$swarmConfig = array_extend(
	parse_ini_file( $defaultSettingsFile, true ),
	parse_ini_file( $localSettingsFile, true ),
	array( "overwrite" )
);

unset( $localSettingsFile, $defaultSettingsFile );

// Timezone
date_default_timezone_set( $swarmConfig["general"]["timezone"] );

// Type conversion
// (parse_ini_file reads everything as strings)

$swarmConfig["debug"]["db_log_queries"] = $swarmConfig["debug"]["db_log_queries"] === "1";
$swarmConfig["debug"]["show_exception_details"] = $swarmConfig["debug"]["show_exception_details"] === "1";
$swarmConfig["debug"]["php_error_reporting"] = $swarmConfig["debug"]["php_error_reporting"] === "1";

$swarmConfig["client"]["cooldown_sleep"] = intval( $swarmConfig["client"]["cooldown_sleep"] );
$swarmConfig["client"]["nonewruns_sleep"] = intval( $swarmConfig["client"]["nonewruns_sleep"] );
$swarmConfig["client"]["run_timeout"] = intval( $swarmConfig["client"]["run_timeout"] );
$swarmConfig["client"]["savereq_timeout"] = intval( $swarmConfig["client"]["savereq_timeout"] );
$swarmConfig["client"]["saveretry_max"] = intval( $swarmConfig["client"]["saveretry_max"] );
$swarmConfig["client"]["saveretry_sleep"] = intval( $swarmConfig["client"]["saveretry_sleep"] );
$swarmConfig["client"]["require_run_token"] = $swarmConfig["client"]["require_run_token"] === "1";
$swarmConfig["client"]["refresh_control"] = intval( $swarmConfig["client"]["refresh_control"] );

$swarmConfig["web"]["ajax_update_interval"] = intval( $swarmConfig["web"]["ajax_update_interval"] );

$swarmConfig["storage"]["cacheDir"] = str_replace( "$1", $swarmInstallDir, $swarmConfig["storage"]["cacheDir"] );

// Caching directory must exist and be writable
if ( !is_dir( $swarmConfig["storage"]["cacheDir"] ) || !is_writable( $swarmConfig["storage"]["cacheDir"] ) ) {
	echo "<b>TestSwarm Fatal</b>: Caching directory must exist and be writable by the script!\n";
	exit;
}

// Refresh control
// (for documentation see testswarm.ini)
// Contrary to the one in testswarm.ini, this one is for internal changes.
// The one in testswarm.ini is for changes by the local administrator.
// This may be increased when for example run.js changes significantly.

$refresh_control = 2; // 2012-04-06

$swarmConfig["client"]["refresh_control"] += $refresh_control;


/**@}*/

/**
 * AutoLoader
 * @{
 */
$swarmAutoLoadClasses = array(
	# Main includes
	"Action" => "inc/Action.php",
	"Api" => "inc/Api.php",
	"BrowserInfo" => "inc/BrowserInfo.php",
	"Client" => "inc/Client.php",
	"Database" =>"inc/Database.php",
	"DerivativeWebRequest" => "inc/WebRequest.php",
	"MaintenanceScript" => "inc/MaintenanceScript.php",
	"Page" => "inc/Page.php",
	"TestSwarmContext" => "inc/TestSwarm.php",
	"WebRequest" => "inc/WebRequest.php",
	# Actions
	"AddjobAction" => "inc/actions/AddjobAction.php",
	"CleanupAction" => "inc/actions/CleanupAction.php",
	"GetrunAction" => "inc/actions/GetrunAction.php",
	"InfoAction" => "inc/actions/InfoAction.php",
	"JobAction" => "inc/actions/JobAction.php",
	"LoginAction" => "inc/actions/LoginAction.php",
	"LogoutAction" => "inc/actions/LogoutAction.php",
	"ProjectsAction" => "inc/actions/ProjectsAction.php",
	"SaverunAction" => "inc/actions/SaverunAction.php",
	"ScoresAction" => "inc/actions/ScoresAction.php",
	"SignupAction" => "inc/actions/SignupAction.php",
	"SwarmstateAction" => "inc/actions/SwarmstateAction.php",
	"UserAction" => "inc/actions/UserAction.php",
	"WipejobAction" => "inc/actions/WipejobAction.php",
	"WiperunAction" => "inc/actions/WiperunAction.php",
	# Pages
	"AddjobPage" => "inc/pages/AddjobPage.php",
	"ApiDebugPage" => "inc/pages/ApiDebugPage.php",
	"Error404Page" => "inc/pages/Error404Page.php",
	"Error500Page" => "inc/pages/Error500Page.php",
	"HomePage" => "inc/pages/HomePage.php",
	"JobPage" => "inc/pages/JobPage.php",
	"LoginPage" => "inc/pages/LoginPage.php",
	"LogoutPage" => "inc/pages/LogoutPage.php",
	"ProjectsPage" => "inc/pages/ProjectsPage.php",
	"RunPage" => "inc/pages/RunPage.php",
	"RunresultsPage" => "inc/pages/RunresultsPage.php",
	"SaverunPage" => "inc/pages/SaverunPage.php",
	"ScoresPage" => "inc/pages/ScoresPage.php",
	"SignupPage" => "inc/pages/SignupPage.php",
	"UserPage" => "inc/pages/UserPage.php",
	# Libs
	"Browscap" => "inc/libs/GaretJax-phpbrowscap/browscap/Browscap.php",
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

spl_autoload_register( "swarmAutoLoader" );

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
if ( $swarmContext->getConf()->debug->php_error_reporting ) {
	error_reporting( E_ALL );
	ini_set( "display_errors", 1 );
}

// Increase the session timeout to two weeks (3600 * 24 * 14)
ini_set( 'session.gc_maxlifetime', '1209600' );

/**@}*/
