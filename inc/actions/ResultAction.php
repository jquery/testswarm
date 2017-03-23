<?php
/**
 * Get one of the results of a run.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class ResultAction extends Action {
	// Currently being run in a client
	const STATE_BUSY = 1;

	// Run by the client is finished, results have been submitted.
	const STATE_FINISHED = 2;

	// Run by the client was aborted by the client.
	// Either the test (inject.js) lost pulse internally
	// and submitted a partial result, or the test runner (run.js)
	// aborted the test after conf.client.runTimeout.
	const STATE_ABORTED = 3;

	// Client did not submit results, and from CleanAction it
	// was determined that the client died (no longer sends pings).
	const STATE_LOST = 4;

	/**
	 * @actionParam int item: Runresults ID.
	 */
	public function doAction() {
		$context = $this->getContext();
		$db = $context->getDB();
		$conf = $context->getConf();
		$request = $context->getRequest();

		$item = $request->getInt( 'item' );
		if ( !$item ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		$row = $db->getRow(str_queryf(
			'SELECT
				id,
				run_id,
				client_id,
				status,
				updated,
				created
			FROM runresults
			WHERE id = %u;',
			$item
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
		$runRow = $db->getRow(str_queryf(
			'SELECT
				id,
				url,
				name,
				job_id
			FROM runs
			WHERE id = %u;',
			$row->run_id
		));

		if ( !$runRow ) {
			$data['otherRuns'] = null;
			$data['job'] = null;
		} else {
			$data['otherRuns'] = JobAction::getDataFromRunRows( $context, array( $runRow ) );

			$jobID = intval( $runRow->job_id );

			$data['job'] = array(
				'id' => $jobID,
				'url' => swarmpath( "job/$jobID", "fullurl" ),
			);
		}

		$clientRow = $db->getRow(str_queryf(
			'SELECT
				id,
				name,
				useragent_id,
				useragent
			FROM clients
			WHERE id = %u;',
			$row->client_id
		));

		$data['info'] = array(
			'id' => intval( $row->id ),
			'runID' => intval( $row->run_id ),
			'clientID' => intval( $row->client_id ),
			'status' => self::getStatus( $row->status ),
		);

		$data['client'] = array(
			'id' => $clientRow->id,
			'name' => $clientRow->name,
			'uaID' => $clientRow->useragent_id,
			'uaRaw' => $clientRow->useragent,
			'viewUrl' => swarmpath( 'client/' . $clientRow->id ),
		);

		// If still busy or if the client was lost, then the last update time is irrelevant
		// Alternatively this could test if $row->updated == $row->created, which would effectively
		// do the same.
		if ( $row->status == self::STATE_BUSY || $row->status == self::STATE_LOST ) {
			$data['info']['runTime'] = null;
		} else {
			$data['info']['runTime'] = gmstrtotime( $row->updated ) - gmstrtotime( $row->created );
			self::addTimestampsTo( $data['info'], $row->updated, 'saved' );
		}
		self::addTimestampsTo( $data['info'], $row->created, 'started' );

		$this->setData( $data );
	}

	public static function getStatus( $statusId ) {
		$mapping = array();
		$mapping[self::STATE_BUSY] = 'Busy';
		$mapping[self::STATE_FINISHED] = 'Finished';
		$mapping[self::STATE_ABORTED] = 'Aborted';
		$mapping[self::STATE_LOST] = 'Client lost';

		return isset( $mapping[$statusId] )
			? $mapping[$statusId]
			: false;
	}
}
