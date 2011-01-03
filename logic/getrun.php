<?php
	# Uncomment to reload all connected clients.
	#echo "{cmd:'reload',args:''}";
	#exit();

	require "inc/init.php";

	$sth = $pdo->prepare('SELECT run_id FROM run_useragent WHERE useragent_id=? AND runs < max AND NOT EXISTS (SELECT 1 FROM run_client WHERE run_useragent.run_id=run_id AND client_id=?) ORDER BY run_id DESC LIMIT 1;');
	$sth->execute(array($useragent_id, $client_id));

	# A run was found
	if ($row = $sth->fetch()) {
		$run_id = $row[0];

		$sth = $pdo->prepare('SELECT url, jobs.name, runs.name FROM runs, jobs WHERE runs.id=? AND jobs.id=runs.job_id LIMIT 1;');
		$sth->execute(array($run_id));

		if ($row = $sth->fetch()) {
			$url = $row[0];
			$text = $row[1] . " " . ucfirst($row[2]);
		}

		$pdo->beginTransaction();

		# Mark the run as "in progress" on the useragent
		$update_progress_sth = $pdo->prepare('UPDATE run_useragent SET runs=runs + 1, status=1, updated=? WHERE run_id=? AND useragent_id=?');
		$update_progress_sth->execute(array(time(), $run_id, $useragent_id));

		# Initialize the client run
		$client_run_sth = $pdo->prepare('INSERT INTO run_client (run_id, client_id, status, created, updated) VALUES(?,?,1,?,?);');
		$client_run_sth->execute(array($run_id, $client_id, sql_datetime_now(), time()));

		$pdo->commit();

		echo json_encode(array('id' => $run_id, 'url' => $url, 'desc' => $text));
	}

	exit();
