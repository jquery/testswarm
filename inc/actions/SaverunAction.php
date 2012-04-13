<?php
/**
 * "Saverun" action.
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class SaverunAction extends Action {

	/**
	 * @actionMethod POST: Required.
	 * @actionParam client_id int
	 * @actionParam run_token string
	 * @actionParam run_id int
	 * @actionParam fail int
	 * @actionParam error int
	 * @actionParam total int
	 * @actionParam results string: HTML snapshot of the test results page.
	 */
	public function doAction() {
		$browserInfo = $this->getContext()->getBrowserInfo();
		$conf = $this->getContext()->getConf();
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( "requires-post" );
			return;
		}

		$runToken = $request->getVal( "run_token" );
		if ( $conf->client->require_run_token && !$runToken ) {
			$this->setError( "invalid-input", "This TestSwarm does not allow unauthorized clients to join the swarm." );
			return;
		}

		$clientID = $request->getInt( "client_id" );

		if ( !$clientID ) {
			$this->setError( "invalid-input" );
			return;
		}

		// Create a Client object that verifies client id, user agent and run token.
		// Also updates the client 'alive' timestamp.
		// Throws exception (caught higher up) if stuff is invalid.
		$client = Client::newFromContext( $this->getContext(), $runToken, $clientID );

		$runID = $request->getInt( "run_id" );
		$fail = $request->getInt( "fail" );
		$error = $request->getInt( "error" );
		$total = $request->getInt( "total" );

		$results = gzencode( $request->getVal( "results", "" ) );

		$db->query(str_queryf(
			"UPDATE
				run_client
			SET
				status = 2,
				fail = %u,
				error = %u,
				total = %u,
				results = %s,
				updated = %s
			WHERE client_id = %u
			AND   run_id = %u
			LIMIT 1;",
			$fail,
			$error,
			$total,
			$results,
			swarmdb_dateformat( SWARM_NOW ),
			$clientID,
			$runID
		));

		if ( mysql_affected_rows() > 0 ) {

			// If we're 100% passing we don't need any more runs
			// Clear out old runs that were bad, since we now have a good one
			if ( $total > 0 && $fail === 0 && $error === 0 ) {
				$rows = $db->getRows(str_queryf(
					"SELECT client_id
					FROM
						run_client
					WHERE run_id = %u
					AND   client_id != %u
					AND   (total <= 0 OR error > 0 OR fail > 0);",
					$runID,
					$clientID
				));

				if ( $rows ) {
					foreach ( $rows as $row ) {
						$db->query(str_queryf(
							"DELETE
							FROM run_client
							WHERE run_id = %u
							AND   client_id = %u;",
							$runID,
							$row->client_id
						));
					}
				}

				$db->query(str_queryf(
					"UPDATE
						run_useragent
					SET
						runs = max,
						completed = completed + 1,
						status = 2,
						updated = %s
					WHERE useragent_id = %s
					AND   run_id = %u
					LIMIT 1;",
					swarmdb_dateformat( SWARM_NOW ),
					$browserInfo->getSwarmUaID(),
					$runID
				));

			} else {

				// Clear out old runs that timed out.
				if ( $total > 0 ) {
					$rows = $db->getRows(str_queryf(
						"SELECT
							client_id
						FROM
							run_client
						WHERE run_id = %u
						AND   client_id != %u
						AND   total <= 0;",
						$runID,
						$clientID
					));

					if ( $rows ) {
						foreach ( $rows as $row ) {
							$db->query(str_queryf(
								"DELETE
								FROM run_client
								WHERE run_id = %u
								AND   client_id = %u;",
								$runID,
								$row->client_id
							));
						}
					}
				}

				$db->query(str_queryf(
					"UPDATE
						run_useragent
					SET
						completed = completed + 1,
						status = IF(completed + 1 < max, 1, 2),
						updated = %s
					WHERE useragent_id = %s
					AND   run_id = %u
					LIMIT 1;",
					swarmdb_dateformat( SWARM_NOW ),
					$browserInfo->getSwarmUaID(),
					$runID
				));
			}
		}

		$this->setData( "ok" );
	}
}

