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

	$job_id = ereg_replace("[^0-9]", "", $_REQUEST['job_id']);

	$result = mysql_queryf("SELECT name, status FROM jobs WHERE id=%u;", $job_id);

	if ( $row = mysql_fetch_array($result) ) {
		$job_name = $row[0];
		$job_status = get_status(intval($row[1]));
	}

?>

<h2><?=$job_name?> (<?=$job_status?>)</h2>

<?php

	$result = mysql_queryf("SELECT runs.id as run_id, runs.url as run_url, runs.name as run_name, useragents.name as browser, useragents.id as useragent_id, run_useragent.status as status FROM run_useragent, runs, useragents, jobs WHERE jobs.id=%u AND runs.job_id=jobs.id AND run_useragent.run_id=runs.id AND run_useragent.useragent_id=useragents.id ORDER BY run_id;", $job_id);

	$last = "";

	while ( $row = mysql_fetch_assoc($result) ) {
		if ( $row["run_id"] != $last ) {
			if ( $last ) {
				echo "</ul>";
			}

			$useragents = array();

			$runResult = mysql_queryf("SELECT run_client.client_id as client_id, run_client.status as status, run_client.fail as fail, run_client.total as total, clients.useragent_id as useragent_id, users.name as name FROM run_client, clients, users WHERE run_client.run_id=%u AND run_client.client_id=clients.id AND clients.user_id=users.id ORDER BY useragent_id;", $row["run_id"]);

			while ( $ua_row = mysql_fetch_assoc($runResult) ) {
				if ( !$useragents[ $ua_row['useragent_id'] ] ) {
					$useragents[ $ua_row['useragent_id'] ] = array();
				}

				array_push( $useragents[ $ua_row['useragent_id'] ], $ua_row );
			}

			echo '<b><a href="' . $row["run_url"] . '">' . $row["run_name"] . '</a></b><ul>';
		}

		echo "<li>" . $row["browser"] . " (" . get_status(intval($row["status"])) . ")<ul>";

		if ( $useragents[ $row["useragent_id"] ] ) {
			foreach ( $useragents[ $row["useragent_id"] ] as $ua ) {
				echo "<li><a href='/?state=runresults&run_id=" . $row["run_id"] . "&client_id=" . $ua["client_id"] . "'>Results from " . $ua["name"] . "</a> (" . get_status(intval($ua["status"])) . "): " . $ua["fail"] . " test(s) failed.</li>";
			}
		}

		echo "</ul></li>";

		$last = $row["run_id"];
	}

	echo "</ul>";
?>
