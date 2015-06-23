<?php
/**
 * Get details about a project and list its jobs.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class ProjectAction extends Action {

	private $defaultLimit = 10;

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
			if ( $dir === 'back' ) {
				$conds = 'AND id > ' . intval( $offset );
			} else {
				$conds = 'AND id < ' . intval( $offset );
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

		$jobs = array();

		// List of all user agents used in recent jobs
		// This is as helper to allow easy creation of placeholder gaps in a UI
		// when iterating over jobs, because not all jobs have the same user agents.
		$userAgents = array();

		if ( !$jobRows ) {
			$pagination = array();

		} else {

			$pagination = $this->getPaginationData( $dir, $offset, $limit, $jobRows, $projectID );

			if ( $dir === 'back' ) {
				$jobRows = array_reverse( $jobRows );
			}

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
		$limitUrl = '';
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
			$firstID = $lastRowID;
			$lastID = $firstRowID;
		} else {
			$isFirst = !$offset;
			$isLast = $numRows <= $limit;
			$firstID = $firstRowID;
			$lastID = $lastRowID;
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

	/**
	 * @param string $id
	 * @param array $options
	 * @return array Exposes the new auth token
	 */
	public function create( $id, Array $options = null ) {
		$db = $this->getContext()->getDB();

		$password = isset( $options['password'] ) ? $options['password'] : null;
		$displayTitle = isset( $options['displayTitle'] ) ? $options['displayTitle'] : null;
		$siteUrl = isset( $options['siteUrl'] ) ? $options['siteUrl'] : '';

		if ( !$id || !$displayTitle || !$password ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		// Check if a project by this id doesn't exist already
		$row = $db->getOne( str_queryf( 'SELECT id FROM projects WHERE id = %s;', $id ) );
		if ( $row ) {
			$this->setError( 'invalid-input', 'Unable to create project, a project by that name exists already.' );
			return;
		}

		// Validate project id
		if ( !LoginAction::isValidName( $id ) ) {
			$this->setError( 'invalid-input', 'Project ids must be in format: "' . LoginAction::getNameValidationRegex() . '".' );
			return;
		}

		// maxlength (otherwise MySQL will crop it)
		if ( strlen( $displayTitle ) > 255 ) {
			$this->setError( 'Display title has to be no longer than 255 characters.' );
			return;
		}

		// Create the project
		$authToken = LoginAction::generateRandomHash( 40 );
		$authTokenHash = sha1( $authToken );

		$isInserted = $db->query(str_queryf(
			'INSERT INTO projects
			(id, display_title, site_url, password, auth_token, updated, created)
			VALUES(%s, %s, %s, %s, %s, %s, %s);',
			$id,
			$displayTitle,
			$siteUrl,
			LoginAction::generatePasswordHash( $password ),
			$authTokenHash,
			swarmdb_dateformat( SWARM_NOW ),
			swarmdb_dateformat( SWARM_NOW )
		));

		if ( !$isInserted ) {
			$this->setError( 'internal-error', 'Insertion of row into database failed.' );
			return;
		}

		return array(
			'authToken' => $authToken,
		);
	}
}
