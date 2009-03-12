<?php
	include "inc/init.php";

	mysql_query("BEGIN");
	$result = mysql_queryf("SELECT run_id FROM run_useragent WHERE useragent_id=%u AND runs < max;", $useragent_id);
	
	# A run was found
	if ( $row = mysql_fetch_array($result) ) {
		$run_id = $row[0];
	
		# Make sure that we don't re-run the tests in the same client
		$result = mysql_queryf("SELECT * FROM run_client WHERE run_id=%u AND client_id=%u;", $run_id, $client_id);

		if ( mysql_num_rows($result) == 0 ) {
			# Mark the run as "in progress" on the useragent
			mysql_queryf("UPDATE run_useragent SET runs = runs + 1, status = 1 WHERE run_id=%u AND useragent_id=%u;", $run_id, $useragent_id);

			# Mark the complete run as "in progress"
			mysql_queryf("UPDATE runs SET status = 1 WHERE id=%u;", $run_id);

			$result = mysql_queryf("SELECT job_id FROM runs WHERE id=%u;", $run_id);

			if ( $row = mysql_fetch_array($result) ) {
				# Mark the complete job as "in progress"
				mysql_queryf("UPDATE jobs SET status = 1 WHERE id=%u;", $row[0]);
			}

			# Initialize the client run
			mysql_queryf("INSERT INTO run_client (run_id,client_id,status,created) VALUES(%u,%u,1,NOW());", $run_id, $client_id);

			echo "$run_id";
		}

		# TODO: There needs to be a cronjob that marks dead clients as inactive
		#       and decrements the run_useragent run count.
	}

	mysql_query("COMMIT");

	exit();
?>
