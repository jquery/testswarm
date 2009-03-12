<?php

	$title = "Add New Job";

	if ( $_POST['state'] === "addjob" ) {
		mysql_queryf("INSERT INTO jobs (user_id,name,created) VALUES(%u,%s,NOW());",
			$user_id, $_POST['job_name']);

		$job_id = mysql_insert_id();

		foreach ( $_POST['suites'] as $suite_num => $suite_name ) {
			if ( $suite_name ) {
				#echo "$suite_num " . $_POST['suites'][$suite_num] . " " . $_POST['urls'][$suite_num] . "<br>";
				mysql_queryf("INSERT INTO runs (job_id,name,url,created) VALUES(%u,%s,%s,NOW());",
					$job_id, $suite_name, $_POST['urls'][$suite_num]);

				$run_id = mysql_insert_id();

				foreach ( $_POST['browsers'] as $browser_num ) {
					mysql_queryf("INSERT INTO run_useragent (run_id,useragent_id,max,created) VALUES(%u,%u,%u,NOW());",
						$run_id, $browser_num, $_POST['max']);
				}
			}
		}

		header("Location: /?state=jobstatus&job_id=" . $job_id);
		exit();
	}

?>
