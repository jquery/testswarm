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
	 * @actionParam int item: Job ID.
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$jobID = $request->getInt( 'item' );
		if ( !$jobID ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		// Get job information
		$jobInfo = self::getJobInfoFromId( $db, $jobID );

		if ( !$jobInfo ) {
			$this->setError( 'invalid-input', 'Job not found' );
			return;
		}

		// Get runs for this job
		$runRows = $db->getRows(str_queryf(
			'SELECT
				id,
				url,
				name
			FROM
				runs
			WHERE job_id = %u
			ORDER BY id;',
			$jobID
		));

		$data = self::getDataFromRunRows( $db, $runRows );

		// Start of response data
		$respData = array(
			'jobInfo' => $jobInfo,
			'runs' => $data['runs'],
			// Mapping of useragent id and information about them
			// Will contain all distinct user agents that one or more
			// runs of this job is scheduled to run for
			'userAgents' => $data['userAgents'],
		);


		// Save data
		$this->setData( $respData );
	}

	/**
	 * @param Database $db
	 * @param int $jobID
	 * @return array|bool
	 */
	public static function getJobInfoFromId( Database $db, $jobID ) {
		$jobRow = $db->getRow(str_queryf(
			'SELECT
				jobs.id as job_id,
				jobs.name as job_name,
				jobs.created as job_created,
				users.name as user_name
			FROM
				jobs, users
			WHERE jobs.id = %u
			AND   users.id = jobs.user_id;',
			$jobID
		));

		if ( !$jobRow ) {
			return false;
		}

		return array(
			'id' => $jobID,
			'name' => $jobRow->job_name,
			'ownerName' => $jobRow->user_name,
			'creationTimestamp' => $jobRow->job_created
		);
	}

	/**
	 * @param Database $db
	 * @param array $runRows: one or more rows from the `runs` table.
	 * @return array with properties 'runs' and 'userAgents'
	 */
	public static function getDataFromRunRows( Database $db, $runRows ) {
		$userAgentIDs = array();
		$runs = array();

		foreach ( $runRows as $runRow ) {
			$runInfo = array(
				'id' => $runRow->id,
				'name' => $runRow->name,
				'url' => $runRow->url,
			);

			$runUaRuns = array();

			// Get list of useragents that this run is scheduled for
			$runUaRows = $db->getRows(str_queryf(
				'SELECT
					status,
					useragent_id,
					results_id
				FROM
					run_useragent
				WHERE run_useragent.run_id = %u;',
				$runRow->id
			));
			if ( $runUaRows ) {
				foreach ( $runUaRows as $runUaRow ) {

					// Add UA ID to the list. After we've collected
					// all the UA IDs we'll perform one query for all of them
					// to gather the info from the useragents table
					$userAgentIDs[] = $runUaRow->useragent_id;


					if ( !$runUaRow->results_id ) {
						$runUaRuns[$runUaRow->useragent_id] = array(
							'runStatus' => 'new',
						);
					} else {
						$runresultsRow = $db->getRow(str_queryf(
							'SELECT
								client_id,
								status,
								total,
								fail,
								error
							FROM runresults
							WHERE id = %u;',
							$runUaRow->results_id
						));

						if ( !$runresultsRow ) {
							$this->setError( 'data-corrupt' );
							return;
						}

						$runUaRuns[$runUaRow->useragent_id] = array(
							'useragentID' => $runUaRow->useragent_id,
							'clientID' => $runresultsRow->client_id,

							'failedTests' => $runresultsRow->fail,
							'totalTests' => $runresultsRow->total,
							'errors' => $runresultsRow->error,

							'runStatus' => self::getRunresultsStatus( $runresultsRow ),
							// Add link to runresults
							'runResultsUrl' => swarmpath( 'result/' . $runUaRow->results_id ),
							'runResultsLabel' =>
								$runresultsRow->status != ResultAction::$STATE_FINISHED
								// If not finished, we don't have any numeric label to show
								// (test could be in progress, or maybe it was aborted/lost)
								? ''
								: ( $runresultsRow->error > 0
										// If there were errors, show number of errors
										? $runresultsRow->error
										: ( $runresultsRow->fail > 0
											// If it failed, show number of failures
											? $runresultsRow->fail
											// If it passed, show total number of tests
											: $runresultsRow->total
										)
									),
						);
					}
				}

				natcaseksort( $runUaRuns );

				$runs[] = array(
					'info' => $runInfo,
					'uaRuns' => $runUaRuns,
				);
			}
		}

		// Get information for all encounted useragents
		$swarmUaIndex = BrowserInfo::getSwarmUAIndex();
		$userAgents = array();
		foreach ( $userAgentIDs as $userAgentID ) {
			if ( !isset( $swarmUaIndex->$userAgentID ) ) {
				throw new SwarmException( "Job $jobID has runs for unknown brower ID `$userAgentID`." );
			} else {
				$userAgents[$userAgentID] = (array)$swarmUaIndex->$userAgentID;
			}
		}
		natcaseksort( $userAgents );

		return array(
			'runs' => $runs,
			'userAgents' => $userAgents,
		);
	}

	/**
	 * @param $row object: Database row from runresults.
	 * @return string: One of 'progress', 'timedout', 'passed', 'failed' or 'error'.
	 */
	public static function getRunresultsStatus( $row ) {
		$status = (int)$row->status;
		if ( $status === 1 ) {
			return 'progress';
		}
		if ( $status === 2 ) {
			// A total of 0 tests ran is also considered an error
			if ( $row->error > 0 || intval( $row->total ) === 0 ) {
				return 'error';
			}
			// Passed or failed
			return $row->fail > 0 ? 'failed' : 'passed';
		}
		if ( $status === 3 ) {
			return 'timedout';
		}
		throw new SwarmException( 'Corrupt useragent run result.' );
	}
}
