<?php
	$result = mysql_queryf("SELECT DISTINCT useragent_id FROM run_useragent WHERE runs < 1 AND status = 0;");
	$useragent_ids = array();

	while($row = mysql_fetch_array($result)){
		array_push($useragent_ids, intval($row[0]));
	}
	echo json_encode($useragent_ids);

	exit();