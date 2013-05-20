<?php
/**
 * TestSwarm REPL.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class ReadEvalPrintLoopScript extends MaintenanceScript {

	protected function init() {
		global $swarmInstallDir;
		$this->checkAtty();

		$supportsReadline = function_exists( 'readline_add_history' );

		if ( $supportsReadline ) {
			$historyFile = isset( $_ENV['HOME'] ) ? "{$_ENV['HOME']}/.testswarm_eval_history" : "$swarmInstallDir/config/.testswarm_eval_history";
			readline_read_history( $historyFile );
		}

		while ( !!( $line = $this->cliInput() ) ) {
			if ( $supportsReadline ) {
				readline_add_history( $line );
				readline_write_history( $historyFile );
			}
			$ret = eval( $line . ";" );
			if ( is_null( $ret ) ) {
				echo "\n";
			} elseif ( is_string( $ret ) || is_numeric( $ret ) ) {
				echo "$ret\n";
			} else {
				var_dump( $ret );
			}
		}

		print "\n";
		exit;
	}

	protected function execute() {}
}

$script = ReadEvalPrintLoopScript::newFromContext( $swarmContext );
$script->run();
