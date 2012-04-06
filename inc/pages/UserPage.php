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
			return '<div class="alert alert-error">User does not exist</div>';
		}

		$uaIndex = BrowserInfo::getSwarmUAIndex();

		$clientRows = $db->getRows($q = str_queryf(
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

		$html = "";

		if ( $clientRows ) {

			$html .= '<h2>Active clients</h2><div class="row">';

			foreach ( $clientRows as $clientRow ) {
				$since_local = date( "r", gmstrtotime( $clientRow->created ) );
				// PHP's "c" claims to be ISO compatible but prettyDate JS disagrees
				// ("2004-02-12T15:19:21+00:00" vs. "2004-02-12T15:19:21Z")
				// Constructing format manually instead
				$since_zulu_iso = gmdate( "Y-m-d\TH:i:s\Z", gmstrtotime( $clientRow->created ) );

				$bi = BrowserInfo::newFromContext( $this->getContext(), $clientRow->useragent );

				if ( isset( $uaIndex->{$clientRow->useragent_id} ) ) {
					$displayicon = $uaIndex->{$clientRow->useragent_id}->displayicon;
					$label = $uaIndex->{$clientRow->useragent_id}->displaytitle;
				} else {
					$displayicon = "unknown";
					$label = "Unrecognized [{$clientRow->useragent_id}]";
				}
				$html .=
					'<div class="span4"><div class="well">'
					. '<img class="pull-right" src="' . swarmpath( "img/{$displayicon}.sm.png" ) . '">'
					. '<strong class="label">' . htmlspecialchars( $label ) . '</strong>'
					. '<p><small>Platform: ' . htmlspecialchars( $bi->getBrowscap()->Platform )
					. '</small><br><small>Connected <span title="'
					. htmlspecialchars( $since_zulu_iso ) . '" class="pretty">'
					. htmlspecialchars( $since_local ) . '</small></p>'
					. '</div></div>';
			}

			$html .= '</div>';

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
			$html .= '<h2>Recent jobs</h2><table class="table table-bordered swarm-results"><tbody>';

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
						$header .= '<th>' .
							'<img src="' . swarmpath( 'img/' ) . $browser["engine"] .
							'.sm.png" class="swarm-browsericon ' . $browser["engine"] .
							'" alt="' . $browser["name"] .
							'" title="' . $browser["name"] .
							'"><br>' .
							preg_replace('/\w+ /', "", $browser["name"]) .
							'</th>';
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
