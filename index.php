<?php
	// $DEBUG_ON = true;
	include "inc/utilities.php";
	include "inc/browser.php";
	include "inc/db.php";

	$state = ereg_replace("[^a-z]", "", $_REQUEST['state']);

	if ( !$state ) {
		$state = "run";
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
?>
<!DOCTYPE html>
<html>
<head>
	<title><?= $title ?></title>
	<?= $scripts ?>
</head>
<body>
	<h1><?= $title ?></h1>
	<?php if ( $state && file_exists($contentFile) ) {
		include $contentFile;
	} ?>
</body>
</html>
