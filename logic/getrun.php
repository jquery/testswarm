<?php
	include "inc/init.php";

	$result = mysql_queryf("SELECT run_id FROM run_useragent WHERE useragent_id=%u AND runs < max ORDER BY run_id DESC LIMIT 1;", $useragent_id);
	
	# A run was found
	if ( $row = mysql_fetch_array($result) ) {
		$run_id = $row[0];

		$result = mysql_queryf("SELECT url FROM runs WHERE id=%u LIMIT 1;", $run_id);

		# TODO: Return more run info to the client (Name, etc.)
		if ( $row = mysql_fetch_array($result) ) {
			$url = $row[0];
		}
	
		# Mark the run as "in progress" on the useragent
		mysql_queryf("UPDATE run_useragent SET runs = runs + 1, status = 1 WHERE run_id=%u AND useragent_id=%u LIMIT 1;", $run_id, $useragent_id);

		# Initialize the client run
		mysql_queryf("INSERT INTO run_client (run_id,client_id,status,created) VALUES(%u,%u,1,NOW());", $run_id, $client_id);

		echo "$run_id $url";
	}

	exit();
?>
