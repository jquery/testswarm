<?php
/**
 * browserSetsQuery.php
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class BrowserSetsQueryScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Get list of browsers which currently belong in a browserset.'
		);
		$this->registerOption( 'set', 'value', 'If set, only show this set. Otherwise all sets are output.' );
	}

	protected function execute() {
		$conf = $this->getContext()->getConf();
		$swarmUaIndex = BrowserInfo::getSwarmUAIndex();

		// Output
		$set = $this->getOption( 'set' );
		if ( $set ) {
			if ( isset( $conf->browserSets->$set ) ) {
				$this->out( "$set:\n* " . implode( "\n* ", $conf->browserSets->$set ) );
			} else {
				$this->error( "Browser set `$set` does not exist." );
			}
		} else {
			foreach ( $conf->browserSets as $set => $browsers ) {
				$this->out( "\n$set:\n* " . implode( "\n* ", $browsers ) );
			}
			$this->out( "\n(everything):\n* " . implode( "\n* ", array_keys( get_object_vars( $swarmUaIndex ) ) ) );
		}
	}
}

$script = BrowserSetsQueryScript::newFromContext( $swarmContext );
$script->run();
