<?php
/**
 * "Job" action.
 *
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class JobAction extends Action {

	/**
	 * @actionParam item int: Job ID.
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$jobID = $request->getInt( "item" );
		if ( !$jobID ) {
			$this->setError( "missing-parameters" );
			return;
		}

		// Get job information
		$jobRow = $db->getRow(str_queryf(
			"SELECT
				jobs.id as job_id,
				jobs.name as job_name,
				jobs.created as job_created,
				users.name as user_name
			FROM
				jobs, users
			WHERE jobs.id = %u
			AND   users.id=jobs.user_id;",
			$jobID
		));

		if ( !$jobRow ) {
			$this->setError( "invalid-input", "Job not found" );
			return;
		}

		if ( !$jobRow->job_id || !$jobRow->job_name || !$jobRow->user_name ) {
			$this->setError( "data-corrupt" );
			return;
		}

		// Get runs for this job
		$runRows = $db->getRows(str_queryf(
			"SELECT
				runs.id as run_id,
				runs.url as run_url,
				runs.name as run_name
			FROM
				runs
			WHERE runs.job_id = %u
			ORDER BY run_id;",
			$jobID
		));

		// Start of response data
		$respData = array(
			"jobInfo" => array(
				"id" => $jobID,
				"name" => $jobRow->job_name,
				"ownerName" => $jobRow->user_name,
				"creationTimestamp" => $jobRow->job_created
			),
			"runs" => array(),
			// Mapping of useragent id and information about them
			// Will contain all distinct user agents that one or more
			// runs of this job is scheduled to run for
			"userAgents" => array(),
		);

		$userAgentIDs = array();

		foreach ( $runRows as $runRow ) {
			$runInfo = array(
				"id" => $runRow->run_id,
				"name" => $runRow->run_name,
				"url" => $runRow->run_url,
			);

			$runUaRuns = array();

			// Get list of useragents that this run is scheduled for
			$runUaRows = $db->getRows(str_queryf(
				"SELECT
					status,
					useragent_id
				FROM
					run_useragent
				WHERE run_useragent.run_id = %u;",
				$runRow->run_id
			));
			if ( $runUaRows ) {
				foreach ( $runUaRows as $runUaRow ) {
					// Create array for this ua run,
					// If it has been run or is currently running,
					// this array will be re-created in the loop over $clientRunRows
					$runUaRuns[$runUaRow->useragent_id] = array(
						"runStatus" => self::resolveStatusID( (int)$runUaRow->status ),
					);

					// Add UA ID to the list. After we've collected
					// all the UA IDs we'll perform one query for all of them
					// to gather the info from the useragents table
					$userAgentIDs[] = $runUaRow->useragent_id;
				}
			}

			// Get client results for this run
			$clientRunRows = $db->getRows(str_queryf(
				"SELECT
					run_client.client_id as client_id,
					run_client.status as status,
					run_client.fail as fail,
					run_client.error as error,
					run_client.total as total,
					clients.useragent_id as useragent_id
				FROM
					run_client, clients
				WHERE run_client.run_id = %u
				AND   run_client.client_id = clients.id
				ORDER BY useragent_id;",
				$runRow->run_id
			));

			if ( $clientRunRows ) {
				foreach ( $clientRunRows as $clientRunRow ) {
					$runUaRuns[$clientRunRow->useragent_id] = array(
						"useragentID" => $clientRunRow->useragent_id,
						"clientID" => $clientRunRow->client_id,
						"failedTests" => $clientRunRow->fail,
						"totalTests" => $clientRunRow->total,
						"errors" => $clientRunRow->error,
						// new, progress, error, timedout, failed, or passed
						"runStatus" => self::getStatusFromClientRunRow( $clientRunRow ),
						// Add link to runresults
						"runResultsUrl" => swarmpath( "index.php" ) . "?" . http_build_query(array(
							"action" => "runresults",
							"run_id" => $runRow->run_id,
							"client_id" => $clientRunRow->client_id,
						)),
						"runResultsLabel" =>
							$clientRunRow->status < 2
							// If new or in progress, show nothing
							? ""
							: ( $clientRunRow->total < 0
								// Timeout
								? ""
								: ( $clientRunRow->error > 0
										// If there were errors, show number of errors
										? $clientRunRow->error
										: ( $clientRunRow->fail > 0
											// If it failed, show number of failures
											? $clientRunRow->fail
											// If it passed, show total number of tests
											: $clientRunRow->total
										)
									)
								),
					);
				}
			}

			$respData["runs"][] = array(
				"info" => $runInfo,
				"uaRuns" => $runUaRuns,
			);
		}

		// Get information for all encounted useragents
		$swarmUaIndex = BrowserInfo::getSwarmUAIndex();
		foreach ( $userAgentIDs as $userAgentID ) {
			if ( !isset( $swarmUaIndex->$userAgentID ) ) {
				throw new SwarmException( "Job $jobID has runs for unknown brower ID `$userAgentID`." );
			} else {
				$respData["userAgents"][$userAgentID] = (array)$swarmUaIndex->$userAgentID;
			}
		}

		// Save data
		$this->setData( $respData );
	}

	/**
	 * Resolve the numerical status codes found in the database tables.
	 *
	 * @param $statusId integer: A number between 0 and 2.
	 * @return string: One of 'new', 'progress', 'complete' or 'unknown'.
	 */
	public static function resolveStatusID( $statusId ) {
		if ( !is_integer( $statusId ) ) {
			return null;
		}
		switch ( $statusId ) {
			case 0:
				$status = "new";
				break;
			case 1:
				$status = "progress";
				break;
			case 2:
				$status = "complete";
				break;
			default:
				throw new SwarmException( "Invalid status ID `$statusId`." );
				break;
		}
		return $status;
	}

	/**
	 * @param $clientRun object: Database row from run_client.
	 * @return string: One of 'new', 'progress', 'timedout', 'error', 'failed' or 'passed'.
	 */
	public static function getStatusFromClientRunRow( $clientRun ) {
		$overalStatus = self::resolveStatusID( (int)$clientRun->status );
		if ( $overalStatus === "new" || $overalStatus === "progress" ) {
			return $overalStatus;
		}
		if ( $overalStatus === "complete" ) {
			// If a test runner times out, it submits
			// action=saverun&fail=-1&total=-1
			if ( $clientRun->fail == -1 ) {
				return "timedout";
			}
			// A total of 0 tests ran is also considered an error
			if ( $clientRun->error > 0 || $clientRun->total == 1 ) {
				return "error";
			}
			// Passed or failed
			return $clientRun->error > 0 ? "failed" : "passed";
		} else {
			throw new SwarmException( "Corrupt useragent run result." );
		}
	}
}
