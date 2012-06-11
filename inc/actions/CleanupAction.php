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
		$request = $this->getContext()->getRequest();

		// Get runs that were given to a client (status=1),
		// but haven't pinged in over 5 minutes.
		$rows = $db->getRows(str_queryf(
			"SELECT
				run_id,
				client_id,
				(SELECT useragent_id FROM clients WHERE clients.id = client_id LIMIT 1) as useragent_id
			FROM
				run_client
			WHERE updated < %s
			AND   status = 1;",
			swarmdb_dateformat( strtotime( '5 minutes ago' ) )
		));
		$resetTimedoutRuns = 0;

		if ( $rows ) {
			$resetTimedoutRuns = count( $rows );
			foreach ( $rows as $row ) {
				// Undo runcount and reset status
				$db->query(str_queryf(
					"UPDATE
						run_useragent
					SET
						runs = runs - 1,
						status = 0
					WHERE run_id = %u
					AND   useragent_id = %s;",
					$row->run_id,
					$row->useragent_id
				));

				// Remove run_client entry,
				// after 5 minutes we'll assume the client crashed, refreshed, closed the browser
				// or something else...
				$db->query(str_queryf(
					"DELETE FROM
						run_client
					WHERE run_id = %u
					AND   client_id = %u;",
					$row->run_id,
					$row->client_id
				));
			}
		}

		$this->setData(array(
			"resetTimedoutRuns" => $resetTimedoutRuns,
		));
	}
}

