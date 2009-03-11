<?php
	$run_id = ereg_replace("[^0-9]", "", $_REQUEST['run_id']);

	$result = mysql_queryf("SELECT url FROM runs WHERE id=%s;", $run_id);

	if ( $row = mysql_fetch_array($result) ) {
		header("Location: " . $row[0]);

	# TODO: Better error message
	} else {
		echo "ERROR: Incorrect run ID.";
	}

	exit();
?>
