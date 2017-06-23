<?php
/**
 * Abort idle runs older than a certain date.
 *
 * Main use case is when the test suites themselves are only stored for a limited
 * period of time. In that case, it makes sense to automatically abort runs
 * beyond a certain date as they would never completed and only waste browser
 * cycles waiting for a 404 Not Found page to time out.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class AbortIdleRuns extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Mark as "aborted" the idle runs older than a certain date in `run_useragent`.'
		);
		$this->registerOption( 'maxage', 'value', 'Replace all data older than this number of days.' );
		$this->registerOption( 'batch-size', 'value', 'How many rows to analyize at one time (default: 100).' );
	}

	protected function execute() {
		$db = $this->getContext()->getDB();

		$maxage = $this->getOption( 'maxage' );
		if ( $maxage === false || !ctype_digit( $maxage ) ) {
			$this->error( '--maxage=INT is required.' );
		}
		$maxage = intval( $maxage );
		$batchSize = intval( $this->getOption( 'batch-size' ) ) ?: 100;
		$timestamp = time() - ( $maxage * 24 * 3600 );

		$this->purgeRuns( $timestamp, $batchSize );
	}

	protected function purgeRuns( $timestamp, $batchSize ) {
		$date = swarmdb_dateformat( $timestamp );

		// Based on CleanupAction and SaverunAction
		$stats = array();
		$db = $this->getContext()->getDB();
		while ( true ) {
			$res = $db->getRows(str_queryf(
				'SELECT id
				FROM run_useragent
				WHERE status = 0
				AND created < %s
				LIMIT %u;',
				$date,
				$batchSize
			));
			if ( !$res ) {
				// Done
				break;
			}
			$runIDs = array_map( function ( $row ) { return $row->id; }, $res );
			$this->out( '...procesing ' . count( $res ) . ' rows' );
			$db->query(str_queryf(
				'UPDATE run_useragent
				SET status = 2
				WHERE id IN %l;',
				$runIDs
			));
			$affected = $db->getAffectedRows();
			if ( $affected != count( $runIDs ) ) {
				$this->out( 'aborted ' . $affected . ' runs' );
			}
		}
		$this->out( '' );
		$this->out( 'Done!' );
	}
}

$script = AbortIdleRuns::newFromContext( $swarmContext );
$script->run();
