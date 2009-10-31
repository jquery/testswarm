<?php
	$config = parse_ini_file("config.ini", true);
	$db = mysql_connect(
		$config['database']['host'],
		$config['database']['username'],
		$config['database']['password']
	);
	if (!$db) {
	    die('Not connected: ' . mysql_error());
	}

	$db_selected = mysql_select_db($config['database']['database'], $db);
	if (!$db_selected) {
	    die ('Can\'t use ' . $config['database']['database'] . ': ' . mysql_error());
	}
?>
