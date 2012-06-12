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
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( "requires-post" );
			return;
		}

		$runID = $request->getInt( "run_id" );
		$clientID = $request->getInt( "client_id" );
		$useragentID = $request->getVal( "useragent_id" );

		if ( !$runID || !$clientID ) {
			$this->setError( "missing-parameters" );
			return;
		}

		$jobID = (int)$db->getOne(str_queryf(
			"SELECT job_id FROM runs WHERE id = %u;",
			$runID
		));

		if ( !$jobID ) {
			$this->setError( "invalid-input", "Run $runID not found." );
			return;
		}

		$jobOwner = $db->getOne(str_queryf(
			"SELECT
				users.name as user_name
			FROM jobs, users
			WHERE jobs.id = %u
			AND   users.id = jobs.user_id
			LIMIT 1;",
			$jobID
		));

		if ( !$jobOwner ) {
			$this->setError( "invalid-input", "Job $jobID not found." );
			return;
		}

		// Check authentication
		if ( $request->getSessionData( "auth" ) !== "yes" || $request->getSessionData( "username" ) !== $jobOwner ) {
			$this->setError( "requires-auth" );
			return;
		}

		$runJobID = (int)$db->getOne(str_queryf(
			"SELECT job_id
			FROM runs
			WHERE id = %u;",
			$runID
		));
		if ( $runJobID !== $jobID ) {
			$this->setError( "invalid-input", "Run $runID does not belong to job $jobID." );
			return;
		}

		$clientUseragentID = $db->getOne(str_queryf(
			"SELECT useragent_id
			FROM clients
			WHERE id = %u;",
			$clientID
		));
		if ( $clientUseragentID !== $useragentID ) {
			$this->setError( "invalid-input", "Client $clientID does not run useragent $useragentID" );
			return;
		}

		$db->query(str_queryf(
			"UPDATE
				run_useragent
			SET
				status = 0,
				completed = 0,
				results_id = NULL,
				updated = %s
			WHERE run_id = %u
			AND   useragent_id = %s;",
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
