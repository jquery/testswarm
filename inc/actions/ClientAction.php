<?php
/**
 * "Client" action.
 *
 * @author Timo Tijhof, 2012-2013
 * @since 1.0.0
 * @package TestSwarm
 */

class ClientAction extends Action {

	/**
	 * @actionParam string item: Client id.
	 */
	public function doAction() {
		$context = $this->getContext();
		$db = $context->getDB();
		$request = $context->getRequest();

		$item = $request->getInt( 'item' );
		if ( !$item ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		// Client information
		$row = $db->getRow(str_queryf(
			'SELECT
				id,
				name,
				useragent,
				updated,
				created
			FROM
				clients
			WHERE id = %u;',
			$item
		));
		if ( !$row ) {
			$this->setError( 'invalid-input', 'Client not found' );
			return;
		}

		$bi = BrowserInfo::newFromContext( $context, $row->useragent );

		$info = array(
			'id' => intval( $row->id ),
			'name' => $row->name,
			'viewUrl' => swarmpath( "clients/{$row->name}" ),
			'uaID' => $bi->getSwarmUaID(),
			'uaRaw' => $bi->getRawUA(),
			'uaData' => $bi->getUaData(),
			'sessionAge' => gmstrtotime( $row->updated ) - gmstrtotime( $row->created ),
		);
		self::addTimestampsTo( $info, $row->created, 'connected' );
		self::addTimestampsTo( $info, $row->updated, 'pinged' );

		// Run results
		$results = array();
		$rows = $db->getRows(str_queryf(
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
			ORDER BY created DESC;',
			$item
		));
		if ( $rows ) {
			foreach ( $rows as $row ) {
				$runRow = $jobRow = false;
				$result = array(
					'id' => intval( $row->id ),
					'viewUrl' => swarmpath( "result/{$row->id}" ),
					'status' => JobAction::getRunresultsStatus( $row ),
				);
				$runRow = $db->getRow(str_queryf(
					'SELECT
						name,
						job_id
					FROM runs
					WHERE id = %u;',
					$row->run_id
				));
				if ( $runRow ) {
					$jobRow = $db->getRow(str_queryf(
						'SELECT
							id,
							name,
							project_id
						FROM
							jobs
						WHERE id = %u',
						$runRow->job_id
					));
					if ( $jobRow ) {
						$projectRow = $db->getRow(str_queryf(
							'SELECT
								display_title
							FROM projects
							WHERE id = %s;',
							$jobRow->project_id
						));
						$result['job'] = array(
							// See also JobAction:;getInfo
							'nameText' => strip_tags( $jobRow->name ),
							'viewUrl' => swarmpath( "job/{$jobRow->id}" ),
						);
						$result['run'] = array(
							'name' => $runRow->name
						);
						$result['project'] = array(
							'id' => $jobRow->project_id,
							'display_title' => $projectRow->display_title,
							'viewUrl' => swarmpath( "project/{$jobRow->project_id}" ),
						);
					}
				}
				// Runs and jobs could be deleted, results are preserved.
				if ( !$jobRow ) {
					$result['job'] = null;
					$result['run'] = null;
					$result['project'] = null;
				}
				$results[] = $result;
			}
		}

		$this->setData( array(
			'info' => $info,
			'results' => $results
		) );
	}
}
