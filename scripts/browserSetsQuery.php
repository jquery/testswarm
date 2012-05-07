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
		$swarmUaIndex = BrowserInfo::getSwarmUAIndex();

		$browserSets = array();
		foreach ( $swarmUaIndex as $uaId => $uaData ) {
			foreach ( $uaData as $propKey => $propVal ) {
				if ( $propKey !== 'displaytitle' && $propKey !== 'displayicon' && $propVal ) {
					$browserSets[$propKey][] = $uaData->displaytitle;
				}
			}
		}

		// Output
		$set = $this->getOption( 'set' );
		if ( $set ) {
			if ( isset( $browserSets[$set] ) ) {
				$this->out( "$set:\n* " . implode( "\n* ", $browserSets[$set] ) );
			} else {
				$this->error( "Browser set `$set` does not exist." );
			}
		} else {
			natcaseksort($browserSets);
			foreach ( $browserSets as $set => $browsers ) {
				$this->out( "\n$set:\n* " . implode( "\n* ", $browsers ) );
			}
		}
	}
}

$script = BrowserSetsQueryScript::newFromContext( $swarmContext );
$script->run();
