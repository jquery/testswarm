<?php
/**
 * List all known clients.
 *
 * @author John Resig
 * @author Jörn Zaefferer
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
class ClientsAction extends Action {

	/**
	 * @actionParam string sort: [optional] What to sort the results by.
	 *  Must be one of "name" or "updated". Defaults to "name".
	 * @actionParam string sort_dir: [optional]
	 *  Must be one of "asc" (ascending) or "desc" (decending). Defaults to "asc".
	 * @actionParam string include: [optional] What filter to apply.
	 *  Must be one of "all" or "active". Defaults to "active".
	 * @actionParam string item: Fetch only information from clients by this name.
	 */
	public function doAction() {
		$context = $this->getContext();
		$request = $context->getRequest();

		$mode = $request->getVal( 'mode', 'clients' );
		$sortField = $request->getVal( 'sort', 'name' );
		$sortDir = $request->getVal( 'sort_dir', 'asc' );
		$include = $request->getVal( 'include', 'active' );
		$item = $request->getVal( 'item', false );

		if ( !in_array( $sortField, array( 'name', 'updated' ) ) ) {
			$this->setError( 'invalid-input', "Unknown sort `$sortField`." );
			return;
		}

		if ( !in_array( $sortDir, array( 'asc', 'desc' ) ) ) {
			$this->setError( 'invalid-input', "Unknown sort direction `$sortDir`." );
			return;
		}

		if ( !in_array( $include, array( 'all', 'active' ) ) ) {
			$this->setError( 'invalid-input', "Unknown filter `$include`." );
			return;
		}

		if ( !in_array( $mode, array( 'clients', 'names' ) ) ) {
			$this->setError( 'invalid-input', "Unknown mode `$mode`." );
			return;
		}

		$clients = $this->getActiveClients( $item );
		$overview = $this->getOverview( $sortField, $sortDir, $include, $item );

		foreach ( $clients as $client ) {
			if ( isset( $overview[ $client['name'] ]['clientIDs'] ) ) {
				$overview[ $client['name'] ]['clientIDs'][] = $client['id'];
			}
		}

		$this->setData( array(
			'name' => $item,
			'clients' => $clients,
			'overview' => $overview,
		) );
	}

	/**
	 * @param string|bool $name
	 */
	protected function getActiveClients( $name = false ) {
		$context = $this->getContext();
		$db = $context->getDB();

		$nameQuery = $name
			? 'AND name = \'' . $db->strEncode( $name ) . '\''
			: '';

		$results = array();

		$rows = $db->getRows(str_queryf(
			"SELECT
				id,
				name,
				useragent,
				updated,
				created
			FROM
				clients
			WHERE updated >= %s
			$nameQuery
			ORDER BY created DESC;",
			swarmdb_dateformat( Client::getMaxAge( $context ) )
		));

		if ( $rows ) {
			foreach ( $rows as $row ) {
				$bi = BrowserInfo::newFromContext( $this->getContext(), $row->useragent );

				$resultRow = $db->getRow(str_queryf(
					'SELECT
						id,
						run_id,
						client_id,
						status,
						total,
						fail,
						error,
						updated,
						created
					FROM runresults
					WHERE client_id = %u
					ORDER BY created DESC
					LIMIT 1;',
					$row->id
				));
				$client = array(
					'id' => $row->id,
					'name' => $row->name,
					'uaID' => $bi->getSwarmUaID(),
					'uaRaw' => $bi->getRawUA(),
					'uaData' => $bi->getUaData(),
					'viewUrl' => swarmpath( "client/{$row->id}" ),
					'lastResult' => !$resultRow ? null : array(
						'id' => intval( $resultRow->id ),
						'viewUrl' => swarmpath( "result/{$resultRow->id}" ),
						'status' => JobAction::getRunresultsStatus( $resultRow ),
					),
				);
				self::addTimestampsTo( $client, $row->created, 'connected' );
				self::addTimestampsTo( $client, $row->updated, 'pinged' );
				$results[$row->id] = $client;
			}
		}

		return $results;
	}

	/**
	 * @param string $sortField
	 * @param string $sortDir
	 * @param string $include
	 * @param string|bool $name
	 */
	protected function getOverview( $sortField, $sortDir, $include, $name ) {
		$context = $this->getContext();
		$db = $context->getDB();

		$sortDirQuery = strtoupper( $sortDir );
		$sortFieldQuery = "ORDER BY $sortField $sortDirQuery";

		$whereClause = array();
		if ( $include === 'active' ) {
			$whereClause[] = 'updated >= ' . swarmdb_dateformat( Client::getMaxAge( $context ) );
		}
		if ( $name ) {
			$whereClause[] = 'name = \'' . $db->strEncode( $name ) . '\'';
		}
		if ( count( $whereClause ) ) {
			$whereClause = 'WHERE ' . implode( ' AND ', $whereClause );
		} else {
			$whereClause = '';
		}

		$rows = $db->getRows(
			"SELECT
				name,
				MAX(updated) as updated
			FROM
				clients
			$whereClause
			GROUP BY name
			$sortFieldQuery;"
		);

		$results = array();
		if ( $rows ) {
			foreach ( $rows as $row ) {
				$result = array(
					'name' => $row->name,
					'viewUrl' => swarmpath( "clients/{$row->name}" ),
					'clientIDs' => array(),
				);
				$this->addTimestampsTo( $result, $row->updated, 'updated' );
				$results[$row->name] = $result;
			}
		}

		return $results;
	}
}
