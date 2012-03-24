<?php
	require "inc/init-usersession.php";

	global $swarmContext;

	$bi = $swarmContext->getBrowserInfo();

	$result = mysql_queryf(
		"SELECT
			run_id
		FROM
			run_useragent
		WHERE	useragent_id = %u
		AND 	runs < max
		AND NOT EXISTS (SELECT 1 FROM run_client WHERE run_useragent.run_id = run_id AND client_id = %u)
		ORDER BY run_id DESC
		LIMIT 1;",
		$bi->getSwarmUserAgentID(),
		$client_id
	);

	$runInfo = false;

	# A run was found
	if ( $row = mysql_fetch_array( $result ) ) {
		$run_id = $row[0];

		$result = mysql_queryf(
			"SELECT
				url,
				jobs.name,
				runs.name
			FROM
				runs, jobs
			WHERE	runs.id=%u
			AND 	jobs.id=runs.job_id
			LIMIT 1;",
			$run_id
		);

		if ( $row = mysql_fetch_array( $result ) ) {
			$run_url = $row[0];
			$run_desc = $row[1] . " " . ucfirst($row[2]);
		}

		# Mark the run as "in progress" on the useragent
		mysql_queryf(
			"UPDATE run_useragent SET runs = runs + 1, status = 1 WHERE run_id=%u AND useragent_id=%u LIMIT 1;",
			$run_id,
			$bi->getSwarmUserAgentID()
		);

		# Initialize the client run
		mysql_queryf(
			"INSERT INTO run_client (run_id, client_id, status, created) VALUES(%u, %u, 1, %s);",
			$run_id,
			$client_id,
			swarmdb_dateformat( SWARM_NOW )
		);

		if ( $run_id && $run_url && $run_desc ) {
			$runInfo =  array(
				"id" => $run_id,
				"url" => $run_url,
				"desc" => $run_desc
			);
		}
	}

	echo json_encode( array(
		"swarmUpdate" => array( "client" => $swarmContext->getConf()->client ),
		"runInfo" => $runInfo,
	) );

	exit;
