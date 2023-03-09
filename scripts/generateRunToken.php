<?php
/**
 * Refresh run token.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class GenerateRunTokenScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Generates a new run token for clients and its hash for the configuration file.'
		);
	}

	protected function execute() {
		$runToken = sha1( mt_rand() );
		$runTokenHash = sha1( $runToken );
		$this->out( "New run token:  $runToken" );
		$this->out( "New token hash: $runTokenHash" );
	}
}

$script = GenerateRunTokenScript::newFromContext( $swarmContext );
$script->run();
