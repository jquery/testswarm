<?php
/**
 * "Getrun" action.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */

class GetrunAction extends Action {

	public function doAction() {
		$browserInfo = $this->getContext()->getBrowserInfo();
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( "requires-post" );
			return;
		}

		$clientID = $request->getInt( "client_id" );

		if ( !$clientID ) {
			$this->setError( "invalid-input" );
			return;
		}

		// Create a Client object to verify that the client exists
		// throws an exception, caught higher up, if it doesn't exist.
		// Also updates the timestamp so that it shows up on HomePage and UserPage
		$client = Client::newFromContext( $this->getContext(), $clientID );

		$runID = $db->getOne(str_queryf(
			"SELECT
				run_id
			FROM
				run_useragent
			WHERE	useragent_id = %u
			AND 	runs < max
			AND NOT EXISTS (SELECT 1 FROM run_client WHERE run_useragent.run_id = run_id AND client_id = %u)
			ORDER BY run_id DESC
			LIMIT 1;",
			$browserInfo->getSwarmUserAgentID(),
			$clientID
		));

		$runInfo = false;

		// A run was found for the current user_agent
		if ( $runID ) {

			$row = $db->getRow(str_queryf(
				"SELECT
					runs.url as run_url,
					jobs.name as job_name,
					runs.name as run_name
				FROM
					runs, jobs
				WHERE	runs.id=%u
				AND 	jobs.id=runs.job_id
				LIMIT 1;",
				$runID
			));

			if ( $row->run_url && $row->job_name && $row->run_name ) {
				# Mark the run as "in progress" on the useragent
				$db->query(str_queryf(
					"UPDATE run_useragent SET runs = runs + 1, status = 1 WHERE run_id=%u AND useragent_id=%u LIMIT 1;",
					$runID,
					$browserInfo->getSwarmUserAgentID()
				));

				# Initialize the client run
				$db->query(str_queryf(
					"INSERT INTO run_client (run_id, client_id, status, created) VALUES(%u, %u, 1, %s);",
					$runID,
					$clientID,
					swarmdb_dateformat( SWARM_NOW )
				));

				$runInfo = array(
					"id" => $runID,
					"url" => $row->run_url,
					"desc" => $row->job_name . ' ' . $row->run_name,
				);
			}
		}

		$this->setData( array(
			"confUpdate" => array( "client" => $this->getContext()->getConf()->client ),
			"runInfo" => $runInfo,
		) );
	}
}

