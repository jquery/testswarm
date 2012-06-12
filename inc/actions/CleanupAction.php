<?php
/**
 * "Cleanup" action (previously WipeAction)
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */
class CleanupAction extends Action {

	/**
	 * @actionNote This action takes no parameters.
	 */
	public function doAction() {
		$browserInfo = $this->getContext()->getBrowserInfo();
		$db = $this->getContext()->getDB();
		$conf = $this->getContext()->getConf();
		$request = $this->getContext()->getRequest();

		// Get runs that were given to a client (status=1),
		// but haven't pinged back when they should.

		$maxage = time()
			// Maximum execution time
			- $conf->client->runTimeout
			// Maximum time it may take to save
			- ( $conf->client->saveRetryMax * ( $conf->client->saveReqTimeout + $conf->client->saveRetrySleep ) );

		$rows = $db->getRows(str_queryf(
			"SELECT
				id,
				results_id
			FROM
				run_useragent
			WHERE status = 1
			AND   updated < %s;",
			swarmdb_dateformat( $maxage )
		));
		$resetTimedoutRuns = 0;

		// For clients that have stopped pinging,
		// assume disconnection (browser crashed, network lost, closed browser, ..)
		// @todo: Incorrect, the above query finds runs that have timed out.
		// Not dead runs from no longer connected clients, both should be checked.
		// The latter involves 3 cross-checks. Get runresults entry. Get client_id.
		// Get clients entry. Check updated property against pingTime+pingTimeMargin (see UserAction/SwarmstateAction).
		// Make 2 arrays of runUaIds and runresultsIds and unique them before the if(). Change if to if-count()

		if ( $rows ) {
			$resetTimedoutRuns = count( $rows );
			foreach ( $rows as $row ) {
				$db->query(str_queryf(
					"UPDATE run_useragent
					SET
						status = 0,
						results_id = NULL
					WHERE id = %u;",
					$row->id
				));

				// Record runresults status as having timed-out (status=3)
				$db->query(str_queryf(
					"UPDATE runresults
					SET status = %s
					WHERE id = %u;",
					ResultAction::$STATE_LOST,
					$row->results_id
				));
			}
		}

		$this->setData(array(
			"resetTimedoutRuns" => $resetTimedoutRuns,
		));
	}
}

