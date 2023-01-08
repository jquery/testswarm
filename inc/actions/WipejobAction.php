<?php
/**
 * Delete or reset all runs of a specific job.
 *
 * @author John Resig
 * @since 0.1.0
 * @package TestSwarm
 */
class WipejobAction extends Action {

	/**
	 * @actionMethod POST Required.
	 * @actionParam int job_id
	 * @actionParam string type One of 'delete', 'reset'.
	 * @actionAuth Required.
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$jobID = $request->getInt( 'job_id' );
		$wipeType = $request->getVal( 'type' );

		if ( !$jobID || !$wipeType ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		if ( !in_array( $wipeType, array( 'delete', 'reset' ) ) ) {
			$this->setError( 'invalid-input', 'Invalid wipeType' );
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
			$this->setError( 'invalid-input', 'Job not found' );
			return;
		}

		// Check authentication
		if ( !$this->doRequireAuth( $projectID ) ) {
			return;
		}

		$this->doWipeJobs( $wipeType, [ $jobID ] );

		$this->setData( array(
			'jobID' => $jobID,
			'type' => $wipeType,
			'result' => 'ok',
		) );
	}

	public function doWipeJobs( $wipeType, array $jobIDs, $batchSize = 100 ) {
		$db = $this->getContext()->getDB();
		$stats = array(
			'jobs' => 0,
			'runs' => 0,
			'run_useragent' => 0,
			'runresults' => 0,
		);

		$allRunRows = $db->getRows(str_queryf(
			'SELECT id
			FROM runs
			WHERE job_id IN %l;',
			$jobIDs
		));

		if ( $allRunRows ) {
			$chunks = array_chunk( $allRunRows, $batchSize );
			foreach ( $chunks as $runRows ) {
				$runIDs = array_map( function ( $row ) { return $row->id; }, $runRows );
				if ( $wipeType === 'delete' ) {
					$db->query(str_queryf(
						'DELETE
						FROM run_useragent
						WHERE run_id in %l;',
						$runIDs
					));
				} elseif ( $wipeType === 'reset' ) {
					$db->query(str_queryf(
						'UPDATE run_useragent
						SET
							status = 0,
							completed = 0,
							results_id = NULL,
							updated = %s
						WHERE run_id in %l;',
						swarmdb_dateformat( SWARM_NOW ),
						$runIDs
					));
				}
				$stats['run_useragent'] += $db->getAffectedRows();

				if ( $wipeType === 'delete' ) {
					$db->query(str_queryf(
						'DELETE
						FROM runresults
						WHERE run_id in %l;',
						$runIDs
					));
					$stats['runresults'] += $db->getAffectedRows();
				}
			}
		}

		// This should be outside the if for $allRunRows, because jobs
		// can sometimes be created without any runs (by accident).
		// Those should be deletable as well, thus this has to be outside the loop.
		// Also, no need to do this in a loop, just delete them all in one query.
		if ( $wipeType === 'delete' ) {
			$db->query(str_queryf(
				'DELETE
				FROM runs
				WHERE job_id IN %l;',
				$jobIDs
			));
			$stats['runs'] += $db->getAffectedRows();
			$db->query(str_queryf(
				'DELETE
				FROM jobs
				WHERE id IN %l;',
				$jobIDs
			));
			$stats['jobs'] += $db->getAffectedRows();
		}

		return $stats;
	}
}
