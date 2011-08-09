<?php
	$job_id = preg_replace("/[^0-9]/", "", $_POST["job_id"]);
	$type = $_POST["type"];

	if ( $job_id && $_SESSION["username"] && $_SESSION["auth"] == "yes" ) {

		$results = mysql_queryf("SELECT runs.id as id FROM users, jobs, runs WHERE users.name=%s AND jobs.user_id=users.id AND jobs.id=%u AND runs.job_id=jobs.id;", $_SESSION["username"], $job_id);

		if ( mysql_num_rows($results) > 0 ) {
			if ( $type == "delete" ) {
				mysql_queryf("DELETE FROM run_client WHERE run_id in (select id from runs where job_id=%u);", $job_id);
				mysql_queryf("DELETE FROM run_useragent WHERE run_id in (select id from runs where job_id=%u);", $job_id);
				mysql_queryf("DELETE FROM runs WHERE job_id=%u;", $job_id);
				mysql_queryf("DELETE FROM jobs WHERE id=%u;", $job_id);
			} else {
				mysql_queryf("UPDATE jobs SET status=0, updated=NOW() WHERE id=%u;", $job_id);
				mysql_queryf("UPDATE runs SET status=0, updated=NOW() WHERE job_id=%u;", $job_id);
			}
		}

		while ( $row = mysql_fetch_row($results) ) {
			$run_id = $row[0];

			mysql_queryf("DELETE FROM run_client WHERE run_id=%u;", $run_id);

			if ( $type == "delete" ) {
				mysql_queryf("DELETE FROM run_useragent WHERE run_id=%u;", $run_id);
			} else {
				mysql_queryf("UPDATE run_useragent SET runs=0, completed=0, status=0, updated=NOW() WHERE run_id=%u;", $run_id);
			}
		}

		if ( $type == "delete" ) {
			header("Location: " . swarmpath( "user/{$_SESSION["username"]}/" ) );
		} else {
		header("Location: " . swarmpath( "job/{$job_id}/" ) );
		}
	}

	exit();
