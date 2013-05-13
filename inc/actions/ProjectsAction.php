<?php
/**
 * "Projects" action.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
class ProjectsAction extends Action {

	/**
	 * @actionParam string sort: [optional] What to sort the results by.
	 *  Must be one of "title", "id" or "creation". Defaults to "title".
	 * @actionParam string sort_dir: [optional]
	 *  Must be one of "asc" (ascending) or "desc" (decending). Defaults to "asc".
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$sortField = $request->getVal( 'sort', 'title' );
		$sortDir = $request->getVal( 'sort_dir', 'asc' );

		if ( !in_array( $sortField, array( 'title', 'id', 'creation' ) ) ) {
			$this->setError( 'invalid-input', "Unknown sort `$sortField`." );
			return;
		}

		if ( !in_array( $sortDir, array( 'asc', 'desc' ) ) ) {
			$this->setError( 'invalid-input', "Unknown sort direction `$sortDir`." );
			return;
		}

		$sortDirQuery = '';
		switch ( $sortDir ) {
			case 'asc':
				$sortDirQuery = 'ASC';
				break;
			case 'desc':
				$sortDirQuery = 'DESC';
				break;
		}

		$sortFieldQuery = '';
		switch ( $sortField ) {
			case 'title':
				$sortFieldQuery = "ORDER BY display_title $sortDirQuery";
				break;
			case 'id':
				$sortFieldQuery = "ORDER BY id $sortDirQuery";
				break;
			case 'creation':
				$sortFieldQuery = "ORDER BY created $sortDirQuery";
				break;
		}

		$projects = array();
		$projectRows = $db->getRows(
			"SELECT
				id,
				display_title,
				created
			FROM projects
			$sortFieldQuery;"
		);

		if ( $projectRows ) {
			foreach ( $projectRows as $projectRow ) {
				// Get information about the latest job (if any)
				$jobRow = $db->getRow(str_queryf(
					'SELECT id FROM jobs WHERE project_id = %s ORDER BY id DESC LIMIT 1;',
					$projectRow->id
				));
				if ( !$jobRow ) {
					$job = false;
				} else {
					$jobAction = JobAction::newFromContext( $this->getContext()->createDerivedRequestContext(
						array(
							'item' => $jobRow->id
						)
					) );
					$jobAction->doAction();
					$job = $jobAction->getData();
				}

				$project = array(
					'id' => $projectRow->id,
					'displayTitle' => $projectRow->display_title,
					'job' => $job
				);
				self::addTimestampsTo( $project, $projectRow->created, 'created' );
				$projects[] = $project;
			}
		}

		$this->setData( $projects );
	}
}
