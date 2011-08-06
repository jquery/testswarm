<?php
	require "inc/init.php";

	$run_id = preg_replace("/[^0-9]/", "", getItem('run_id', $_POST, ''));
	$fail = preg_replace("/[^0-9-]/", "", getItem('fail', $_POST, ''));
	$error = preg_replace("/[^0-9-]/", "", getItem('error', $_POST, ''));
	$total = preg_replace("/[^0-9-]/", "",getItem('total', $_POST, ''));
	$results = gzencode(getItem('results', $_POST, ''));

	# Make sure we've received some results from the client
	if ( $results ) {
		mysql_queryf("UPDATE run_client SET status=2, fail=%u, error=%u, total=%u, results=%s WHERE client_id=%u AND run_id=%u LIMIT 1;", $fail, $error, $total, $results, $client_id, $run_id);

		if ( mysql_affected_rows() > 0 ) {
			# If we're 100% passing we don't need any more runs
			if ( $total > 0 && $fail == 0 && $error == 0 ) {
				# Clear out old runs that were bad, since we now have a good one
				$result = mysql_queryf("SELECT client_id FROM run_client, clients WHERE run_id=%u AND client_id!=%u AND (total <= 0 OR error > 0 OR fail > 0) AND clients.id=client_id AND clients.useragent_id=%u;", $run_id, $client_id, $useragent_id);

				while ( $row = mysql_fetch_array($result) ) {
					mysql_queryf("DELETE FROM run_client WHERE run_id=%u AND client_id=%u;", $run_id, $row[0]);
				}
	
				mysql_queryf("UPDATE run_useragent SET runs = max, completed = completed + 1, status = 2 WHERE useragent_id=%u AND run_id=%u LIMIT 1;", $useragent_id, $run_id);
			} else {
				if ( $total > 0 ) {
					# Clear out old runs that timed out.
					$result = mysql_queryf("SELECT client_id FROM run_client, clients WHERE run_id=%u AND client_id!=%u AND total <= 0 AND clients.id=client_id AND clients.useragent_id=%u;", $run_id, $client_id, $useragent_id);
	
					while ( $row = mysql_fetch_array($result) ) {
						mysql_queryf("DELETE FROM run_client WHERE run_id=%u AND client_id=%u;", $run_id, $row[0]);
					}
				}

				mysql_queryf("UPDATE run_useragent SET completed = completed + 1, status = IF(completed+1<max, 1, 2) WHERE useragent_id=%u AND run_id=%u LIMIT 1;", $useragent_id, $run_id);
			}
		}
		
		mysql_queryf("UPDATE scores SET score = score + %u WHERE name = %s LIMIT 1", $total, $username);
		
		if(mysql_affected_rows() <= 0) {
			mysql_queryf("INSERT INTO scores (name, score) SELECT users.name, sum(total) FROM clients, run_client, users WHERE clients.id = run_client.client_id and users.id = clients.user_id and users.id = %u;", $user_id);
		}
	}
	echo "<script>window.top.done();</script>";
	exit();
