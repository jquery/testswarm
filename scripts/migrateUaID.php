<?php
/**
 * Migrate user agent IDs.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class MigrateUaIDScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription( 'Migrate one uaID to another.' );
		$this->registerOption( 'find-unknown', 'boolean', 'Search for uaID references that are not (anymore) defined in localSettings.' );
		$this->registerOption( 'replace-unknown', 'boolean', 'Search and replace unknown uaID references (interactive).' );
		$this->registerOption( 'from', 'value', 'Replace this uaID with the "to" value.' );
		$this->registerOption( 'to', 'value', 'Replace the "from" uaID with this one.' );
		$this->registerOption( 'batch-size', 'value', 'How many rows to analyize at one time (default: 500).' );
	}

	protected function execute() {
		$browserIndex = BrowserInfo::getBrowserIndex();

		$batchSize = $this->getOption( 'batch-size' );
		if ( !$batchSize ) {
			$batchSize = 500;
		}

		$findUnknown = $this->getOption( 'find-unknown' );
		$replaceUnknown = $this->getOption( 'replace-unknown' );
		if ( $findUnknown || $replaceUnknown ) {
			$found1 = $this->findUnknown( 'clients', $batchSize, $replaceUnknown );
			$found2 = $this->findUnknown( 'run_useragent', $batchSize, $replaceUnknown );
			if ( !$replaceUnknown ) {
				$found = array_values( array_unique( array_merge( $found1, $found2 ) ) );
				natsort( $found );
				if ( $found ) {
					$this->out( 'Found ' . count( $found ) . " unknown uaIDs: \n* " . implode( "\n* ", $found ) );
				} else {
					$this->out( 'No unknown uaIDs found.' );
				}
			}
			return;
		}

		$fromId = $this->getOption( 'from' );
		$toId = $this->getOption( 'to' );

		if ( !$fromId && !$toId ) {
			$this->displayHelp();
			return;
		}

		if ( !$fromId || !$toId ) {
			$this->error( 'Option "from" and "to" are both required.' );
		}

		if ( !isset( $browserIndex->$toId ) ) {
			$this->error( 'Define uaID "' . $toId . '" in the userAgents index before the migration.' );
		}

		$this->runBatch( $fromId, $toId, $batchSize );
	}

	protected function runBatch( $fromId, $toId, $batchSize ) {
		$c1 = $this->runBatchTable( 'clients', $fromId, $toId, $batchSize );
		$c2 = $this->runBatchTable( 'run_useragent', $fromId, $toId, $batchSize );

		$this->out( "Migration '$fromId' -> '$toId' complete [$c1 clients rows, $c2 run_useragent rows]" );
	}

	protected function runBatchTable( $table, $fromId, $toId, $batchSize ) {
		$db = $this->getContext()->getDB();

		$start = $db->getOne( "SELECT MIN(id) FROM $table;" );
		$end = $db->getOne( "SELECT MAX(id) FROM $table;" );
		if ( !$start || !$end ) {
			$this->error( "The $table table appears empty." );
		}

		$count = 0;
		$end += $batchSize - 1; // Do remaining chunk
		$blockStart = $start;
		$blockEnd = min( $start + $batchSize - 1, $end );
		while ( $blockEnd <= $end ) {
			$this->out( "...migrating $table.id $blockStart to $blockEnd" );

			$db->query( str_queryf(
				"UPDATE $table
				SET useragent_id = %s
				WHERE id BETWEEN $blockStart AND $blockEnd
				AND   useragent_id = %s;
				",
				$toId,
				$fromId
			) );

			$blockStart += $batchSize;
			$blockEnd += $batchSize;

			// Optimise for continuing a batch, don't wait if this chunk didn't
			// affect any rows. This way we fast-forward to the next affectable chunk.
			if ( $db->getAffectedRows() ) {
				$count += $db->getAffectedRows();
				usleep( 100 * 1000 ); // Wait 100ms
			}
		}
		return $count;
	}

	protected function findUnknown( $table, $batchSize, $interactive ) {
		$db = $this->getContext()->getDB();
		$browserIndex = BrowserInfo::getBrowserIndex();
		$uaIDs = array_keys( (array) $browserIndex );

		$start = $db->getOne( "SELECT MIN(id) FROM $table;" );
		$end = $db->getOne( "SELECT MAX(id) FROM $table;" );
		if ( !$start || !$end ) {
			$this->error( "The $table table appears empty." );
		}

		$found = array();

		$end += $batchSize - 1; // Do remaining chunk
		$blockStart = $start;
		$blockEnd = min( $start + $batchSize - 1, $end );
		while ( $blockEnd <= $end ) {
			$this->out( "...scanning $table.id $blockStart to $blockEnd" );

			$rows = $db->getRows( str_queryf(
				"SELECT DISTINCT(useragent_id) FROM $table
				WHERE id BETWEEN $blockStart AND $blockEnd
				AND useragent_id NOT IN %l;
				",
				$uaIDs
			) );

			$blockStart += $batchSize;
			$blockEnd += $batchSize;

			// Optimise for continuing a batch, don't wait if this chunk didn't
			// affect any rows. This way we fast-forward to the next affectable chunk.
			if ( $rows ) {
				foreach ( $rows as $row ) {
					// Don't list the same one twice.
					if ( in_array( $row->useragent_id, $found ) ) {
						continue;
					}
					$found[] = $row->useragent_id;
					$this->out( '* ' . $row->useragent_id );

					if ( $interactive ) {
						$this->out( 'Enter replacement uaID (or leave blank to skip):' );

						// Input should either be empty (to skip) or be a valid uaID,
						// otherwise, repeat the question.
						while ( ( $input = $this->cliInput() ) && !isset( $browserIndex->$input ) ) {
							$this->out( 'The entered uaID was not found. Try again.' );
						}
						if ( $input ) {
							$this->runBatch( $row->useragent_id, $input, $batchSize );
						}
					}
				}
			}
		}

		return $found;
	}
}

$script = MigrateUaIDScript::newFromContext( $swarmContext );
$script->run();
