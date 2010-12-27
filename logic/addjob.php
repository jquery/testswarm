<?php

	$title = "Add New Job";

	if ( $_REQUEST['state'] == "addjob" ) {
		$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $_REQUEST['user']);
		$auth = preg_replace("/[^a-z0-9]/", "", $_REQUEST['auth']);

        $sth = $pdo->prepare('SELECT id FROM users WHERE name=? AND auth=?;');
        $sth->execute(array($username, $auth));

		if ($row = $sth->fetch()) {
			$user_id = intval($row[0]);

		# TODO: Improve error message quality.
		} else {
			echo "Incorrect username or auth token.";
			exit();
		}

        $job_sth = $pdo->prepare('INSERT INTO jobs (user_id,name,created) VALUES(?,?,NOW());');
        $job_sth->execute(array($user_id, $_REQUEST['job_name']));

        $job_id = $pdo->lastInsertId();

		foreach ( $_REQUEST['suites'] as $suite_num => $suite_name ) {
			if ( $suite_name ) {
				#echo "$suite_num " . $_REQUEST['suites'][$suite_num] . " " . $_REQUEST['urls'][$suite_num] . "<br>";

                $pdo->prepare('INSERT INTO runs (job_id,name,url,created) VALUES(?,?,?,NOW());')
                    ->execute(array($job_id, $suite_name, $_REQUEST['urls'][$suite_num]));
                $run_id = $pdo->lastInsertId();

				$ua_type = "1 = 1";

				if ( $_REQUEST['browsers'] == "popular" ) {
					$ua_type = "popular = 1";
				} else if ( $_REQUEST['browsers'] == "current" ) {
					$ua_type = "current = 1";
				} else if ( $_REQUEST['browsers'] == "gbs" ) {
					$ua_type = "gbs = 1";
				} else if ( $_REQUEST['browsers'] == "beta" ) {
					$ua_type = "beta = 1";
				} else if ( $_REQUEST['browsers'] == "mobile" ) {
					$ua_type = "mobile = 1";
				} else if ( $_REQUEST['browsers'] == "popularbeta" ) {
					$ua_type = "(popular = 1 OR beta = 1)";
				} else if ( $_REQUEST['browsers'] == "popularbetamobile" ) {
					$ua_type = "(popular = 1 OR beta = 1 OR mobile = 1)";
				}

                $sth = $pdo->query("SELECT id FROM useragents WHERE active = 1 AND $ua_type;");

                $pdo->beginTransaction();

                $insert_sth = $pdo->prepare('INSERT INTO run_useragent (run_id,useragent_id,max,created) VALUES(?,?,?,NOW());');

				while ($row = $sth->fetch()) {
					$browser_num = $row[0];
                    $insert_sth->execute(array($run_id, $browser_num, $_REQUEST['max']));
				}

                $pdo->commit();
			}
		}

		$url = "$contextpath/job/$job_id/";

		if ( $_REQUEST['output'] == "dump" ) {
			echo $url;
		} else {
			header("Location: $url");
		}

		exit();
	}

?>
