<?php
/**
 * Purge jobs older than a certain date.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class PurgeScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Purge database entries for `jobs`.'
			. ' Including related rows in other tables, such as `runs`, `run_useragent`'
			. ' and `runresults`.'
		);
		$this->registerOption( 'quick', 'boolean', 'Skip the countdown warning that allowed aborting the script without damage.' );
		$this->registerOption( 'maxage', 'value', 'Replace all data older than this number of days. Use "0" to delete all jobs.' );
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

		if ( !$this->getOption( 'quick' ) ) {
			if ( $maxage === 0 ) {
				$this->timeWarningForScriptWill( 'permanently delete all jobs!' );
			} else {
				$date = gmdate( 'Y-m-d H:i:s', $timestamp );
				$this->timeWarningForScriptWill( "permanently delete jobs created before $date ($maxage day ago)" );
			}
		}

		$this->purgeData( $timestamp, $batchSize );
	}

	protected function purgeData( $timestamp, $batchSize ) {
		$date = swarmdb_dateformat( $timestamp );

		// Based on ManageProjectScript::delete()
		$stats = array();
		$db = $this->getContext()->getDB();
		while ( true ) {
			$jobRows = $db->getRows(str_queryf(
				'SELECT id
				FROM jobs
				WHERE created < %s
				LIMIT %u;',
				$date,
				$batchSize
			));
			if ( !$jobRows ) {
				// Done
				break;
			}
			$jobIDs = array_map( function ( $row ) { return $row->id; }, $jobRows );
			$this->out( '...deleting ' . count( $jobIDs ) . ' jobs' );
			$action = WipejobAction::newFromContext( $this->getContext() );
			$result = $action->doWipeJobs( 'delete', $jobIDs, $batchSize );
			$this->mergeStats( $stats, $result );
		}
		// TODO: Purge rows from clients table for clients that are no
		// longer active and don't have 0 runresults after the purge.
		foreach ( $stats as $key => $val ) {
			$this->out( "deleted $key rows: $val");
		}
		$this->out( '' );
		$this->out( 'Done!' );
	}

	protected function mergeStats( array &$target, array $add ) {
		foreach ( $add as $key => $val ) {
			if ( isset( $target[$key] ) ) {
				$target[$key] += $val;
			} else {
				$target[$key] = $val;
			}
		}
	}
}

$script = PurgeScript::newFromContext( $swarmContext );
$script->run();
