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

$state = preg_replace("/[^a-z]/", "", $swarmContext->getRequest()->getVal( "state", "" ) );

if ( !$state ) {
	$state = "home";
}

$logicFile = "logic/$state.php";
$contentFile = "content/$state.php";

if ( $state ) {
	if ( file_exists( $logicFile ) ) {
		require $logicFile;
	} elseif ( !file_exists($contentFile) ) {
		header( $_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404 );
		echo '<h2>TestSwarm: Invalid state</h2>';
		exit;
	}
}

// If $title is set, then the logic-file intends to make an HTML response
// Otherwise it doesn't (such as runresults for example)
if ( isset( $title ) ) {
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title><?php echo htmlentities( $swarmContext->getConf()->web->title . ': ' . $title ); ?></title>
	<link rel="stylesheet" href="<?php echo swarmpath( "css/site.css" ); ?>"/>
	<script>window.SWARM = <?php echo json_encode( array(
		// Export a simplified version of the TestSwarm configuration object to the browser
		// (not the entire object since it also contains DB password and such..).
		"web" => array(
			"contextpath" => swarmpath( "" ),
		),
		"client" => $swarmContext->getConf()->client,
	) ); ?>;</script>
<?php
		echo isset( $scripts ) ? "\t" . $scripts . "\n" : "";
?>
</head>
<body>
	<ul class="nav">
		<?php if ( isset( $_SESSION["username"] ) && isset( $_SESSION["auth"] ) && $_SESSION["auth"] == "yes" ) { ?>
		<li><strong><a href="<?php echo swarmpath( "user/{$_SESSION["username"]}/" ); ?>"><?php echo $_SESSION["username"];?></a></strong></li>
		<li><a href="<?php echo swarmpath( "run/{$_SESSION["username"]}" );?>">Join the Swarm</a></li>
		<li><a href="<?php echo swarmpath( "logout/" ); ?>">Logout</a></li>
		<?php } else { ?>
		<li><a href="<?php echo swarmpath( "login/" ); ?>">Login</a></li>
		<li><a href="<?php echo swarmpath( "signup/" ); ?>">Signup</a></li>
		<?php } ?>
		<li><a href="//github.com/jquery/testswarm">Source Code</a></li>
		<li><a href="//github.com/jquery/testswarm/issues">Issue Tracker</a></li>
		<li><a href="//github.com/jquery/testswarm/wiki">About</a></li>
		<li><a href="//groups.google.com/group/testswarm">Discuss</a></li>
		<li><a href="//twitter.com/testswarm">Twitter</a></li>
	</ul>
	<h1><a href="<?php echo swarmpath( "/" ); ?>"><img src="<?php echo swarmpath( "images/testswarm_logo_wordmark.png" ); ?>" alt="TestSwarm" title="TestSwarm"/></a></h1>
	<h2><?php echo  $title; ?></h2>
	<div id="main">
	<?php
}

if ( $state && file_exists( $contentFile ) ) {
	require $contentFile;
}

// Wrap up the HTML response
if ( isset( $title ) ) { ?>
	</div>
</body>
</html>
<?php
}
