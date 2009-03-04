<?php
	if (get_magic_quotes_gpc()) {
		function stripslashes_deep($value) {
			$value = is_array($value) ?
				array_map('stripslashes_deep', $value) :
				stripslashes($value);

			return $value;
		}

		$_POST = array_map('stripslashes_deep', $_POST);
		$_GET = array_map('stripslashes_deep', $_GET);
		$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
		$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	}

	$state = $_REQUEST['state'];

	if ( $state == "queue" ) {
		echo implode( "\n", array_diff(scandir( "tests" ), array('.', '..')) );
		exit();
	} else {
		$run = ereg_replace("[^0-9]", "", $_POST['run']);
		$browser = ereg_replace("[^a-z]", "", $_REQUEST['browser']);
		$version = ereg_replace("[^0-9.]", "", $_REQUEST['version']);
		$results = $_POST['results'];

		if ( !empty($run) && !empty($browser) && !empty($version) && !empty($results) ) {
			$f = fopen( "tests/$run/results/$browser-$version.html", "w" );
			fwrite( $f, $results );
			fclose( $f );

			echo "done";
			exit();
		}
	}
?>
<html>
<head>
	<title>Run the Test Swarm</title>
	<script src="jquery.js"></script>
	<script src="run.js"></script>
</head>
<body>
	<h1>Run the Test Swarm</h1>
</body>
</html>
