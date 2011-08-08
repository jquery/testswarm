<?php

	function get_status($num){
		if ( $num == 0 ) {
			return "Not started yet.";
		} else if ( $num == 1 ) {
			return "In progress.";
		} else {
			return "Completed.";
		}
	}

	function get_status2($num, $fail, $error, $total){
		if ( $num == 0 ) {
			return "notstarted notdone";
		} else if ( $num == 1 ) {
			return "progress notdone";
		} else if ( $num == 2 && $fail == -1 ) {
			return "timeout";
		} else if ( $num == 2 && ($error > 0 || $total == 0) ) {
			return "error";
		} else {
			return $fail > 0 ? "fail" : "pass";
		}
	}

	$result = mysql_query("SELECT useragents.engine as engine, useragents.name as name, clients.os as os, DATE_FORMAT(clients.created, '%Y-%m-%dT%H:%i:%sZ') as since FROM users, clients, useragents WHERE clients.useragent_id=useragents.id AND DATE_ADD(clients.updated, INTERVAL 1 minute) > NOW() AND clients.user_id=users.id AND users.name='$search_user' ORDER BY useragents.engine, useragents.name;");

	if ( mysql_num_rows($result) > 0 ) {

	echo "<h3>Active Clients:</h3><ul class='clients'>";

	while ( $row = mysql_fetch_array($result) ) {
		$engine = $row[0];
		$browser_name = $row[1];
		$name = $row[2];
		$since = $row[3];

		if ( $name == "xp" ) {
			$name = "Windows XP";
		} else if ( $name == "vista" ) {
			$name = "Windows Vista";
		} else if ( $name == "win7" ) {
			$name = "Windows 7";
		} else if ( $name == "2000" ) {
			$name = "Windows 2000";
		} else if ( $name == "2003" ) {
			$name = "Windows 2003";
		} else if ( $name == "osx10.4" ) {
			$name = "OS X 10.4";
		} else if ( $name == "osx10.5" ) {
			$name = "OS X 10.5";
		} else if ( $name == "osx10.6" ) {
			$name = "OS X 10.6";
		} else if ( $name == "osx" ) {
			$name = "OS X";
		} else if ( $name == "linux" ) {
			$name = "Linux";
		}

		echo "<li><img src='" . swarmpath( "/" ) . "images/$engine.sm.png' class='$engine'/> <strong class='name'>$browser_name $name</strong><br>Connected <span title='$since' class='pretty'>$since</span></li>";
	}

	echo "</ul>";

	}

	$job_search = preg_replace("/[^a-zA-Z ]/", "", getItem( "job", $_REQUEST, "" ) );
	$job_search .= "%";

	$search_result = mysql_queryf("SELECT jobs.name, jobs.status, jobs.id FROM jobs, users WHERE jobs.name LIKE %s AND users.name=%s AND jobs.user_id=users.id ORDER BY jobs.created DESC LIMIT 15;", $job_search, $search_user);

	if ( mysql_num_rows($search_result) > 0 ) {

	echo '<br/><h3>Recent Jobs:</h3><table class="results"><tbody>';

	$output = "";
	$browsers = array();
	$addBrowser = true;

	while ( $row = mysql_fetch_array($search_result) ) {
		$job_name = $row[0];
		$job_status = get_status(intval($row[1]));
		$job_id = $row[2];

		$output .= '<tr><th><a href="' . swarmpath( "job/{$job_id}/" ) . '">' . strip_tags($job_name) . "</a></th>\n";

		$results = array();
		$states = array();

	$result = mysql_queryf("SELECT runs.id as run_id, runs.url as run_url, runs.name as run_name, useragents.engine as browser, useragents.name as browsername, useragents.id as useragent_id, run_useragent.status as status FROM run_useragent, runs, useragents WHERE runs.job_id=%u AND run_useragent.run_id=runs.id AND run_useragent.useragent_id=useragents.id ORDER BY run_id, browsername;", $job_id);

	$last = "";

	while ( $row = mysql_fetch_assoc($result) ) {
		if ( $row["run_id"] != $last ) {
			if ( $last ) {
				if ( $addBrowser ) {
					$header = "<tr><th></th>\n";
					$last_browser = array();
					foreach ( $browsers as $browser ) {
						if ( $last_browser["id"] != $browser["id"] ) {
							$header .= '<th><div class="browser">' .
								'<img src="' . swarmpath( 'images/' ) . $browser["engine"] .
								'.sm.png" class="browser-icon ' . $browser["engine"] .
								'" alt="' . $browser["name"] .
								'" title="' . $browser["name"] .
								'"/><span class="browser-name">' .
								preg_replace('/\w+ /', "", $browser["name"]) . ', ' .
								'</span></div></th>';
						}
						$last_browser = $browser;
					}
					$header .= "</tr>\n";
					$output = $header . $output;
				}

				$addBrowser = false;
			}

			$useragents = array();

			$runResult = mysql_queryf("SELECT run_client.client_id as client_id, run_client.status as status, run_client.fail as fail, run_client.error as error, run_client.total as total, clients.useragent_id as useragent_id, useragents.name as browser FROM useragents, run_client, clients WHERE run_client.run_id=%u AND run_client.client_id=clients.id AND useragents.id=useragent_id ORDER BY browser;", $row["run_id"]);

			while ( $ua_row = mysql_fetch_assoc($runResult) ) {
				if ( !$useragents[ $ua_row["useragent_id"] ] ) {
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

		if ( $useragents[ $row["useragent_id"] ] ) {
			foreach ( $useragents[ $row["useragent_id"] ] as $ua ) {
				$status = get_status2(intval($ua["status"]), intval($ua["fail"]), intval($ua["error"]), intval($ua["total"]));
				if ( $last_browser != $ua["browser"] ) {
					$cur = $results[ $ua["useragent_id"] ];
					$results[ $ua["useragent_id"] ] = $cur + intval($ua["fail"]);

					$cur = $states[ $ua["useragent_id"] ];

					if ( strstr($status, "notdone") || strstr($cur, "notdone") ) {
						$status = "notstarted notdone";
					} else if ( $status == "error" || $cur == "error" ) {
						$status = "error";
					} else if ( $status == "timeout" || $cur == "timeout" ) {
						$status = "timeout";
					} else if ( $status == "fail" || $cur == "fail" ) {
						$status = "fail";
					} else {
						$status = "pass";
					}

					$states[ $ua["useragent_id"] ] = $status;
				}
				$last_browser = $ua["browser"];
			}
		} else {
				$cur = $results[ $row["useragent_id"] ];
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

	echo "$output</tr>\n</tbody>\n</table>";

	}
