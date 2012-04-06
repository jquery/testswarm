<?php
/**
 * "Swarmstate" action.
 *
 * @author Jörn Zaefferer, 2012
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */

class SwarmstateAction extends Action {

	/**
	 * @requestParam "onlyactive" Only include user agents that online clients and/or pending runs.
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$showOnlyactive = $request->hasKey( "onlyactive" );

		$data = array(
			"useragents" => array(),
		);

		// @todo FIXME: query
		$uaRows = $db->getRows(
			"SELECT
				id,
				name,
				engine
			FROM
				useragents
			WHERE active = 1;"
		);

		if ( !$uaRows ) {
			$this->setError( "data-corrupt" );
			return;
		}

		foreach( $uaRows as $uaRow ) {
			// Count online clients with this UA
			$clients = $db->getOne(str_queryf(
				"SELECT
					COUNT(id)
				FROM clients
				WHERE useragent_id = %s
				AND   updated > %u",
				$uaRow->id,
				swarmdb_dateformat( strtotime( '1 minute ago' ) )
			));

			// Count pending runs for this UA
			$pendingRuns = $db->getOne(str_queryf(
				"SELECT
					COUNT(*)
				FROM run_useragent
				WHERE useragent_id = %s
				AND   status = 0;",
				$uaRow->id
			));

			if ( $showOnlyactive && !$clients && !$pendingRuns ) {
				continue;
			}

			$data["useragents"][] = array(
				"name" => $uaRow->name,
				"engine" => $uaRow->engine,
				"stats" => array(
					"onlineClients" => intval( $clients ),
					"pendingRuns" => intval( $pendingRuns ),
				),
			);
		}

		$this->setData( $data );
	}
}
