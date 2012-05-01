<?php
/**
 * "Swarmstate" action.
 *
 * @author Jörn Zaefferer, 2012
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */

class SwarmstateAction extends Action {

	/**
	 * @requestParam onlyactive bool: If true, only user agents that
	 * have online clients and/or pending runs are included.
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$showOnlyactive = $request->hasKey( "onlyactive" );

		$data = array(
			"userAgents" => array(),
		);

		$uaIndex = BrowserInfo::getSwarmUAIndex();

		foreach ( $uaIndex as $uaID => $uaData ) {
			if ( $uaData->active !== true ) {
				continue;
			}

			// Count online clients with this UA
			$clients = $db->getOne(str_queryf(
				"SELECT
					COUNT(id)
				FROM clients
				WHERE useragent_id = %s
				AND   updated > %s",
				$uaID,
				swarmdb_dateformat( strtotime( '1 minute ago' ) )
			));
			$clients = intval( $clients );

			// Count pending runs for this UA
			$pendingRuns = $db->getOne(str_queryf(
				"SELECT
					COUNT(*)
				FROM run_useragent
				WHERE useragent_id = %s
				AND   status = 0;",
				$uaID
			));
			$pendingRuns = intval( $pendingRuns );

			// Count past runs that can still be re-run to
			// possibly fix non-passing results
			$pendingReRuns = $db->getOne(str_queryf(
				"SELECT
					COUNT(*)
				FROM run_useragent
				WHERE useragent_id = %s
				AND   runs < max
				AND   completed > 0;",
				$uaID
			));
			$pendingReRuns = intval( $pendingReRuns );

			if ( $showOnlyactive && !$clients && !$pendingRuns && !$pendingReRuns ) {
				continue;
			}

			$data["userAgents"][$uaID] = array(
				"data" => $uaData,
				"stats" => array(
					"onlineClients" => $clients,
					"pendingRuns" => $pendingRuns,
					"pendingReRuns" => $pendingReRuns,
				),
			);
		}

		$this->setData( $data );
	}
}
