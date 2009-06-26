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

	function get_status2($num, $fail){
		if ( $num == 0 ) {
			return "notstarted notdone";
		} else if ( $num == 1 ) {
			return "progress notdone";
		} else {
			return $fail > 0 ? "fail" : "pass";
		}
	}

	$job_search = ereg_replace("[^a-zA-Z ]", "", $_REQUEST['job']);
	$job_search .= "%";

	$search_result = mysql_queryf("SELECT name, status, id FROM jobs WHERE name LIKE %s ORDER BY name DESC;", $job_search);

	echo "<table class='results'><tbody>";

	$output = "";
	$browsers = array();
	$addBrowser = true;

	while ( $row = mysql_fetch_array($search_result) ) {
		$job_name = $row[0];
		$job_status = get_status(intval($row[1]));
		$job_id = $row[2];

		#echo "<h3>$job_name</h3><table class='results'><tbody>";
		$output .= '<tr><th><a href="/?state=jobstatus&job_id=' . $job_id . '">' . $job_name . "</a></th>\n";

		$results = array();
		$states = array();

	$result = mysql_queryf("SELECT runs.id as run_id, runs.url as run_url, runs.name as run_name, useragents.engine as browser, useragents.name as browsername, useragents.os as os, useragents.id as useragent_id, run_useragent.status as status FROM run_useragent, runs, useragents, jobs WHERE jobs.id=%u AND runs.job_id=jobs.id AND run_useragent.run_id=runs.id AND run_useragent.useragent_id=useragents.id ORDER BY run_id, browsername;", $job_id);

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
								'<img src="/images/' . $browser["engine"] .
								'.sm.png" class="browser-icon ' . $browser["engine"] .
								'" alt="' . $browser["name"] . ', ' . $browser["os"] .
								'" title="' . $browser["name"] . ', ' . $browser["os"] .
								'"/><span class="browser-name">' .
								preg_replace('/\w+ /', "", $browser["name"]) . ', ' .
								$browser["os"] . '</span></div></th>';
						}
						$last_browser = $browser;
					}
					$header .= "</tr>\n";
					$output = $header . $output;
				}

				#$output .= "</tr>\n";
				$addBrowser = false;
			}

			$useragents = array();

			$runResult = mysql_queryf("SELECT run_client.client_id as client_id, run_client.status as status, run_client.fail as fail, run_client.total as total, clients.useragent_id as useragent_id, users.name as name, useragents.name as browser FROM useragents, run_client, clients, users WHERE run_client.run_id=%u AND run_client.client_id=clients.id AND clients.user_id=users.id AND useragents.id=useragent_id ORDER BY browser;", $row["run_id"]);

			while ( $ua_row = mysql_fetch_assoc($runResult) ) {
				if ( !$useragents[ $ua_row['useragent_id'] ] ) {
					$useragents[ $ua_row['useragent_id'] ] = array();
				}

				array_push( $useragents[ $ua_row['useragent_id'] ], $ua_row );
			}

			#$output .= '<tr><th><a href="/?state=jobstatus&job_id=' . $job_id . '">' . $job_name . "</a></th>\n";
		}

		if ( $addBrowser ) {
			array_push( $browsers, array(
				"name" => $row["browsername"],
				"engine" => $row["browser"],
				"os" => $row["os"],
				"id" => $row["useragent_id"]
			) );
		}

		#echo "<li>" . $row["browser"] . " (" . get_status(intval($row["status"])) . ")<ul>";

		$last_browser = "";

		if ( $useragents[ $row["useragent_id"] ] ) {
			foreach ( $useragents[ $row["useragent_id"] ] as $ua ) {
				$status = get_status2(intval($ua["status"]), intval($ua["fail"]));
				if ( $last_browser != $ua["browser"] ) {
					# $output .= "<td class='$status " . $row["browser"] . "'><a href='/?state=runresults&run_id=" . $row["run_id"] . "&client_id=" . $ua["client_id"] . "'>" . ($ua["status"] == 2 ? $ua["fail"] > 0 ? $ua["fail"] . " test(s) failed." : "Pass" : "") . "</a></td>\n";

					$cur = $results[ $ua['useragent_id'] ];
					$results[ $ua['useragent_id'] ] = $cur + intval($ua["fail"]);

					$cur = $states[ $ua['useragent_id'] ];

					if ( !$cur ) {
						$states[ $ua['useragent_id'] ] = "pass";
					}

					if ( $status != "pass" ) {
						$states[ $ua['useragent_id'] ] = $status;
					}
				}
				$last_browser = $ua["browser"];
			}
		} else {
				$cur = $results[ $row['useragent_id'] ];
				$results[ $row['useragent_id'] ] = $cur + 0;
				$states[ $row['useragent_id'] ] = "notstarted notdone";
			# $output .= "<td class='notstarted notdone'></td>";
		}

		#echo "</ul></li>";

		$last = $row["run_id"];
	}

	foreach ( $results as $key => $fail ) {
		$output .= "<td class='" . $states[$key] . "'>" .
			# ($states[$key] == "pass" || $states[$key] == "fail" ? $fail > 0 ? $fail . " test(s) failed." : "Pass" : "") .
			"</td>";
	}

	$output .= "</tr>\n";

	}

	echo "$output</tr>\n</tbody>\n</table>";
?>
