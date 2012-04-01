<?php
/**
 * "Scores" action.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class ScoresAction extends Action {

	public function doAction() {
		$db = $this->getContext()->getDB();

		$rows = $db->getRows(
			"SELECT
				users.name as user_name,
				SUM(total) as score
			FROM
				clients, run_client, users
			WHERE clients.id = run_client.client_id
			AND   clients.user_id = users.id
			GROUP BY user_id
			HAVING score > 0
			ORDER by score DESC;"
		);

		$scores = array();
		if ( $rows ) {
			foreach ( $rows as $pos => $row ) {
				$scores[] = array(
					"position" => intval( $pos + 1 ), // Array is 0 based
					"userName" => $row->user_name,
					"score" => intval( $row->score )
				);
			}
		}

		$this->setData( $scores );
	}
}
