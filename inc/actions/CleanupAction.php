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
		// but haven't responded with a save (status=2) within 5 minutes.
		$rows = $db->getRows(str_queryf(
			"SELECT
				run_id,
				client_id,
				useragent_id
			FROM
				run_client, clients
			WHERE run_client.updated < %s
			AND   clients.id = run_client.client_id
			AND   run_client.status = 1;",
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

		// Reset runs that race-condition deleted themselves
/***
 * Disabled, this was causing the mysql server to lock on swarm.jquery.org
 * (see also issue #185). The race-condition that this query is trying to
 * fix shouldn't happen anymore in TestSwarm 1.0 anyway.
 * -- krinkle 2012-05-03
		$db->query(
			"UPDATE
				run_useragent
			SET
				runs = 0,
				completed = 0,
				status = 0
			WHERE runs = max
			AND   NOT EXISTS (
				SELECT *
				FROM run_client, clients
				WHERE run_client.run_id = run_useragent.run_id
				AND   run_client.client_id = clients.id
				AND   clients.useragent_id = run_useragent.useragent_id
			);"
		);
		$resetRaceConditionDeleted = $db->getAffectedRows();
*/
		// back compat.
		$resetRaceConditionDeleted = 0;

		$this->setData(array(
			"resetTimedoutRuns" => $resetTimedoutRuns,
			"resetRaceConditionDeleted" => $resetRaceConditionDeleted,
		));
	}
}

