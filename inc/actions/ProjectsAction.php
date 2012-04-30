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
	 * @requestParam sort string: [optional] What to sort the results by.
	 * Must be one of "name", "id", "creation" or "jobcount". Defaults to "name".
	 * @requestParam sort_order string: [optional]
	 * Must be one of "asc" (ascending" or "desc" (decending). Defaults to "asc".
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$filterSort = $request->getVal( "sort", "name" );
		$filterSortOrder = $request->getVal( "sort_order", "asc" );

		if ( !in_array( $filterSort, array( "name", "id", "creation", "jobcount" ) ) ) {
			$this->setError( "invalid-input", "Unknown sort `$filterSort`." );
			return;
		}

		if ( !in_array( $filterSortOrder, array( "asc", "desc" ) ) ) {
			$this->setError( "invalid-input", "Unknown sort order `$filterSortOrder`." );
			return;
		}

		$filterSortOrderQuery = "";
		switch ( $filterSortOrder ) {
			case "asc":
				$filterSortOrderQuery = "ASC";
				break;
			case "desc":
				$filterSortOrderQuery = "DESC";
				break;
		}

		$filterSortQuery = "";
		switch ( $filterSort ) {
			case "name":
				$filterSortQuery = "ORDER BY users.name $filterSortOrderQuery";
				break;
			case "id":
				$filterSortQuery = "ORDER BY users.id $filterSortOrderQuery";
				break;
			case "creation":
				$filterSortQuery = "ORDER BY users.created $filterSortOrderQuery";
				break;
			case "jobcount":
				$filterSortQuery = "ORDER BY job_count $filterSortOrderQuery";
				break;
		}

		$projects = array();
		$projectRows = $db->getRows(
			"SELECT
				DISTINCT(jobs.user_id) as user_id,
				users.name as user_name,
				users.created as user_created,
				COUNT(jobs.id) as job_count
			FROM jobs, users
			WHERE users.id = jobs.user_id
			GROUP BY jobs.user_id
			$filterSortQuery;"
		);

		if ( $projectRows ) {
			foreach ( $projectRows as $projectRow ) {
				$project = array(
					"id" => intval( $projectRow->user_id ),
					"name" => $projectRow->user_name,
					"jobCount" => intval( $projectRow->job_count ),
				);
				self::addTimestampsTo( $project, $projectRow->user_created, "created" );
				$projects[] = $project;
			}
		}

		$this->setData( $projects );
	}
}
