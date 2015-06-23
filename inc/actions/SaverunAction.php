<?php
/**
 * Submit the results of a run.
 *
 * @author John Resig
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
class SaverunAction extends Action {

	/**
	 * @actionMethod POST: Required.
	 * @actionParam int client_id
	 * @actionParam string run_token
	 * @actionParam int run_id
	 * @actionParam string results_id
	 * @actionParam string results_store_token
	 * @actionParam int total
	 * @actionParam int fail
	 * @actionParam int error
	 * @actionParam int status: `runresults.status`
	 * @actionParam string report_html: HTML snapshot of the test results page.
	 */
	public function doAction() {
		$browserInfo = $this->getContext()->getBrowserInfo();
		$conf = $this->getContext()->getConf();
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( 'requires-post' );
			return;
		}

		$runToken = $request->getVal( 'run_token' );
		if ( $conf->client->requireRunToken && !$runToken ) {
			$this->setError( 'missing-parameters', 'This TestSwarm does not allow unauthorized clients to join the swarm.' );
			return;
		}

		$runID = $request->getInt( 'run_id' );
		$clientID = $request->getInt( 'client_id' );
		$resultsID = $request->getVal( 'results_id' );
		$resultsStoreToken = $request->getVal( 'results_store_token' );

		if ( !$runID || !$clientID || !$resultsID || !$resultsStoreToken ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		// Create a Client object that verifies client id, user agent and run token.
		// Also updates the client 'alive' timestamp.
		// Throws exception (caught higher up) if stuff is invalid.
		$client = Client::newFromContext( $this->getContext(), $runToken, $clientID );

		$total = $request->getInt( 'total', 0 );
		$fail = $request->getInt( 'fail', 0 );
		$error = $request->getInt( 'error', 0 );
		$status = $request->getInt( 'status', 2 );
		$reportHtml = $request->getVal( 'report_html', '' );

		if ( !in_array( $status, array( 2, 3 ) ) ) {
			$this->setError( 'invalid-input', 'Illegal status to be set from the client side in action=saverun.' );
			return;
		}

		// Verify this runresults row exists,
		// also naturally validates run_id and store_token
		$res = $db->query(str_queryf(
			'SELECT
				id
			FROM runresults
			WHERE id = %u
			AND   run_id = %u
			AND   store_token = %s;',
			$resultsID,
			$runID,
			sha1( $resultsStoreToken )
		));

		if ( !$res || $db->getNumRows( $res ) !== 1 ) {
			$this->setError( 'invalid-input' );
			return;
		}

		$db->query(str_queryf(
			'UPDATE
				runresults
			SET
				status = %u,
				total = %u,
				fail = %u,
				error = %u,
				report_html = %s,
				updated = %s
			WHERE id = %u
			LIMIT 1;',
			$status,
			$total,
			$fail,
			$error,
			gzencode( $reportHtml ),
			swarmdb_dateformat( SWARM_NOW ),

			$resultsID
		));

		if ( $db->getAffectedRows() !== 1 ) {
			$this->setError( 'internal-error', 'Updating of results table failed.' );
			return;
		}

		$isPassed = $total > 0 && $fail === 0 && $error === 0;

		// Use results_id in the WHERE clause as additional check, just in case
		// this runresults row is no longer the primary linked one.
		// This fixes a race condition where (for some reason) 2 clients run the
		// same run, and the "good" one started last and finishes first. When the
		// "bad" client finishes and updates this run as not passing, it would
		// mismatch the results the user would find in the linked report from runresults.
		// Be sure to use it only as "WHERE" not in "SET", as that could cause
		// an equally bad effect (unlink a good run).
		if ( $isPassed ) {
			$db->query(str_queryf(
				'UPDATE
					run_useragent
				SET
					completed = completed + 1,
					status = 2,
					updated = %s
				WHERE run_id = %u
				AND   useragent_id = %s
				AND   results_id = %u
				LIMIT 1;',
				swarmdb_dateformat( SWARM_NOW ),
				$runID,
				$browserInfo->getSwarmUaID(),
				$resultsID
			));
		} else {
			// If we don't pass and we haven't reached max yet,
			// set status back to 0 so that this run may be
			// distributed again (see also GetrunAction),
			// If we don't pass and did reach the max, set
			// status=2.
			$db->query(str_queryf(
				'UPDATE
					run_useragent
				SET
					completed = completed + 1,
					status = IF(completed + 1 < max, 0, 2),
					updated = %s
				WHERE run_id = %u
				AND   useragent_id = %s
				AND   results_id = %u
				LIMIT 1;',
				swarmdb_dateformat( SWARM_NOW ),
				$runID,
				$browserInfo->getSwarmUaID(),
				$resultsID
			));
		}

		$this->setData( 'ok' );
	}
}

