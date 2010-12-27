<?php
	$job_id = preg_replace("/[^0-9]/", "", $_POST['job_id']);
	$type = $_POST['type'];

	if ( $job_id && $_SESSION['username'] && $_SESSION['auth'] == 'yes' ) {

        $sth = $pdo->prepare('SELECT runs.id as id FROM users, jobs, runs WHERE users.name=? AND jobs.user_id=users.id AND jobs.id=? AND runs.job_id=jobs.id;');
        $sth->execute(array($_SESSION['username'], $job_id));
        $results = $sth->fetchAll();

		if (count($results) > 0) {
            $pdo->beginTransaction();
			if ( $type == "delete" ) {
                $delete_sth = $pdo->prepare('DELETE FROM run_client WHERE run_id in (select id from runs where job_id=?);');
                $delete_sth->execute(array($job_id));

                $delete_sth = $pdo->prepare('DELETE FROM run_useragent WHERE run_id in (select id from runs where job_id=?);');
                $delete_sth->execute(array($job_id));

                $delete_sth = $pdo->prepare('DELETE FROM runs WHERE job_id=?;');
                $delete_sth->execute(array($job_id));

                $delete_sth = $pdo->prepare('DELETE FROM jobs WHERE id=?;');
                $delete_sth->execute(array($job_id));
			} else {
                $update_sth = $pdo->prepare('UPDATE jobs SET status=0, updated=NOW() WHERE id=?;');
                $update_sth->execute(array($job_id));

                $update_sth = $pdo->prepare('UPDATE runs SET status=0, updated=NOW() WHERE job_id=?;');
                $update_sth->execute(array($job_id));
			}


            foreach ($results as $row) {
                $run_id = $row[0];

                $delete_sth = $pdo->prepare('DELETE FROM run_client WHERE run_id=?;');
                $delete_sth->execute(array($run_id));

                if ( $type == "delete" ) {
                    $delete_sth = $pdo->prepare('DELETE FROM run_useragent WHERE run_id=?;');
                    $delete_sth->execute(array($run_id));
                } else {
                    $update_sth = $pdo->prepare('UPDATE run_useragent SET runs=0, completed=0, status=0, updated=NOW() WHERE run_id=?;');
                    $update_sth->execute(array($run_id));
                }
            }

            $pdo->commit();
        }

		if ( $type == "delete" ) {
			header("Location: $contextpath/user/" . $_SESSION['username'] . "/");
		} else {
			header("Location: $contextpath/job/$job_id/");
		}
	}

	exit();
?>
