<?php
	$config = parse_ini_file("config.ini", true);
	$db = mysql_connect(
		$config['database']['host'],
		$config['database']['username'],
		$config['database']['password']
	);

	mysql_select_db($config['database']['database']);
?>
