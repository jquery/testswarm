<?php
/**
 * "Addjob" action.
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class AddjobAction extends Action {

	/**
	 * Addjob ignores the current session.
	 * Instead it uses tokens, which (although all registered users have an auth token
	 * in the database), only trusted users know their own token.
	 */
	public function doAction() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( "requires-post" );
			return;
		}

		$authUsername = $request->getVal( "authUsername" );
		$authToken = $request->getVal( "authToken" );

		$jobName = $request->getVal( "jobName" );
		$runMax = $request->getInt( "runMax" );
		$runNames = $request->getArray( "runNames" );
		$runUrls = $request->getArray( "runUrls" );
		$browserSets = $request->getArray( "browserSets" );

		if ( !$authUsername || !$authToken || !$jobName
			|| !$runNames || count( $runNames ) === 0
			|| !$runUrls || count( $runUrls ) === 0
			|| !$browserSets || count( $browserSets ) === 0
		) {
			$this->setError( "missing-parameters" );
			return;
		}

		if ( $runMax < 1 || $runMax > 99 ) {
			$this->setError( "invalid-input", "runMax must be an integer between 1 and 99." );
			return;
		}

		// Authenticate
		$authUserId = $db->getOne(str_queryf(
			"SELECT id
			FROM users
			WHERE name = %s
			AND   auth = %s;",
			$authUsername,
			$authToken
		));

		if ( !$authUserId ) {
			$this->setError( "invalid-input", "Authentication failed." );
			return;
		}

		// Create job
		$isInserted = $db->query(str_queryf(
			"INSERT INTO jobs (user_id, name, created)
			VALUES (%u, %s, %s);",
			$authUserId,
			$jobName,
			swarmdb_dateformat( SWARM_NOW )
		));

		$newJobId = $db->getInsertId();
		if ( !$isInserted || !$newJobId ) {
			$this->setError( "internal-error", "Insertion of job into database failed." );
			return;
		}

		// Generate a list of user agent IDs based on the selected browser sets
		$browserSetsCnt = count( $browserSets );
		$browserSets = array_unique( $browserSets );
		if ( $browserSetsCnt != count( $browserSets ) ) {
			$this->setError( "invalid-input", "Duplicate entries in browserSets parameter." );
			return;
		}
		$uaWhereClause = "";
		foreach ( $browserSets as $browserSet ) {
			switch ( $browserSet ) {
				case "current":
				case "popular":
				case "gbs":
				case "beta":
				case "mobile":
					// space before/after is important
					$uaWhereClause .= " AND $browserSet = 1 ";
					break;
				default:
					$this->setError( "invalid-input", "Unknown browserset `$browserSet`." );
					return;
			}
		}
		$uaRows = $db->getRows(
			"SELECT
				id
			FROM useragents
			WHERE active = 1 $uaWhereClause;"
		);
		if ( !$uaRows || !count( $uaRows ) ) {
			$this->setError( "data-corrupt", "No user agents matched the generated browserset filter." );
			return;
		}

		$createdRuns = 0;

		// Create all runs and schedule them for the wanted browsersets in run_useragent
		foreach ( $runNames as $runNr => $runName ) {

			if ( !isset( $runUrls[$runNr] ) ) {
				$this->setError( "invalid-input", "One or more runs is missing a URL." );
				return;
			}

			// Filter out empty submissions,
			// AddjobPage may submit more input fields then filled in
			if ( $runUrls[$runNr] == '' && $runName == '' ) {
				continue;
			}

			if ( $runUrls[$runNr] == '' || $runName == '') {
				$this->setError( "invalid-input", "Run names and urls must be non-empty." );
				return;
			}

			$runUrl = $runUrls[$runNr];

			// Create this run
			$isInserted = $db->query(str_queryf(
				"INSERT INTO runs (job_id, name, url, created)
				VALUES(%u, %s, %s, %s);",
				$newJobId,
				$runName,
				$runUrl,
				swarmdb_dateformat( SWARM_NOW )
			));
			$newRunId = $db->getInsertId();
			if ( !$isInserted || !$newRunId ) {
				$this->setError( "internal-error", "Insertion of job into database failed." );
				return;
			}

			$createdRuns += 1;

			// Schedule run_useragent entries for all user agents matching
			// the browerset(s) for this job.
			foreach ( $uaRows as $uaRow ) {
				$isInserted = $db->query(str_queryf(
					"INSERT INTO run_useragent (run_id, useragent_id, max, created)
					VALUES(%u, %u, %u, %s);",
					$newRunId,
					$uaRow->id,
					$runMax,
					swarmdb_dateformat( SWARM_NOW )
				));
			}

		}

		$this->setData(array(
			"id" => $newJobId,
			"runTotal" => $createdRuns,
			"uaTotal" => count( $uaRows ),
		));
	}
}
