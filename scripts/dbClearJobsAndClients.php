<?php
/**
 * clearJobsAndClients.php
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class ClearJobsAndClientsScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Deletes all database entries for `jobs` and `clients`.'
			. ' Including related rows in other tables, such as `runs`, `run_useragent`'
			. ' and `runresults.'
		);
		$this->registerOption( 'quick', 'boolean', 'Skip the countdown warning that allowed aborting the script without damage.' );
	}

	protected function execute() {
		$db = $this->getContext()->getDB();

		if ( !$this->getOption( 'quick' ) ) {
			$this->timeWarningForScriptWill( 'permanently delete all jobs' );
		}

		$clearTables = array( 'runresults', 'run_useragent', 'runs', 'clients', 'jobs' );
		foreach ( $clearTables as $clearTable ) {
			$this->out( "...clearing {$clearTable}" );
			$db->query( "DELETE FROM $clearTable WHERE 1" );
			$this->out( "...deleted {$db->getAffectedRows()} rows from $clearTable" );
		}
		$this->out( 'Done!' );
	}
}

$script = ClearJobsAndClientsScript::newFromContext( $swarmContext );
$script->run();
