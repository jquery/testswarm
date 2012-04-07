<?php
/**
 * "User" action.
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */
class UserAction extends Action {

	public function doAction() {
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

		$uaIndex = BrowserInfo::getSwarmUAIndex();

		// Active clients
		$activeClients = array();

		$clientRows = $db->getRows(str_queryf(
			"SELECT
				useragent_id,
				useragent,
				created
			FROM
				clients
			WHERE user_id = %u
			AND   updated > %s
			ORDER BY created DESC;",
			$userID,
			swarmdb_dateformat( strtotime( "1 minutes ago" ) )
		));

		if ( $clientRows ) {
			foreach ( $clientRows as $clientRow ) {
				$since_local = date( "r", gmstrtotime( $clientRow->created ) );

				// PHP's "c" claims to be ISO compatible but prettyDateJS disagrees
				// ("2004-02-12T15:19:21+00:00" vs. "2004-02-12T15:19:21Z").
				// Constructing format manually instead.
				$since_zulu_iso = gmdate( "Y-m-d\TH:i:s\Z", gmstrtotime( $clientRow->created ) );

				$bi = BrowserInfo::newFromContext( $this->getContext(), $clientRow->useragent );

				$activeClients[] = array(
					"connectedRawUTC" => $clientRow->created,
					"connectedISO" => $since_zulu_iso,
					"connectedLocalFormatted" => $since_local,
					"uaID" => $clientRow->useragent_id,
					"uaRaw" => $bi->getRawUA(),
					"uaData" => $bi->getSwarmUaItem(),
					"uaBrowscap" => $bi->getBrowscap(),
				);
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
				$userAgents += $jobActionData["userAgents"];

				// The summerized status for each user agent run
				// of this job. e.g. if all are new except one,
				// then it will be on "progress", if all are complete
				// then the worst failure is put in the summary
				$uaSummary = array();

				foreach ( $jobActionData["runs"] as $run ) {
					$uaNotNew = array();
					$uaHasIncomplete = array();
					$uaStrongestStatus = array();
					foreach ( $run["uaRuns"] as $uaID => $uaRun ) {
						if ( $uaRun["runStatus"] !== "new" ) {
							$uaNotNew[$uaID] = true;
						}
						if ( $uaRun["runStatus"] === "new" || $uaRun["runStatus"] === "progress" ) {
							$uaHasIncomplete[$uaID] = true;
						}
						if ( !isset( $uaStrongestStatus[$uaID] ) || $uaRunStatusStrength[$uaRun["runStatus"]] > $uaRunStatusStrength[$uaRunStatusStrength[$uaID]] ) {
							$uaStrongestStatus[$uaID] = $uaRun["runStatus"];
						}
						$uaSummary[$uaID] = !isset( $uaNotNew[$uaID] )
							? "new"
							: ( isset( $uaHasIncomplete[$uaID] )
								? "progress"
								: $uaStrongestStatus[$uaID]
							);
					}
				}

				$recentJobs[] = array(
					"id" => $jobID,
					"name" => $jobRow->name,
					"url" => swarmpath( "job/$jobID" ),
					"uaSummary" => $uaSummary,
				);
			}
		}

		$this->setData(array(
			"userName" => $userName,
			"activeClients" => $activeClients,
			"recentJobs" => $recentJobs,
			"uasInJobs" => $userAgents,
		));
	}
}
