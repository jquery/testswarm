<?php
/**
 * "Wipejob" action
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */

class WipejobAction extends Action {

	/**
	 * @requestParam "item" integer: job id
	 * @requestParam "type" string: one of 'delete', 'reset'
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( "requires-post" );
			return;
		}

		$jobID = $request->getInt( "job_id" );
		$wipeType = $request->getVal( "type" );

		if ( !$jobID || !$wipeType ) {
			$this->setError( "missing-parameters" );
			return;
		}

		if ( !in_array( $wipeType, array( "delete", "reset" ) ) ) {
			$this->setError( "invalid-input" );
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
			// Job row by this ID didn't exist
			$this->setError( "invalid-input" );
			return;
		}

		// Check authentication
		if ( $request->getSessionData( "auth" ) !== "yes" || $request->getSessionData( "username" ) !== $jobOwner ) {
			$this->setError( "requires-auth" );
			return;
		}

		$runRows = $db->getRows(str_queryf(
			"SELECT
				id
			FROM
				runs
			WHERE runs.job_id = %u;",
			$jobID
		));

		if ( $runRows ) {
			if ( $wipeType === "delete" ) {
				$db->query(str_queryf( "DELETE FROM run_client WHERE run_id in (SELECT id FROM runs WHERE job_id=%u);", $jobID ));
				$db->query(str_queryf( "DELETE FROM run_useragent WHERE run_id in (SELECT id FROM runs WHERE job_id=%u);", $jobID ));
				$db->query(str_queryf( "DELETE FROM runs WHERE job_id=%u;", $jobID ));
				$db->query(str_queryf( "DELETE FROM jobs WHERE id=%u;", $jobID ));
			} elseif ( $wipeType === "reset" ) {
				$db->query(str_queryf( "UPDATE jobs SET status=0, updated=%s WHERE id=%u;", swarmdb_dateformat( SWARM_NOW ), $jobID ));
				$db->query(str_queryf( "UPDATE runs SET status=0, updated=%s WHERE job_id=%u;", swarmdb_dateformat( SWARM_NOW ), $jobID ));
			}

			foreach ( $runRows as $runRow ) {
				$db->query(str_queryf(
					"DELETE FROM run_client WHERE run_id=%u;",
					$runRow->id
				));

				if ( $wipeType === "delete" ) {
					$db->query(str_queryf(
						"DELETE FROM run_useragent WHERE run_id=%u;",
						$runRow->id
					));
				} elseif ( $wipeType === "reset" ) {
					$db->query(str_queryf(
						"UPDATE run_useragent SET runs=0, completed=0, status=0, updated=%s WHERE run_id=%u;",
						swarmdb_dateformat( SWARM_NOW ),
						$runRow->id
					));
				}
			}
		}

		$this->setData( array(
			"jobID" => $jobID,
			"type" => $wipeType,
			"result" => "ok",
		) );
	}
}
