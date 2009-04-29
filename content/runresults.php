<?php
	$run_id = ereg_replace("[^0-9]", "", $_REQUEST['run_id']);
	$client_id = ereg_replace("[^0-9]", "", $_REQUEST['client_id']);

	$result = mysql_queryf("SELECT results FROM run_client WHERE run_id=%s AND client_id=%s;",
		$run_id, $client_id);
	
	if ( $row = mysql_fetch_array($result) ) {
		echo $row[0];
	}
?>
