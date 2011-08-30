<?php

	$title = "Add New Job";

	if ( $_REQUEST["state"] == "addjob" ) {
		$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $_REQUEST["user"]);
		$auth = preg_replace("/[^a-z0-9]/", "", $_REQUEST["auth"]);

		$result = mysql_queryf("SELECT id FROM users WHERE name=%s AND auth=%s;", $username, $auth);

		if ( $row = mysql_fetch_array($result) ) {
			$user_id = intval($row[0]);

		# TODO: Improve error message quality.
		} else {
			echo "Incorrect username or auth token.";
			exit();
		}

		mysql_queryf("INSERT INTO jobs (user_id,name,created) VALUES(%u,%s,NOW());",
			$user_id, $_REQUEST["job_name"]);

		$job_id = mysql_insert_id();

		foreach ( $_REQUEST["suites"] as $suite_num => $suite_name ) {
			if ( $suite_name ) {
				#echo "$suite_num " . $_REQUEST["suites"][$suite_num] . " " . $_REQUEST["urls"][$suite_num] . "<br>";
				mysql_queryf("INSERT INTO runs (job_id,name,url,created) VALUES(%u,%s,%s,NOW());",
					$job_id, $suite_name, $_REQUEST["urls"][$suite_num]);

				$run_id = mysql_insert_id();

				$ua_type = "1 = 1";

				if ( $_REQUEST["browsers"] == "popular" ) {
					$ua_type = "popular = 1";
				} else if ( $_REQUEST["browsers"] == "current" ) {
					$ua_type = "current = 1";
				} else if ( $_REQUEST["browsers"] == "gbs" ) {
					$ua_type = "gbs = 1";
				} else if ( $_REQUEST["browsers"] == "beta" ) {
					$ua_type = "beta = 1";
				} else if ( $_REQUEST["browsers"] == "mobile" ) {
					$ua_type = "mobile = 1";
				} else if ( $_REQUEST["browsers"] == "popularbeta" ) {
					$ua_type = "(popular = 1 OR beta = 1)";
				} else if ( $_REQUEST["browsers"] == "popularbetamobile" ) {
					$ua_type = "(popular = 1 OR beta = 1 OR mobile = 1)";
				}

				$result = mysql_queryf("SELECT id FROM useragents WHERE active = 1 AND $ua_type;");

				while ( $row = mysql_fetch_array($result) ) {
					$browser_num = $row[0];
					mysql_queryf("INSERT INTO run_useragent (run_id,useragent_id,max,created) VALUES(%u,%u,%u,NOW());",
						$run_id, $browser_num, $_REQUEST["max"]);
				}
			}
		}

		$url = "job/$job_id/";

		if ( $_REQUEST["output"] == "dump" ) {
			echo $url;
		} else {
			header("Location: $url");
		}

		exit();
	}
