<?php
/**
 * Project action.
 *
 * @author Timo Tijhof, 2012-2013
 * @since 1.0.0
 * @package TestSwarm
 */
class ProjectAction extends Action {

	private $defaultLimit = 25;

	/**
	 * @actionParam string item: Project ID.
	 */
	public function doAction() {
		$conf = $this->getContext()->getConf();
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$projectID = $request->getVal( 'item' );
		if ( !$projectID ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		// Note: The job list is reverse chronologic (descending).
		// To query the "next" page, you get the jobs with an id
		// that is lower than the last entry on the current page.

		// Parameters for navigation of job list in this project
		$dir = $request->getVal( 'dir', '' );
		$offset = $request->getInt( 'offset' );
		$limit = $request->getInt( 'limit', $this->defaultLimit );
		if ( !in_array( $dir, array( '', 'back' ) )
			|| $limit < 1
			|| $limit > 100
		) {
			$this->setError( 'invalid-input' );
			return;
		}

		// Get project info
		$projectRow = $db->getRow(str_queryf(
			'SELECT
				id,
				display_title,
				site_url,
				updated,
				created
			FROM projects
			WHERE id = %s;',
			$projectID
		));
		if ( !$projectRow ) {
			$this->setError( 'invalid-input', 'Project does not exist' );
			return;
		}

		$conds = '';
		if ( $offset ) {
			if ( $dir !== 'back' ) {
				$conds = 'AND id < ' . intval( $offset );
			} else {
				$conds = 'AND id > ' . intval( $offset );
			}
		}

		// Get list of jobs
		$jobRows = $db->getRows(str_queryf(
			'SELECT
				id,
				name
			FROM
				jobs
			WHERE project_id = %s
			' . $conds . '
			ORDER BY id ' . ( $dir === 'back' ? 'ASC' : 'DESC' ) . '
			LIMIT %u;',
			$projectID,
			// Get one more so we know whether to display navigation
			$limit + 1
		));

		if ( $dir === 'back' ) {
			array_reverse( $jobRows );
		}

		$jobs = array();

		// List of all user agents used in recent jobs
		// This is as helper to allow easy creation of placeholder gaps in a UI
		// when iterating over jobs, because not all jobs have the same user agents.
		$userAgents = array();

		if ( !$jobRows ) {
			$pagination = array(
			);
		} else {

			$pagination = $this->getPaginationData( $dir, $offset, $limit, $jobRows, $projectID );

			foreach ( $jobRows as $jobRow ) {
				$jobID = intval( $jobRow->id );

				$jobAction = JobAction::newFromContext( $this->getContext()->createDerivedRequestContext(
					array(
						'item' => $jobID,
					)
				) );
				$jobAction->doAction();
				if ( $jobAction->getError() ) {
					$this->setError( $jobAction->getError() );
					return;
				}
				$jobActionData = $jobAction->getData();

				// Add user agents array of this job to the overal user agents list.
				// php array+ automatically fixes clashing keys. The values are always the same
				// so it doesn't matter whether or not it overwrites.
				$userAgents += $jobActionData['userAgents'];

				$jobs[] = array(
					'info' => $jobActionData['info'],
					'name' => $jobRow->name,
					'summaries' => $jobActionData['uaSummaries'],
				);
			}
		}

		uasort( $userAgents, 'BrowserInfo::sortUaData' );


		$projectInfo = (array)$projectRow;
		unset( $projectInfo['updated'], $projectInfo['created'] );

		self::addTimestampsTo( $projectInfo, $projectRow->updated, 'updated' );
		self::addTimestampsTo( $projectInfo, $projectRow->created, 'created' );

		$this->setData( array(
			'info' => $projectInfo,
			'jobs' => $jobs,
			'pagination' => $pagination,
			'userAgents' => $userAgents,
		));
	}

	private function getPaginationData( $dir, $offset, $limit, &$jobRows, $projectID ) {
		if ( $limit !== $this->defaultLimit ) {
			$limitUrl = '&limit=' . $limit;
		}
		$numRows = count( $jobRows );
		if ( $numRows ) {
			$row = reset( $jobRows );
			$firstRowID = $row->id;

			if ( $numRows > $limit ) {
				array_pop( $jobRows );
			}
			$row = end( $jobRows );
			$lastRowID = $row->id;
		} else {
			$firstRowID = '';
			$lastRowID = '';

		}

		if ( $dir === 'back' ) {
			$isFirst = $numRows <= $limit;
			$isLast = !$offset;
			$firstID = $firstRowID;
			$lastID = $lastRowID;
		} else {
			$isFirst = !$offset;
			$isLast = $numRows <= $limit;
			$firstID = $lastRowID;
			$lastID = $firstRowID;
		}

		if ( $isFirst ) {
			$prev = false;
		} else {
			$prev = array(
				'dir' => 'back',
				'offset' => $firstID,
				'viewUrl' => swarmpath( "/project/$projectID?offset={$firstID}&dir=back" . $limitUrl, 'fullurl' ),
			);
		}
		if ( $isLast ) {
			$next = false;
		} else {
			$next = array(
				'offset' => $lastID,
				'viewUrl' => swarmpath( "/project/$projectID?offset={$lastID}" . $limitUrl, 'fullurl' ),
			);
		}

		return array(
			'prev' => $prev,
			'next' => $next,
		);
	}
}
