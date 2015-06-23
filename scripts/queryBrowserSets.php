<?php
/**
 * Query browser sets.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class QueryBrowserSetsScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Get list of browsers which currently belong in a browserset.'
		);
		$this->registerOption( 'set', 'value', 'If set, only show this set. Otherwise all sets are output.' );
	}

	protected function execute() {
		$conf = $this->getContext()->getConf();
		$browserIndex = BrowserInfo::getBrowserIndex();

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
			$this->out( "\n(everything):\n* " . implode( "\n* ", array_keys( (array)$browserIndex ) ) );
		}
	}
}

$script = QueryBrowserSetsScript::newFromContext( $swarmContext );
$script->run();
