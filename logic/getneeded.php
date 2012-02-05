<?php
	$result = mysql_queryf("select distinct useragent_id from run_useragent where runs<1 and status=0;");
	$useragent_ids = array();

	while($row = mysql_fetch_array($result)){
		array_push($useragent_ids, intval($row[0]));
	}
	echo json_encode($useragent_ids);

	exit();