<?php
/**
 * "User" action.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
class UserAction extends Action {

	/**
	 * @actionParam int item: Username.
	 */
	public function doAction() {
		$conf = $this->getContext()->getConf();
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$userName = $request->getVal( "item" );
		if ( !$userName ) {
			$this->setError( "missing-parameters" );
			return;
		}

		$userID = $db->getOne(str_queryf( "SELECT id FROM users WHERE name = %s;", $userName ));
		$userID = intval( $userID );
		if ( !$userID ) {
			$this->setError( "invalid-input", "User does not exist" );
			return;
		}

		// Active clients
		$activeClients = array();

		$clientRows = $db->getRows(str_queryf(
			"SELECT
				useragent_id,
				useragent,
				updated,
				created
			FROM
				clients
			WHERE user_id = %u
			AND   updated >= %s
			ORDER BY created DESC;",
			$userID,
			swarmdb_dateformat( time() - ( $conf->client->pingTime + $conf->client->pingTimeMargin ) )
		));

		if ( $clientRows ) {
			foreach ( $clientRows as $clientRow ) {
				$bi = BrowserInfo::newFromContext( $this->getContext(), $clientRow->useragent );

				$activeClient = array(
					'uaID' => $bi->getSwarmUaID(),
					'uaRaw' => $bi->getRawUA(),
					'uaData' => $bi->getUaData()
				);
				self::addTimestampsTo( $activeClient, $clientRow->created, "connected" );
				self::addTimestampsTo( $activeClient, $clientRow->updated, "pinged" );
				$activeClients[] = $activeClient;
			}
		}

		// Recent jobs
		$recentJobs = array();

		// List of all user agents used in recent jobs
		// This is as helper allow creating proper gaps when iterating
		// over jobs.
		$userAgents = array();

		$jobRows = $db->getRows(str_queryf(
			"SELECT
				id,
				name
			FROM
				jobs
			WHERE jobs.user_id = %u
			ORDER BY jobs.created DESC
			LIMIT 15;",
			$userID
		));
		if ( $jobRows ) {
			$uaRunStatusStrength = array_flip(array(
				"passed",
				"new",
				"progress",
				"failed",
				"timedout",
				"error", // highest priority
			));

			foreach ( $jobRows as $jobRow ) {
				$jobID = intval( $jobRow->id );

				$jobActionContext = $this->getContext()->createDerivedRequestContext(
					array(
						"action" => "job",
						"item" => $jobID,
					),
					"GET"
				);

				$jobAction = JobAction::newFromContext( $jobActionContext );
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

				// The summerized status for each user agent run
				// of this job. e.g. if all are new except one,
				// then it will be on "progress", if all are complete
				// then the worst failure is put in the summary
				$uaSummary = array();

				$uaNotNew = array();
				$uaHasIncomplete = array();
				$uaStrongestStatus = array();

				foreach ( $jobActionData["runs"] as $run ) {
					foreach ( $run["uaRuns"] as $uaID => $uaRun ) {
						if ( $uaRun["runStatus"] !== "new" && !in_array( $uaID, $uaNotNew ) ) {
							$uaNotNew[] = $uaID;
						}
						if ( $uaRun["runStatus"] === "new" || $uaRun["runStatus"] === "progress" ) {
							if ( !in_array( $uaID, $uaHasIncomplete ) ) {
								$uaHasIncomplete[] = $uaID;
							}
						}
						if ( !isset( $uaStrongestStatus[$uaID] )
							|| $uaRunStatusStrength[$uaRun["runStatus"]] > $uaRunStatusStrength[$uaStrongestStatus[$uaID]]
						) {
							$uaStrongestStatus[$uaID] = $uaRun["runStatus"];
						}
						$uaSummary[$uaID] = !in_array( $uaID, $uaNotNew )
							? "new"
							: ( in_array( $uaID, $uaHasIncomplete )
								? "progress"
								: $uaStrongestStatus[$uaID]
							);
					}
				}

				$recentJobs[] = array(
					"id" => $jobID,
					"name" => $jobRow->name,
					"url" => swarmpath( "job/$jobID", "fullurl" ),
					"uaSummary" => $uaSummary,
				);
			}
		}

		natcaseksort( $userAgents );

		$this->setData(array(
			"userName" => $userName,
			"activeClients" => $activeClients,
			"recentJobs" => $recentJobs,
			"uasInJobs" => $userAgents,
		));
	}
}
