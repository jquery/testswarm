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
	 * @actionMethod POST: Required.
	 * @actionParam int job_id
	 * @actionParam string type: one of 'delete', 'reset'.
	 * @actionAuth: Required.
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

		$runRows = $db->getRows(str_queryf(
			'SELECT id
			FROM runs
			WHERE job_id = %u;',
			$jobID
		));

		if ( $runRows ) {
			foreach ( $runRows as $runRow ) {
				if ( $wipeType === 'delete' ) {
					$db->query(str_queryf(
						'DELETE
						FROM run_useragent
						WHERE run_id = %u;',
						$runRow->id
					));
				} elseif ( $wipeType === 'reset' ) {
					$db->query(str_queryf(
						'UPDATE run_useragent
						SET
							status = 0,
							completed = 0,
							results_id = NULL,
							updated = %s
						WHERE run_id = %u;',
						swarmdb_dateformat( SWARM_NOW ),
						$runRow->id
					));
				}
			}
		}

		// This should be outside the if for $runRows, because jobs
		// can sometimes be created without any runs (by accidently).
		// Those should be deletable as well, thus this has to be outside the loop.
		// Also, no need to do this in a loop, just delete them all in one query.
		if ( $wipeType === 'delete' ) {
			$db->query(str_queryf(
				'DELETE
				FROM runs
				WHERE job_id = %u;',
				$jobID
			));
			$db->query(str_queryf(
				'DELETE
				FROM jobs
				WHERE id = %u;',
				$jobID
			));
		}

		$this->setData( array(
			'jobID' => $jobID,
			'type' => $wipeType,
			'result' => 'ok',
		) );
	}
}
