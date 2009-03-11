<?php
	// $DEBUG_ON = true;
	include "inc/utilities.php";
	include "inc/browser.php";
	include "inc/db.php";
	include "inc/init.php";

	$state = ereg_replace("[^a-z]", "", $_REQUEST['state']);
	$stateFile = "logic/$state.php";

	if ( $state ) {
		if ( file_exists($stateFile) ) {
			include $stateFile;
		} else {
			header("HTTP/1.0 404 Not Found");
			exit();
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Run the Test Swarm</title>
	<?php if ( $client_id ) {
		echo "<script type='text/javascript'>var client_id = $client_id;</script>";
	}?>
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/run.js"></script>
</head>
<body>
	<h1>Run the Test Swarm</h1>
	<p class="msg"></p>
</body>
</html>
