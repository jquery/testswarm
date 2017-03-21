<?php
/**
 * Get details about a specific job.
 *
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
class JobAction extends Action {
	protected $item, $runs, $userAgents;

	/**
	 * @actionParam int item: Job ID.
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$this->item = $request->getInt( 'item' );
		if ( !$this->item ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		// Get job information
		$jobInfo = $this->getInfo();

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
			$this->item
		));

		$processed = self::getDataFromRunRows( $this->getContext(), $runRows );
		$this->runs = $processed['runs'];
		$this->userAgents = $processed['userAgents'];

		$uaSummaries = $this->getUaSummaries();

		// Start of response data
		$this->setData( array(
			'info' => $jobInfo,
			'runs' => $this->runs,
			// Mapping of useragent id and information about them.
			// Will contain all distinct user agents that one or more
			// runs of this job is scheduled to run for.
			'userAgents' => $this->userAgents,
			'uaSummaries' => $uaSummaries,
			'summary' => $this->getSummary( $uaSummaries ),
			'pagination' => $this->getPaginationData( $jobInfo ),
		) );
	}

	protected function getUaSummaries() {
		$uaStatuses = array();
		foreach ( $this->runs as $run ) {
			foreach ( $run['uaRuns'] as $uaID => $uaRun ) {
				$uaStatuses[$uaID][] = $uaRun['runStatus'];
			}
		}

		$uaSummaries = array();
		foreach ( $uaStatuses as $uaID => $statuses ) {
			$uaSummaries[$uaID] = self::getUaSummaryFromStatuses( $statuses );
		}

		return $uaSummaries;
	}

	protected function getSummary( $uaSummaries ) {
		return self::getUaSummaryFromStatuses( array_values( $uaSummaries ) );
	}

	/**
	 * @return array|bool
	 */
	protected function getInfo() {
		$db = $this->getContext()->getDB();
		$jobRow = $db->getRow(str_queryf(
			'SELECT
				id,
				name,
				project_id,
				created
			FROM
				jobs
			WHERE id = %u',
			$this->item
		));

		if ( !$jobRow ) {
			return false;
		}
		$jobID = intval( $jobRow->id );

		$projectRow = $db->getRow(str_queryf(
			'SELECT
				display_title
			FROM projects
			WHERE id = %s;',
			$jobRow->project_id
		));

		$ret = array(
			'id' => $jobID,
			'nameHtml' => $jobRow->name,
			'nameText' => strip_tags( $jobRow->name ),
			'project' => array(
				'id' => $jobRow->project_id,
				'display_title' => $projectRow->display_title,
				'viewUrl' => swarmpath( "project/{$jobRow->project_id}" ),
			),
			'viewUrl' => swarmpath( "job/$jobID", 'fullurl' )
		);
		self::addTimestampsTo( $ret, $jobRow->created, 'created' );
		return $ret;
	}


	/**
	 * @param array $jobInfo from getInfo(), containing 'id' and 'project'
	 * @return array Rows with 'id', 'name', 'dir'
	 */
	protected function getPaginationData( array $jobInfo ) {
		$prev = $next = null;
		$pagingRows = $this->getPrevAndNext( $jobInfo );
		if ( $pagingRows ) {
			foreach ( $pagingRows as $row ) {
				if ( $row->dir === 'prev' ) {
					$prev = array(
						'nameText' => strip_tags( $row->name ),
						'viewUrl' => swarmpath( 'job/' . $row->id )
					);
				} else {
					$next = array(
						'nameText' => strip_tags( $row->name ),
						'viewUrl' => swarmpath( 'job/' . $row->id )
					);
				}
			}
		}
		return array(
			'prev' => $prev,
			'next' => $next,
		);
	}

	/**
	 * @param array $jobInfo from getInfo(), containing 'id' and 'project'
	 * @return array Rows with 'id', 'name', 'dir'
	 */
	private function getPrevAndNext( array $jobInfo ) {
		$db = $this->getContext()->getDB();
		return $db->getRows(str_queryf(
			// Get previous and next job within this project (if any)
			'SELECT * FROM (SELECT id, name, "prev" as dir
			FROM jobs
			WHERE id < %u
			AND project_id = %u
			ORDER BY id DESC LIMIT 1) prev
			UNION ALL
			SELECT * FROM (SELECT id, name, "next" as dir
			FROM jobs
			WHERE id > %u
			AND project_id = %u
			ORDER BY id ASC LIMIT 1) next',
			$jobInfo['id'],
			$jobInfo['project']['id'],
			$jobInfo['id'],
			$jobInfo['project']['id']
		));
	}

	/**
	 * Iterate over all run rows and aggregate the runs and user agents.
	 * @return Array List of runs and userAgents.
	 */
	public static function getDataFromRunRows( TestSwarmContext $context, $runRows ) {
		$db = $context->getDB();
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
								id,
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

				uksort( $runUaRuns, array( $context->getBrowserInfo(), 'sortUaId' ) );

				$runs[] = array(
					'info' => $runInfo,
					'uaRuns' => $runUaRuns,
				);
			}
		}

		// Get information for all encounted useragents
		$browserIndex = BrowserInfo::getBrowserIndex();
		$userAgents = array();
		foreach ( $userAgentIDs as $uaID ) {
			if ( !isset( $browserIndex->$uaID ) ) {
				// If it isn't in the index anymore, it means it has been removed from the browserSets
				// configuration. Use a generic fallback object;
				$userAgents[$uaID] = BrowserInfo::makeGenericUaData( $uaID );
			} else {
				$userAgents[$uaID] = (array)$browserIndex->$uaID;
			}
		}
		uasort( $userAgents, 'BrowserInfo::sortUaData' );

		return array(
			'runs' => $runs,
			'userAgents' => $userAgents,
		);
	}

	public static function getUaSummaryFromStatuses( Array $statuses ) {
		$strengths = array_flip(array(
			'passed',
			'new',
			'progress',
			'lost',
			'timedout',
			'failed',
			'error', // highest priority
		));

		$isNew = true;
		$strongest = null;
		$hasIncomplete = false;

		foreach ( $statuses as $status ) {
			if ( $status !== 'new' && $isNew ) {
				$isNew = false;
			}
			if ( $status === 'new' || $status === 'progress' ) {
				if ( !$hasIncomplete ) {
					$hasIncomplete = true;
				}
			}
			if ( !$strongest || $strengths[$status] > $strengths[$strongest] ) {
				$strongest = $status;
			}
		}

		return $isNew
			? 'new'
			: ( $hasIncomplete
				? 'progress'
				: $strongest
			);
	}

	/**
	 * @param $row object: Database row from runresults.
	 * @return string: One of 'progress', 'passed', 'failed', 'timedout', 'error', or 'lost'
	 */
	public static function getRunresultsStatus( $row ) {
		$status = (int)$row->status;
		if ( $status === ResultAction::$STATE_BUSY ) {
			return 'progress';
		}
		if ( $status === ResultAction::$STATE_FINISHED ) {
			// A total of 0 tests ran is also considered an error
			if ( $row->error > 0 || intval( $row->total ) === 0 ) {
				return 'error';
			}
			// Passed or failed
			return $row->fail > 0 ? 'failed' : 'passed';
		}
		if ( $status === ResultAction::$STATE_ABORTED ) {
			return 'timedout';
		}
		if ( $status === ResultAction::$STATE_LOST ) {
			return 'lost';
		}
		// If status is 4 (ResultAction::$STATE_LOST) it means a CleanupAction
		// was aborted between two queries. This is no longer possible, but old
		// data may still be corrupted. Run fixRunresultCorruption.php to fix
		// these entries.
		throw new SwarmException( 'Corrupt run result #' . $row->id );
	}
}
