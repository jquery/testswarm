<?php
	include "inc/init.php";

	$run_id = ereg_replace("[^0-9]", "", $_POST['run_id']);
	$fail = ereg_replace("[^0-9]", "", $_POST['fail']);
	$total = ereg_replace("[^0-9]", "", $_POST['total']);
	$results = $_POST['results'];

	# Make sure we've received some results from the client
	if ( $results ) {
		mysql_queryf("UPDATE run_client SET status=2, fail=%u, total=%u, results=%s WHERE client_id=%u AND run_id=%u;", $fail, $total, $results, $client_id, $run_id);
		mysql_queryf("UPDATE run_useragent SET completed = completed + 1, status = IF(completed+1<max, 1, 2) WHERE useragent_id=%u AND run_id=%u;", $useragent_id, $run_id);

		# Figure out if we can mark the full run as being completed
		$result = mysql_queryf("SELECT * FROM run_useragent WHERE run_id=%u AND status < 2;", $run_id);

		if ( mysql_num_rows($result) == 0 ) {
			mysql_queryf("UPDATE runs SET status=2 WHERE id=%u;", $run_id);

			# Figure out if we can mark the full job as being completed
			$job_id = 0;

			$result = mysql_queryf("SELECT job_id FROM runs WHERE id=%u;", $run_id);

			if ( $row = mysql_fetch_array($result) ) {
				$job_id = $row[0];
			}

			$result = mysql_queryf("SELECT id FROM runs WHERE job_id=%u AND status < 2;", $job_id);

			if ( mysql_num_rows($result) == 0 ) {
				mysql_queryf("UPDATE jobs SET status=2 WHERE id=%u;", $job_id);
			}
		}
	}

	echo "<script>window.top.done();</script>";

	exit();
?>
