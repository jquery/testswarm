<?php

	$sth = $pdo->query("SELECT run_id, client_id, useragent_id FROM run_client, clients WHERE DATE_ADD(run_client.updated, INTERVAL 5 MINUTE) < NOW() AND clients.id = client_id AND run_client.status = 1;");

    $pdo->beginTransaction();

    $update_sth = $pdo->prepare('UPDATE run_useragent SET runs = runs - 1 WHERE run_id=? AND useragent_id=?;');
    $delete_sth = $pdo->prepare('DELETE FROM run_client WHERE run_id=? AND client_id=?;');

    while ($row = $sth->fetch()) {
		$run_id = $row[0];
		$client_id = $row[1];
		$useragent_id = $row[2];

		# Update run_useragent (clients, useragents)
        $update_sth->execute(array($run_id, $useragent_id));
        $delete_sth->execute(array($run_id, $client_id));
	}

    $pdo->commit();

	# Reset runs that race-condition deleted themselves
    $pdo->query('UPDATE run_useragent SET runs=0, completed=0, status=0 WHERE runs=max AND NOT EXISTS (SELECT * FROM run_client, clients WHERE run_client.run_id=run_useragent.run_id AND run_client.client_id=clients.id AND clients.useragent_id=run_useragent.useragent_id);');

	echo "done";
	exit;
?>
