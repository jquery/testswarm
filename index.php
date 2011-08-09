<?php
	session_start();

	require "inc/utilities.php";

	$swarmConfig = parse_ini_file("config.ini", true);
	// Extend default options
	$swarmConfig = array_extend(array(
		"database" => array(
			"host" => "localhost",
			"username" => "root",
			"password" => "root",
			"database" => "testswarm",
		),
		"web" => array(
			"title" => "Test Swarm",
			"contextpath" => "",
		),
	), $swarmConfig);

	// $swarmDebug = true;
	require "inc/browser.php";
	require "inc/db.php";

	// Increase the session timeout to two weeks
	ini_set("session.gc_maxlifetime", "1209600");

	$state = preg_replace("/[^a-z]/", "", getItem( "state", $_REQUEST, "" ) );

	if ( !$state ) {
		$state = "home";
	}

	$logicFile = "logic/$state.php";
	$contentFile = "content/$state.php";

	if ( $state ) {
		if ( file_exists($logicFile) ) {
			require $logicFile;
		} else if ( !file_exists($contentFile) ) {
			header("HTTP/1.0 404 Not Found");
			exit();
		}
	}

if ( $title ) {
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo htmlentities( $swarmConfig['web']['title'] . ': ' . $title ); ?></title>
	<link rel="stylesheet" href="<?php echo swarmpath( "css/site.css" ); ?>"/>
	<?php echo isset($scripts) ? $scripts : ""; ?>
</head>
<body>
	<ul class="nav">
		<?php if ( $_SESSION["username"] && $_SESSION["auth"] == "yes" ) { ?>
		<li><strong><a href="<?php echo swarmpath( "user/{$_SESSION["username"]}/" ); ?>"><?php echo $_SESSION["username"];?></a></strong></li>
		<li><a href="<?php echo swarmpath( "run/{$_SESSION["username"]}" );?>">Join the Swarm</a></li>
		<li><a href="<?php echo swarmpath( "logout/" ); ?>">Logout</a></li>
		<?php } else { ?>
		<li><a href="<?php echo swarmpath( "login/" ); ?>">Login</a></li>
		<li><a href="<?php echo swarmpath( "signup/" ); ?>">Signup</a></li>
		<?php } ?>
		<li><a href="http://github.com/jquery/testswarm">Source Code</a></li>
		<li><a href="http://github.com/jquery/testswarm/issues">Bug Tracker</a></li>
		<li><a href="http://groups.google.com/group/testswarm">Discuss</a></li>
		<li><a href="http://twitter.com/testswarm">Updates</a></li>
		<li><a href="http://wiki.github.com/jquery/testswarm">About</a></li>
	</ul>
	<h1><a href="<?php echo swarmpath( "/" ); ?>"><img src="<?php echo swarmpath( "images/testswarm_logo_wordmark.png" ); ?>" alt="TestSwarm" title="TestSwarm"/></a></h1>
	<h2><?php echo  $title; ?></h2>
	<div id="main">
	<?php } if ( $state && file_exists($contentFile) ) {
		require $contentFile;
	} if ( $title ) { ?>
	</div>
</body>
</html>
<?php }
