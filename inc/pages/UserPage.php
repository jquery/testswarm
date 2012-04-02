<?php
/**
 * "User" page.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
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

		// Get the user's ID
		$result = $db->getOne(str_queryf( "SELECT id FROM users WHERE name=%s;", $username ));
		if ( $result ) {
			$userID = intval( $result );
		} else {
			return '<h3>User does not exist</h3>';
		}

		$rows = $db->getRows(str_queryf(
			"SELECT
				useragents.engine as engine,
				useragents.name as name,
				clients.os as os,
				clients.created as since
			FROM
				clients, useragents
			WHERE clients.useragent_id = useragents.id
			AND   clients.updated > %s
			AND   clients.user_id = %u
			ORDER BY
				useragents.engine,
				useragents.name;",
			swarmdb_dateformat( strtotime( '1 minutes ago' ) ),
			$userID
		));

		$html = '';

		if ( $rows ) {

			$html .= '<h3>Active clients</h3><ul class="clients">';

			foreach ( $rows as $row ) {
				$since_local = date( 'r', gmstrtotime( $row->since ) );
				// PHP's "c" claims to be ISO compatible but prettyDate JS disagrees
				// ("2004-02-12T15:19:21+00:00" vs. "2004-02-12T15:19:21Z")
				// Constructing format manually instead
				$since_zulu_iso = gmdate( 'Y-m-d\TH:i:s\Z', gmstrtotime( $row->since ) );

				$name = $row->os;
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

				$html .= '<li><img src="' . swarmpath( "images/{$row->engine}.sm.png" ) . '" class="'
					. $row->engine . '"> <strong class="name">' . $row->name . $name
					. '</strong><br>Connected <span title="'
					. htmlspecialchars( $since_zulu_iso ) . '" class="pretty">'
					. htmlspecialchars( $since_local ) . '</span></li>';
			}

			$html .= '</ul>';

		}

		$job_search = preg_replace( "/[^a-zA-Z ]/", "", $request->getVal( "job", "" ) );
		$job_search .= "%";

		$rows = $db->getRows(str_queryf(
			"SELECT
				jobs.id as job_id,
				jobs.name as job_name
			FROM
				jobs, users
			WHERE jobs.name LIKE %s
			AND   users.name = %s
			AND   jobs.user_id = users.id
			ORDER BY jobs.created DESC
			LIMIT 15;",
			$job_search,
			$username
		));

		if ( $rows ) {
			$html .= '<h3>Recent jobs</h3><table class="results"><tbody>';

			$output = "";
			$browsers = array();
			$addBrowser = true;
			$last = "";

			foreach ( $rows as $row ) {
				// Job names can and may contain HTML, thats fine for in the heading of the JobPage,
				// but in this overview we neem them to be plain text so that we can actually link
				// to the JobPage, stripping html.
				// @todo Revisit this.
				$output .= '<tr><th><a href="' . swarmpath( "job/{$row->job_id}" ) . '">' . htmlspecialchars( strip_tags( $row->job_name ) ) . "</a></th>\n";

				$results = array();
				$states = array();

				$rows = $db->getRows(str_queryf(
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
					$row->job_id
				));
				if ( !$rows ) {
					$rows = array();
				}

				foreach ( $rows as $row ) {
					if ( $row->run_id != $last ) {
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
							$row->run_id
						);

						while ( $clientRunRow = mysql_fetch_assoc($runResult) ) {
							if ( !isset( $useragents[ $clientRunRow["useragent_id"] ] ) ) {
								$useragents[ $clientRunRow["useragent_id"] ] = array();
							}

							array_push( $useragents[ $clientRunRow["useragent_id"] ], $clientRunRow );
						}
					}

					if ( $addBrowser ) {
						array_push( $browsers, array(
							"name" => $row->browsername,
							"engine" => $row->browser,
							"id" => $row->useragent_id,
						) );
					}

					$last_browser = "";

					// @todo This throws 'Undefined index' notices, figure out why...
					// surpressed now with @
					if ( isset( $useragents[ $row->useragent_id ] ) ) {
						foreach ( $useragents[ $row->useragent_id ] as $ua ) {
							$status = JobAction::getStatusFromClientRunRow( (object)$ua );
							if ( $last_browser != $ua["browser"] ) {
								$cur = @$results[ $ua["useragent_id"] ];
								$results[ $ua["useragent_id"] ] = $cur + intval($ua["fail"]);

								$cur = @$states[ $ua["useragent_id"] ];

								$states[ $ua["useragent_id"] ] = $status;
							}
							$last_browser = $ua["browser"];
						}
					} else {
						$cur = @$results[ $row->useragent_id ];
						$results[ $row->useragent_id ] = $cur + 0;
						$states[ $row->useragent_id ] = "new";
					}

					$last = $row->run_id;
				}


				foreach ( $results as $key => $fail ) {
					$output .= '<td class="status-' . $states[$key] . '"></td>';
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
							preg_replace('/\w+ /', "", $browser["name"]) .
							'</span></div></th>';
					}
					$last_browser = $browser;
				}
				$header .= "</tr>\n";
				$output = $header . $output;
			}

			$html .= "$output</tr>\n</tbody>\n</table>";
		}

		if ( !$html ) {
			return "No active useragents or jobs.";
		}

		return $html;
	}

}
