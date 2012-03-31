<?php
/**
 * "Cleanup" action (previously action=wipe)
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class CleanupAction extends Action {

	public function doAction() {
		$browserInfo = $this->getContext()->getBrowserInfo();
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		// Reset runs that were given to a client,
		// but haven't been active for over 5 minutes
		$rows = $db->getRows(str_queryf(
			"SELECT
				run_id,
				client_id,
				useragent_id
			FROM
				run_client, clients
			WHERE	run_client.updated < %s
			AND 	clients.id = client_id
			AND 	run_client.status = 1;",
			swarmdb_dateformat( strtotime( '5 minutes ago' ) )
		));

		if ( $rows ) {
			foreach ( $rows as $row ) {
				// Undo run count
				mysql_queryf(
					"UPDATE
						run_useragent
					SET
						runs = runs - 1
					WHERE	run_id = %u
					AND 	useragent_id = %u;",
					$row->run_id,
					$row->useragent_id
				);
				// Remove run_client entry
				mysql_queryf(
					"DELETE FROM
						run_client
					WHERE	run_id = %u
					AND	client_id = %u;",
					$row->run_id,
					$row->client_id
				);
			}
		}

		// Reset runs that race-condition deleted themselves
		$db->query(
			"UPDATE
				run_useragent
			SET
				runs = 0,
				completed = 0,
				status = 0
			WHERE	runs = max
			AND NOT EXISTS (
					SELECT *
					FROM run_client, clients
					WHERE	run_client.run_id = run_useragent.run_id
					AND 	run_client.client_id = clients.id
					AND 	clients.useragent_id = run_useragent.useragent_id
				);"
		);

		$this->setData( "ok" );
	}
}

