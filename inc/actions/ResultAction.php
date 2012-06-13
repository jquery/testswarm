<?php
/**
 * "Result" action.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
class ResultAction extends Action {
	// Currently being run in a client
	public static $STATE_BUSY = 1;

	// Run by the client is finished, results
	// have been submitted.
	public static $STATE_FINISHED = 2;

	// Run by the client was aborted by the client.
	// Either the test (inject.js) lost pulse internally
	// and submitted a partial result, or the test runner (run.js)
	// aborted the test after conf.client.runTimeout.
	public static $STATE_ABORTED = 3;

	// Client did not submit results, and from CleanAction it
	// was determined that the client died (no longer sends pings).
	public static $STATE_LOST = 4;

	/**
	 * @actionParam int item: Runresults ID.
	 */
	public function doAction() {
		$context = $this->getContext();
		$db = $context->getDB();
		$conf = $context->getConf();
		$request = $context->getRequest();

		$resultsID = $request->getInt( 'item' );
		$row = $db->getRow(str_queryf(
			'SELECT
				run_id,
				client_id,
				status,
				updated,
				created
			FROM runresults
			WHERE id = %u;',
			$resultsID
		));

		if ( !$row ) {
			$this->setError( 'invalid-input', 'Runresults ID not found.' );
			return;
		}

		$data = array();

		// A job can be deleted without nuking the runresults,
		// this is by design so results stay permanently accessible
		// under a simple url.
		// If the job is no longer in existance, properties
		// 'otherRuns' and 'job' will be set to null.
		$runRows = $db->getRows(str_queryf(
			'SELECT
				id,
				url,
				name,
				job_id
			FROM runs
			WHERE id = %u;',
			$row->run_id
		));

		if ( !$runRows || !count( $runRows ) ) {
			$data['otherRuns'] = null;
			$data['job'] = null;
		} else {
			$data['otherRuns'] = JobAction::getDataFromRunRows( $db, $runRows );

			$jobID = intval( $runRows[0]->job_id );

			$data['job'] = array(
				'id' => $jobID,
				'url' => swarmpath( "job/$jobID", "fullurl" ),
			);
		}

		$clientRow = $db->getRow(str_queryf(
			'SELECT
				id,
				user_id,
				useragent_id,
				useragent
			FROM clients
			WHERE id = %u;',
			$row->client_id
		));
		$userRow = $db->getRow(str_queryf(
			'SELECT
				id,
				name
			FROM users
			WHERE id = %u;',
			$clientRow->user_id
		));

		$data['client'] = array(
			'id' => $clientRow->id,
			'uaID' => $clientRow->useragent_id,
			'userAgent' => $clientRow->useragent,
			'userID' => $userRow->id,
			'userName' => $userRow->name,
			'userUrl' => swarmpath( 'user/' . $userRow->name ),
		);

		$data['resultInfo'] = array(
			'id' => $resultsID,
			'runID' => $row->run_id,
			'clientID' => $row->client_id,
			'status' => self::getStatus( $row->status ),
		);

		// If still busy or if the client was lost, then the last update time is irrelevant
		// Alternatively this could test if $row->updated == $row->created, which would effectively
		// do the same.
		if ( $row->status == self::$STATE_BUSY || $row->status == self::$STATE_LOST ) {
			$data['resultInfo']['runTime'] = null;
		} else {
			$data['resultInfo']['runTime'] = gmstrtotime( $row->updated ) - gmstrtotime( $row->created );
			self::addTimestampsTo( $data['resultInfo'], $row->updated, 'saved' );
		}
		self::addTimestampsTo( $data['resultInfo'], $row->created, 'started' );

		$this->setData( $data );
	}

	public static function getStatus( $statusId ) {
		$mapping = array();
		$mapping[self::$STATE_BUSY] = 'Busy';
		$mapping[self::$STATE_FINISHED] = 'Finished';
		$mapping[self::$STATE_ABORTED] = 'Aborted';
		$mapping[self::$STATE_LOST] = 'Client lost';

		return isset( $mapping[$statusId] )
			? $mapping[$statusId]
			: false;
	}
}
