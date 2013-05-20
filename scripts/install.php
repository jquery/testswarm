<?php
/**
 * Install database.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class DBInstallScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Install the TestSwarm database. Can also clear a database if it exists.'
		);
		$this->registerOption( 'force', 'boolean', 'Skip confirmation for dropping existing tables.' );
	}

	protected function execute() {
		global $swarmInstallDir;

		if ( $this->getContext()->dbLock() ) {
			$this->error( 'Database is currently locked, please remove ./cache/database.lock first.' );
		}

		$db = $this->getContext()->getDB();

		$this->out( 'Setting database.lock, other requests may not access the database during installation.' );
		$this->getContext()->dbLock( true );

		$dbTables = array(
			// Order matters (due to foreign key restrictions before 1.0)
			'projects', // New in 1.0.0
			'runresults', // New in 1.0.0
			'run_client', // Removed in 1.0.0
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
			if ( !$this->getOption( 'force' ) ) {
				$this->out( 'Database already contains tables. If you continue, all tables will be dropped. (Y/N)' );
				$doDrop = $this->cliInput();
				if ( $doDrop !== 'Y' ) {
					$this->getContext()->dbLock( false );
					$this->out( "Installation aborted.\nRemoved database.lock" );
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
				$dropped = $db->query( 'DROP TABLE ' . $db->addIdentifierQuotes( $dbTable ) );
				$this->out( ' ' . ($dropped ? 'OK' : 'FAILED' ) );
			} else {
				$this->out( 'OK (skipped, didn\'t exist)' );
			}
		}
	}

	protected function doInstallDB() {
		global $swarmInstallDir;
		$db = $this->getContext()->getDB();

		// Create new tables
		$this->outRaw( 'Creating new tables (this may take a few minutes)...' );

		$fullSchemaFile = "$swarmInstallDir/config/tables.sql";
		if ( !is_readable( $fullSchemaFile ) ) {
			$this->error( 'Can\'t read schema file' );
		}
		$fullSchemaSql = file_get_contents( $fullSchemaFile );
		$executed = $db->batchQueryFromFile( $fullSchemaSql );
		if ( !$executed ) {
			$this->error( 'Creating new tables failed' );
		}
		$this->out( 'OK' );
	}
}

$script = DBInstallScript::newFromContext( $swarmContext );
$script->run();

