<?php
	require "inc/init.php";

	$run_id = preg_replace("/[^0-9]/", "", getItem('run_id', $_POST, ''));
	$fail = preg_replace("/[^0-9-]/", "", getItem('fail', $_POST, ''));
	$error = preg_replace("/[^0-9-]/", "", getItem('error', $_POST, ''));
	$total = preg_replace("/[^0-9-]/", "",getItem('total', $_POST, ''));
	$results = gzencode(getItem('results', $_POST, ''));

	# Make sure we've received some results from the client
	if ( $results ) {

		$sth = $pdo->prepare('UPDATE run_client SET status=2, fail=?, error=?, total=?, results=?, updated=? WHERE client_id=? AND run_id=?;');
		$sth->execute(array($fail, $error, $total, $results, time(), $client_id, $run_id));

		if ($sth->rowCount() > 0) {
			# If we're 100% passing we don't need any more runs
			if ( $total > 0 && $fail == 0 && $error == 0 ) {
				# Clear out old runs that were bad, since we now have a good one
				$sth = $pdo->prepare('SELECT client_id FROM run_client, clients WHERE run_id=? AND client_id!=? AND (total <= 0 OR error > 0 OR fail > 0) AND clients.id=client_id AND clients.useragent_id=?;');
				$sth->execute(array($run_id, $client_id, $useragent_id));

				$pdo->beginTransaction();

				$clearout_sth = $pdo->prepare('DELETE FROM run_client WHERE run_id=? AND client_id=?;');

				while ($row = $sth->fetch()) {
					$clearout_sth->execute(array($run_id, $row[0]));
				}

				$sth = $pdo->prepare('UPDATE run_useragent SET runs=max, completed=completed + 1, status=2, updated=? WHERE useragent_id=? AND run_id=?;');
				$sth->execute(array(time(), $useragent_id, $run_id));

				$pdo->commit();
			} else {
				if ( $total > 0 ) {
					# Clear out old runs that timed out.
					$sth = $pdo->prepare('SELECT client_id FROM run_client, clients WHERE run_id=? AND client_id!=? AND total <= 0 AND clients.id=client_id AND clients.useragent_id=?;');
					$sth->execute(array($run_id, $client_id, $useragent_id));

					$pdo->beginTransaction();

					$clearout_sth = $pdo->prepare('DELETE FROM run_client WHERE run_id=? AND client_id=?;');

					while ($row = $sth->fetch()) {
						$clearout_sth->execute(array($run_id, $row[0]));
					}

					$pdo->commit();
				}

				$sth = $pdo->prepare('UPDATE run_useragent SET completed=completed + 1, status=IF(completed+1<max, 1, 2), updated=? WHERE useragent_id=? AND run_id=?;');
				$sth->execute(array(time(), $useragent_id, $run_id));
			}
		}
	}
	echo "<script>window.top.done();</script>";
	exit();
