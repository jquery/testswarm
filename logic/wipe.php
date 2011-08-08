<?php

	$result = mysql_queryf("SELECT run_id, client_id, useragent_id FROM run_client, clients WHERE DATE_ADD(run_client.updated, INTERVAL 5 MINUTE) < NOW() AND clients.id = client_id AND run_client.status = 1;");

	while ( $row = mysql_fetch_array($result) ) {
		$run_id = $row[0];
		$client_id = $row[1];
		$useragent_id = $row[2];

		# Update run_useragent (clients, useragents)
		mysql_queryf("UPDATE run_useragent SET runs = runs - 1 WHERE run_id=%u AND useragent_id=%u;", $run_id, $useragent_id);
		mysql_queryf("DELETE FROM run_client WHERE run_id=%u AND client_id=%u;", $run_id, $client_id);
	}

	# Reset runs that race-condition deleted themselves
	mysql_queryf("UPDATE run_useragent SET runs=0, completed=0, status=0 WHERE runs=max AND NOT EXISTS (SELECT * FROM run_client, clients WHERE run_client.run_id=run_useragent.run_id AND run_client.client_id=clients.id AND clients.useragent_id=run_useragent.useragent_id);");

	echo "done";
	exit;
