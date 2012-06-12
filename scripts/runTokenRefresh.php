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

class RefreshRunTokenScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Sets a (new) run token. Overwrites an existing token if there is one.'
			. ' Running this script does not change any settings. For the token'
			. ' requirement to be enforced, make sure you have `client.requireRunToken = true`'
			. ' set in your configuration file.'
		);
	}

	protected function execute() {
		$this->timeWarningForScriptWill( 'invalidate any existing token' );

		$cacheDir = $this->getContext()->getConf()->storage->cacheDir;
		$cacheFile = $cacheDir . '/run_token_hash.cache';
		if ( file_exists( $cacheFile ) ) {
			$deleted = unlink( $cacheFile );
			if ( !$deleted ) {
				$this->error( "Deletion of cache file failed:\n$cacheFile" );
			}
		}
		$runToken = sha1( mt_rand() );
		$runTokenHash = sha1( $runToken );
		$saved = file_put_contents( $cacheFile, $runTokenHash );
		if ( $saved === false ) {
			$this->error( "Saving of cache file failed:\n$cacheFile" );
		}
		$this->out(
			"Run token has been generated and stored in place.\nNew run token: $runToken"
		);
	}
}

$script = RefreshRunTokenScript::newFromContext( $swarmContext );
$script->run();
