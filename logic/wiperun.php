<?php
	$run_id = preg_replace("/[^0-9]/", "", $_POST["run_id"]);
	$client_id = preg_replace("/[^0-9]/", "", $_POST["client_id"]);

	if ( $run_id && $client_id && $_SESSION["username"] && $_SESSION["auth"] == "yes" ) {

		$results = mysql_queryf("SELECT jobs.id FROM users, jobs, runs WHERE users.name=%s AND jobs.user_id=users.id AND runs.id=%u AND runs.job_id=jobs.id;", $_SESSION["username"], $run_id);

		if ( $row = mysql_fetch_row($results) ) {
			$job_id = $row[0];

			$results = mysql_queryf("SELECT useragent_id FROM clients WHERE id=%u;", $client_id);

			if ( $row = mysql_fetch_row($results) ) {
				$useragent_id = $row[0];

				mysql_queryf("DELETE run_client FROM run_client,clients WHERE run_id=%u AND clients.id=client_id AND clients.useragent_id=%u;", $run_id, $useragent_id);
				mysql_queryf("UPDATE run_useragent SET status=0, runs=0, completed=0, updated=NOW() WHERE run_id=%u AND useragent_id=%u;", $run_id, $useragent_id);
				mysql_queryf("UPDATE runs SET status=1, updated=NOW() WHERE run_id=%u;", $run_id);
			}
		}

		header("Location: " . swarmpath( "job/{$job_id}/" ) );
	}

	exit();
