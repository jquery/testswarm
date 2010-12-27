<?php
	$run_id = preg_replace("/[^0-9]/", "", $_REQUEST['run_id']);
	$client_id = preg_replace("/[^0-9]/", "", $_REQUEST['client_id']);

    $sth = $pdo->prepare('SELECT results FROM run_client WHERE run_id=? AND client_id=?;');
    $sth->execute(array($run_id, $client_id));

    if ($row = $sth->fetch()) {
		header("Content-Encoding: gzip");
		echo $row[0];
	}
?>
