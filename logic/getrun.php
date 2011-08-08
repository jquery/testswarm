<?php
	# Uncomment to reload all connected clients.
	#echo "{cmd:'reload',args:''}";
	#exit();

	require "inc/init.php";

	$result = mysql_queryf("SELECT run_id FROM run_useragent WHERE useragent_id=%u AND runs < max AND NOT EXISTS (SELECT 1 FROM run_client WHERE run_useragent.run_id=run_id AND client_id=%u) ORDER BY run_id DESC LIMIT 1;", $useragent_id, $client_id);

	# A run was found
	if ( $row = mysql_fetch_array($result) ) {
		$run_id = $row[0];

		$result = mysql_queryf("SELECT url, jobs.name, runs.name FROM runs, jobs WHERE runs.id=%u AND jobs.id=runs.job_id LIMIT 1;", $run_id);

		if ( $row = mysql_fetch_array($result) ) {
			$url = $row[0];
			$text = $row[1] . " " . ucfirst($row[2]);
		}

		# Mark the run as "in progress" on the useragent
		mysql_queryf("UPDATE run_useragent SET runs = runs + 1, status = 1 WHERE run_id=%u AND useragent_id=%u LIMIT 1;", $run_id, $useragent_id);

		# Initialize the client run
		mysql_queryf("INSERT INTO run_client (run_id,client_id,status,created) VALUES(%u,%u,1,NOW());", $run_id, $client_id);

		echo json_encode(array(
			"id" => $run_id,
			"url" => $url,
			"desc" => $text
		));
	}


	exit();

