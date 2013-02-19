<?php
/**
 * fixRunresultCorruption.php
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class FixRunresultCorruptionScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Scan runresults and run_useragent tables for data that was corrupted. In particular runresults with status=4 (Client Lost) that were not unlinked from run_useragent (as CleanupAction should do).'
		);
	}

	protected function execute() {
		$db = $this->getContext()->getDB();

		$corrupt = 0;
		$this->out( "Scanning..." );
		$resultRows = $db->getRows('SELECT id FROM runresults WHERE status=4;');
		if ( $resultRows ) {
			foreach ( $resultRows as $resultRow ) {
				$runRow = $db->getOne( str_queryf( 'SELECT 1 FROM run_useragent WHERE results_id=%u;', $resultRow->id ) );
				if ( $runRow ) {
					$corrupt++;
					$this->outRaw( "Result #{$resultRow->id}" );
					// See also CleanupAction::doAction					
					$executed = $db->query(str_queryf(
						"UPDATE run_useragent
						SET
							status = 0,
							results_id = NULL
						WHERE results_id = %u;",
						$resultRow->id
					));
					if ( $executed ) {
						$this->outRaw( " ... Fixed!\n" );
					} else {
						$this->outRaw( " ... Failed!\n" );
					}
					$this->out( "..." );
				}
				
			}
		}
		
		$this->out( "Found {$corrupt} instances of corrupted data." );
	}
}

$script = FixRunresultCorruptionScript::newFromContext( $swarmContext );
$script->run();
