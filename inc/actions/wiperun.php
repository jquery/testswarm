<?php
	$request = $swarmContext->getRequest();

	$wipedRun = false;
	$jobID = false;

	if ( $request->wasPosted() ) {

		$run_id = $request->getInt( "run_id" );
		$client_id = $request->getInt( "client_id" );

		if ( $run_id && $client_id && $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) === "yes" ) {

			$results = mysql_queryf(
				"SELECT
					jobs.id as job_id
				FROM
					users, jobs, runs
				WHERE	users.name=%s
				AND	jobs.user_id=users.id
				AND	runs.id=%u
				AND	runs.job_id=jobs.id;",
				$request->getSessionData( "username" ),
				$run_id
			);
			$row = mysql_fetch_row($results);

			if ( $row ) {
				$jobID = $row["job_id"];

				$results = mysql_queryf( "SELECT useragent_id FROM clients WHERE id=%u;", $client_id );
				$row = mysql_fetch_row( $results );

				if ( $row ) {
					$useragent_id = $row["useragent_id"];

					mysql_queryf(
						"DELETE run_client FROM run_client, clients
						WHERE	run_id = %u
						AND	clients.id = client_id
						AND	clients.useragent_id = %u;",
						$run_id,
						$useragent_id
					);
					mysql_queryf(
						"UPDATE run_useragent
						SET status = 0, runs = 0, completed = 0, updated = %s
						WHERE	run_id = %u
						AND	useragent_id = %u;",
						swarmdb_dateformat( SWARM_NOW ),
						$run_id,
						$useragent_id
					);
					mysql_queryf(
						"UPDATE runs
						SET status = 1, updated = %s
						WHERE id = %u;",
						swarmdb_dateformat( SWARM_NOW ),
						$run_id
					);

					$wipedRun = true;
				}
			}
		}
	}

	if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] )
		&& strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'
	) {
		echo json_encode( $wipedRun && $jobID ? "ok" : "error" );

	} elseif ( $wipedRun && $jobID ) {
		header("Location: " . swarmpath( "job/{$jobID}" ) );

	} else {
		header("Location: " . swarmpath( "" ) );
	}

	exit;
