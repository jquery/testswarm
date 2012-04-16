<?php
/**
 * dbInstall.php
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once 'inc/init.php';

class DBInstallScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Install the TestSwarm database. Can also clear a database if it exists.'
		);
		$this->registerOption( "force", "boolean", "Skip confirmation for dropping tables." );
	}

	protected function execute() {
		global $swarmInstallDir;

		if ( $this->getContext()->dbLock() ) {
			$this->error( "Database is currently locked, please remove ./cache/database.lock first." );
		}

		$db = $this->getContext()->getDB();

		$this->out( "Setting database.lock, other requests may not access the database during installation." );
		$this->getContext()->dbLock( true );

		$dbTables = array(
			'run_client',
			'clients',
			'run_useragent',
			'useragents', // Removed in 1.0.0
			'runs',
			'jobs',
			'users',
		);
		$tablesExists = false;
		foreach ( $dbTables as $dbTable ) {
			$exists = $db->tableExists( $dbTable );
			if ( $exists ) {
				 $tablesExists = true;
				 break;
			}
		}
		if ( $tablesExists ) {
			if ( !$this->getOption( "force" ) ) {
				$this->out( "Database already contains tables. If you continue, all tables will be dropped. (Y/N)" );
				$doDrop = $this->cliInput();
				if ( $doDrop !== "Y" ) {
					$this->out( "Installation aborted. Removed database.lock" );
					$this->getContext()->dbLock( false );
					return;
				}
			}
			$this->doDropTables( $dbTables );
		}

		$this->doInstallDB();

		$this->getContext()->dbLock( false );
		$this->out( "Removed database.lock.\nInstallation finished!" );

	}

	protected function doDropTables( Array $dbTables ) {
		$db = $this->getContext()->getDB();

		foreach( $dbTables as $dbTable ) {
			$this->outRaw( "Dropping $dbTable table..." );
			$exists = $db->tableExists( $dbTable );
			if ( $exists ) {
				$dropped = $db->query( "DROP TABLE " . $db->addIdentifierQuotes( $dbTable ) );
				$this->out( " " . ($dropped ? "OK" : "FAILED" ) );
			} else {
				$this->out( "OK (skipped, didn't exist)" );
			}
		}
	}

	protected function doInstallDB() {
		global $swarmInstallDir;
		$db = $this->getContext()->getDB();

		// Create new tables
		$this->outRaw( "Creating new tables... (this may take a few minutes)" );

		$fullSchemaFile = "$swarmInstallDir/config/testswarm.sql";
		if ( !is_readable( $fullSchemaFile ) ) {
			$this->error( "Can't read testswarm.sql" );
		}
		$fullSchemaSql = file_get_contents( $fullSchemaFile );
		$executed = $db->batchQueryFromFile( $fullSchemaSql );
		if ( !$executed ) {
			$this->error( "Creating new tables failed" );
		}
		$this->out( "OK" );
	}
}

$script = DBInstallScript::newFromContext( $swarmContext );
$script->run();

