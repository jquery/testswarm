<?php
	$run_id = preg_replace("/[^0-9]/", "", $_POST['run_id']);
	$client_id = preg_replace("/[^0-9]/", "", $_POST['client_id']);

	if ( $run_id && $client_id && $_SESSION['username'] && $_SESSION['auth'] == 'yes' ) {

		$sth = $pdo->prepare('SELECT jobs.id FROM users, jobs, runs WHERE users.name=? AND jobs.user_id=users.id AND runs.id=? AND runs.job_id=jobs.id;');
		$sth->execute(array($_SESSION['username'], $run_id));

		if ($row = $sth->fetch()) {
			$job_id = $row[0];

			$sth = $pdo->prepare('SELECT useragent_id FROM clients WHERE id=?;');
			$sth->execute(array($client_id));

			if ($row = $sth->fetch()) {
				$useragent_id = $row[0];

				$pdo->beginTransaction();

				$sth = $pdo->prepare('DELETE run_client FROM run_client,clients WHERE run_id=? AND clients.id=client_id AND clients.useragent_id=?;');
				$sth->execute(array($run_id, $useragent_id));

				$sth = $pdo->prepare('UPDATE run_useragent SET status=0, runs=0, completed=0, updated=NOW() WHERE run_id=? AND useragent_id=?;');
				$sth->execute(array($run_id, $useragent_id));

				$sth = $pdo->prepare('UPDATE runs SET status=1, updated=NOW() WHERE run_id=?;');
				$sth->execute(array($run_id));

				$pdo->commit();
			}
		}

		header("Location: $contextpath/job/$job_id/");
	}

	exit();
?>
