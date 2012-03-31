<?php
/**
 * "User" page.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class UserPage extends Page {

	protected function initContent() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$username = $request->getVal( "item" );

		$this->setTitle( "User" );
		$this->setSubTitle( $username );
		$this->bodyScripts[] = swarmpath( "js/jquery.js" );
		$this->bodyScripts[] = swarmpath( "js/pretty.js" );
		$this->bodyScripts[] = swarmpath( "js/user.js" );

		// TODO extract and share with JobPage
		function get_status($num) {
			if ( $num == 0 ) {
				return "Not started yet.";
			} elseif ( $num == 1 ) {
				return "In progress.";
			} else {
				return "Completed.";
			}
		}

		// TODO extract and share with JobPage
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

		// Get the user's ID
		$result = $db->getOne(str_queryf( "SELECT id FROM users WHERE name=%s;", $username ));
		if ( $result ) {
			$userID = intval( $result );
		} else {
			return '<h3>User does not exist</h3>';
		}

		$result = mysql_queryf(
			"SELECT
				useragents.engine as engine,
				useragents.name as name,
				clients.os as os,
				clients.created as since
			FROM
				clients, useragents
			WHERE
				clients.useragent_id=useragents.id
				AND clients.updated > %s
				AND clients.user_id=%u
			ORDER BY
				useragents.engine,
				useragents.name;",
			swarmdb_dateformat( strtotime( '1 minutes ago' ) ),
			$userID
		);

		$html = '';

		if ( mysql_num_rows( $result ) > 0 ) {

			$html .= '<h3>Active Clients:</h3><ul class="clients">';

			while ( $row = mysql_fetch_array($result) ) {
				$engine = $row[0];
				$browser_name = $row[1];
				$name = $row[2];
				$since = $row[3];

				$since_local = date( 'r', gmstrtotime( $since ) );
				// PHP's "c" claims to be ISO compatible but prettyDate JS disagrees
				// ("2004-02-12T15:19:21+00:00" vs. "2004-02-12T15:19:21Z")
				// Constructing format manually instead
				$since_zulu_iso = gmdate( 'Y-m-d\TH:i:s\Z', gmstrtotime( $since ) );

				if ( $name == "xp" ) {
					$name = "Windows XP";
				} elseif ( $name == "vista" ) {
					$name = "Windows Vista";
				} elseif ( $name == "win7" ) {
					$name = "Windows 7";
				} elseif ( $name == "2000" ) {
					$name = "Windows 2000";
				} elseif ( $name == "2003" ) {
					$name = "Windows 2003";
				} elseif ( $name == "osx10.4" ) {
					$name = "OS X 10.4";
				} elseif ( $name == "osx10.5" ) {
					$name = "OS X 10.5";
				} elseif ( $name == "osx10.6" ) {
					$name = "OS X 10.6";
				} elseif ( $name == "osx" ) {
					$name = "OS X";
				} elseif ( $name == "linux" ) {
					$name = "Linux";
				}

				$html .= "<li><img src=\"" . swarmpath( "images/$engine.sm.png" ) . "\" class=\"$engine\"> <strong class=\"name\">$browser_name $name</strong><br>Connected <span title=\"" . htmlspecialchars( $since_zulu_iso ) . "\" class=\"pretty\">" . htmlspecialchars( $since_local ) . "</span></li>";
			}

			$html .=  "</ul>";

		}

		$job_search = preg_replace( "/[^a-zA-Z ]/", "", $request->getVal( "job", "" ) );
		$job_search .= "%";

		$search_result = mysql_queryf(
			"SELECT
				jobs.name,
				jobs.status,
				jobs.id
			FROM
				jobs, users
			WHERE	jobs.name LIKE %s
			AND	users.name = %s
			AND	jobs.user_id = users.id
			ORDER BY jobs.created DESC
			LIMIT 15;",
			$job_search,
			$username
		);

		if ( mysql_num_rows( $search_result ) > 0 ) {

			$html .=  '<h3>Recent Jobs:</h3><table class="results"><tbody>';

			$output = "";
			$browsers = array();
			$addBrowser = true;
			$last = "";

			while ( $row = mysql_fetch_array( $search_result ) ) {
				$job_name = $row[0];
				$job_status = get_status( intval( $row[1] ) );
				$job_id = $row[2];

				$output .= '<tr><th><a href="' . swarmpath( "job/{$job_id}" ) . '">' . strip_tags($job_name) . "</a></th>\n";

				$results = array();
				$states = array();

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
					ORDER BY run_id, browsername;",
					$job_id
				);

				while ( $row = mysql_fetch_assoc($result) ) {
					if ( $row["run_id"] != $last ) {
						if ( $last ) {
							$addBrowser = false;
						}

						$useragents = array();

						$runResult = mysql_queryf(
							"SELECT
								run_client.client_id as client_id,
								run_client.status as status,
								run_client.fail as fail,
								run_client.error as error,
								run_client.total as total,
								clients.useragent_id as useragent_id,
								useragents.name as browser
							FROM
								useragents, run_client, clients
							WHERE
								run_client.run_id=%u
								AND run_client.client_id=clients.id
								AND useragents.id=useragent_id
							ORDER BY browser;",
							$row["run_id"]
						);

						while ( $ua_row = mysql_fetch_assoc($runResult) ) {
							if ( !isset( $useragents[ $ua_row["useragent_id"] ] ) ) {
								$useragents[ $ua_row["useragent_id"] ] = array();
							}

							array_push( $useragents[ $ua_row["useragent_id"] ], $ua_row );
						}
					}

					if ( $addBrowser ) {
						array_push( $browsers, array(
							"name" => $row["browsername"],
							"engine" => $row["browser"],
							"id" => $row["useragent_id"]
						) );
					}

					$last_browser = "";

					if ( isset( $useragents[ $row["useragent_id"] ] ) ) {
						foreach ( $useragents[ $row["useragent_id"] ] as $ua ) {
							$status = get_status2(intval($ua["status"]), intval($ua["fail"]), intval($ua["error"]), intval($ua["total"]));
							if ( $last_browser != $ua["browser"] ) {
								$cur = @$results[ $ua["useragent_id"] ];
								$results[ $ua["useragent_id"] ] = $cur + intval($ua["fail"]);

								$cur = @$states[ $ua["useragent_id"] ];

								if ( strstr($status, "notdone") || strstr($cur, "notdone") ) {
									$status = "notstarted notdone";
								} elseif ( $status == "error" || $cur == "error" ) {
									$status = "error";
								} elseif ( $status == "timeout" || $cur == "timeout" ) {
									$status = "timeout";
								} elseif ( $status == "fail" || $cur == "fail" ) {
									$status = "fail";
								} else {
									$status = "pass";
								}

								$states[ $ua["useragent_id"] ] = $status;
							}
							$last_browser = $ua["browser"];
						}
					} else {
						// TODO this throws 'Undefined index' errors, figure out why...
						$cur = @$results[ $row["useragent_id"] ];
						$results[ $row["useragent_id"] ] = $cur + 0;
						$states[ $row["useragent_id"] ] = "notstarted notdone";
					}

					$last = $row["run_id"];
				}


				foreach ( $results as $key => $fail ) {
					$output .= '<td class="' . $states[$key] . '"></td>';
				}

				$output .= "</tr>\n";

			}

			if ( $last ) {
				$header = "<tr><th></th>\n";
				$last_browser = null;
				foreach ( $browsers as $browser ) {
					if ( !isset( $last_browser ) || $last_browser["id"] != $browser["id"] ) {
						$header .= '<th><div class="browser">' .
							'<img src="' . swarmpath( 'images/' ) . $browser["engine"] .
							'.sm.png" class="browser-icon ' . $browser["engine"] .
							'" alt="' . $browser["name"] .
							'" title="' . $browser["name"] .
							'"><span class="browser-name">' .
							preg_replace('/\w+ /', "", $browser["name"]) . ', ' .
							'</span></div></th>';
					}
					$last_browser = $browser;
				}
				$header .= "</tr>\n";
				$output = $header . $output;
			}

			$html .=  "$output</tr>\n</tbody>\n</table>";
		}
		return $html;
	}

}
