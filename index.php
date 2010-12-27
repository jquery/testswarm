<?php
	session_start();

	$config = parse_ini_file("config.ini", true);
	$contextpath = $config['web']['contextpath'];

	// $DEBUG_ON = true;
	require "inc/utilities.php";
	require "inc/browser.php";
	require "inc/db.php";

	// Increase the session timeout to two weeks
	ini_set("session.gc_maxlifetime", "1209600"); 

	$state = preg_replace("/[^a-z]/", "", $_REQUEST['state']);

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
	<meta http-equiv="X-UA-Compatible" content="IE=8" />
	<title>Test Swarm: <?php echo $title; ?></title>
	<link rel="stylesheet" href="<?php echo $contextpath; ?>/css/site.css"/>
	<?php echo isset($scripts) ? $scripts : ''; ?>
</head>
<body>
	<ul class="nav">
		<?php if ( $_SESSION['username'] && $_SESSION['auth'] == 'yes' ) { ?>
		<li><strong><a href="<?php echo $contextpath; ?>/user/<?php echo $_SESSION['username'];?>/"><?php echo $_SESSION['username'];?></a></strong></li>
		<li><a href="<?php echo $contextpath; ?>/run/<?php echo $_SESSION['username'];?>/">Join the Swarm</a></li>
		<li><a href="<?php echo $contextpath; ?>/logout/">Logout</a></li>
		<?php } else { ?>
		<li><a href="<?php echo $contextpath; ?>/login/">Login</a></li>
		<li><a href="<?php echo $contextpath; ?>/signup/">Signup</a></li>
		<?php } ?>
		<li><a href="http://github.com/jeresig/testswarm">Source Code</a></li>
		<li><a href="http://github.com/jeresig/testswarm/issues">Bug Tracker</a></li>
		<li><a href="http://groups.google.com/group/testswarm">Discuss</a></li>
		<li><a href="http://twitter.com/testswarm">Updates</a></li>
		<li><a href="http://wiki.github.com/jeresig/testswarm">About</a></li>
	</ul>
	<h1><a href="<?php echo $contextpath; ?>/"><img src="<?php echo $contextpath; ?>/images/testswarm_logo_wordmark.png" alt="TestSwarm" title="TestSwarm"/></a></h1>
	<h2><?php echo  $title; ?></h2>
	<div id="main">
	<?php } if ( $state && file_exists($contentFile) ) {
		require $contentFile;
	} if ( $title ) { ?>
	</div>
</body>
</html>
<?php } ?>
