<?php
	include "inc/init.php";

	$run_id = ereg_replace("[^0-9]", "", $_POST['run_id']);
	$fail = ereg_replace("[^0-9-]", "", $_POST['fail']);
	$error = ereg_replace("[^0-9-]", "", $_POST['error']);
	$total = ereg_replace("[^0-9-]", "", $_POST['total']);
	$results = $_POST['results'];

	# Make sure we've received some results from the client
	if ( $results ) {
		mysql_queryf("UPDATE run_client SET status=2, fail=%u, error=%u, total=%u, results=%s WHERE client_id=%u AND run_id=%u LIMIT 1;", $fail, $error, $total, $results, $client_id, $run_id);

		# If we're 100% passing we don't need any more runs
		if ( $fail == 0 && $error == 0 ) {
			# Clear out old runs that were bad, since we now have a good one
			$result = mysql_queryf("SELECT client_id FROM run_client, clients WHERE run_id=%u AND client_id!=%u AND client_id=clients.id AND clients.useragent_id=%u;", $run_id, $client_id, $useragent_id);

			while ( $row = mysql_fetch_array($result) ) {
				mysql_queryf("DELETE FROM run_client WHERE run_id=%u AND client_id=%u;", $run_id, $row[0]);
			}

			mysql_queryf("UPDATE run_useragent SET runs = max, completed = completed + 1, status = 2 WHERE useragent_id=%u AND run_id=%u LIMIT 1;", $useragent_id, $run_id);
		} else {
			mysql_queryf("UPDATE run_useragent SET completed = completed + 1, status = IF(completed+1<max, 1, 2) WHERE useragent_id=%u AND run_id=%u LIMIT 1;", $useragent_id, $run_id);
		}
	}

	echo "<script>window.top.done();</script>";

	exit();
?>
