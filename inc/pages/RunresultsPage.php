<?php
/**
 * "Runresults" page.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */

class RunresultsPage extends Page {

	public function execute() {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		$runID = $request->getInt( "run_id" );
		$clientID = $request->getInt( "client_id" );

		if ( $runID && $clientID ) {
			$row = $db->getRow(str_queryf(
				"SELECT
					results
				FROM
					run_client
				WHERE run_id = %s
				AND   client_id = %s;",
				$runID, $clientID
			));

			if ( $row ) {
				header( "Content-Type: text/html; charset=utf-8" );
				header( "Content-Encoding: gzip" );
				echo $row->results;

				// Prevent Page from building
				exit;
			}
		}
		// We're still here, continue building the page,
		parent::execute();
	}

	protected function initContent() {
		// If we got here, we've got an error
		$this->setTitle( "Run results" );
		return '<div class="errorbox">Invalid or missing <code>run_id</code>/<code>client_id</code> parameters.</div>';
	}

}
