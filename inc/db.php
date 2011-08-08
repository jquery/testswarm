<?php
	$db = mysql_pconnect(
		$swarmConfig["database"]["host"],
		$swarmConfig["database"]["username"],
		$swarmConfig["database"]["password"]
	);
	if (!$db) {
	    die("Not connected: " . mysql_error());
	}

	$db_selected = mysql_select_db($swarmConfig["database"]["database"], $db);
	if (!$db_selected) {
	    die ("Can't use " . $swarmConfig["database"]["database"] . ": " . mysql_error());
	}
