<?php
/**
 * "Addjob" action.
 * Addjob ignores the current session. Instead it uses tokens, which (although
 * all registered users have an auth token in the database), only trusted
 * users know their own token.
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class AddjobAction extends Action {

	/**
	 * @actionMethod POST: Required.
	 * @actionParam authUsername string
	 * @actionParam authToken string
	 * @actionParam jobName string: May contain HTML.
	 * @actionParam runMax int
	 * @actionParam runNames array
	 * @actionParam runUrls array
	 * @actionParam browserSets array
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

		$runs = array();

		// Loop through runNames / runUrls to validate them ahead of time,
		// and filter out empty ones from the AddjobPage.
		foreach ( $runNames as $runNr => $runName ) {
			if ( !isset( $runUrls[$runNr] ) ) {
				$this->setError( "invalid-input", "One or more runs is missing a URL." );
				return;
			}

			$runUrl = $runUrls[$runNr];

			// Filter out empty submissions,
			// AddjobPage may submit more input fields then filled in
			if ( $runUrl == '' && $runName == '' ) {
				continue;
			}

			if ( $runUrl == '' || $runName == '') {
				$this->setError( "invalid-input", "Run names and urls must be non-empty." );
				return;
			}

			if ( strlen( $runName ) > 255 ) {
				$formRunNr = $runNr + 1; // offset 0
				$this->setError( "invalid-input", "Run #{$formRunNr} name was too long (up to 255 characters)." );
				return;
			}

			$runs[] = array(
				"name" => $runName,
				"url" => $runUrl,
			);
		}

		if ( count( $runs ) === 0 ) {
			$this->setError( "missing-parameters", "Job must have atleast 1 run." );
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

		// Verify job name maxlength (otherwise MySQL will crop it, which might
		// result in incomplete html, screwing up the JobPage).
		if ( strlen( $jobName ) > 255 ) {
			$this->setError( "invalid-input", "Job name too long (up to 255 characters)." );
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

		$swarmUaIndex = BrowserInfo::getSwarmUAIndex();
		$uaIDs = array();

		foreach ( $swarmUaIndex as $swarmUaID => $swarmUaData ) {
			foreach ( $browserSets as $browserSet ) {
				if ( $swarmUaData->$browserSet === true ) {
					$uaIDs[] = $swarmUaID;
					// Don't include the same browser more than once
					break;
				}
			}
		}

		if ( !count( $uaIDs ) ) {
			$this->setError( "data-corrupt", "No user agents matched the generated browserset filter." );
			return;
		}

		// Create all runs and schedule them for the wanted browsersets in run_useragent
		foreach ( $runs as $run ) {

			// Create this run
			$isInserted = $db->query(str_queryf(
				"INSERT INTO runs (job_id, name, url, created)
				VALUES(%u, %s, %s, %s);",
				$newJobId,
				$run['name'],
				$run['url'],
				swarmdb_dateformat( SWARM_NOW )
			));

			$newRunId = $db->getInsertId();

			if ( !$isInserted || !$newRunId ) {
				$this->setError( "internal-error", "Insertion of job into database failed." );
				return;
			}

			// Schedule run_useragent entries for all user agents matching
			// the browerset(s) for this job.
			foreach ( $uaIDs as $uaID ) {
				$isInserted = $db->query(str_queryf(
					"INSERT INTO run_useragent (run_id, useragent_id, max, updated, created)
					VALUES(%u, %s, %u, %s, %s);",
					$newRunId,
					$uaID,
					$runMax,
					swarmdb_dateformat( SWARM_NOW ),
					swarmdb_dateformat( SWARM_NOW )
				));
			}

		}

		$this->setData(array(
			"id" => $newJobId,
			"runTotal" => count( $runs ),
			"uaTotal" => count( $uaIDs ),
		));
	}
}
