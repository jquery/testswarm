<?php
	require "inc/init-usersession.php";

	$bi = $swarmContext->getBrowserInfo();
	$request = $swarmContext->getRequest();

	if ( $request->wasPosted() ) {
		$run_id  = preg_replace( "/[^0-9]/",  "", $request->getVal( "run_id", "" ) );
		$fail    = preg_replace( "/[^0-9-]/", "", $request->getVal( "fail", "" ) );
		$error   = preg_replace( "/[^0-9-]/", "", $request->getVal( "error", "" ) );
		$total   = preg_replace( "/[^0-9-]/", "", $request->getVal( "total", "" ) );

		$results = gzencode( $request->getVal( "results", "" ) );
	} else {
		$results = false;
	}

	# Make sure we've received some results from the client
	if ( $results ) {
		mysql_queryf(
			"UPDATE
				run_client
			SET
				status=2,
				fail = %u,
				error = %u,
				total = %u,
				results = %s
			WHERE	client_id = %u
			AND 	run_id = %u
			LIMIT 1;",
			$fail,
			$error,
			$total,
			$results,
			$client_id,
			$run_id
		);

		if ( mysql_affected_rows() > 0 ) {
			# If we're 100% passing we don't need any more runs
			if ( $total > 0 && $fail == 0 && $error == 0 ) {
				# Clear out old runs that were bad, since we now have a good one
				$result = mysql_queryf(
					"SELECT client_id
					FROM
						run_client, clients
					WHERE	run_id = %u
					AND 	client_id != %u
					AND 	(total <= 0 OR error > 0 OR fail > 0)
					AND 	clients.id=client_id
					AND 	clients.useragent_id = %u;",
					$run_id,
					$client_id,
					$bi->getSwarmUserAgentID()
				);

				while ( $row = mysql_fetch_array($result) ) {
					mysql_queryf("DELETE FROM run_client WHERE run_id=%u AND client_id=%u;", $run_id, $row[0]);
				}

				mysql_queryf(
					"UPDATE
						run_useragent
					SET
						runs = max,
						completed = completed + 1,
						status = 2
					WHERE	useragent_id = %u
					AND 	run_id = %u
					LIMIT 1;",
					$bi->getSwarmUserAgentID(),
					$run_id
				);
			} else {
				if ( $total > 0 ) {
					# Clear out old runs that timed out.
					$result = mysql_queryf(
						"SELECT
							client_id
						FROM
							run_client, clients
						WHERE	run_id = %u
						AND 	client_id != %u
						AND 	total <= 0
						AND 	clients.id = client_id
						AND 	clients.useragent_id = %u;",
						$run_id,
						$client_id,
						$bi->getSwarmUserAgentID()
					);

					while ( $row = mysql_fetch_array($result) ) {
						mysql_queryf(
							"DELETE FROM run_client WHERE run_id = %u AND client_id = %u;",
							$run_id,
							$row[0]
						);
					}
				}

				mysql_queryf(
					"UPDATE
						run_useragent
					SET
						completed = completed + 1,
						status = IF(completed + 1 < max, 1, 2)
					WHERE	useragent_id = %u
					AND 	run_id = %u
					LIMIT 1;",
					$bi->getSwarmUserAgentID(),
					$run_id
				);
			}
		}
	}

	/**
	 * action=saverun is used in 3 scenarios:
	 *
	 * - A modern browser is viewing action=run&item=username,
	 *   running tests in an iframe with a test suite and inject.js,
	 *   the test suite is done and uses postMessage to contact the parent frame where a
	 *   handler from run.js takes it, and fires AJAX request to action=saverun.
	 *
	 * - An old browser is running tests like above but has no postMessage support.
	 *   In that case inject.js will build a <form> that POSTs to action=saverun,
	 *   The reponse of the form submission will still be in the iframe.
	 *
	 * - In either an old or a new browser, if a test times out something in run.js
	 *   will make an ajax request here to report the time out failure
	 *
	 * In the first and last case we should respond with JSON, becuase that's what the
	 * handlers expect. If the response is valid JSON, it will call SWARM.runDone() (or
	 * something like it) and continue.
	 * In the second case we want to output a little bit of HTML, that will contact the
	 * parent frame to let it know that the form submission completed and it should
	 * continue on.
	 */
	if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] )
		&& strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'
	) {
		echo json_encode( "ok" );
	} else {
		echo '<script>window.parent.SWARM.runDone();</script>';
	}

	exit;
