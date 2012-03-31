<?php
/**
 * "Runresults" page.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class RunresultsPage extends Page {

	public function output() {
		$db = $this->getContext()->getDB();

		$run_id    = preg_replace("/[^0-9]/", "", $_REQUEST["run_id"]    );
		$client_id = preg_replace("/[^0-9]/", "", $_REQUEST["client_id"] );

		$result = $db->getRow(str_queryf(
			"SELECT
				results
			FROM
				run_client
			WHERE
				run_id=%s
			AND client_id=%s;",
			$run_id, $client_id)
		);

		header( "Content-Type: text/html; charset=utf-8" );
		header( "Content-Encoding: gzip" );
		echo $result->results;
	}

}
