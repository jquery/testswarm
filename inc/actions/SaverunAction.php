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

	public function doAction() {
		$browserInfo = $this->getContext()->getBrowserInfo();
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( "requires-post" );
			return;
		}

		$clientID = $request->getInt( "client_id" );

		if ( !$clientID ) {
			$this->setError( "invalid-input" );
			return;
		}

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
				results = %s
			WHERE	client_id = %u
			AND 	run_id = %u
			LIMIT 1;",
			$fail,
			$error,
			$total,
			$results,
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
						run_client, clients
					WHERE	run_id = %u
					AND 	client_id != %u
					AND 	(total <= 0 OR error > 0 OR fail > 0)
					AND 	clients.id=client_id
					AND 	clients.useragent_id = %u;",
					$runID,
					$clientID,
					$browserInfo->getSwarmUserAgentID()
				));

				if ( $rows ) {
					foreach ( $rows as $row ) {
						$db->query(str_queryf(
							"DELETE FROM run_client
							WHERE	run_id=%u
							AND	client_id=%u;",
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
						status = 2
					WHERE	useragent_id = %u
					AND 	run_id = %u
					LIMIT 1;",
					$browserInfo->getSwarmUserAgentID(),
					$runID
				));

			} else {

				// Clear out old runs that timed out.
				if ( $total > 0 ) {
					$rows = $db->getRows(str_queryf(
						"SELECT
							client_id
						FROM
							run_client, clients
						WHERE	run_id = %u
						AND 	client_id != %u
						AND 	total <= 0
						AND 	clients.id = client_id
						AND 	clients.useragent_id = %u;",
						$runID,
						$clientID,
						$browserInfo->getSwarmUserAgentID()
					));

					foreach ( $rows as $row ) {
						$db->query(str_queryf(
							"DELETE FROM run_client WHERE run_id = %u AND client_id = %u;",
							$runID,
							$row->client_id
						));
					}
				}

				$db->query(str_queryf(
					"UPDATE
						run_useragent
					SET
						completed = completed + 1,
						status = IF(completed + 1 < max, 1, 2)
					WHERE	useragent_id = %u
					AND 	run_id = %u
					LIMIT 1;",
					$browserInfo->getSwarmUserAgentID(),
					$runID
				));
			}
		}

		$this->setData( "ok" );
	}
}

