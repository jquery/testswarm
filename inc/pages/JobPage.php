<?php
/**
 * "Job" page.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class JobPage extends Page {

	private function getJobStatus() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		function get_status($num){
			if ( $num == 0 ) {
				return "Not started yet.";
			} elseif ( $num == 1 ) {
				return "In progress.";
			} else {
				return "Completed.";
			}
		}

		$jobId = $request->getInt( "item" );

		if ( !$jobId ) {
			$this->setError( "invalid-input" );
			return;
		}

		$result = $db->getRow(str_queryf(
			"SELECT
				jobs.name as job_name,
				jobs.status as job_status,
				users.name as user_name
			FROM
				jobs, users
			WHERE
				jobs.id=%u
			AND users.id=jobs.user_id;",
			$jobId
		));

		return array(
			"jobId" => $jobId,
			"jobName" => $result->job_name,
			"jobStatus" => get_status( intval( $result->job_status ) ),
			"owner" => $result->user_name === $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) === "yes"
		);
	}

	// TODO clean this up
	protected function initContent() {
		$request = $this->getContext()->getRequest();

		$status = $this->getJobStatus();

		$this->setTitle( "Job Status" );
		$this->setSubTitle( $status["jobName"] );
		$this->bodyScripts[] = swarmpath( "js/jquery.js" );
		$this->bodyScripts[] = swarmpath( "js/job.js" );

		$html = '<h3>Status of runs belonging to this job:</h3>';

		if ( $status["owner"] ) {
			$html .= '<form action="" method="POST">'
				. '<input type="hidden" name="action" value="wipejob">'
				. '<input type="hidden" name="item" value="' . $status["jobId"] . '">'
				. '<input type="submit" name="type" value="delete">'
				. '<input type="submit" name="type" value="reset">'
				. '</form>';
		}

		$html .= '<table class="results"><tbody>';

		$result = mysql_queryf(
			"SELECT
				runs.id as run_id,
				runs.url as run_url,
				runs.name as run_name,
				useragents.engine as browser,
				useragents.name as browsername,
				useragents.id as useragent_id,
				run_useragent.status as status
			FROM
				run_useragent, runs, useragents
			WHERE
				runs.job_id=%u
			AND run_useragent.run_id=runs.id
			AND run_useragent.useragent_id=useragents.id
			ORDER BY
				run_id, browsername;",
			$status["jobId"]
		);

		$last = "";
		$output = "";
		$browsers = array();
		$addBrowser = true;

		function get_status2($num, $fail, $error, $total){
			if ( $num == 0 ) {
				return "notstarted notdone";
			} elseif ( $num == 1 ) {
				return "progress notdone";
			} elseif ( $num == 2 && $fail == -1 ) {
				return "timeout";
			} elseif ( $num == 2 && ($error > 0 || $total == 0) ) {
				return "error";
			} else {
				return $fail > 0 ? "fail" : "pass";
			}
		}

		while ( $row = mysql_fetch_assoc($result) ) {
			if ( $row["run_id"] != $last ) {
				if ( $last ) {
					$addBrowser = false;
				}
				$useragents = array();

				$runResult = mysql_queryf("SELECT run_client.client_id as client_id, run_client.status as status, run_client.fail as fail, run_client.error as error, run_client.total as total, clients.useragent_id as useragent_id FROM run_client, clients WHERE run_client.run_id=%u AND run_client.client_id=clients.id ORDER BY useragent_id;", $row["run_id"]);

				while ( $ua_row = mysql_fetch_assoc($runResult) ) {
					if ( !isset( $useragents[ $ua_row["useragent_id"] ] ) ) {
						$useragents[ $ua_row["useragent_id"] ] = array();
					}

					array_push( $useragents[ $ua_row["useragent_id"] ], $ua_row );
				}

				$output .= '<tr><th><a href="' . $row["run_url"] . '">' . $row["run_name"] . "</a></th>\n";
			}

			if ( $addBrowser ) {
				array_push( $browsers, array(
					"name" => $row["browsername"],
					"engine" => $row["browser"],
					"id" => $row["useragent_id"]
				) );
			}

			$last_browser = -1;

			if ( isset( $useragents[ $row["useragent_id"] ] ) ) {
				foreach ( $useragents[ $row["useragent_id"] ] as $ua ) {
					$status = get_status2(intval($ua["status"]), intval($ua["fail"]), intval($ua["error"]), intval($ua["total"]));
					if ( $last_browser != $ua["useragent_id"] ) {
						$output .= "<td class='$status " . $row["browser"] . "'><a href='" . swarmpath ( '/' ) . "?action=runresults&run_id=" . $row["run_id"] . "&client_id=" . $ua["client_id"] . "'>" .
							($ua["status"] == 2 ?
								($ua["total"] < 0 ?
									"Err" :
									($ua["error"] > 0 ?
										$ua["error"] :
										($ua["fail"] > 0 ?
											$ua["fail"] :
											$ua["total"]
										)
									)
								)
								: ""
							) . "</a></td>\n";
					}
					$last_browser = $ua["useragent_id"];
				}
			} else {
				$output .= '<td class="notstarted notdone">&nbsp;</td>';
			}

			$last = $row["run_id"];
		}

		if ( $last ) {
			$header = "<tr><th></th>\n";
			$last_browser = null;
			foreach ( $browsers as $browser ) {
				if ( !isset( $last_browser ) || $last_browser["id"] != $browser["id"] ) {
					$header .= '<th><div class="browser">' .
						'<img src="' . swarmpath( "images/" . $browser["engine"] ) .
						'.sm.png" class="browser-icon ' . $browser["engine"] .
						'" alt="' . $browser["name"] .
						'" title="' . $browser["name"] .
						'"><span class="browser-name">' .
						preg_replace( "/\w+ /", "", $browser["name"] ) . ', ' .
						'</span></div></th>';
				}
				$last_browser = $browser;
			}
			$header .= '</tr>';
			$output = $header . $output;
			$output .= '</tr>';
		}

		$html .= $output . '</tr></tbody></table>';


		return $html;
	}
}
