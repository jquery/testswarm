<?php
/**
 * Gargage collection and and timeout handling.
 *
 * Must be regularly run from cron.
 *
 * @author John Resig
 * @since 0.1.0
 * @package TestSwarm
 */
class CleanupAction extends Action {

	/**
	 * @actionNote This action takes no parameters.
	 */
	public function doAction() {
		$context = $this->getContext();
		$browserInfo = $context->getBrowserInfo();
		$db = $context->getDB();
		$conf = $context->getConf();
		$request = $context->getRequest();

		$resetTimedoutRuns = 0;

		// Get clients that are considered disconnected (not responding to the latest pings).
		// Then mark the runresults of its active runs as timed-out, and reset those runs so
		// they become available again for different clients in GetrunAction.

		$rows = $db->getRows(str_queryf(
			"SELECT
				runresults.id as id
			FROM
				runresults
			INNER JOIN clients ON runresults.client_id = clients.id
			WHERE runresults.status = 1
			AND   clients.updated < %s;",
			swarmdb_dateformat( Client::getMaxAge( $context ) )
		));

		if ($rows) {
			foreach ($rows as $row) {
				// Reset the run
				$ret = $db->query(str_queryf(
					"UPDATE run_useragent
					SET
						status = 0,
						results_id = NULL
					WHERE results_id = %u;",
					$row->id
				));

				// If the previous UPDATE query failed for whatever
				// reason, don't do the below query as that will lead
				// to data corruption (results with state LOST must never
				// be referenced from run_useragent.results_id).
				if ( $ret ) {
					// Update status of the result
					$ret = $db->query(str_queryf(
						"UPDATE runresults
						SET status = %s
						WHERE id = %u;",
						ResultAction::STATE_LOST,
						$row->id
					));
				}

				if ( $ret ) {
					$resetTimedoutRuns++;
				}
			}
		}

		$this->setData(array(
			"resetTimedoutRuns" => $resetTimedoutRuns,
		));
	}
}

