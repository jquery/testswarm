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

	$run = ereg_replace("[^0-9]", "", $_POST['run']);
	$browser = ereg_replace("[^a-z]", "", $_REQUEST['browser']);
	$version = ereg_replace("[^0-9.]", "", $_REQUEST['version']);

	$config = parse_ini_file("config.ini", true);
	$db = mysql_connect(
		$config['database']['host'],
		$config['database']['username'],
		$config['database']['password']
	);

	mysql_select_db($config['database']['database'], $db);

	function filterDirs( $dir ) {
		global $browser, $version;
		return !file_exists("tests/$dir/results/$browser-$version.html");
	}

	$state = $_REQUEST['state'];

	if ( $state == "queue" ) {
		$dirs = array_filter(array_diff(scandir( "tests" ), array('.', '..')), "filterDirs");
		echo implode( "\n", $dirs );
		exit();
	} else {
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
	<p class="msg"></p>
</body>
</html>
