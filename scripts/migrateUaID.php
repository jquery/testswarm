<?php
/**
 * runToken.php
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class MigrateUaIDScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription( 'Migrate one uaID to another.' );
		$this->registerOption( 'from', 'value', 'The uaID to be replaced.', true );
		$this->registerOption( 'to', 'value', 'The uaID to be put in place of the uaID given in the "from" option.', true );
		$this->registerOption( 'batch-size', 'value', 'How many rows to analyize at one time (default: 200).' );
	}

	protected function execute() {
		$browserIndex = BrowserInfo::getBrowserIndex();

		$batchSize = $this->getOption( 'batch-size' );
		if ( !$batchSize ) {
			$batchSize = 200;
		}

		$fromId = $this->getOption( 'from' );
		$toId = $this->getOption( 'to' );
		if ( !isset( $browserIndex->$toId ) ) {
			$this->error( 'uaID "' . $toId . '" must be added to the index.' );
		}

		$c1 = $this->runBatchTable( 'clients', $fromId, $toId, $batchSize );
		$c2 = $this->runBatchTable( 'run_useragent', $fromId, $toId, $batchSize );

		$this->out( "Migration complete [$c1 clients rows, $c2 run_useragent rows]" );
	}

	protected function runBatchTable( $table, $fromId, $toId, $batchSize ) {
		$db = $this->getContext()->getDB();

		$start = $db->getOne( "SELECT MIN(id) FROM $table;" );
		$end = $db->getOne( "SELECT MAX(id) FROM $table;" );
		if ( !$start || !$end ) {
			$this->error( "The $table table appears empty." );
		}

		$count = 0;
		$blockStart = $start;
		$blockEnd = min( $start + $batchSize - 1, $end );
		while ( $blockEnd <= $end ) {
			$this->out( "...doing $table.id $blockStart to $blockEnd" );

			$db->query( str_queryf(
				"UPDATE $table
				SET useragent_id = %s
				WHERE id BETWEEN $blockStart AND $blockEnd
				AND  useragent_id = %s;
				",
				$toId,
				$fromId
			) );
			$count += $db->getAffectedRows(0);

			$blockStart += $batchSize;
			$blockEnd += $batchSize;

			usleep( 250 * 1000 ); // Wait 250ms
		}
		return $count;
	}
}

$script = MigrateUaIDScript::newFromContext( $swarmContext );
$script->run();
