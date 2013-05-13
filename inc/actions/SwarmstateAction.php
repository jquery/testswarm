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
	 * @actionParam string browserSet: Show useragents from a specific
	 *  browserset only.
	 * @actionParam bool onlyactive: If true, only user agents that
	 *  have online clients and/or pending runs are included.
	 *  If both "browserSet" and "onlyactive" are used, the overlaping
	 *  subset will be output.
	 */
	public function doAction() {
		$context = $this->getContext();
		$conf = $context->getConf();
		$db = $context->getDB();
		$request = $context->getRequest();

		$showOnlyactive = $request->getBool( 'onlyactive' );

		$filterBrowserSet = $request->getVal( 'browserSet', false );

		$data = array(
			'userAgents' => array(),
		);

		$browserIndex = BrowserInfo::getBrowserIndex();

		$browserSetByUaId = array();
		foreach ( $conf->browserSets as $browserSet => $browsers ) {
			foreach ( $browsers as $browser ) {
				$browserSetByUaId[$browser] = $browserSet;
			}
		}

		foreach ( $browserIndex as $uaID => $uaData ) {
			if ( $filterBrowserSet && $browserSetByUaId[$uaID] !== $filterBrowserSet ) {
				continue;
			}

			// Count online clients with this UA
			$clients = $db->getOne(str_queryf(
				'SELECT
					COUNT(id)
				FROM clients
				WHERE useragent_id = %s
				AND   updated >= %s',
				$uaID,
				swarmdb_dateformat( Client::getMaxAge( $context ) )
			));
			$clients = intval( $clients );

			// Count active runs for this UA
			$activeRuns = $db->getOne(str_queryf(
				'SELECT
					COUNT(*)
				FROM run_useragent
				WHERE useragent_id = %s
				AND   status = 1;',
				$uaID
			));
			$activeRuns = intval( $activeRuns );

			// Count pending runs for this UA
			$pendingRuns = $db->getOne(str_queryf(
				'SELECT
					COUNT(*)
				FROM run_useragent
				WHERE useragent_id = %s
				AND   status = 0
				AND   completed = 0;',
				$uaID
			));
			$pendingRuns = intval( $pendingRuns );

			// Count past runs that can still be re-run to
			// possibly fix non-passing results
			$pendingReRuns = $db->getOne(str_queryf(
				'SELECT
					COUNT(*)
				FROM run_useragent
				WHERE useragent_id = %s
				AND   status = 0
				AND   completed > 0;',
				$uaID
			));
			$pendingReRuns = intval( $pendingReRuns );

			if ( !$clients && !$activeRuns && !$pendingRuns && !$pendingReRuns ) {
				if ( $showOnlyactive || !isset( $browserSetByUaId[$uaID] ) ) {
					continue;
				}
			}

			$data['userAgents'][$uaID] = array(
				'data' => $uaData,
				'stats' => array(
					'onlineClients' => $clients,
					'activeRuns' => $activeRuns,
					'pendingRuns' => $pendingRuns,
					'pendingReRuns' => $pendingReRuns,
				),
			);
		}

		// Make sure they are sorted nicely
		uasort( $data['userAgents'], function ( $a, $b ) {
			return strnatcasecmp( $a['data']->displayInfo['title'], $b['data']->displayInfo['title'] );
		} );

		$this->setData( $data );
	}
}
