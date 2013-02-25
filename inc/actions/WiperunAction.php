<?php
/**
 * "Wiperun" action
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class WiperunAction extends Action {

	/*
	 * @actionMethod POST: Required.
	 * @actionParam int run_id
	 * @actionParam int client_id
	 * @actionParam int useragent_id
	 * @actionAuth: Required.
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$runID = $request->getInt( "run_id" );
		$clientID = $request->getInt( "client_id" );
		$useragentID = $request->getVal( "useragent_id" );

		if ( !$runID || !$clientID ) {
			$this->setError( "missing-parameters" );
			return;
		}

		$jobID = (int)$db->getOne(str_queryf(
			'SELECT job_id FROM runs WHERE id = %u;',
			$runID
		));

		if ( !$jobID ) {
			$this->setError( "invalid-input", "Run $runID not found." );
			return;
		}


		$projectID = $db->getOne(str_queryf(
			'SELECT
				project_id
			FROM jobs
			WHERE id = %u;',
			$jobID
		));

		if ( !$projectID ) {
			$this->setError( "invalid-input", "Job $jobID not found." );
			return;
		}

		// Check authentication
		if ( !$this->doRequireAuth( $projectID ) ) {
			return;
		}

		$runJobID = (int)$db->getOne(str_queryf(
			'SELECT job_id
			FROM runs
			WHERE id = %u;',
			$runID
		));
		if ( $runJobID !== $jobID ) {
			$this->setError( "invalid-input", "Run $runID does not belong to job $jobID." );
			return;
		}

		$clientUseragentID = $db->getOne(str_queryf(
			'SELECT useragent_id
			FROM clients
			WHERE id = %u;',
			$clientID
		));
		if ( $clientUseragentID !== $useragentID ) {
			$this->setError( "invalid-input", "Client $clientID does not run useragent $useragentID" );
			return;
		}

		$db->query(str_queryf(
			'UPDATE
				run_useragent
			SET
				status = 0,
				completed = 0,
				results_id = NULL,
				updated = %s
			WHERE run_id = %u
			AND   useragent_id = %s;',
			swarmdb_dateformat( SWARM_NOW ),
			$runID,
			$useragentID
		));

		$this->setData( array(
			"jobID" => $jobID,
			"runID" => $runID,
			"clientID" => $clientID,
			"useragentID" => $useragentID,
			"result" => "ok",
		) );
	}
}
