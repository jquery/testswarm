<?php
	// $DEBUG_ON = true;
	include "inc/utilities.php";
	include "inc/browser.php";
	include "inc/db.php";

	$state = ereg_replace("[^a-z]", "", $_REQUEST['state']);

	if ( !$state ) {
		$state = "home";
	}

	$logicFile = "logic/$state.php";
	$contentFile = "content/$state.php";

	if ( $state ) {
		if ( file_exists($logicFile) ) {
			include $logicFile;
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
	<title>Test Swarm: <?= $title ?></title>
	<link rel="stylesheet" href="/css/site.css"/>
	<?= $scripts ?>
</head>
<body>
	<h1>Test Swarm</h1>
	<h2><?= $title ?></h2>
	<div id="main">
	<?php } if ( $state && file_exists($contentFile) ) {
		include $contentFile;
	} if ( $title ) { ?>
	</div>
</body>
</html>
<?php } ?>
